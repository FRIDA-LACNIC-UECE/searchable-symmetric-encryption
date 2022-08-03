from sqlalchemy import create_engine

def search_hash_row (tn, engine, hash):
    with engine.connect() as connection:
        query = "SELECT * from " + tn + " WHERE line_hash = \"%s\"" % (hash)
        result = connection.execute(query)
        for row in result:
            print(row)

if __name__ == "__main__":
    table_name = input("Please input the table you want to search into: ")
    hash_value = input("Input the desired row hash value: ")

    database_name = "NewDatabase" 
    engine = create_engine(f"sqlite:///{database_name}")

    search_hash_row(table_name, engine, hash_value)
