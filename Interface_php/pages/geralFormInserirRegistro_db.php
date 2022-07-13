<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="styles/styleInsercao.css">
<!--===============================================================================================-->
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="../styles/global.css">
<link href="styles/multiselect.css" media="screen" rel="stylesheet" type="text/css">
<script src="../libs/extjs/jquery-3.4.0.min.js"></script>
<script src="../libs/extjs/jquery.mask.min.js"></script>
<script src="../libs/extjs/jquery.multi-select.js"></script>

<script type="text/javascript">
  var MaskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00)00000-0000' : '(00)0000-00009';
  },
  Options = {
    onKeyPress: function(val, e, field, options) {
      field.mask(MaskBehavior.apply({}, arguments), options);
    }
  };
  $(document).ready(function(){
    $('.percent').mask('#0.00%', {
    reverse: true,
    translation: {
      '#': {
        pattern: /-|\d/,
        recursive: true
      }
    },
    onChange: function(value, e) {      
      e.target.value = value.replace(/(?!^)-/g, '').replace(/^,/, '').replace(/^-,/, '-');
    }
    });
  $(".form").submit(function() {
    $(".dec").unmask();
    $(".dec").mask("#0.00", {reverse: true});
  });
  $('#my-select').multiSelect({
    selectableHeader: "<div class='custom-header' style='text-align: center;'>Todos os Itens</div>",
    selectionHeader: "<div class='custom-header' style='text-align: center;'>Itens Selecionados</div>"
  });
})
</script>

</head>

<body>


<?php 
  if(!isset($_SERVER['HTTP_REFERER'])){
    // redirect them to your desired location
    header('Location: ../restricted.php');
    exit;
  }
  error_reporting(0);
  $tabela=$_GET["tabela"];
  
  include ("conexaoBD.php");
  $sql = "SELECT id from administrador.tabelas WHERE nome='".$tabela."';";
  $registros = pg_query($dbcon, $sql);
  $tabela_id = pg_fetch_row($registros)[0];
  //pegando campo descrição da tabela
  $sql = "SELECT descricao FROM public.menu_submenu WHERE acao LIKE '%id=".$tabela_id."' LIMIT 1;";
  $registros = pg_query($dbcon, $sql);
  $rowCombo = pg_fetch_row($registros)[0];
  print "<h2 style='padding-left:1%; text-align:center;'>INSERIR REGISTRO NA TABELA: ".$rowCombo."</h2>";
  $sqlt = "select * from administrador.tabelas where nome='".$tabela."'";
  $rest = pg_query($dbcon, $sqlt);
  while ($rowt = pg_fetch_row($rest)) { $idtabela=$rowt[0]; }
  
  $sqlct = "select * from administrador.campos_tabela where idtabela='".$idtabela."' order by ordem";
  $resct = pg_query($dbcon, $sqlct); $i=0;
  while ($rowct = pg_fetch_row($resct)) { 
     $campos[$i][0]= $rowct[1];
     $campos[$i][1]= $rowct[2];
     $campos[$i][2]= $rowct[3];
     $campos[$i][3]= $rowct[4];
     $i=$i+1;
  }  
    
  $sql = "select * from " .$tabela;
  $res = pg_query($dbcon, $sql);

if (!isset($_POST["tried"])){
  print "<form name='TextForm' class='form' method='post' target='_self'>"; 
    $sql2 = "SELECT COL.COLUMN_NAME, COL.IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS COL WHERE COL.TABLE_NAME = '".$tabela."';";
    $res2 = pg_query($dbcon, $sql2);   
    $campos_notnull = array();
    while($row2 = pg_fetch_array($res2)){
      if ($row2['is_nullable'] == "NO"){
        print "<input type=hidden name=array[] value='" .$row2['column_name']. "'>";
        $campos_notnull[] = $row2['column_name'];
      }
    }
  ?>

  <!-- campos escondidos para manter o nome da tabela -->
  <table width='90%' bgcolor='#5271ff' align='center' cellspacing='10' height='112' style='border: 1px solid #5271ff; border-radius: 8px;'>
  <input type=hidden name=tabela value="<?php print "$tabela" ?>">

  <?php
  $i = pg_num_fields($res);

  for ($j = 1; $j < $i; $j++) {
    print "<tr><td width='4%' height='21' bgcolor='#5271ff'></td>";
    if ($j == 1){
      if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
      } else {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
      }
      $sql="select ".$campos[$j][3]." order by 2";
        $registros = pg_query($dbcon, $sql);
        $n_campos= pg_num_fields($registros);
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><select size='1' name=".$campos[$j][0]." style='max-width: 200px;'>";
        $k=0;
        while ($row = pg_fetch_row($registros)) {
          if ($n_campos <= 2) {
            if ($n_campos == 1) $campo2 = $row[0];
            else $campo2 = $row[1];

            if ($k==0) print "<option selected value='".$row[0]."' >".$campo2."</option>";
            else print "<option value='".$row[0]."' >".$campo2."</option>";
            $k = $k + 1;
          }
        }
        print "</select></td>";
    } elseif ($j == 2 or $j == 3 or $j ==5) {
      if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
      } else {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
      }
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input required name='" .pg_field_name($res, $j). "' ".$campos[$j][3]."></td></tr>";
    } elseif ($j == 4){
      if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
      } else {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
      }
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
        <input required maxlength=13 name='" .pg_field_name($res, $j). "' ".$campos[$j][3]; ?> oninput="$(this).mask('#0', {reverse: true})"></td></tr> <?php
    } elseif ($j == 6) {
      if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
      } else {
        print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
      }
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input type='password' name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3].">
      <input type='submit' value='Testar Conexão'></td></tr>";
    } else {
        continue;
      }
  }
  print "<input type='hidden' name='tried' value='1'/>";
  print "</form>";
} else {
  // form para inserção ou em caso de erro de conexão unset tried
  unset($_POST['tried']);
  try {
    $conn = new PDO("pgsql:dbname=".$_POST['db_name'].";host=".$_POST['host'].";port=".$_POST['port'].";", $_POST['user_access'], $_POST['password']);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connected = 1;
  } catch(PDOException $e) {
    $connected = 0;
    header('Location: ' . $_SERVER['HTTP_REFERER']."&error=".$e->getMessage());
  }
  if ($connected == 1) {
    print "<form name='TextForm' class='form' action='geralRegistroInserir_db.php' method='post' target='_top'>"; 
      $sql2 = "SELECT COL.COLUMN_NAME, COL.IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS COL WHERE COL.TABLE_NAME = '".$tabela."';";
      $res2 = pg_query($dbcon, $sql2);   
      $campos_notnull = array();
      while($row2 = pg_fetch_array($res2)){
        if ($row2['is_nullable'] == "NO"){
          print "<input type=hidden name=array[] value='" .$row2['column_name']. "'>";
          $campos_notnull[] = $row2['column_name'];
        }
      }
    ?>
    <table width='90%' bgcolor='#5271ff' align='center' cellspacing='10' height='112' style='border: 1px solid #5271ff; border-radius: 8px;'>
    <input type=hidden name=tabela value="<?php print "$tabela" ?>">

    <?php
    $i = pg_num_fields($res);
    for ($j = 1; $j < $i; $j++) {
      print "<tr><td width='4%' height='21' bgcolor='#5271ff'></td>";
      if ($j == 1){
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        $sql="select ".$campos[$j][3]." order by 2";
        $registros = pg_query($dbcon, $sql);
        $n_campos= pg_num_fields($registros);
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><select size='1' name=".$campos[$j][0]." style='max-width: 200px;'>";
        $k=0;
        while ($row = pg_fetch_row($registros)) {
            if ($row[0] == $_POST['id_database']){ print "<option selected value='".$row[0]."' >".$row[1]."</option>";}
            else {print "<option value='".$row[0]."' >".$row[1]."</option>";
            $k = $k + 1;
            }
          }
        print "</select></td></tr>";
      } elseif ($j == 2 or $j == 3 or $j ==5) {
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input required name='" .pg_field_name($res, $j). "' value=".$_POST[pg_field_name($res, $j)]."></td></tr>";
      } elseif ($j == 4){
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
          <input required maxlength=13 name='" .pg_field_name($res, $j). "' value=".$_POST[pg_field_name($res, $j)]; ?> oninput="$(this).mask('#0', {reverse: true})"></td></tr> <?php
      } elseif ($j == 6) {
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input type='password' name='" .pg_field_name($res, $j)."' value=".$_POST[pg_field_name($res, $j)]."></td></tr>";
      } else {
        if ($j == 7){
          if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
            print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
          } else {
            print "<td class='identificador' width='30%' height='21' align='center' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
          }
          $sql="select schema_name from information_schema.schemata;";
          $registros = pg_query($dbcon, $sql);
          $n_campos= pg_num_fields($registros);
          print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><select multiple='multiple' id='my-select' name='".$campos[$j][0]."[]'>";
          $k=0;
          while ($row = pg_fetch_row($registros)) {
            if ($n_campos==1) $campo2=$row[0]; else $campo2=$row[1];
            print "<option value='".$row[0]."' >".$campo2."</option>";
            $k=$k+1;
          }
          print "</select></td></tr>";
        } else {
          continue;
        }
      }
    }
    print "</form>";
    print " <tr>
                <td width='4%' height='21' bgcolor='#5271ff'></td>
                <td width='30%' height='21' align='right' bgcolor='#5271ff'></td>
                <td width='30%' height='21' align='right' bgcolor='#5271ff'>
                  &nbsp;&nbsp;&nbsp;&nbsp; <input class='button' type='SUBMIT' value='Salvar'>
                </td>
              </tr>";
      print "</table>";
  } else {
    unset($_POST["tried"]);
  }
}
?>

<div style="height: 30px;"></div>

<p style='color: #b15757; text-align:center;'>
  <?php 
    if(isset($_GET["empty"])) {
      echo "Erro de inserção: ";
      echo "campos com * não podem ser vazios.";
      unset($_GET["empty"]);
    } 
    if(isset($_GET["error"])) {
      echo "Erro de conexão: ";
      echo $_GET['error'];
      unset($_GET["empty"]);
    } 
  ?>
</p>

<?php  pg_close($dbcon); ?>

</body>
</html>
