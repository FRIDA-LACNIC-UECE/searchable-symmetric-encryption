from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy import create_engine
from flask_marshmallow import Marshmallow

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost:3306/flask_jwt_users'
app.config['SQLALCHEMY_BINDS'] = {
    'db2': 'mysql://root:@localhost:3306/flask_jwt_appmeta',
    'db3': 'mysql://root:@localhost:3306/flask_jwt_maindb'
}
app.config['SECRET_KEY'] = 'secret'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = True

db = SQLAlchemy(app)
ma = Marshmallow(app)
