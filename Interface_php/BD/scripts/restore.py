import os
import subprocess
import psycopg2
from psycopg2.extensions import ISOLATION_LEVEL_AUTOCOMMIT

print(os.getcwd())

def restore_db(db_host, db, port, user, password, backup_file, verbose):
    if verbose:
        try:
            print(user,password,db_host,port,db)
            process = subprocess.Popen(
                ['pg_restore',
                 '--no-owner',
                 f'--dbname=postgresql://{user}:{password}@{db_host}:{port}/{db}',
                 '-v',
                 backup_file],
                stdout=subprocess.PIPE
            )
            output = process.communicate()[0]
            if int(process.returncode) != 0:
                print(f'Command failed. Return code : {process.returncode}')

            return output
        except Exception as e:
            print(f"Issue with the db restore : {e}")
    else:
        try:
            process = subprocess.Popen(
                ['pg_restore',
                 '--no-owner',
                 f'--dbname=postgresql://{user}:{password}@{db_host}:{port}/{db}',
                 backup_file],
                stdout=subprocess.PIPE
            )
            output = process.communicate()[0]
            if int(process.returncode) != 0:
                print(f"Command failed. Return code : {process.returncode}")
            return output
        except Exception as e:
            print(f"Issue with the db restore : {e}")

def get_db_info():
    with open(".env") as file:
        dic = dict()
        lines = [line.replace('\n','').replace(' ', '') for line in file.readlines()]
        for line in lines:
            l = line.split("=")
            dic.setdefault(l[0],l[1])
    return dic

def get_backup_name():
    text_files = [f for f in os.listdir('backup') if f.endswith('.backup')]
    text_files.sort()
    file = text_files[-1]
    return 'backup/'+file

def drop_and_create_db(dic):
    conn = psycopg2.connect(f"dbname=postgres user={dic['USER']} host={dic['HOST']} port={dic['PORT']} password={dic['PASSWORD']}")
    conn.set_isolation_level(ISOLATION_LEVEL_AUTOCOMMIT)
    cur = conn.cursor()
    cur.execute(f"DROP DATABASE IF EXISTS {dic['DBNAME']};")
    cur.execute(f"CREATE DATABASE {dic['DBNAME']};")
    cur.execute("""DO
                    $do$
                    BEGIN
                    IF NOT EXISTS (
                        SELECT FROM pg_catalog.pg_roles  -- SELECT list can be empty for this
                        WHERE  rolname = 'eoffice') THEN
                        CREATE ROLE eoffice;
                    END IF;
                    END
                    $do$;""")
    cur.execute("""DO
                    $do$
                    BEGIN
                    IF NOT EXISTS (
                        SELECT FROM pg_catalog.pg_roles  -- SELECT list can be empty for this
                        WHERE  rolname = 'dev_eoffice') THEN
                        CREATE ROLE dev_eoffice;
                    END IF;
                    END
                    $do$;""")

if (__name__ == '__main__'):
    dic = get_db_info()
    file = get_backup_name()
    drop_and_create_db(dic)
    restore_db(dic["HOST"], dic["DBNAME"], dic["PORT"], dic["USER"], dic["PASSWORD"], file, True)
    