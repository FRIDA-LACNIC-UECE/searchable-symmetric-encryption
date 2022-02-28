from app import db, ma
from flask_login import UserMixin
from werkzeug.security import check_password_hash, generate_password_hash


class User(db.Model, UserMixin):
    __tablename__ = 'users'
    id = db.Column(db.Integer, autoincrement=True, primary_key=True)
    name = db.Column(db.String(86), nullable=False)
    email = db.Column(db.String(84), nullable=False, unique=True)
    password = db.Column(db.String(128), nullable=False)

    def __init__(self, name, email, password):
        self.name = name
        self.email = email
        self.password = generate_password_hash(password)

    def verify_password(self, pwd):
        return check_password_hash(self.password, pwd)

    def __repr__(self):
        return f"<User : {self.username}>"


class UserSchema(ma.Schema):
    class Meta:
        fields = ('id', 'name', 'email', 'password')


user_share_schema = UserSchema()
users_share_schema = UserSchema(many=True)


class AppMeta(db.Model, UserMixin):
    __tablename__ = 'appmeta'
    __bind_key__ = 'db2'

    id = db.Column(db.Integer, autoincrement=True, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(84), nullable=False, unique=True)
    cpf = db.Column(db.String(86), nullable=False)

    def __init__(self, email, cpf):
        self.email = email
        self.cpf = cpf

    def __repr__(self):
        return f"<AppMeta : {self.email}>"


class AppMetaSchema(ma.Schema):
    class Meta:
        fields = ('id', 'email', 'cpf')


appmeta_share_schema = AppMetaSchema()
appmetas_share_schema = AppMetaSchema(many=True)


class MainDB(db.Model, UserMixin):
    __tablename__ = 'sensitive_data'
    __bind_key__ = 'db3'

    id = db.Column(db.Integer, autoincrement=True, primary_key=True)
    user_id = db.Column(db.String(84), nullable=False, unique=True)
    cpf = db.Column(db.String(86), nullable=False)

    def __init__(self, user_id, cpf):
        self.user_id = user_id
        self.cpf = cpf

    def __repr__(self):
        return f"<MainDB : {self.cpf}>"


class MainDBSchema(ma.Schema):
    class Meta:
        fields = ('id', 'user_id', 'cpf')


maindb_share_schema = MainDBSchema()
maindbs_share_schema = MainDBSchema(many=True)
