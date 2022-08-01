import sqlite3

def search_hash_row (tn, cursor, hash):
    query = "SELECT * from " + tn + " WHERE line_hash = \"%s\"" % (hash)
    cursor.execute(query)
    
    results = cursor.fetchall()
    print(results)

if __name__ == "__main__":
    table_name = input("Please input the table you want to search into: ")
    hash_value = input("Input the desired row hash value: ")

    database_name = "NewDatabase" 
    connection = sqlite3.connect(database_name)
    cursor = connection.cursor()

    search_hash_row(table_name, cursor, hash_value)