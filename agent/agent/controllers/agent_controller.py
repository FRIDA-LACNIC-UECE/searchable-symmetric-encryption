from agent import app
from flask import Flask, request, jsonify
from agent.service.agent_service import check_server
from agent.service.agent_service import get_sensitive_data_from_the_server
from agent.service.agent_service import anonymize_data_in_the_server

@app.route("/check", methods=['GET', 'POST'])
def check():
    return check_server('larces!')

@app.route("/get_sensitive_from_user", methods=['GET', 'POST'])
def get_sensitive_from_user():
    return get_sensitive_data_from_the_server('token 1', 'token 2')

@app.route("/anonymize_data", methods=['GET', 'POST'])
def anonymize_data():
    return anonymize_data_in_the_server('data')