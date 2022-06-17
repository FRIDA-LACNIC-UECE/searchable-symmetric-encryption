import datetime

import jwt
from flask import jsonify, request
from flask_migrate import Migrate
from sqlalchemy import (Column, Integer, MetaData, String, Table, Text,
                        Unicode, and_, create_engine, func, select, types,
                        update, inspect)
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import Session, sessionmaker

from api.app import app, db
from api.app.authenticate import jwt_required
from api.app.models import (AppMeta, MainDB, User, appmeta_share_schema,
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
    # engine = db.get_engine()
    # insp = inspect(engine)
    # columns_table = insp.get_columns('users')
    # meta = MetaData()
    # table = Table('users', meta, autoload=True, autoload_with=engine)
    # print(table.columns['id'].type)
    # primaryKeyColNames = [
    #     pk_column.name for pk_column in table.primary_key.columns.values()]
    # print(primaryKeyColNames)
    # for c in columns_table:
    # print(c)
    return 'Flask is running'


@app.route('/create_model', methods=['POST'])
def create_model():

    types = {
        "INTEGER": "Integer",
        "VARCHAR": "String",
        "TINYINT": "Boolean"
    }

    engine = db.get_engine()
    meta = MetaData()
    table_name = request.json['table']
    table = Table(table_name, meta,
                  autoload=True, autoload_with=engine)
    insp = inspect(engine)
    columns_table = insp.get_columns(table_name)
    indexes_table = insp.get_indexes(table_name)
    f = open("demoModel.py", "w")
    f.write("from app import db, ma\n")
    f.write("from flask_login import UserMixin\n")
    f.write(
        "from werkzeug.security import check_password_hash, generate_password_hash\n\n")
    f.write(f"class {table_name}(db.Model, UserMixin):\n")
    f.write(f"\t__tablename__ = '{table_name}'\n")
    columns = ''
    for c in table.c:
        type = str(c.type)
        if(len(columns)):
            columns += ', '
        columns += f"{c.name}"
        for word, initial in types.items():
            type = type.replace(word, initial)
        string = f"\t{c.name} = db.Column(db.{type}"
        if(c.server_default):
            string += f", default={c.server_default}"
        if(c.nullable == False):
            string += f", nullable={c.nullable}"
        if(c.autoincrement == True):
            string += f", autoincrement={c.autoincrement}"
        if(c.primary_key == True):
            string += f", primary_key={c.primary_key}"
        if([index for index in indexes_table if index['name'] == c.name]):
            string += f", unique=True"
        string += ")\n\n"
        f.write(string)
    f.write(f"\tdef __init__(self, {columns}):\n")
    for c in columns.split(', '):
        f.write(f"\t\tself.{c} = {c}\n")
    f.write("\n\tdef __repr__(self):\n")
    f.write(
        f"\t\treturn f'<{table_name} : ")
    for idx, c in enumerate(columns.split(', ')):
        if(idx):
            f.write(f", {{self.{c}}}")
        else:
            f.write(f"{{self.{c}}}")
    f.write("'")
    return 'Model Created'


@ app.route('/copy_database', methods=['GET'])
def copy_database():
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
    return jsonify({
        'message': 'Banco de dados copiado com sucesso!'
    })


@ app.route('/register', methods=['POST'])
def register():
    name = request.json['name']
    email = request.json['email']
    pwd = request.json['password']
    is_admin = False

    user = User.query.filter_by(email=email).first()

    if user:
        return jsonify({
            'error': 'Usuário já registrado, tente novamente!'
        }), 409

    user = User(name, email, pwd, is_admin)
    db.session.add(user)
    db.session.commit()

    return jsonify({
        'message': 'Usuário registrado com sucesso!'
    })


@ app.route('/login', methods=['POST'])
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
        'message': 'Login efetuado com sucesso!',
        'is_admin': True,
        "token": token
    })


@ app.route('/getUser', methods=['GET'])
@ jwt_required
def getUser(current_user):
    return jsonify(user_share_schema.dump(current_user))


@ app.route('/getUsers', methods=['GET'])
@ jwt_required
def getUsers(current_user):
    result = users_share_schema.dump(
        User.query.all()
    )
    return jsonify(result)


@ app.route('/deleteUser', methods=['POST'])
@ jwt_required
def deleteUser(current_user):

    email = request.json['email']

    user = User.query.filter_by(email=email).first()

    if not user:
        return jsonify({
            'error': 'Usuário não existe, tente novamente!'
        }), 409

    db.session.delete(user)
    db.session.commit()

    result = user_share_schema.dump(
        User.query.filter_by(email=email).first()
    )

    if not result:
        return jsonify({'message': 'Usuário deletado com sucesso!'})
    else:
        return jsonify({'error': 'Não foi possível deletar usuário, tente novamente!'})


@ app.route('/protected', methods=['GET'])
@ jwt_required
def protected(current_user):
    result = appmetas_share_schema.dump(
        AppMeta.query.all()
    )
    result2 = maindbs_share_schema.dump(
        MainDB.query.all()
    )
    return jsonify(AppMeta=result, MainDB=result2)


@ app.route('/anonymize_data', methods=['GET'])
@ jwt_required
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
            id=x['id']).update({"cpf": scrubadub.clean(x['cpf'])})
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
    app.run(debug=True, port="5002")

'''ssl_context=('ca/cert.pem', 'ca/key.pem')'''
