<?php

require_once("Conexao.php");
require_once("./apis/logs/LogMgr.php");

define("MIGRATIONS_PATH", "./BD/migrations/");

// Pega as migrations já aplicadas no banco
$sql = "SELECT filename FROM public.migrations ORDER BY id";
$stmt = Conexao::getConn()->prepare($sql);
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

// Pega as migrations de arquivos
$migrations = array_diff(scandir(MIGRATIONS_PATH), array("..", "."));

// Remove do array de migrations de arquivos as
// migrations que já foram aplicadas no banco.
$new_mig = array_diff($migrations, $res);

// Aplica as migrações que ainda não foram aplicadas
foreach($new_mig as $mig):
  echo "Applying migration $mig...\n";
  $sql = file_get_contents(MIGRATIONS_PATH . $mig);
  Conexao::getConn()->exec($sql);
  $errArray = Conexao::getConn()->errorInfo();
  if ($errArray[0] != '00000') {
    echo "Error applying migration:\n";
    die(print_r($errArray, true));
  }

  // Adiciona a migration na tabela de migrations já aplicadas:
  $sql = "INSERT INTO public.migrations (id, filename) VALUES ((SELECT COALESCE(MAX(id), 0) + 1 FROM public.migrations), ?);";
  $stmt = Conexao::getConn()->prepare($sql);
  $stmt->bindValue(1, $mig);
  $stmt->execute();

  if ($stmt->errorCode() != "00000"):
    $error = "Erro de inserção da migration (".$stmt->errorCode()."):\n" . implode(", ", $stmt->errorInfo());
    LogMgr::write("error.log", $error);
    echo $error . "\n";
    exit;
  endif;
endforeach;

echo "Done!\n";

?>
