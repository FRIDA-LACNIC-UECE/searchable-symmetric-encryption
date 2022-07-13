<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="/pages/styles/main.css">
<!--===============================================================================================-->

<script>
  function reload_(){
    document.location.reload(true);
  }
</script>


<?php
  session_start();
  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");

  print "<font face='arial' size='2' color='silver'><b>Administrador > Cadastro > Permissões dos Itens de Menus do Grupo</b></font>";
  print "<br><br>";
  include($_SERVER['DOCUMENT_ROOT']."/BD/scripts/Conexao.php");

  $tabela="grupo";

  include ("VarTabelasBD.php");

  $sql = "SELECT * FROM administrador.grupo ORDER BY idgrupo";
  $stmt = Conexao::getConn()->prepare($sql);
  $stmt->execute();
  $record = $stmt->rowCount();

  print "<b>Total de Registros Encontrados: " .$record. ".</b> ";
  print "Clique no 1º campo para editar as permissões de menus e submenus do grupo.<br><br>";
  // Insere o cabeçalho da tabela

  print "  <form method='get'>";
  print "<button class='button' type='button' onClick='reload_();'><img height='25px' src='../images/refresh.png' alt='Recarregar'></button>";
  print "  </form>";

  print "<table>";
  print "   <thead background-color=black>";
  print "     <tr class='table100-head'><th class='column1'>Id</th><th class='column2'>Grupo</th></tr>";
  print "   </thead>";
  // Insere o corpo da tabela
  print "   <tbody>";
  while($row = $stmt->fetch()){
    print "   <tr onClick='return popup3(\"CadastroPermissoesFormulario.php?tabela=".$tabela."&id=".$row['idgrupo']."\")'><td class='column1'>";
    print     $row['idgrupo']."</td>";
    print "   <td class='column2'>".$row['descricao']."</td></tr>";
  }
  print "   </tbody>";
  print "</table>";

  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");

?>
