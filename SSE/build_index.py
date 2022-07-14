import pandas as pd
from Crypto.Cipher import AES
from Crypto.Hash import MD5
from Crypto.Random import random
import time
import numpy as np
import sqlite3
import hashlib

from sqlalchemy import table


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

def searchable_encryption(master_key, columns_list, tn, cursor, connection_db):
    index_header = []
    for i in range(1, len(columns_list) + 1):
        index_header.append("index_" + str(i))

    #row_number = "0"
    from_db = []
    document_index = []
    #query = "SELECT * from " + table_name + " limit 1000 offset " + row_number
    #cursor.execute(query)

    #results = cursor.fetchall()

    query = "SELECT * from " + tn
    cursor.execute(query)

    size = 1000
    results = cursor.fetchmany(size)

    while results:
        for result in results:
            result = list(result)
            from_db.append(result)

        results = cursor.fetchmany(size)
        #print(results)
        
    #print(from_db)

    raw_data = pd.DataFrame(from_db, columns=columns_list)
    features = list(raw_data)
    raw_data = raw_data.values

    column_number = [i for i in range(0, len(features)) if features[i] in columns_list]

    include_hash_column(tn, cursor, raw_data)

    for row in range(raw_data.shape[0]):
        record = raw_data[row]
        record_columns_list = [record[i] for i in column_number]
        record_index = build_index(master_key, row, record_columns_list)
        document_index.append(record_index)

    document_index_dataframe = pd.DataFrame(np.array(document_index), columns=index_header)
    new_file_name = tn + "_index"
    document_index_dataframe.to_sql(new_file_name, connection_db, if_exists='replace', index=False)

def include_hash_column(tn, cursor, raw_data):
    try:
        query = "ALTER TABLE " + tn + " ADD line_hash TEXT"
        cursor.execute(query)
    except:
        pass

    try:
        query = "ALTER TABLE " + tn + " ADD id SERIAL"
        cursor.execute(query)
    except:
        pass

    id = 1

    for row in range(raw_data.shape[0]):
        record = raw_data[row]
        record = record.copy(order='C')
        hashed_line = hashlib.sha256(record).hexdigest()

        h_query = "UPDATE " + tn + " SET line_hash = \"%s\" WHERE id = \"%d\"" % (str(hashed_line), id)
        id = id + 1
        #print(h_query)
        cursor.execute(h_query)
        #print(id)


if __name__ == "__main__":

    document_name = "Database.db" #name of the database to be encrypted
    connection = sqlite3.connect(document_name)
    cursor = connection.cursor()

    table_names = [] #name of the table in database to be encrypted

    for i in range (1, 54):
        tn = "Tabela" + str(i)
        table_names.append(tn)
        #print(tn)

    master_key_file_name = "masterkey" #password autentication
    master_key = open(master_key_file_name).read()
    if len(master_key) > 16:
        print("the length of master key is larger than 16 bytes, only the first 16 bytes are used")
        master_key = bytes(master_key[:16])

    #keyword_list_file_name = "keywordlist" #name of the columns in database
    #keyword_type_list = open(keyword_list_file_name).read().split(",")

    total_time = 0

    for tn in table_names:
        start_time = time.time()
        columns_list = []
        data = cursor.execute("SELECT * from " + tn + " limit 1")
        #print(tn)

        for column in data.description:
            columns_list.append(column[0])

        connection_db = sqlite3.connect('Encrypted_Database')
        #cursor_db = connection_db.cursor()
        #print(columns_list)
        searchable_encryption(master_key, columns_list, tn, cursor, connection_db)

        time_cost = time.time() - start_time
        total_time += time_cost

    connection.commit()
    connection_db.commit()
    cursor.close()

    #cursor_db.close()
    print(total_time)
    print("Finished")