<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="/styles/global.css">

<script type="text/JavaScript">

function formatar_mascara(src, mascara) {
  var campo = src.value.length;
  var saida = mascara.substring(0,1);
  var texto = mascara.substring(campo);
  if(texto.substring(0,1) != saida) {
    src.value += texto.substring(0,1);
  }
}

</script>
</head>

<body>
<?php 
  $tabela=$_GET["tabela"];
  print "<h2 style='padding-left:1%'>INSERIR REGISTRO NA TABELA: ".$tabela."</h2>";

  include ("../conexaoBD.php");
  include ("VarTabelasBD.php");

  $sql = "select * from administrador." .$tabela;
  $res = pg_query($dbcon, $sql);
?>
  <form name="TextForm" action="RegistroInserir.php" method="post" target="_top">
    <table width="90%" cellspacing="0" height="112">
      <tr>
        <td width="4%" height="21"></td>
        <td width="48%" height="21"></td>
        <td width="48%" height="21"></td>
      </tr>
      <tr>
        <td width="4%" height="21"></td>
        <td width="48%" height="21"></td>
        <td width="48%" height="21" align="right">
            &nbsp;&nbsp;&nbsp;&nbsp; <input  type="SUBMIT" value="Salvar">
        </td>
      </tr>
      <tr>
        <td width="4%" height="21"></td>
        <td width="48%" height="21"></td>
        <td width="48%" height="21"></td>
      </tr>
    <?php
      $sql2 = "SELECT COL.COLUMN_NAME, COL.IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS COL WHERE COL.TABLE_NAME = '".$tabela."';";
      $res2 = pg_query($dbcon, $sql2);   
      $campos_notnull = array();
      while($row2 = pg_fetch_array($res2)){
        if ($row2['is_nullable'] == "NO"){
          print "<input type=hidden name=array[] value='" .$row2['column_name']. "'>";
          $campos_notnull[] = $row2['column_name'];
        }
      }
      // campos escondidos para manter o nome da tabela
      print "<input type=hidden name=tabela value='" .$tabela. "'>";
      $i = pg_num_fields($res);
      for ($j = 1; $j < $i; $j++) {
        print "<tr><td width='4%' height='21' bgcolor='#4e78b1'></td>";
        if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
          print "<td width='30%' height='21' align='right' bgcolor='#4e78b1'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
        } else {
          print "<td width='30%' height='21' align='right' bgcolor='#4e78b1'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
        }
        if ($j>0) {
          if (strcmp($campos[$j][2],"Combobox")==0) {  // Campo do tipo Combobox
            $sql="select ".$campos[$j][3];
            $registros = pg_query($dbcon, $sql);
            $n_campos= pg_num_fields($registros);
            print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><select size='1' name=".$campos[$j][0].">";
            $k=0;
            while ($row = pg_fetch_row($registros)) {
              if ($n_campos==1) $campo2=$row[0]; else $campo2=$row[1];
              if ($k==0) print "<option selected value='".$row[0]."' >".$campo2."</option>";
              else print "<option value='".$row[0]."' >".$campo2."</option>";
              $k=$k+1;
            }
            print "</select></td>";
          }
          elseif (strcmp($campos[$j][2],"Text")==0) { // campo do tipo Text
              print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  name='" .pg_field_name($res, $j). "' ".$campos[$j][3]."></td></tr>";
          }
          elseif (strcmp($campos[$j][2],"Password")==0) { // campo do tipo Text
            print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input type='password' maxlength='15' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]." required></td></tr>";
          }
          elseif (strcmp($campos[$j][2],"Boolean")==0) { // campo do tipo Boolean
              print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  type='checkbox' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]."></td></tr>";
          }
          elseif (strcmp($campos[$j][2],"DataBr")==0) { // campo do tipo Date
              print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'>
              <input  name='" .pg_field_name($res, $j). "' ".$campos[$j][3]; ?> onkeypress="formatar_mascara(this, '##/##/####')"></td></tr> <?php
          }
        } else
          print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  name=id ".$campos[$j][3]."></td></tr>";
      }
   ?>
   <tr>
     <td width='4%' height='21' bgcolor='#4e78b1'></td>
   <td width='30%' height='21' bgcolor='#4e78b1'></td>
   <td width='66%' height='21' bgcolor='#4e78b1'></td>
   </tr>
   <tr>
     <td width="4%" height="21"></td>
     <td width="48%" height="21"></td>
     <td width="48%" height="21"></td>
   </tr>

  </table>
</form>
<p style='color: #b15757;'>
  <?php 
    if(isset($_GET["empty"])) {
      echo "Erro de inserção: ";
      echo "campos com * não podem ser vazios.";
      unset($_GET["empty"]);
    }
  ?>
</p>
<?php  pg_close($dbcon); ?>
</body>
</html>
