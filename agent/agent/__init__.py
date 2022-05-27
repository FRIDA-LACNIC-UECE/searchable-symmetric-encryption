from flask import Flask

app = Flask(__name__)
app.config["DEBUG"] = True

from .controllers import (
    agent_controller
)

from .service import (
    agent_service
)