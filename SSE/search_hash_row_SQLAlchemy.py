from unittest import result
from sqlalchemy import create_engine
from sqlalchemy.orm import Session
import models_original_db

def search_hash_row (tn, engine, hash):
    session = Session(engine) # Section to run sql operation

    chosen_table = eval(f"models_original_db.{tn}") # classes to model database
    table_schema = eval(f"models_original_db.Schema{tn}()") # schemas of models_original_db

    result = session.query(chosen_table).filter_by(line_hash=str(hash)).all()

    for row in result:
        print(list(table_schema.dump(row).values()))

if __name__ == "__main__":
    table_name = input("Please input the table you want to search into: ")
    hash_value = input("Input the desired row hash value: ")

    database_name = "NewDatabase" 
    engine = create_engine(f"sqlite:///{database_name}")

    search_hash_row(table_name, engine, hash_value)
