<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="/styles/global.css">

<script language="JavaScript" type="text/JavaScript">
  function deletarRec(form)
  {  st=form.tabela.value;
    st= st + "&id=" + form.id.value;
    st= st + "&campo_id=" + form.campo_id.value;
    window.open("RegistroDeletar.php?tabela="+st,"_top");
  }
</script>

</head>

<body>
  <?php 
    $tabela=$_GET["tabela"];
    $id= $_GET["id"];
    print "<h2 style='padding-left:1%'>DADOS DO REGISTRO DA TABELA: ".$tabela."</h2>";

    include ("../conexaoBD.php");
    include ("VarTabelasBD.php");

    $sql = "select * from administrador." .$tabela. " where " .$campos[0][0]. "=" .$id;
    $res = pg_query($dbcon, $sql);
  ?>

  <form name="TextForm" action="RegistroAtualizar.php" method="post" target="_top">
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
        <?php
          print "<input type='button' value='Deletar' onClick='deletarRec(this.form);'>";
          print "   ";
          print "<input type='SUBMIT' value='Salvar'>";
        ?>
      </td>
    </tr>
    <tr>
      <td width="4%" height="21"></td>
      <td width="48%" height="21"></td>
      <td width="48%" height="21"></td>
    </tr>

    <?php
      // Campos escondidos para preservar as variáveis tabela e id
      print "<input type=hidden name=tabela value='" .$tabela. "'>";
      print "<input type=hidden name=campo_id value='" .$campos[0][0]. "'>";

      $row = pg_fetch_row($res);
      $i = pg_num_fields($res);
      for ($j = 0; $j < $i; $j++) {
        print "<tr><td width='4%' height='21' bgcolor='#4e78b1'></td>";
        print "<td width='30%' height='21' align='right' bgcolor='#4e78b1'><b>" .$campos[$j][1]. " :</b></td>";
        if ($j>0) {
          if (strcmp($campos[$j][2],"Combobox")==0) {  // Campo do tipo Combobox
            $sql="select ".$campos[$j][3];
            $registros = pg_query($dbcon, $sql);
            $n_campos= pg_num_fields($registros);
            print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><select size='1' name=".$campos[$j][0].">";
            while ($rowCombo = pg_fetch_row($registros)) {
              if ($n_campos==1) $campo2=$rowCombo[0]; else $campo2=$rowCombo[1];
              if ($row[$j]==$rowCombo[0]) print "<option selected value='".$rowCombo[0]."' >".$campo2."</option>";
              else print "<option value='".$rowCombo[0]."' >".$campo2."</option>";
            }
            print "</select></td>";
          }
          if (strcmp($campos[$j][2],"Text")==0) { // campo do tipo Text
            print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3]."></td></tr>";
          }
          if (strcmp($campos[$j][2],"Password")==0) { // campo do tipo Text
            print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input type='password' maxlength='15' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]." required></td></tr>";
          }
          if (strcmp($campos[$j][2],"Boolean")==0) { // campo do tipo Boolean
            if (strcmp($row[$j],"t")==0)
              print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  type='checkbox' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]." checked></td></tr>";
            else
              print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  type='checkbox' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]."></td></tr>";
          }
          if (strcmp($campos[$j][2],"DataBr")==0) { // campo do tipo Date
            print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'>
            <input  name='" .pg_field_name($res, $j). "' ".$campos[$j][3]; ?> onkeypress="formatar_mascara(this, '##/##/####')"></td></tr> <?php
          }
        } else print "<td width='66%' height='21' align='left' bgcolor='#4e78b1'><input  name=id value='" .$row[$j]."' ".$campos[0][3]." readonly=''></td></tr>";
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

  <?php  pg_close($dbcon); ?>

</body>
</html>
