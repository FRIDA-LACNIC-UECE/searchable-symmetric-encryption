import datetime
import json

import jwt
from flask import jsonify, request
from flask_migrate import Migrate
from sqlalchemy import (Column, Integer, MetaData, String, Table, Text,
                        Unicode, and_, create_engine, func, select, types,
                        update)
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import Session, sessionmaker

from app import app, db
from app.authenticate import jwt_required
from app.models import (AppMeta, MainDB, User, appmeta_share_schema,
                        appmetas_share_schema, maindb_share_schema,
                        maindbs_share_schema, user_share_schema,
                        users_share_schema)

Migrate(app, db)


@app.shell_context_processor
def make_shell_context():
    return dict(
        app=app,
        db=db,
        User=User
    )


@app.route('/')
def index():

    # create engine, reflect existing columns, and create table object for oldTable
    # change this for your source database
    srcEngine = db.get_engine(bind='db2')
    SourceSession = sessionmaker(srcEngine)
    srcEngine._metadata = MetaData(bind=srcEngine)
    srcEngine._metadata.reflect(srcEngine)  # get columns from existing table
    srcEngine._metadata.tables['appmeta'].columns = [
        i for i in srcEngine._metadata.tables['appmeta'].columns if (i.name in ['id', 'email', 'cpf'])]
    srcTable = Table('appmeta', srcEngine._metadata)

    # create engine and table object for newTable
    # change this for your destination database
    destEngine = db.get_engine(bind='db3')
    DestSession = sessionmaker(destEngine)
    destEngine._metadata = MetaData(bind=destEngine)
    destTable = Table('appmeta_copy', destEngine._metadata)

    sourceSession = SourceSession()
    destSession = DestSession()

    # copy schema and create newTable from oldTable
    for column in srcTable.columns:
        if(column.name in ['id', 'email', 'cpf']):
            destTable.append_column(column._copy())

    query = sourceSession.query(
        AppMeta.id, AppMeta.email, AppMeta.cpf).all()

    try:
        if not destTable.exists():
            destTable.create(checkfirst=True)
        destSession.execute('DELETE FROM appmeta_copy')
        destSession.commit()
        for row in query:
            destSession.execute(destTable.insert(row))
        destSession.commit()
    except Exception as e:
        print(e)
    return 'Flask is running'


@app.route('/register', methods=['POST'])
def register():
    name = request.json['name']
    email = request.json['email']
    pwd = request.json['password']

    user = User(name, email, pwd)
    db.session.add(user)
    db.session.commit()

    result = user_share_schema.dump(
        User.query.filter_by(email=email).first()
    )

    return jsonify(result)


@app.route('/login', methods=['POST'])
def login():
    email = request.json['email']
    pwd = request.json['password']

    user = User.query.filter_by(email=email).first()

    if not user or not user.verify_password(pwd):
        return jsonify({
            "error": "Dados incorretos, tente novamente!"
        }), 403

    payload = {
        "id": user.id,
        "exp": datetime.datetime.utcnow() + datetime.timedelta(minutes=60)
    }

    token = jwt.encode(payload, app.config['SECRET_KEY'])

    return jsonify({
        "token": token
    })


@app.route('/protected', methods=['GET'])
@jwt_required
def protected(current_user):
    result = appmetas_share_schema.dump(
        AppMeta.query.all()
    )
    result2 = maindbs_share_schema.dump(
        MainDB.query.all()
    )
    return jsonify(AppMeta=result, MainDB=result2)


def anonymize_cpf(cpf):
    return 'xxx' + cpf[3:12] + 'xx'


@app.route('/anonymize_data', methods=['GET'])
@jwt_required
def encrypt_data(current_user):
    result = appmetas_share_schema.dump(
        AppMeta.query.all()
    )
    for x in result:
        exists = MainDB.query.filter_by(user_id=x['id']).update(
            {"cpf": x['cpf']}, synchronize_session="fetch")
        if(not exists):
            db.session.add(MainDB(x['id'], x['cpf']))

        AppMeta.query.filter_by(
            id=x['id']).update({"cpf": anonymize_cpf(x['cpf'])})
    db.session.commit()
    result = maindbs_share_schema.dump(
        MainDB.query.all()
    )
    result2 = appmetas_share_schema.dump(
        AppMeta.query.all()
    )
    return jsonify(MainDB=result, AppMeta=result2)


@app.route('/deanonymize_data', methods=['GET'])
@jwt_required
def deencrypt_data(current_user):
    result = maindbs_share_schema.dump(
        MainDB.query.all()
    )
    for x in result:
        exists = AppMeta.query.filter_by(id=x['user_id']).update(
            {"cpf": x['cpf']}, synchronize_session="fetch")
        if(not exists):
            return jsonify({
                "error": "User not found!"
            }), 403
        MainDB.query.filter_by(
            user_id=x['user_id']).delete()
    db.session.commit()
    result = maindbs_share_schema.dump(
        MainDB.query.all()
    )
    result2 = appmetas_share_schema.dump(
        AppMeta.query.all()
    )
    return jsonify(MainDB=result, AppMeta=result2)


'''
@app.route('/logout')
def logout():
    logout_user()
    return redirect(url_for('login'))
 '''

if __name__ == "__main__":
    app.run(debug=True, ssl_context=('ca/cert.pem', 'ca/key.pem'))
