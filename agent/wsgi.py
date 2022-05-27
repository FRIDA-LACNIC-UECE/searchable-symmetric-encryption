from wsgiref.simple_server import make_server
from agent import app

if __name__ == '__main__':
    app.run(port="5003")