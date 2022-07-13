<?php 

$full_path = $_SERVER['DOCUMENT_ROOT'] === "" ? "." : $_SERVER['DOCUMENT_ROOT'];
define("LOGS_PATH", $full_path."/logs/");

class LogMgr
{
  public static function read($log_file)
  {
    return file_get_contents(LOGS_PATH . $log_file);
  }

  public static function write($log_file, $content)
  {
    $file = fopen(LOGS_PATH . $log_file, 'a');
    fwrite($file, "[".date('m/d/Y h:i:s a', time())."] " . $content . "\n");
    fclose($file);
  }
}

?>
