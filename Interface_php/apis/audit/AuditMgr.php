<?php

require_once($_SERVER['DOCUMENT_ROOT']."/BD/scripts/Conexao.php");
require_once($_SERVER['DOCUMENT_ROOT']."/apis/logs/LogMgr.php");

class AuditMgr
{
  public static function createAudit($username, $query_type, $query)
  {
    $sql = "INSERT INTO administrador.query_audit (username, query_type, query, exec_date, client_ip) VALUES (?, ?, ?, ?, ?);";
    $conn = Conexao::getConn();
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(1, $username);
    $stmt->bindValue(2, $query_type);
    $stmt->bindValue(3, $query);
    $stmt->bindValue(4, AuditMgr::getTime());
    $stmt->bindValue(5, AuditMgr::getClientIp());
    $stmt->execute();

    if ($stmt->errorCode() != "00000"):
      $error = "Erro em inserção de auditoria:\n" .
               "\tUsuario: $username\n" .
               "\tTipo de query: $query_type\n" .
               "\tQuery: $query\n" .
               "\tIP: ".AuditMgr::getClientIp()."\n".
               "\tErro: ".implode(", ", $stmt->errorInfo())."\n";
      LogMgr::write("error.log", $error);
      echo $error;
      exit;
    endif;
  }

  private static function getTime()
  {
    return date('Y-m-d H:i:s', time());
  }

  private static function getClientIp()
  {
    $clientIP = $_SERVER['HTTP_CLIENT_IP'] 
              ?? $_SERVER["HTTP_CF_CONNECTING_IP"] 
              ?? $_SERVER['HTTP_X_FORWARDED'] 
              ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
              ?? $_SERVER['HTTP_FORWARDED'] 
              ?? $_SERVER['HTTP_FORWARDED_FOR'] 
              ?? $_SERVER['REMOTE_ADDR'] 
              ?? '0.0.0.0';
    return $clientIP;
  }
}

?>