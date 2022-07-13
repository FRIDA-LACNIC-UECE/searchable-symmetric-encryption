<html lang='pt-BR'>
<head>
<meta charset="UTF-8">
<title>Digital Shield Security</title>
<link rel="stylesheet" type="text/css" href="styles/main.css">
<script src="../libs/extjs/jquery-3.4.0.min.js"></script>
<script src="../libs/extjs/jquery.mask.min.js"></script>
<script src="../libs/extjs/jquery.dataTables.min.js"></script>
<script src="../libs/extjs/dataTables.buttons.min.js"></script>
<script src="../libs/extjs/buttons.html5.min.js"></script>
<!--===============================================================================================-->
<script>
  function reload_(){
    document.location.reload(true);
  };
  $(document).ready(function(){ 
    $("div[name=dec]").mask('#.##0,00', {reverse: true});
    $('#cadastro').DataTable( {
      dom: '<"acima"lf>t<"abaixo_tabela"p>',
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tudo"] ],
      "language": {
        "lengthMenu": "Mostrar _MENU_ registos.",
        "search": "Pesquisar: ",
        "paginate": {
          "first": "Primeiro",
          "previous": "Anterior",
          "next": "Seguinte",
          "last": "Último"
        },
        "emptyTable": "Tabela sem registros encontrados..."
      },
      "ordering": false
  } )
  });
</script>
</head>
<?php
  if(!isset($_SERVER['HTTP_REFERER'])){
    // redirect them to your desired location
    header('Location: ../restricted.php');
    exit;
  }
  session_start(); // acessar id e nome do usuario ativo atraves de $_SESSION['id_usuario']e $_SESSION['nome']
  error_reporting(0);
  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");
  require_once("test_connection_db.php");

  $idtabela=$_SESSION['id_usuario'];
  $user_id=$_SESSION['id_usuario'];

  include($_SERVER['DOCUMENT_ROOT']."/BD/scripts/Conexao.php");

  if (!isset($_POST['database_id']) or isset($_POST['voltar'])) {
    unset($_GET['voltar']);
    $sql = "SELECT * FROM administrador.tabelas WHERE id=".$idtabela;
    $stmt = Conexao::getConn()->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    $tabela = $row['nome']; // Nome da tabela

    print "<font face='arial' size='2' color='silver'><b>Anonimização > Selecionar Tabelas</b></font>";
    print "<br><br>";
    $sql = "SELECT * FROM public.".$tabela." ORDER BY id DESC";
    $stmt2 = Conexao::getConn()->prepare($sql);
    $stmt2->execute();
    $record = $stmt2->rowCount();
    print "<b>Total de Registros Encontrados: " .$record. ".</b> ";
    print "Clique na linha para consultar com detalhes o registro.";

    //recarregar página
    print "<button class='button' style='margin-top: 30px;' type='button' onClick='reload_();'><img height='25px' src='images/refresh.png' alt='Recarregar'></button>";

    $sql = "SELECT * FROM administrador.campos_tabela WHERE idtabela=".$idtabela. " ORDER BY ordem";
    $stmt3 = Conexao::getConn()->prepare($sql);
    $stmt3->execute();
    $rows = $stmt3->fetchAll();
    $total_rows = count($rows);
    // Insere o cabeçalho da tabela
    print "<table id='cadastro'>";
    print "<thead>";
    print "<tr class='table100-head'>";
    for($i=0; $i<$total_rows; $i++){
      $row = $rows[$i];
      if($i == 0 or $i == 6 or $i == 8){
        continue;
      } else {
        print "<th>".$row['rotulo']."</th>";
      }
    }
    print "<th>Status</th>";
    print "<th>Ação</th>";
    print "</tr></thead>";

    // Insere corpo da tabela
    print "<tbody>";
    while($row = $stmt2->fetch(PDO::FETCH_BOTH)){
      print "<tr>";
      for($i=1; $i<$total_rows; $i++) {
        if ($i == 6){
          continue;
        }
        if ($rows[$i]['tipo'] == 'Combobox') {
          $sql = "SELECT " . $rows[$i]['propriedade'] . ' WHERE id=' . $row[$i];
          $stmt4 = Conexao::getConn()->prepare($sql);
          $stmt4->execute();
          $res = $stmt4->fetchAll();
          print "<td onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'><div class='limitedHeight' title='".$res[0][1]."'>".$res[0][1]."</div></td>";
        }
        elseif ($i == 7){
          $row[$i] = str_replace('{', '', $row[$i]);
          $row[$i] = str_replace('}', '', $row[$i]);
          $row[$i] = explode(',', $row[$i]);
          $row[$i] = implode(', ', $row[$i]);
          print "<td onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'><div class='limitedHeight' title='".$row[$i]."'>".$row[$i]."</div></td>";
        }
        else {
          print "<td onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'><div class='limitedHeight' title='".$row[$i]."'>".$row[$i]."</div></td>";
        }
      }
      $result = test_connection($user_id, $row[0], $tabela);
      print $result[0];
      if ($result[1]) {
        print "<td><div class='limitedHeight'>
        <form method='post' target='_self'>
        <input type=hidden name=database_id value=".$row[0].">
        <input class='button' style='float: none;margin: 0;' type=submit value='Anonimizar'>
        </form>
        </div></td>";
      } else {
        print "<td onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'><div class='limitedHeight'><input disabled class='button' style='float: none;background-color: #d7d6d6;margin: 0;' type=submit value='Anonimizar'></div></td>";
      }
      print "</tr>";
    }
    print "</tbody>";
    print "</table>";
  } else {
    print "<font face='arial' size='2' color='silver'><b>Anonimização > Selecionar Tabelas</b></font>";
    print "<br><br>";
    print "<form name='TextForm' method='post' target='_self'>";
    print "<input class='button' type='submit' value='Voltar'/>";
    print "<input type='hidden' name=voltar value='1'/>";
    print "</form>";

    #print_r($_POST);
    #print_r($_GET);
    print "<form><input type=hidden name=database_id value=".$_POST['database_id'].">";
    print "<table width='90%' bgcolor='#5271ff' align='center' cellspacing='10' height='112' style='border: 1px solid #5271ff; border-radius: 8px;'>";
    $sql2 = "SELECT COL.COLUMN_NAME, COL.IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS COL WHERE COL.TABLE_NAME = '".$tabela."';";
    $res2 = pg_query($dbcon, $sql2);   
    $campos_notnull = array();
    while($row2 = pg_fetch_array($res2)){
      if ($row2['is_nullable'] == "NO"){
        print "<input type=hidden name=array[] value='" .$row2['column_name']. "'>";
        $campos_notnull[] = $row2['column_name'];
      }
    }
      // Campos escondidos para preservar as variáveis tabela e id
    print "<input type=hidden name=tabela value='" .$tabela. "'>";
    print "<input type=hidden name=campo_id value='" .$campos[0][0]. "'>";
      
    $row = pg_fetch_row($res);
    $i = pg_num_fields($res);
    $id = $row[0];
    for ($j = 1; $j < $i; $j++) {
      print "<tr><td width='4%' height='21' bgcolor='#5271ff'></td>";
      if ($j == 1){
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
          // Toda 'propriedade' em campos_tabela deve buscar 2 ou mais campos, de forma a
          // ordenarmos sempre pelo segundo.
          $sql="select ".$campos[$j][3]." order by 2;";
          $registros = pg_query($dbcon, $sql);
          $n_campos= pg_num_fields($registros);
          print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><select size='1' name=".$campos[$j][0]." style='max-width: 200px;'>";
          while ($rowCombo = pg_fetch_row($registros)) {
            if ($n_campos <= 2) {
              if ($n_campos == 1) $campo2 = $rowCombo[0];
              else $campo2 = $rowCombo[1];

              if ($row[$j]==$rowCombo[0]) print "<option selected value='".$rowCombo[0]."' >".$campo2."</option>";
              else print "<option value='".$rowCombo[0]."' >".$campo2."</option>";
            }
            else {
              if ($row[$j]==$rowCombo[0]) {
                print "<option selected value='".$rowCombo[0]."' >";
                print implode(" - ", array_slice($rowCombo, 1));
                print "</option>";
              }
              else {
                print "<option value='".$rowCombo[0]."' >";
                print implode(" - ", array_slice($rowCombo, 1));
                print "</option>";
              }
              $k = $k + 1;
            }
          }
          print "</select></td>";
      }
      elseif ($j == 2 or $j == 3 or $j ==5) { // campo do tipo Text
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input  name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3]."></td></tr>";
      }
      elseif ($j == 4) { // campo do tipo Inteiro
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
        <input maxlength=13 name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask('#0', {reverse: true})"></td></tr> <?php
      }
      elseif ($j == 6) { // campo do tipo Text
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input type='password' name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3].">
        <input type='submit' value='Testar Conexão'></td></tr>";
      } 
      else {
        continue;
      } 
      print "<input type='hidden' name='tried' value='1'/>";
      print "</form>";
    }
      print"</form>";
    }

  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");

?>
</html>