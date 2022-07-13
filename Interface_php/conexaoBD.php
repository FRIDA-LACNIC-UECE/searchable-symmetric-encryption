<?php
    if(!isset($_SERVER['HTTP_REFERER'])){
        // redirect them to your desired location
        header('Location: ../restricted.php');
        exit;
    }
    require_once ($_SERVER['DOCUMENT_ROOT']."/scripts/read_env.php");

    $envVariables = read_env($_SERVER['DOCUMENT_ROOT']."/.env");
    $dbname = $envVariables['DBNAME'];
    $user = $envVariables['USER'];
    $pass = $envVariables['PASSWORD'];
    $port = isset($envVariables['PORT']) ? $envVariables['PORT'] : '5432';
    $host = isset($envVariables['HOST']) ? $envVariables['HOST'] : 'localhost';

    $con_string = "host=$host port=$port dbname=$dbname user=$user password=$pass";
    if(!$dbcon=pg_connect($con_string)) die ("Erro ao conectar ao banco<br>".pg_last_error($dbcon));
?>
