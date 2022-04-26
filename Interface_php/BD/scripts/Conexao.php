<?php

class Conexao
{
  private static $instance;

  public static function getConn()
  {
    if (!isset(self::$instance)):
      if ($_SERVER['DOCUMENT_ROOT'] != ""):
        $env = self::read_env($_SERVER['DOCUMENT_ROOT'].'/.env');
      else:
        $env = self::read_env();
      endif;
      $db = $env['DBNAME'];
      $host = isset($env['HOST']) ? $env['HOST'] : 'localhost';
      $port = isset($env['PORT']) ? $env['PORT'] : '5432';
      $user = $env['USER'];
      $pass = $env['PASSWORD'];
      self::$instance = new \PDO("pgsql:dbname=$db;host=$host;port=$port;", $user, $pass);
    endif;

    return self::$instance;
  }

  private static function read_env($envFilename=".env") {
    $content = file_get_contents($envFilename);
    $lines = explode("\n", $content);
    $keys = array();
    $vals = array();

    foreach ($lines as $line) {
      $keys[] = explode("=", $line)[0];
      $vals[] = explode("=", str_replace("\r", "", $line))[1];
    }

    $env_variables = array_combine($keys, $vals);
    return $env_variables;
  }
}

?>