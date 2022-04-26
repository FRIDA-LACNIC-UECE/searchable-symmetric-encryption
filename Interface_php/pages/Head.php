<!DOCTYPE html>
<html lang='pt-BR'>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="author" content="Larces">
  <title>Digital Shield Security</title>

  <!-- ** CSS ** -->
  <!-- base library -->
  <link rel="shortcut icon" href="../images/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="/libs/extjs/resources/css/ext-all.css" />

  <!-- overrides to base library -->
  <link rel="stylesheet" type="text/css" href="/styles/global.css" />
  <script type="text/javascript" src="/libs/extjs/ext-base.js"></script>
  <script type="text/javascript" src="/libs/extjs/ext-all.js"></script>
  <script type="text/javascript" src="/libs/extjs/Richtag.js"></script>
  <script type="text/javascript" src="/libs/extjs/TableGrid.js"></script>

  <script language="JavaScript" type="text/javascript">
    function popup3(mylink) {
      if (! window.focus)
        return true;

      var href;

      if (typeof(mylink) == "string")
        href=mylink;
      else
        href=mylink.href;

      window.open(href, "notes", "width=754,height=305,left=0,top=0,scrollbars=1");
      return false;
    }

    function inserirForm(form) {
      st=form.tabela.value;
      window.open("FormInserirRegistro.php?tabela="+st, "notes", "width=754,height=305,left=0,top=0,scrollbars=1");
    }
    
    function inserirForm1(form) {
      window.open("CadastroPermissoesFormulario.php?id=0", "notes", "width=754,height=505,left=0,top=0,scrollbars=1");
    }
    
    function inserirForm2(form) {  
      st=form.tabela.value;
      window.open("geralFormInserirRegistro.php?tabela="+st, "notes", "width=754,height=400,left=0,top=0,scrollbars=1");
    }

    function inserirForm3(form) {  
      st=form.tabela.value;
      window.open("geralFormInserirRegistro_db.php?tabela="+st, "notes", "width=754,height=400,left=0,top=0,scrollbars=1");
    }
    
    function inserirForm22(form) {
      st=form.tabela.value;
      window.open("geral2FormInserirRegistro.php?tabela="+st, "notes", "width=754,height=305,left=0,top=0,scrollbars=1");
    }
  </script>
</head>
<body class="pagebody">
  <?php
  if(!isset($_SERVER['HTTP_REFERER'])){
    // redirect them to your desired location
    header('Location: ../restricted.php');
    exit;
  }
  ?>
</body>
