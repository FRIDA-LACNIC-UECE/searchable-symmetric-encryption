import datetime

import jwt
from flask import jsonify, request
from flask_migrate import Migrate

from controller import app, db
from service.authenticate import jwt_required
from model.models import (AppMeta, MainDB, User, appmeta_share_schema,
                          appmetas_share_schema, maindb_share_schema,
                          maindbs_share_schema, user_share_schema,
                          users_share_schema)
from service.service import copy_database_fc, create_model

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
    create_model('mysql://root:Admin538*@localhost:3306/public',
                 'anonymizations', ['id', 'id_database', 'id_anonymization_type', 'table', 'columns'])
    return 'Flask is running'


@ app.route('/copy_database', methods=['GET'])
def copy_database():

    src_db = request.json['src_db']
    src_db_path = "{}://{}:{}@{}:{}/{}".format(src_db['type'], src_db['user'],
                                               src_db['password'], src_db['ip'], src_db['port'], src_db['name'])
    src_table = src_db['table']

    dest_db = request.json['dest_db']
    dest_db_path = "{}://{}:{}@{}:{}/{}".format(dest_db['type'], dest_db['user'],
                                                dest_db['password'], dest_db['ip'], dest_db['port'], dest_db['name'])
    dest_table = dest_db['table']
    dest_columns = dest_db['columns']

    copy_database_fc(src_db_path, dest_db_path,
                     src_table, dest_columns, dest_table)
    create_model(dest_db_path, dest_table, dest_columns)

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
        'is_admin': user.is_admin,
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

    result2 = result

    for index, x in enumerate(result):
        exists = MainDB.query.filter_by(user_id=x['id']).update(
            {'cpf': x['cpf']}, synchronize_session="fetch")
        if(not exists):
            db.session.add(MainDB(x['id'], x['email'], x['cpf']))

        AppMeta.query.filter_by(
            id=x['id']).update({'email': result2[index]['email'], 'cpf': result2[index]['cpf']})
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
            {'cpf': x['cpf'], 'email': x['email']}, synchronize_session="fetch")
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
    app.run(debug=True)

'''ssl_context=('ca/cert.pem', 'ca/key.pem')'''
