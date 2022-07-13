from controller import db, ma
from flask_login import UserMixin
from werkzeug.security import check_password_hash, generate_password_hash


class User(db.Model, UserMixin):
    __tablename__ = 'users'
    id = db.Column(db.Integer, autoincrement=True, primary_key=True)
    name = db.Column(db.String(200), nullable=False)
    email = db.Column(db.String(300), nullable=False)
    password = db.Column(db.String(200), nullable=False)
    is_admin = db.Column(db.Integer, nullable=False, server_default='0')

    def __init__(self, name, email, password, is_admin):
        self.name = name
        self.email = email
        self.password = generate_password_hash(password)
        self.is_admin = is_admin

    def verify_password(self, pwd):
        return check_password_hash(self.password, pwd)

    def __repr__(self):
        return f'<User : {self.name}>'


class UserSchema(ma.Schema):
    class Meta:
        fields = ('id', 'name', 'email', 'password', 'is_admin')


user_share_schema = UserSchema()
users_share_schema = UserSchema(many=True)


class AnonymizationType(db.Model, UserMixin):
    __tablename__ = 'anonymization_types'

    id = db.Column(db.Integer, nullable=False,
                   autoincrement=True, primary_key=True)
    name = db.Column(db.String(100), nullable=False)

    def __init__(self, id, name):
        self.id = id
        self.name = name

    def __repr__(self):
        return f'<AnonymizationType : {self.name}>'


class AnonymizationTypeSchema(ma.Schema):
    class Meta:
        fields = ('id', 'name')


anonymization_type_share_schema = AnonymizationTypeSchema()
anonymization_types_share_schema = AnonymizationTypeSchema(many=True)


class ValidDatabase(db.Model, UserMixin):
    __tablename__ = 'valid_databases'

    id = db.Column(db.Integer, nullable=False,
                   autoincrement=True, primary_key=True)
    name = db.Column(db.String(100), nullable=False)

    def __init__(self, id, name):
        self.id = id
        self.name = name

    def __repr__(self):
        return f'<ValidDatabase : {self.name}>'


class ValidDatabaseSchema(ma.Schema):
    class Meta:
        fields = ('id', 'name')


valid_database_share_schema = ValidDatabaseSchema()
valid_databases_share_schema = ValidDatabaseSchema(many=True)


class Database(db.Model, UserMixin):
    __tablename__ = 'databases'

    id = db.Column(db.Integer, nullable=False,
                   autoincrement=True, primary_key=True)
    id_user = db.Column(db.Integer, db.ForeignKey(
        "users.id"), nullable=False)
    id_db_type = db.Column(db.Integer, db.ForeignKey(
        "valid_databases.id"), nullable=False)
    name = db.Column(db.String(200), nullable=False)
    host = db.Column(db.String(200), nullable=False)
    user = db.Column(db.String(200), nullable=False)
    port = db.Column(db.Integer, nullable=False)
    password = db.Column(db.String(200), nullable=False)
    ssh = db.Column(db.String(500))

    def __init__(self, id, id_user, id_db_type, name, host, user, port, password, ssh):
        self.id = id
        self.id_user = id_user
        self.id_db_type = id_db_type
        self.name = name
        self.host = host
        self.user = user
        self.port = port
        self.password = password
        self.ssh = ssh

    def __repr__(self):
        return f'<Database : {self.name}>'


class DatabaseSchema(ma.Schema):
    class Meta:
        fields = ('id', 'id_user', 'id_db_type', 'name',
                  'host', 'user', 'port', 'password', 'ssh')


database_share_schema = DatabaseSchema()
databases_share_schema = DatabaseSchema(many=True)


class Anonymization(db.Model, UserMixin):
    __tablename__ = 'anonymizations'

    id = db.Column(db.Integer, nullable=False,
                   autoincrement=True, primary_key=True)
    id_database = db.Column(db.Integer, db.ForeignKey(
        "databases.id"), nullable=False)
    id_anonymization_type = db.Column(db.Integer, db.ForeignKey(
        "anonymization_types.id"), nullable=False)
    table = db.Column(db.String(150), nullable=False)
    columns = db.Column(db.JSON, nullable=False)

    def __init__(self, id, id_database, id_anonymization_type, table, columns):
        self.id = id
        self.id_database = id_database
        self.id_anonymization_type = id_anonymization_type
        self.table = table
        self.columns = columns

    def __repr__(self):
        return f'<Anonymization : {self.table}>'


class AnonymizationSchema(ma.Schema):
    class Meta:
        fields = ('id', 'id_database', 'id_anonymization_type', 'table',
                  'columns')


anonymization_share_schema = AnonymizationSchema()
anonymization_share_schema = AnonymizationSchema(many=True)


"""class User(db.Model, UserMixin):
    __tablename__ = 'users'
    __bind_key__ = 'db4'
    id = db.Column(db.Integer, autoincrement=True, primary_key=True)
    name = db.Column(db.String(86), nullable=False)
    email = db.Column(db.String(84), nullable=False, unique=True)
    password = db.Column(db.String(128), nullable=False)
    is_admin = db.Column(db.Integer, nullable=False, server_default='0')

    def __init__(self, name, email, password, is_admin):
        self.name = name
        self.email = email
        self.password = generate_password_hash(password)
        self.is_admin = is_admin

    def verify_password(self, pwd):
        return check_password_hash(self.password, pwd)

    def __repr__(self):
        return f'<User : {self.name}>'


class UserSchema(ma.Schema):
    class Meta:
        fields = ('id', 'name', 'email', 'password', 'is_admin')


user_share_schema = UserSchema()
users_share_schema = UserSchema(many=True)"""


class AppMeta(db.Model, UserMixin):
    __tablename__ = 'appmeta'
    __bind_key__ = 'db2'

    id = db.Column(db.Integer, autoincrement=True, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(84), nullable=False, unique=True)
    cpf = db.Column(db.String(86), nullable=False)

    def __init__(self, name, email, cpf):
        self.name = name
        self.email = email
        self.cpf = cpf

    def __repr__(self):
        return f'<AppMeta : {self.email}>'


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
    email = db.Column(db.String(84), nullable=False)
    cpf = db.Column(db.String(86), nullable=False)

    def __init__(self, user_id, email, cpf):
        self.user_id = user_id
        self.email = email
        self.cpf = cpf

    def __repr__(self):
        return f'<MainDB : {self.cpf}>'


class MainDBSchema(ma.Schema):
    class Meta:
        fields = ('id', 'user_id', 'email', 'cpf')


maindb_share_schema = MainDBSchema()
maindbs_share_schema = MainDBSchema(many=True)


# db.create_all()
# db.session.commit()
