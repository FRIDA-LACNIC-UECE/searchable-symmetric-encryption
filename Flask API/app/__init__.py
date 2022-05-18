from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask_marshmallow import Marshmallow
from flask_cors import CORS

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///../databases/flask_jwt_users.db'
app.config['SQLALCHEMY_BINDS'] = {
    'db2': 'sqlite:///../databases/flask_jwt_appmeta.db',
    'db3': 'sqlite:///../databases/flask_jwt_maindb.db'
}
app.config['SECRET_KEY'] = 'secret'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = True

db = SQLAlchemy(app)
ma = Marshmallow(app)
CORS(app)