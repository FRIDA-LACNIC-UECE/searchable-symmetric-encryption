import mysql.connector
from mysql.connector import Error
import requests
import json

url = "http://privacyprotection.eastus.cloudapp.azure.com:3000/v1/user"

def create_user(data):
    payload = json.dumps(data)
    headers = {
        'Content-Type': 'application/json',
        'X-Bunker-Token': 'DEMO'
    }
    response = requests.request("POST", url, headers=headers, data=payload)
    d = json.loads(response.text)
    try:
        return d['token']
    except:
        return 'User aleady in the databunker'

def get_user_by_token(token):
    payload={}
    headers = {
        'X-Bunker-Token': 'DEMO'
    }
    response = requests.request("GET", url + "/token/" + token, headers=headers, data=payload)
    return response.text

try:
    connection = mysql.connector.connect(host='localhost',
                                         database='larces',
                                         user='root',
                                         password='grafo')

    cursor = connection.cursor()
    cursor.execute("select * from members")
    records = cursor.fetchall()
    print("Total number of rows in table: ", cursor.rowcount)

    newdb = mysql.connector.connect(
        host="localhost",
        user="root",
        password="grafo"
    )
    newcursor = newdb.cursor()
    newcursor.execute("CREATE DATABASE teste9")
    newcursor.execute("USE teste9")
    newcursor.execute("CREATE TABLE members (Id INT, User_token VARCHAR(100))")

    print("\nDatabase from LARCES")
    for row in records:
        data = {
            "Id": row[0],
            "Name": row[1],
            "Email": row[2],
            "Salary": row[3]
        }

        print('USER DATA: ', data)

        token = create_user(data)

        insert_query = "INSERT INTO members (Id, User_token) VALUES( {}, \"{}\" )".format(row[0], token)
        print(insert_query)
        newcursor.execute(insert_query)
        
        print("QUERY RESULT: ", get_user_by_token(token))
        print()

        newdb.commit()

except mysql.connector.Error as e:
    print("Error reading data from MySQL table", e)
finally:
    if connection.is_connected():
        connection.close()
        cursor.close()
        print("MySQL connection is closed - old db")

    if newdb.is_connected():
        newdb.close()
        newcursor.close()
        print("MySQL connection is closed - new db")