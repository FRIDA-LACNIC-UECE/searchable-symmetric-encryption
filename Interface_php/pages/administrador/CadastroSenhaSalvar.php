<?php
  session_start();
  error_reporting(0);
  $iduser=$_SESSION['id_usuario'];
  include ("../conexaoBD.php");

  // Verifica se a senha atual está correta
  $senhaatual = $_POST["senhaatual"];
  $sql = "select senha from administrador.usuario where idusuario=".$iduser;
  $res = pg_query($dbcon, $sql);
  $row = pg_fetch_row($res);
  if (sha1($senhaatual) != $row[0]) {
    pg_close($dbcon);
    header("Location: /pages/administrador/CadastroSenha.php?senhaerrada");
  }

  //obtem o nome do primeiro campo e o seu valor na tabela da vari�vel $tabela
  $senha= $_POST["senha1"];
  $senhacri=sha1($senha);
  $dica= $_POST["dica"];

  $sql = "update administrador.usuario set senha='".$senhacri."', dica='".$dica."' where idusuario=".$iduser;

  // atualiza os dados se tiverem consistidos integralmentes
  $res = pg_query($dbcon, $sql);
  if (!$res) {
    echo "<br> Erro na atualização dos dados.";
    exit;
  }

  pg_close($dbcon);
  header("Location: /pages/administrador/CadastroSenha.php?sucesso");
?>

</body>
</html>
