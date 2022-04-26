<HTML lang='pt-BR'>
<head>
  <title>Digital Shield Security</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="shortcut icon" href="styles/images/favicon.ico">
  <link rel="stylesheet" type="text/css" href="styles/global.css?v=3">
  <link rel="stylesheet" href="styles/login.css">
  <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body>
    
  <div class="main" id="register">
    <div class="sign" ><img src="/images/newlogo.png" class="new_logo"/></div>
      <form class="form1" id="formLogin" name="formlogin" action="register.php" method="post">
        <div class="inputsGroup"> 
          <input class="inputLabel" type="text"  placeholder="NOME" maxlength="100" name="nome" required>
          <input class="inputLabel" type="email"  placeholder="EMAIL" maxlength="100" name="email" required>
          <input class="inputLabel" type="password" placeholder="SENHA" maxlength="15" name="senha" required>
          <div class="imgCaptcha"><img src='captcha.php' alt='código captcha'></div>
          <input class="inputLabel" type="text" maxlength="6" name="captcha" id="captcha" placeholder="DIGITAR CÓDIGO ACIMA">
          <input type="submit" value="Cadastrar" class="submit" name="submite">
        </div>
        <span class="warning"><a href='index.php'>Já é registrado? Logue-se!</a></span>
        <p class="forgot">
          <?php
          if (isset($_GET["CharInvalido"]) && $_GET["CharInvalido"] == true){
            echo "Os caracteres ', < e > não são permitidos assim como espaços vazios!";
            unset($_GET["CharInvalido"]);
          } elseif(isset($_GET["empty"])) {
            echo "Preencha todos os campos!";
            unset($_GET["empty"]);
          } elseif (isset($_GET["captcha"])) {
            echo "Código Captcha incorreto!";
            unset($_GET["captcha"]);
          } elseif (isset($_GET["registered"])) {
            echo "Email já cadastrado!";
            unset($_GET["registered"]);
          } elseif (isset($_GET["pass"])) {
            echo "Senha incorreta!";
            unset($_GET["pass"]);
          }
          ?>
        </p>
        </form>  
    </div> 
</body>

</html>
