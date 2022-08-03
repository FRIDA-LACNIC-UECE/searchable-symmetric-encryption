import pandas as pd
from Crypto.Cipher import AES
from Crypto.Hash import MD5
import sqlite3
import time


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

def search_index(index_table_name, trapdoor, cursor, columns_list):
    start_time = time.time()
    search_result = []
    #data_index = pd.read_csv(document)
    #data_index = data_index.values
    # start_time = time.time()
    query = "SELECT * from " + index_table_name
    cursor.execute(query)

    size = 1000
    results = cursor.fetchmany(size)
    #results = cursor.fetchall()

    from_db = []
    while results:
        for result in results:
            result = list(result)
            from_db.append(result)
        
        results = cursor.fetchmany(size)

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
    index_table_name = table_name + "_index" #input("Please input the index file you want to search:  ")
    connection = sqlite3.connect(database_name)
    cursor = connection.cursor()

    columns_list = []
    data = cursor.execute("SELECT * from " + index_table_name)
    for column in data.description:
        columns_list.append(column[0])

    keyword_trapdoor = keyword + "_trapdoor" #input("Please input the file stored the trapdoor you want to search:  ")
    #keyword_trapdoor = open(keyword_trapdoor).read().strip()
    with open(keyword_trapdoor, "rb") as f:
        keyword_trapdoor = f.read()
    search_result = search_index(index_table_name, keyword_trapdoor, cursor, columns_list)

    or_database_name = "NewDatabase"
    or_connection = sqlite3.connect(or_database_name)
    or_cursor = or_connection.cursor()
    
    cursor.close()
    
    print("The identifiers of files that contain the keyword are: \n", search_result)