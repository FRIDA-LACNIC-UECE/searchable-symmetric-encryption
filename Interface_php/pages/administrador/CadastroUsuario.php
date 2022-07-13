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

  print "<font face='arial' size='2' color='silver'><b>Administrador > Cadastro > Usuário</b></font>";
  print "<br><br>";
  include($_SERVER['DOCUMENT_ROOT']."/BD/scripts/Conexao.php");

  $tabela="usuario";

  include ("VarTabelasBD.php");

  $sql = "SELECT * FROM administrador.usuario ORDER BY idusuario";
  $stmt = Conexao::getConn()->prepare($sql);
  $stmt->execute();
  $record = $stmt->rowCount();

  print "<b>Total de Registros Encontrados: " .$record. ".</b> ";
  print "Clique no 1º campo para editar, deletar ou consultar com detalhes o registro.";
  // Insere na página o botão INSERIR
  print "  <form method='get'>";
  print "    <input class='button' type='button' value='Inserir' onClick='inserirForm(this.form);'>";
  print "<button class='button' type='button' onClick='reload_();'><img height='25px' src='../images/refresh.png' alt='Recarregar'></button>";
  print "    <input type=hidden name=tabela value='" .$tabela. "'>";
  print "  </form>";
  // Insere o cabeçalho da tabela
  print "<table>";
  print "   <thead background-color=black>";
  print "     <tr class='table100-head'>";
  print "       <th class='column1'>Id</th>";
  print "       <th class='column2'>Nome</th>";
  print "       <th class='column3'>Conta</th>";
  print "     </tr>";
  print "   </thead>";
  // Insere o corpo da tabela
  print "   <tbody>";
  while($row = $stmt->fetch()){
    print "   <tr onClick='return popup3(\"RegistroConsultar.php?tabela=".$tabela."&id=".$row['idusuario']."\")'>";
    print "     <td class='column1'>".$row['idusuario']."</td>";
    print "     <td class='column2'>".$row['nome']."</td>";
    print "     <td class='column3'>".$row['conta']."</td>";
    print "   </tr>";
  }
  print "   </tbody>";
  print "</table>";

  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");

?>
