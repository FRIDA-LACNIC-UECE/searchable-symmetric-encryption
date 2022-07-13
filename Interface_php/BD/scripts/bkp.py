import os
from datetime import datetime

def load_dotenv(filename=".env"):
  env = dict()
  with open(filename) as f:
    lines = [line.replace('\n','') for line in f.readlines()]
  for line in lines:
    var, val = line.split('=')
    env[var] = val
  return env

def is_windows():
  return os.name == 'nt'

def make_name():
  t = datetime.now()
  y = "%02d" % (t.year % 1000,)
  m = "%02d" % (t.month)
  d = "%02d" % (t.day)
  h = "%02d" % (t.hour)
  mi = "%02d" % (t.minute)
  name = f"db_{y}{m}{d}_{h}{mi}.backup"
  return name

def backup_command():
  env = load_dotenv()
  db = env["DBNAME"]
  user = env["USER"]
  password = env["PASSWORD"]
  port = env["PORT"]
  host = env["HOST"]

  os.environ["PGPASSWORD"] = password
  os.environ["PGHOST"] = host

  name = make_name()
  command = f"pg_dump.exe " if is_windows() else f"pg_dump "
  command += f"-p {port} -Fc -v -c -f {name} -U {user} {db}"

  return command


if (__name__ == '__main__'):
  cmd = backup_command()
  os.system(cmd)
