<?php
    if(!isset($_SERVER['HTTP_REFERER'])){
      // redirect them to your desired location
      header('Location: ../restricted.php');
      exit;
    }
    include_once($_SERVER['DOCUMENT_ROOT']."/classes/LayoutManager.class.php");

    $submenu = array();
    $items = array();
    $menu = array();  $k_m=0; $k_s=0; $k_s0=0;

    // Presidência / Diretorias / Administrador
    $sql2 = "select idmenu, descricao from menu order by ordem";
    $res2 = pg_query($dbcon, $sql2);

    while ($men = pg_fetch_row($res2)) {
      // Busca apenas as diretorias em que o 'idgrupo' tem permissão.
      $sql3 = "select idmenu from administrador.permenu where idgrupo='".$_SESSION['idgrupo']."' ";
      $res3 = pg_query($dbcon, $sql3);

      while ($permenu = pg_fetch_row($res3)) {
        // Se (administrador.permenu['idmenu'] == public.menu['idmenu'])
        // A coluna 'idmenu' é o id da diretoria correspondente.
        // Ou seja, este if serve para todo o código abaixo entrar só na diretoria correspondente.
        if ($permenu[0]==$men[0]) {
          // Submenus "Consultar" e "Cadastrar" entram em $submen[1]
          $sql4 = "select idsubmenu, descricao from submenu where idmenu='".$men[0]."' order by ordem";
          $res4 = pg_query($dbcon, $sql4);

          while ($submen = pg_fetch_row($res4)) {
            // Guarda em $persubmenu[0] os submenus nos quais o 'idgrupo' tem permissão.
            $sql5 = "select idsubmenu from administrador.persubmenu where idgrupo='".$_SESSION['idgrupo']."' ";
            $res5 = pg_query($dbcon, $sql5);

            while ($persubmenu = pg_fetch_row($res5)) {
              if ($persubmenu[0]==$submen[0])  {
                // Busca os "sub-submenus" das diretorias e guarda em $menusubmenu. Explicação:
                // Na tabela menu_submenu temos os "sub-submenus" dos "submenus" das diretorias. Isto é,
                // Quando clicamos em "Consultar" ou "Cadastrar", aparecem os "menu_submenu".
                $sql6 = "select idmenu_submenu, descricao, acao from menu_submenu where idsubmenu='".$submen[0]."' order by descricao";
                $res6 = pg_query($dbcon, $sql6);
                $menu3 = array();

                while ($menusubmenu = pg_fetch_row($res6)) {
                  // Busca os ids de sub-submenus nos quais o 'idgrupo' tem permissão e guarda em $permenusubmenu;
                  $sql7 = "select iditemsubmenu from administrador.peritemsubmenu where idgrupo='".$_SESSION['idgrupo']."' ";
                  $res7 = pg_query($dbcon, $sql7);

                  while ($permenusubmenu = pg_fetch_row($res7)) {
                    if ($permenusubmenu[0]==$menusubmenu[0]) {
                      // Mapeia um sub-submenu em uma ação
                      $menu3[$menusubmenu[1]]=$menusubmenu[2];
                    }
                  }
                }

                // finalizei um submenu com os itens deste submenu
                // Salva, em $submenu os submenus "Consultar" e "Cadastrar" ordenadamente.
                $submenu[$k_s] = array("label"=>$submen[1],"id"=>$k_s);
                $items[$k_s] = $menu3;
                $k_s = $k_s + 1;

              } // end  if  ($persubmenu[0]==$submenu[0]) ...
            } // end  while ($persubmenu = pg_fetch_row($res5)) ...
          } // end  while ($submenu = pg_fetch_row($res4)) ...
          
          // finalizei o meu menu com submenus e itens de cada submenu
          $menu0= $men[1];
          $menu1= array();
          for ($i = $k_s0; $i < $k_s; $i++) {
            $menu1[$i]=$submenu[$i];
          }
          $menu[$k_m] = array("label"=>$menu0, "submenu"=>$menu1);
          $k_m= $k_m + 1; $k_s0= $k_s;
        } // end  if  ($permenu[0]==$menu[0]) ...
      } // end  while ($permenu = pg_fetch_row($res3))) ...
    } // end  while ($menu = pg_fetch_row($res2)) ...
	/*
	print "<html><body>";
	print "k_m=".$k_m."  k_s=".$k_s." <br>"; 
	
	for ($i = 0; $i < $k_s; $i++) {
			print " submenu ".$i." label ".$submenu[$i]["label"]." Id ".$submenu[$i]["id"];
			print "<br>";
	}
	print "</body>	</html>";   */

    LayoutManager::$menuManager['submenu'] 	= $submenu;
    LayoutManager::$menuManager['items'] 	= $items;
    LayoutManager::$menuManager['menu'] 	= $menu;
    LayoutManager::buildLayout();
?>
