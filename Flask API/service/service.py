from sqlalchemy import (MetaData, Table, create_engine, inspect)
from sqlalchemy.orm import sessionmaker

from model.models import (AppMeta)

import numpy as np
import scipy.linalg as la


def create_model(db, table, columns):

    types = {
        'INTEGER': 'Integer',
        'VARCHAR': 'String',
        'TINYINT': 'Boolean'
    }

    engine = create_engine(db)
    meta = MetaData(bind=engine)
    table_name = table  # request.json['table']
    columns_to_copy = columns  # request.json['columns']
    table = Table(table_name, meta,
                  autoload=True, autoload_with=engine)
    insp = inspect(engine)
    columns_table = insp.get_columns(table_name)
    indexes_table = insp.get_indexes(table_name)
    f = open('demoModel.py', 'w')

    s = ''

    s += 'from app import db, ma\n'
    s += 'from flask_login import UserMixin\n'
    s += 'from werkzeug.security import check_password_hash, generate_password_hash\n\n'
    s += f'class {table_name}(db.Model, UserMixin):\n'
    s += f'\t__tablename__ = "{table_name}"\n'
    columns = ''
    for c in table.c:
        if(not c.name in columns_to_copy):
            continue
        type = str(c.type)
        if(len(columns)):
            columns += ', '
        columns += f'{c.name}'
        for word, initial in types.items():
            type = type.replace(word, initial)
        s2 = f'\t{c.name} = db.Column(db.{type}'
        if(c.server_default):
            s2 += f', default={c.server_default}'
        if(c.nullable == False):
            s2 += f', nullable={c.nullable}'
        if(c.autoincrement == True):
            s2 += f', autoincrement={c.autoincrement}'
        if(c.primary_key == True):
            s2 += f', primary_key={c.primary_key}'
        if([index for index in indexes_table if index['name'] == c.name]):
            s2 += f', unique=True'
        s2 += ')\n\n'
        s += s2
    s += f'\tdef __init__(self, {columns}):\n'
    for c in columns.split(', '):
        s += f'\t\tself.{c} = {c}\n'
    s += '\n\tdef __repr__(self):\n'
    s += f'\t\treturn f"<{table_name} : '
    for idx, c in enumerate(columns.split(', ')):
        if(idx):
            s += f', {{self.{c}}}'
        else:
            s += f'{{self.{c}}}'
    s += '>"'
    f.write(s)
    return 'Model Created'


def copy_database_fc(src_db_path, dest_db_path, src_table, dest_columns, dest_table):

    # create engine, reflect existing columns, and create table object for oldTable
    # change this for your source database
    srcEngine = create_engine(src_db_path)
    SourceSession = sessionmaker(srcEngine)
    srcEngine._metadata = MetaData(bind=srcEngine)
    srcEngine._metadata.reflect(srcEngine)  # get columns from existing table
    srcEngine._metadata.tables[src_table].columns = [
        i for i in srcEngine._metadata.tables[src_table].columns if (i.name in dest_columns)]
    srcTable = Table(src_table, srcEngine._metadata)

    # create engine and table object for newTable
    # change this for your destination database
    destEngine = create_engine(dest_db_path)
    DestSession = sessionmaker(destEngine)
    destEngine._metadata = MetaData(bind=destEngine)
    destTable = Table(dest_table, destEngine._metadata)

    sourceSession = SourceSession()
    destSession = DestSession()

    # copy schema and create newTable from oldTable
    for column in srcTable.columns:
        if(column.name in dest_columns):
            destTable.append_column(column._copy())

    query = sourceSession.query(
        AppMeta.id, AppMeta.email, AppMeta.cpf).all()

    try:
        if not destTable.exists():
            destTable.create(checkfirst=True)
        destSession.execute('DELETE FROM {}'.format(dest_table))
        destSession.commit()
        for row in query:
            destSession.execute(destTable.insert(row))
        destSession.commit()
    except Exception as e:
        print(e)


def anonimization(data):
    # calculate the mean of each column
    mean = np.array(np.mean(data, axis=0).T)

    # center data
    data_centered = data - mean

    # calculate the covariance matrix
    cov_matrix = np.cov(data_centered, rowvar=False)

    # calculate the eignvalues and eignvectors
    evals, evecs = la.eigh(cov_matrix)

    # sort them
    idx = np.argsort(evals)[::-1]

    # Each columns of this matrix is an eingvector
    evecs = evecs[:, idx]
    evals = evals[idx]

    # explained variance
    variance_retained = np.cumsum(evals)/np.sum(evals)

    # calculate the transformed data
    data_transformed = np.dot(evecs.T, data_centered.T).T

    # randomize eignvectors
    new_evecs = evecs.copy().T
    for i in range(len(new_evecs)):
        np.random.shuffle(new_evecs[i])
    new_evecs = np.array(new_evecs).T

    # go back to the original dimension
    data_original_dimension = np.dot(data_transformed, new_evecs.T)
    data_original_dimension += mean

    return data_original_dimension
