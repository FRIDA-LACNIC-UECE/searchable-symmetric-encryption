from flask import Flask, request, jsonify
from agent import app

def check_server(data):
    #do something logical here
    return 'I\'m working ' + data + '!'

def get_sensitive_data_from_the_server(user_token, data_token):
    return 'get sensitive data from the server'

def anonymize_data_in_the_server(data):
    return 'anonymize data'