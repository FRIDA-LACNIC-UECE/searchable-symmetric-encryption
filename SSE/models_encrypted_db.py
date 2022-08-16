# coding: utf-8
from sqlalchemy import Column, MetaData, Table, Text
from marshmallow import Schema


metadata = MetaData()


Tabela1_index = Table(
    'Tabela1_index', metadata,
    Column('index_1', Text),
    Column('index_2', Text),
    Column('index_3', Text),
    Column('index_4', Text),
    Column('index_5', Text),
    Column('index_6', Text),
    Column('index_7', Text),
    Column('index_8', Text),
    Column('index_9', Text),
    Column('index_10', Text),
    Column('index_11', Text),
    Column('index_12', Text)
)

Tabela2_index = Table(
    'Tabela2_index', metadata,
    Column('index_1', Text),
    Column('index_2', Text),
    Column('index_3', Text),
    Column('index_4', Text),
    Column('index_5', Text),
    Column('index_6', Text),
    Column('index_7', Text),
    Column('index_8', Text),
    Column('index_9', Text)
)


Tabela3_index = Table(
    'Tabela3_index', metadata,
    Column('index_1', Text),
    Column('index_2', Text),
    Column('index_3', Text),
    Column('index_4', Text),
    Column('index_5', Text),
    Column('index_6', Text),
    Column('index_7', Text),
    Column('index_8', Text)
)


Tabela4_index = Table(
    'Tabela4_index', metadata,
    Column('index_1', Text),
    Column('index_2', Text),
    Column('index_3', Text),
    Column('index_4', Text),
    Column('index_5', Text),
    Column('index_6', Text),
    Column('index_7', Text),
    Column('index_8', Text),
    Column('index_9', Text),
    Column('index_10', Text),
    Column('index_11', Text),
    Column('index_12', Text),
    Column('index_13', Text),
    Column('index_14', Text),
    Column('index_15', Text)
)


Tabela5_index = Table(
    'Tabela5_index', metadata,
    Column('index_1', Text),
    Column('index_2', Text),
    Column('index_3', Text),
    Column('index_4', Text),
    Column('index_5', Text),
    Column('index_6', Text),
    Column('index_7', Text),
    Column('index_8', Text),
    Column('index_9', Text),
    Column('index_10', Text)
)
