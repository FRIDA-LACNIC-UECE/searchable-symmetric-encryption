<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang='pt-BR'>
<head>
<title>Digital Shield Security</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="styles/styleInsercao.css">
<link rel="shortcut icon" href="../styles/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="../styles/global.css">
<link href="styles/multiselect.css" media="screen" rel="stylesheet" type="text/css">
<script src="../libs/extjs/jquery-3.4.0.min.js"></script>
<script src="../libs/extjs/jquery.mask.min.js"></script>
<script src="../libs/extjs/jquery.multi-select.js"></script>

<script language="JavaScript" type="text/JavaScript">

function deletarRec(form) {
  var result = confirm("A deleção é irreversível. O registro será deletado, ok?");
  if (result) {
    st=form.tabela.value;
    st= st + "&id=" + form.id.value;
    st= st + "&campo_id=" + form.campo_id.value;
    window.open("geralRegistroDeletar.php?tabela="+st,"_top");
  }
}

var MaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00)00000-0000' : '(00)0000-00009';
},
Options = {
  onKeyPress: function(val, e, field, options) {
      field.mask(MaskBehavior.apply({}, arguments), options);
  }
};
$(document).ready(function(){
  $(".dec").mask('#,##0.00', {reverse: true})
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
  include ("conexaoBD.php");
  error_reporting(0);
  $tabela=$_GET["tabela"];
  $id= $_GET["id"];
  //pegando id da tabela
  $sql = "SELECT id from administrador.tabelas WHERE nome='".$tabela."';";
  $registros = pg_query($dbcon, $sql);
  $tabela_id = pg_fetch_row($registros)[0];
  //pegando campo descrição da tabela
  $sql = "SELECT descricao FROM public.menu_submenu WHERE acao LIKE '%=".$tabela_id."' LIMIT 1;";
  $registros = pg_query($dbcon, $sql);
  $rowCombo = pg_fetch_row($registros)[0];
  print "<h2 style='padding-left:1%; text-align:center;'>DADOS DO REGISTRO DA TABELA: ".$rowCombo."</h2>";

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
    
  $sql = "select * from " .$tabela. " where " .$campos[0][0]. "='".$id."'";
  $res = pg_query($dbcon, $sql);
  
?>

  <form name="TextForm" class="form" action="geralRegistroAtualizar.php" method="post" target="_top">
    <table width='90%' bgcolor='#5271ff' align='center' cellspacing='10' height='112' style='border: 1px solid #5271ff; border-radius: 8px;'>
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
    // Campos escondidos para preservar as variáveis tabela e id
  print "<input type=hidden name=tabela value='" .$tabela. "'>";
  print "<input type=hidden name=campo_id value='" .$campos[0][0]. "'>";
    
  $row = pg_fetch_row($res);
  $i = pg_num_fields($res);
  $id = $row[0];
  for ($j = 1; $j < $i; $j++) {
    print "<tr><td width='4%' height='21' bgcolor='#5271ff'></td>";
    if (in_array($campos[$j][0], $campos_notnull) and substr($campos[$j][0], 0, 2) != 'fg') {
      print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>*" .$campos[$j][1]. " :</b></font></td>";
    } else {
      print "<td class='identificador' width='30%' height='21' align='right' bgcolor='#5271ff'><font size='2' face='verdana'><b>" .$campos[$j][1]. " :</b></font></td>";
    }
    if (strcmp($campos[$j][2],"Combobox")==0) {  // Campo do tipo Combobox
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
    elseif (strcmp($campos[$j][2],"Text")==0) { // campo do tipo Text
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input  name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3]."></td></tr>";
    }
    elseif (strcmp($campos[$j][2],"Senha")==0) { // campo do tipo Text
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input type='password' name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3]."></td></tr>";
    }
    elseif (strcmp($campos[$j][2],"Boolean")==0) { // campo do tipo Boolean
      if ($row[$j] == 't') {
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input  type='checkbox' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]." checked></td></tr>";
      } else {
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input  type='checkbox' name='" .pg_field_name($res, $j). "' ".$campos[$j][3]."></td></tr>";
      }
    }
    elseif (strcmp($campos[$j][2],"Textlong")==0) { // campo do tipo Text
              print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><textarea  name='" .pg_field_name($res, $j). "' ".$campos[$j][3].">".$row[$j]."</textarea></td></tr>";
          }
    elseif (strcmp($campos[$j][2],"Link")==0) { // campo do tipo Link
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><input  name='" .pg_field_name($res, $j). "' value='" .$row[$j]."' ".$campos[$j][3]."></td></tr>";
      print "<tr><td width='4%' height='21' bgcolor='#5271ff'></td>";
      print "<td width='30%' height='21' align='right' bgcolor='#5271ff'><b>Link de acesso :</b></td>";
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><a href='Anexos/".$row[$j]."' target='_blank'><font face='arial' size='2' color='blue'>".$row[$j]."</font></a></td></tr>";
    }            
    elseif (strcmp($campos[$j][2],"DataBr")==0) { // campo do tipo Date
      if (!empty($row[$j])){
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
        <input  name='" .pg_field_name($res, $j). "' value='".date( "d/m/Y", strtotime($row[$j]))."' ".$campos[$j][3]; ?> oninput="$(this).mask('##/##/####')"></td></tr> <?php
      } else {
        print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
        <input  name='" .pg_field_name($res, $j). "' value='' ".$campos[$j][3]; ?> oninput="formatar_data(this, '##/##/####')"></td></tr> <?php
      }
    }
    elseif (strcmp($campos[$j][2],"Telefone")==0) { // campo do tipo Telefone
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
      <input maxlength=13 class='phone' name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask(MaskBehavior, Options)"></td></tr> <?php
    }
    elseif (strcmp($campos[$j][2],"Inteiro")==0) { // campo do tipo Inteiro
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
      <input maxlength=13 name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask('#0', {reverse: true})"></td></tr> <?php
    }
    elseif (strcmp($campos[$j][2],"Decimal")==0) { // campo do tipo Decimal
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
      <input class='dec' maxlength=16 name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask('#,##0.00', {reverse: true})"></td></tr> <?php
    }
    elseif (strcmp($campos[$j][2],"CPF")==0) { // campo do tipo CPF
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
      <input type='text' class='cpf' maxlength=14 name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask('000.000.000-00')"></td></tr> <?php
    }
    elseif (strcmp($campos[$j][2],"CNPJ")==0) { // campo do tipo CNPJ
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
      <input type='text' class='cnpj' maxlength=18 name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask('00.000.000/0000-00')"></td></tr> <?php
    }
    elseif (strcmp($campos[$j][2],"Porcentagem")==0) { // campo do tipo Porcentagem
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'>
      <input type='text' class='percent' name='" .pg_field_name($res, $j). "' value='".$row[$j]."' ".$campos[$j][3]; ?> oninput="$(this).mask('##0,00%', {reverse: true})"></td></tr> <?php
    }
    elseif (strcmp($campos[$j][2],"Multselect")==0) {  // Campo do tipo Multselect
      $sql="select ".$campos[$j][3]." order by 2";
      $registros = pg_query($dbcon, $sql);
      $n_campos= pg_num_fields($registros);
      print "<td width='66%' height='21' align='left' bgcolor='#5271ff'><select multiple='multiple' id='my-select' name='".$campos[$j][0]."[]'>";
      $k=0;
      $replaced =  str_replace('{', '', $row[$j]);
      $replaced =  str_replace('}', '', $replaced);
      $selected = explode(',', $replaced);
      while ($rowMult = pg_fetch_row($registros)) {
        if ($n_campos==1) $campo2=$rowMult[0]; else $campo2=$rowMult[1];
        if (in_array($rowMult[0], $selected)){
          print "<option value='".$rowMult[0]."'selected >".$campo2."</option>";
        } else {
          print "<option value='".$rowMult[0]."' >".$campo2."</option>";
        }
        $k=$k+1;
      }
      print "</select></td>";
    }
  }
  print "<input type=hidden name=id value='" .$row[0]."'</td></tr>";
  
  print " <tr>
              <td width='4%' height='21' bgcolor='#5271ff'></td>
              <td width='30%' height='21' align='right' bgcolor='#5271ff'></td>
              <td width='30%' height='21' align='right' bgcolor='#5271ff'>
              <input class='button' type='submit' value='Salvar'>
              <input id='del' class='button' type='button' value='Deletar' onClick='deletarRec(this.form);'>
              </td>
            </tr>";
?>

</table>
</form>

<div style="height: 30px;"></div>

<p style='color: #b15757; text-align:center;'>
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
