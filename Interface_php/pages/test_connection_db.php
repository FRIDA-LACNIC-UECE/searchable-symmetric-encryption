<?php
    function test_connection($user_id, $row_id, $tabela){
        $sql = "SELECT * FROM databases_".$user_id." WHERE id=".$row_id.";";
        $stmt = Conexao::getConn()->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        try {
            $conn = new PDO("pgsql:dbname=".$row['db_name'].";host=".$row['host'].";port=".$row['port'].";", $row['user_access'], $row['password']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return [0=>"<td onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row_id . "\")'><div style='width=80%; border-style: solid;border-radius:10px; color:white; background-color:#15428bc2;'>Conectado</div></td>",1=> true];
          } catch(PDOException $e) {
            return [0=>"<td onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row_id . "\")'><div title='Verifique se as configurações do Banco estão corretas.' style='width=80%; border-style: solid;border-radius:10px; color:grey;'>Desconectado</div></td>",1=> false];
          }
    }
?>