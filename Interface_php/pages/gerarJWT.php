<?php
  session_start();
?>
<html lang='pt-BR'>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="styles/main.css">
<script src="../libs/extjs/jquery-3.4.0.min.js"></script>
<!--===============================================================================================-->
<script>
  function reload_(){
    document.location.reload(true);
  };
  function copyContents() {
    var $temp = $("<input>");
    var content = $('#textarea').text();
    $("body").append($temp);
    $temp.val(content).select();
    document.execCommand("copy");
    $temp.remove();
}
</script>
</head>
<?php
  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");
  require_once "jwt_utils.php";
  require_once "../BD/scripts/Conexao.php";

  $user_id = $_SESSION['id_usuario'];
  $sql = "SELECT jwt FROM administrador.usuario WHERE idusuario = ".$user_id.";";
  $stmt = Conexao::getConn()->prepare($sql);
  $stmt->execute();
  $jwt = $stmt->fetch()[0];

  print "<font face='arial' size='2' color='silver'><b>API > Gerar Token</b></font>";
  print "<br><br>";
  if (isset($_SESSION['aux'])){
    unset($_SESSION['aux']);
    $headers = array('alg'=>'HS256','typ'=>'JWT');
    $payload = array('sub'=>$user_id, 'exp'=>(time() + 3600));
    $jwt = generate_jwt($headers, $payload);
    #$data = date("d/m/Y");
    #$time = date("H:m");
    $sql = "UPDATE administrador.usuario SET jwt = '".$jwt."' WHERE idusuario = ".$user_id.";";
    $stmt = Conexao::getConn()->prepare($sql);
    $stmt->execute();
    $stmt->fetch();
  }
  if (is_jwt_valid($jwt)){
    print "<form name='TextForm1' method='get' target='_self'>";
    print "<b style='display: inline-block;vertical-align: middle;margin: 25px 0;'>Token de Acesso:</b>";
    print "<button class='button' type='button' onClick='reload_();'><img height='25px' src='images/refresh.png' alt='Recarregar'></button>";
    print "<br><div><textarea id='textarea' disabled style='padding:10px;font-size:20px;margin-top:10px; max-width: 100%;' cols='1000' rows='10'>".$jwt."</textarea><div>";
    print "<p style='left:right;margin-top:5px;display:inline-block'>Este Token tem validade de 1h.</p>";
    print "<button style='float:right;' class='button' type='button' onClick='copyContents();'>Copiar Token</button>";
    print "</form>";
  } else {
    print "<form name='TextForm2' method='get' target='_self'>";
    print "<b style='display: inline-block;vertical-align: middle;margin: 25px 0;'>Token de Acesso:</b>";
    print "<button class='button' type='button' onClick='reload_();'><img height='25px' src='images/refresh.png' alt='Recarregar'></button>";
    print "<br><div><textarea disabled style='font-size:20px;margin-top:10px; max-width: 100%;' cols='1000' rows='10'></textarea><div>";
    print "</form>";
    $_SESSION['aux'] = 't';
  }
  ?>
 
</html>