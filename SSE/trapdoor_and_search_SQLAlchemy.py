from __future__ import print_function
import time
import pandas as pd
import sqlite3
import models_encrypted_db
from Crypto.Cipher import AES
from Crypto.Hash import MD5
from sqlalchemy import create_engine, select
from sqlalchemy.orm import Session


def build_trapdoor(MK, keyword):
    keyword_index = MD5.new()
    keyword_index.update(str(keyword).encode())
    ECB_cipher = AES.new(MK.encode("utf8"), AES.MODE_ECB)
    return ECB_cipher.encrypt(keyword_index.digest())

def build_codeword(ID, trapdoor):
    ID_index = MD5.new()
    ID_index.update(str(ID).encode())
    ECB_cipher = AES.new(trapdoor, AES.MODE_ECB)
    return ECB_cipher.encrypt(ID_index.digest()).hex()

def search_index(index_table_name, trapdoor, engine, columns_list):
    start_time = time.time()
    search_result = []
    #data_index = pd.read_csv(document)
    #data_index = data_index.values
    # start_time = time.time()

    #query = "SELECT * from " + index_table_name
    #result_proxy = connection.execute(query)

    session_db = Session(engine) # Section to run sql operation
    
    chosen_table = eval(f"models_encrypted_db.{index_table_name}") # classes to model database

    size = 1000
    statement = select(chosen_table)
    results_proxy = session_db.execute(statement) # Proxy to get data on batch
    results = results_proxy.fetchmany(size) # Getting data

    from_db = []
    while results:
        #print(results[0].to_list())
        for result in results:
            from_db.append(list(result))

        results = results_proxy.fetchmany(size) # Getting data
    
    #print(from_db)
    session_db.close()

    data_index = pd.DataFrame(from_db, columns=columns_list)
    data_index = data_index.values

    for row in range(data_index.shape[0]):
        if build_codeword(row, trapdoor) in data_index[row]:
            search_result.append(row+1)

    #time_cost = time.time() - start_time
    #print(time_cost)

    return search_result

if __name__ == "__main__":

    table_name = input("Please input the table you want to search into: ")

    keyword = input("Please input the keyword you want to search:  ")

    master_key_file_name = "masterkey" #input("Please input the file stored the master key:  ")
    master_key = open(master_key_file_name).read()
    if len(master_key) > 16:
        print("the length of master key is larger than 16 bytes, only the first 16 bytes are used")
        master_key = bytes(master_key[:16])

    trapdoor_file = open(keyword + "_trapdoor", "wb")
    trapdoor_of_keyword = build_trapdoor(master_key, keyword)
    trapdoor_file.write(trapdoor_of_keyword)
    trapdoor_file.close()

    database_name = "EncryptedDB"
    engine = create_engine(f"sqlite:///{database_name}")

    index_table_name = table_name + "_index" #input("Please input the index file you want to search:  ")

    chosen_table = eval(f"models_encrypted_db.{index_table_name}") # classes to model database
    
    session_db = Session(engine) # Section to run sql operation

    statement = select(chosen_table)
    data_description = statement.column_descriptions

    columns_list = []
    for column in data_description:
        columns_list.append(column['name'])

    keyword_trapdoor = keyword + "_trapdoor" #input("Please input the file stored the trapdoor you want to search:  ")
    #keyword_trapdoor = open(keyword_trapdoor).read().strip()
    with open(keyword_trapdoor, "rb") as f:
        keyword_trapdoor = f.read()
    search_result = search_index(index_table_name, keyword_trapdoor, engine, columns_list)

    #or_database_name = "NewDatabase"
    #or_connection = sqlite3.connect(or_database_name)
    #or_cursor = or_connection.cursor()
    
    print("The identifiers of files that contain the keyword are: \n", search_result)