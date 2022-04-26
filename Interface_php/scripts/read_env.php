<?php
  function read_env($envFilename=".env") {
    $content = file_get_contents($envFilename);
    $lines = explode("\n", $content);
    $keys = array();
    $vals = array();

    foreach ($lines as $line) {
      $keys[] = explode("=", $line)[0];
      $vals[] = explode("=", $line)[1];
    }

    $env_variables = array_combine($keys, $vals);
    return $env_variables;
  }
?>