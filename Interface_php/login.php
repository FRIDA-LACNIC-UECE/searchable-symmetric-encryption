<?php session_start(); ?>
<link rel="shortcut icon" href="styles/images/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:wght@700&display=swap" rel="stylesheet">
<?php 
  include ("pages/conexaoBD.php");
  include ("apis/security/preventionSQLInjection.php");
  include ("apis/security/preventionXSSInjection.php");

  $captcha = $_POST["captcha"];
  $email = $_POST["email"];
  $senha = sha1($_POST["senha"]);

  validarInjecaoSQL($_POST["email"], $_POST["senha"]);
  validarInjecaoXSS($_POST["email"], $_POST["senha"], $_POST["captcha"]);

  $sql = "select * from administrador.usuario where email='".$email."' and senha='".$senha."'"; 
  $res = pg_query($dbcon, $sql);
  $record =pg_num_rows($res);

  # Verifica se os campos estão vazios.
  if (empty($email) or empty($senha)) {
    header("Location: index.php?empty=true");
    exit;
  }

  else {
    # Verifica se há apenas um registro de email e senha.
    if ($record == 1) {
      // Determina o grupo e as permissoes do usuario
      $row = pg_fetch_row($res);
      $_SESSION["id_usuario"] = $row[0];
      $_SESSION["idgrupo"] = $row[5];
      $_SESSION["nome"] = $row[1];

      if ($_SESSION["idgrupo"] != "1") {
        if (empty($captcha)) {
          header("Location: index.php?empty=true");
          exit;
        } elseif ($captcha != $_SESSION["captcha"]) {
          header("Location: index.php?captcha=incorrect");
          exit;
        }
      }

      include("main.php");
    }

    # Verifica se pelo menos a email está registrada
    else {
      $sql = "select * from administrador.usuario where email='".$email."'"; 
      $res = pg_query($dbcon, $sql);
      $record = pg_num_rows($res);

      # Se sim, senha incorreta.
      if ($record == 1) {
        header("Location: index.php?pass=incorrect");
        exit;
      } 

      # Caso constrário, email incorreto.
      else {
        header("Location: index.php?email=notregister");
        exit;
      }
    }
  }

  pg_close($dbcon);
?>