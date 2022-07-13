<?php session_start(); ?>
<HTML lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="/styles/global.css">

<script src="/apis/security/passwordValidation.js"></script>
</HEAD>
<BODY>

<?php
  error_reporting(0);
  $iduser=$_SESSION['id_usuario'];
  print "<font face='arial' size='2' color='silver'><b>Administrador > Cadastro > Senha</b></font>";
  print "<br><br>";

  include ("../conexaoBD.php");
  include ("VarTabelasBD.php");

  $sql = "select conta, dica from administrador.usuario where idusuario=".$iduser;
  $res = pg_query($dbcon, $sql);
  while ($row = pg_fetch_row($res)) {
    $cont = $row[0];
    $dica = $row[1];
  }

  if (isset($_GET['senhaerrada'])) {
    print "<script>alert('Senha atual incorreta!');</script>";
    unset($_GET['senhaerrada']);
  }

  if (isset($_GET['sucesso'])) {
    print "<script>alert('Senha modificada com sucesso!');</script>";
    unset($_GET['sucesso']);
  }
?>

<div id='head'>
  <A name='topo'></A>
</div>

<div id='corpo'>
  <form name="formlogin" onSubmit="return verifica(this);" action="CadastroSenhaSalvar.php" method="post">

  <table width="100%" cellspacing="0" height="112">
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" height="20" bgcolor="#00008B"></td>
      <td width="65%" height="20" bgcolor="#00008B"></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" bordercolordark="#000000" bgcolor="white" height="20" align="right"><b>Conta : </b></td>
      <td width="65%" bordercolordark="#000000" bgcolor="white" height="20" align="left">
        <?php print "<input type='text' name='conta' value='".$cont."' readonly>"; ?>
      </td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" bordercolordark="#000000" bgcolor="white" height="20" align="right"><b>Senha atual: </b></td>
      <td width="65%" bordercolordark="#000000" bgcolor="white" height="20" align="left"><input type="password" maxlength="15" name="senhaatual" value="" required></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" bordercolordark="#000000" bgcolor="white" height="20" align="right"><b>Nova Senha: </b></td>
      <td width="65%" bordercolordark="#000000" bgcolor="white" height="20" align="left"><input type="password" maxlength="15" name="senha1" value="" required></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" bordercolordark="#000000" bgcolor="white" height="20" align="right"><b>Repita a Nova Senha: </b></td>
      <td width="65%" bordercolordark="#000000" bgcolor="white" height="20" align="left"><input type="password" maxlength="15" name="senha2" value="" required></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" bordercolordark="#000000" bgcolor="white" height="20" align="right"><b>Dica para a Senha: </b></td>
      <td width="65%" bordercolordark="#000000" bgcolor="white" height="20" align="left"><input type="text" size="60" maxlength="30" name="dica" value="<?php print $dica; ?>"></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" bordercolordark="#000000" bgcolor="white" height="20" align="right"></td>
      <td width="65%" bordercolordark="#000000" bgcolor="white" height="20" align="left"><input type="submit" name="submite" value="Salvar"></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" height="20" bgcolor="#00008B"></td>
      <td width="65%" height="20" bgcolor="#00008B"><font size="2" color="white">A senha deve possuir no mínimo 8 e no máximo 15 dígitos com pelo menos um número, uma letra maiúscula e um caractere especial.</font></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
    <tr>
      <td width="5%" height="20" bgcolor="#00008B"></td>
      <td width="25%" height="20" bgcolor="#00008B"></td>
      <td width="65%" height="20" bgcolor="#00008B"></td>
      <td width="5%" height="20" bgcolor="#00008B"></td>
    </tr>
  </table>

  </form>
</div>

</BODY>
</HTML>
