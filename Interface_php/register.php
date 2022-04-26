<?php session_start(); ?>
<link rel="shortcut icon" href="styles/images/favicon.ico">
<?php 
  if(!isset($_SERVER['HTTP_REFERER'])){
    // redirect them to your desired location
    header('Location: ../restricted.php');
    exit;
  }
  include ("pages/conexaoBD.php");
  #include ("apis/security/preventionSQLInjection_nome.php");
  #include ("apis/security/preventionXSSInjection_name.php");

  $captcha = $_POST["captcha"];
  $nome = $_POST["nome"];
  $email = $_POST["email"];
  $senha = sha1($_POST["senha"]);

  #validarInjecaoSQL($_POST["nome"], $_POST["senha"], $_POST["email"]);
  #validarInjecaoXSS($_POST["nome"], $_POST["email"], $_POST["senha"], $_POST["captcha"]);

  $sql = "select * from administrador.usuario where email='".$email."';"; 
  $res = pg_query($dbcon, $sql);
  $record =pg_num_rows($res);

  # Verifica se os campos estão vazios.
  if (empty($nome) or empty($senha) or empty($email)) {
    header("Location: index_register.php?empty=true");
    exit;
  }
  else {
    # Verifica se há apenas um registro de conta e senha.
    if ($record == 1) {
      header("Location: index_register.php?registered=true");
      exit;
    }
    elseif (empty($captcha)) {
        header("Location: index_register.php?empty=true");
        exit;
    } 
    elseif ($captcha != $_SESSION["captcha"]) {
        header("Location: index_register.php?captcha=incorrect");
        exit;
    }
    else {
      //criar grupo OK
      $sql = "INSERT INTO administrador.grupo (idgrupo,descricao)
	    VALUES ((SELECT COALESCE(MAX(idgrupo) + 1, 1) FROM administrador.grupo), '".$email."');";
      $insert = pg_query($dbcon, $sql);

      //criar user
      $sql = "INSERT INTO administrador.usuario (idusuario,nome,email,senha,idgrupo,idunidade) 
      VALUES ((SELECT COALESCE(MAX(idusuario) + 1, 1) FROM administrador.usuario), '".$nome."', '".$email."', '".$senha."', (SELECT idgrupo FROM administrador.grupo where descricao='".$email."'), 1);";
      $insert = pg_query($dbcon, $sql);

      //add tabela OK
      $sql = "SELECT idusuario FROM administrador.usuario WHERE email='".$email."';";
      $select = pg_query($dbcon, $sql);
      $id_tabela = strval(pg_fetch_row($select)[0]);
      $nome_tabela = "databases_".$id_tabela;
      $sql = "INSERT INTO administrador.tabelas (id,nome,rotulo) 
      VALUES ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.tabelas), '".$nome_tabela."', 'Cadastrar > Database');";
      $insert = pg_query($dbcon, $sql);

      //criar tabela
      $sql = "CREATE TABLE IF NOT EXISTS public.".$nome_tabela."(
        id INT NOT NULL,
        id_database INT NOT NULL,
        db_name VARCHAR(60) NOT NULL,
        host VARCHAR(60) NOT NULL,
        port VARCHAR(60) NOT NULL,
        user_access VARCHAR(60) NOT NULL,
        password VARCHAR(60) NOT NULL,
        schema VARCHAR(60) NOT NULL,
        CONSTRAINT ".$nome_tabela."_pkey PRIMARY KEY (id),
        CONSTRAINT ".$nome_tabela."_tbdbaceitos_fkey FOREIGN KEY (id_database) REFERENCES public.tb_dbaceitos (id)
      )";
      $create = pg_query($dbcon, $sql);

      //add menu_submenu OK
      $sql = "INSERT INTO public.menu_submenu (idmenu_submenu,descricao,ordem,idsubmenu,acao) 
      VALUES ((SELECT COALESCE(MAX(idmenu_submenu) + 1, 1) FROM public.menu_submenu), 'Databases', 1, 1, '/pages/geralMostrarConsultaInicial_db.php?id=".$id_tabela."');";
      $insert = pg_query($dbcon, $sql);
      $sql = "INSERT INTO public.menu_submenu (idmenu_submenu,descricao,ordem,idsubmenu,acao) 
      VALUES ((SELECT COALESCE(MAX(idmenu_submenu) + 1, 1) FROM public.menu_submenu), 'Selecionar Tabelas', 2, 4, '/pages/cadastroTabelasColunas.php?id=".$id_tabela."');";
      $insert = pg_query($dbcon, $sql);

      //add permissão OK
      $sql = "SELECT idgrupo FROM administrador.grupo WHERE descricao='".$email."';";
      $select = pg_query($dbcon, $sql);
      $id_grupo = strval(pg_fetch_row($select)[0]);
      $sql = "INSERT INTO administrador.permenu (idpermenu,idmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idpermenu) + 1, 1) FROM administrador.permenu), 1, ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      $sql = "INSERT INTO administrador.persubmenu (idpersubmenu,idsubmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idpersubmenu) + 1, 1) FROM administrador.persubmenu), 1, ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      $sql = "INSERT INTO administrador.persubmenu (idpersubmenu,idsubmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idpersubmenu) + 1, 1) FROM administrador.persubmenu), 3, ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      $sql = "INSERT INTO administrador.persubmenu (idpersubmenu,idsubmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idpersubmenu) + 1, 1) FROM administrador.persubmenu), 4, ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      $sql = "SELECT idmenu_submenu FROM public.menu_submenu WHERE acao='/pages/geralMostrarConsultaInicial_db.php?id=".$id_tabela."';";
      $select = pg_query($dbcon, $sql);
      $id_menu= pg_fetch_row($select)[0];
      $sql = "INSERT INTO administrador.peritemsubmenu (idperitemsubmenu,iditemsubmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idperitemsubmenu) + 1, 1) FROM administrador.peritemsubmenu), ".$id_menu.", ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      $sql = "SELECT idmenu_submenu FROM public.menu_submenu WHERE acao='/pages/cadastroTabelasColunas.php?id=".$id_tabela."';";
      $select = pg_query($dbcon, $sql);
      $id_menu= pg_fetch_row($select)[0];
      $sql = "INSERT INTO administrador.peritemsubmenu (idperitemsubmenu,iditemsubmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idperitemsubmenu) + 1, 1) FROM administrador.peritemsubmenu), ".$id_menu.", ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      $sql = "INSERT INTO administrador.peritemsubmenu (idperitemsubmenu,iditemsubmenu,idgrupo) 
      VALUES ((SELECT COALESCE(MAX(idperitemsubmenu) + 1, 1) FROM administrador.peritemsubmenu), 11, ".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
      
      
      //add campos_tabela
    $sql = "INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'id','Id.','Text','size=''10''',1,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'id_database','Base de Dados','Combobox','id, nome from tb_dbaceitos',2,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'db_name','Nome do Banco','Text','size=''30''',3,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'host','Host','Text','size=''30''',4,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'port','Porta','Inteiro','size=''30''',5,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'user_access','Usuário','Text','size=''30''',6,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'password','Senha','Senha','size=''30''',7,".$id_grupo.");
      INSERT INTO administrador.campos_tabela (id,campo,rotulo,tipo,propriedade,ordem,idtabela) VALUES
      ((SELECT COALESCE(MAX(id) + 1, 1) FROM administrador.campos_tabela),'schema','Schema(s)','Text','size=''30''',8,".$id_grupo.");";
      $insert = pg_query($dbcon, $sql);
    }

    header("Location: index.php");
    exit;
  }

  pg_close($dbcon);
?>