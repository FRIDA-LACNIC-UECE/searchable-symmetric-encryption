import pandas as pd
from Crypto.Cipher import AES
from Crypto.Hash import MD5
from Crypto.Random import random
#import time
import numpy as np
import sqlite3


def build_trapdoor(MK, keyword):
    keyword_index = MD5.new()
    keyword_index.update(str(keyword).encode())
    ECB_cipher = AES.new(MK, AES.MODE_ECB)
    return ECB_cipher.encrypt(keyword_index.digest())


def build_codeword(ID, trapdoor):
    ID_index = MD5.new()
    ID_index.update(str(ID).encode())
    ECB_cipher = AES.new(trapdoor, AES.MODE_ECB)
    return ECB_cipher.encrypt(ID_index.digest()).hex()


def build_index(MK, ID, record_columns_list):
    secure_index = [0] * len(record_columns_list)
    for i in range(len(record_columns_list)):
        codeword = build_codeword(ID, build_trapdoor(MK, record_columns_list[i]))
        secure_index[i] = codeword
    random.shuffle(secure_index)
    return secure_index

def searchable_encryption(master_key, columns_list, table_name, cursor, connection_db):
    #start_time = time.time()
    index_header = []
    for i in range(1, len(columns_list) + 1):
        index_header.append("index_" + str(i))

    document_index = []
    query = "SELECT * from " + table_name
    cursor.execute(query)

    results = cursor.fetchall()

    #print(results)

    from_db = []
    for result in results:
        result = list(result)
        from_db.append(result)

    #print(from_db)

    raw_data = pd.DataFrame(from_db, columns=columns_list)
    features = list(raw_data)
    raw_data = raw_data.values

    column_number = [i for i in range(0, len(features)) if features[i] in columns_list]

    for row in range(raw_data.shape[0]):
        record = raw_data[row]
        record_columns_list = [record[i] for i in column_number]
        record_index = build_index(master_key, row, record_columns_list)
        document_index.append(record_index)

    document_index_dataframe = pd.DataFrame(np.array(document_index), columns=index_header)
    new_file_name = table_name + "_index"
    document_index_dataframe.to_sql(new_file_name, connection_db, if_exists='replace', index=False)

    #time_cost = time.time() - start_time
    #print(time_cost)

if __name__ == "__main__":

    document_name = "Database.db" #name of the database to be encrypted
    connection = sqlite3.connect(document_name)
    cursor = connection.cursor()

    table_name = "users" #name of the table in database to be encrypted

    master_key_file_name = "masterkey" #password autentication
    master_key = open(master_key_file_name).read()
    if len(master_key) > 16:
        print("the length of master key is larger than 16 bytes, only the first 16 bytes are used")
        master_key = bytes(master_key[:16])

    #keyword_list_file_name = "keywordlist" #name of the columns in database
    #keyword_type_list = open(keyword_list_file_name).read().split(",")

    columns_list = []
    data = cursor.execute("SELECT * from " + table_name)
    for column in data.description:
        columns_list.append(column[0])

    connection_db = sqlite3.connect('Encrypted_Database')
    cursor_db = connection_db.cursor()

    #print(columns_list)

    searchable_encryption(master_key, columns_list, table_name, cursor, connection_db)

    connection_db.commit()
    cursor.close()
    cursor_db.close()

    print("Finished")
