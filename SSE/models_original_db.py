# coding: utf-8
from sqlalchemy import Column, Integer, Table, Text
from sqlalchemy.sql.sqltypes import NullType
from sqlalchemy.ext.declarative import declarative_base
from marshmallow import Schema


Base = declarative_base()
metadata = Base.metadata


class Tabela1(Base):
    __tablename__ = 'Tabela1'

    id = Column(Integer, primary_key=True)
    field1 = Column(Text)
    field2 = Column(Text)
    field3 = Column(Text)
    field4 = Column(Text)
    field5 = Column(Text)
    field6 = Column(Text)
    field7 = Column(Text)
    field8 = Column(Text)
    field9 = Column(Text)
    field10 = Column(Text)
    line_hash = Column(Text)

class SchemaTabela1(Schema):
    class Meta:
        fields = (
            'id', 'field1', 'field2', 'field3',
            'field4', 'field5', 'field6', 'field7',
            'field8', 'field9', 'field10', 'line_hash')
        ordered = True


class Tabela2(Base):
    __tablename__ = 'Tabela2'

    id = Column(Integer, primary_key=True)
    field1 = Column(Text)
    field2 = Column(Text)
    field3 = Column(Text)
    field4 = Column(Text)
    field5 = Column(Text)
    field6 = Column(Text)
    field7 = Column(Text)
    line_hash = Column(Text)

class SchemaTabela2(Schema):
    class Meta:
        fields = (
            'id', 'field1', 'field2', 'field3',
            'field4', 'field5', 'field6', 'field7',
            'line_hash')
        ordered = True


class Tabela3(Base):
    __tablename__ = 'Tabela3'

    id = Column(Integer, primary_key=True)
    field1 = Column(Text)
    field2 = Column(Text)
    field3 = Column(Text)
    field4 = Column(Text)
    field5 = Column(Text)
    field6 = Column(Text)
    line_hash = Column(Text)

class SchemaTabela3(Schema):
    class Meta:
        fields = (
            'id', 'field1', 'field2', 'field3',
            'field4', 'field5', 'field6', 'line_hash')
        ordered = True


class Tabela4(Base):
    __tablename__ = 'Tabela4'

    id = Column(Integer, primary_key=True)
    field1 = Column(Text)
    field2 = Column(Text)
    field3 = Column(Text)
    field4 = Column(Text)
    field5 = Column(Text)
    field6 = Column(Text)
    field7 = Column(Text)
    field8 = Column(Text)
    field9 = Column(Text)
    field10 = Column(Text)
    field11 = Column(Text)
    field12 = Column(Text)
    field13 = Column(Text)
    line_hash = Column(Text)

class SchemaTabela4(Schema):
    class Meta:
        fields = (
            'id', 'field1', 'field2', 'field3',
            'field4', 'field5', 'field6', 'field7',
            'field8', 'field9', 'field10', 'field11', 
            'field12', 'field13', 'line_hash')
        ordered = True


class Tabela5(Base):
    __tablename__ = 'Tabela5'

    id = Column(Integer, primary_key=True)
    field1 = Column(Text)
    field2 = Column(Text)
    field3 = Column(Text)
    field4 = Column(Text)
    field5 = Column(Text)
    field6 = Column(Text)
    field7 = Column(Text)
    field8 = Column(Text)
    line_hash = Column(Text)

class SchemaTabela5(Schema):
    class Meta:
        fields = (
            'id', 'field1', 'field2', 'field3',
            'field4', 'field5', 'field6', 'field7',
            'field8', 'line_hash')
        ordered = True


t_sqlite_sequence = Table(
    'sqlite_sequence', metadata,
    Column('name', NullType),
    Column('seq', NullType)
)
