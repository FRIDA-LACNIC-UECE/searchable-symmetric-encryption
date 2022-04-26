<?php 
  session_start();  // acessar id e nome do usuario ativo atraves de $_SESSION['id_usuario']e $_SESSION['nome']
  error_reporting(0); 
?>
<html lang='pt-BR'>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="styles/main.css">
<script src="../libs/extjs/jquery-3.4.0.min.js"></script>
<script src="../libs/extjs/jquery.mask.min.js"></script>
<script src="../libs/extjs/jquery.dataTables.min.js"></script>
<script src="../libs/extjs/dataTables.buttons.min.js"></script>
<script src="../libs/extjs/jszip.min.js"></script>
<script src="../libs/extjs/print.min.js"></script>
<script src="../libs/extjs/vfs_fonts.js"></script>
<script src="../libs/extjs/buttons.html5.min.js"></script>

<!--===============================================================================================-->

<script type="text/javascript">
  var MaskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00)00000-0000' : '(00)0000-00009';
  },
  Options = {
    onKeyPress: function(val, e, field, options) {
      field.mask(MaskBehavior.apply({}, arguments), options);
    }
  };

  function reload_(){
    document.location.reload(true);
  }

  function format(){
    $('.filtro').mask('#0.00%', {
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
  }

  function gettime(){
    let date_ob = new Date();
    let date = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    let hours = date_ob.getHours();
    let minutes = date_ob.getMinutes();
    let newdate = year + "_" + month + "_" + date + "_" + hours + minutes;
    return newdate;
  }

  $(document).ready(function(){ //https://datatables.net/
    $("div[name=dec]").mask('#.##0,00', {reverse: true});
    $('#consulta').DataTable( {
      dom: '<"acima"lf>t<"abaixo_tabela"Bp>',
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tudo"] ],
      "language": {
        "lengthMenu": "Mostrar _MENU_ registos",
        "search": "Pesquisar: ",
        "paginate": {
          "first": "Primeiro",
          "previous": "Anterior",
          "next": "Seguinte",
          "last": "Último",
        },
        "emptyTable": "Tabela sem registros encontrados"
      },
      buttons: {
        buttons: [
          { extend: 'excelHtml5', className: 'button-exp-libre', text: "EXCEL", filename: '*_' + gettime()},
          { extend: 'print', className: 'button-exp-pdf', text: "PDF", filename: '*_' + gettime()}
        ]
      }
    } )

    $('#consulta_inicial').DataTable( {
      dom: '<"acima"lf>t<"abaixo_tabela"p>',
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tudo"] ],
      "language": {
        "lengthMenu": "Mostrar _MENU_ registos",
        "search": "Pesquisar: ",
        "paginate": {
          "first": "Primeiro",
          "previous": "Anterior",
          "next": "Seguinte",
          "last": "Último",
        },
        "emptyTable": "Tabela sem registros encontrados"
      },
    })

    $("select[name=campo_da_busca]").on("change",function(){
        $('.filtro').val('');
        $value = $(this).val();
        $type = $value.split("&")[1];
        $input = $(".filtro");
        $(".filtro").unmask();
        if($type == 'Telefone'){
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({type:"text", oninput:"$(this).mask(MaskBehavior, Options)"});
        }
        else if ($type == 'Inteiro') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({type:"text", oninput:"$(this).mask('#0', {reverse: true})"});
        } 
        else if ($type == 'CPF') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({type:"text", oninput:"$(this).mask('000.000.000-00')"});
        } 
        else if ($type == 'CNPJ') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({type:"text", oninput:"$(this).mask('00.000.000/0000-00')"});
        } 
        else if ($type == 'Porcentagem') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({type:"text", oninput:"format()"});
        } 
        else if ($type == 'DataBr') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({minlength:"10", type:"text", oninput:"$(this).mask('00/00/0000')", placeholder: 'data-início'});
          $('.campos_2').append("<b class='sep'>-</b>");
          $('.campos_2').append("<input class='data-fim' name='filtro-data-fim' type='text' placeholder='data-fim'/>"); 
          $('.data-fim').attr({oninput:"$(this).mask('00/00/0000')"})
        } 
        else if ($type == 'Decimal') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
          $input.attr({type:"text", oninput:"$(this).mask('#.##0,00', {reverse: true})"});
        } 
        else if ($type == 'Boolean') {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.attr({type:"checkbox"});
          $input.removeAttr("value");
        }
        else {
          $(".data-fim").remove();
          $(".sep").remove();
          $input.removeAttr("oninput");
          $input.removeAttr("maxlength");
          $input.removeAttr("minlength");
          $input.removeAttr("placeholder");
        }
    })
  })

</script>
</head>
<?php
  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");
  include ("conexaoBD.php");
  $idtabela=$_GET["id"];
  $gs = Null;
  $fil = Null;

  $sql = "SELECT descricao FROM menu_submenu WHERE acao LIKE '%=".$idtabela."';";
  $query = pg_query($dbcon, $sql);
  $response = pg_fetch_row($query);
  $rotulo = $response[0];
  
  if(isset($_GET["campo_da_busca"])){
    $campo_busca = explode("&", $_GET["campo_da_busca"])[0];
    $gs = $campo_busca;
  }
  
  if(isset($_GET["filtro"]))
    $fil = $_GET["filtro"];
   
  //Rótulo
  $sql = "select * from administrador.tabelas where id = '".$idtabela."'";
  $registros = pg_query($dbcon, $sql);
  $rowCombo = pg_fetch_row($registros);
  $tabela = $rowCombo[1];
  $rotulo = $rowCombo[2];
  print "<font face='arial' size='2' color='silver'><b>Consulta > ".explode(">", $rotulo)[1]."</b></font>";
  print "<br><br>";
  
  //Formulário inicial
  if(!isset($_GET["campo_da_busca"]) or isset($_GET['voltar'])) {
    unset($_GET['voltar']);
    print "<b>Consultar registros na tabela: ".explode(">", $rotulo)[1]."</b><br>";
    print "<form name='TextForm' method='get' target='_self'>";
    print "<input class='button' type='submit' value='Consultar'>";
    print "<button class='button' type='button' onClick='reload_();'><img height='25px' src='images/refresh.png' alt='Recarregar'></button>";
    print "<input type='hidden' name='id' value=".$_GET['id'].">";
    print "<div class='teste'>";

    // Insere na página o combobox da seleção do grupo
    print "<div class='campos_1'><font>Filtrar por: </font>";
    $sql = "select * from administrador.campos_tabela where idtabela = '".$idtabela."' order by id";
    $registros = pg_query($dbcon, $sql);
    $linhas = pg_fetch_all($registros);
    $total_rows = count($linhas);
    print "<select size='1' name='campo_da_busca'>";
    $campos_mostrados = [];
    for($i=0; $i < $total_rows; $i++) {
      if ($gs==$linhas[$i]["id"]) {
        //$rowCombo na posição 2 é o campo roulo de campos_tabela, assim atribuo esse valor a super global $_GET[campo_da_busca"], assim o usuário escolhe o campo em que deseja realizar a busca.
        print "<option selected value='".$linhas[$i]["campo"]."&".$linhas[$i]["tipo"]."'>".$linhas[$i]["rotulo"]."</option>";
        array_push($campos_mostrados, $linhas[$i]["campo"]);
      } else { 
        print "<option value='".$linhas[$i]["campo"]."&".$linhas[$i]["tipo"]."' >".$linhas[$i]["rotulo"]."</option>"; If ($gs==Null) $gs=$linhas[$i]["campo"]; }
      }
    $_SESSION['id'] = $idtabela;
    print "</select></div>";
    print "<div class='campos_2'><font>Valor: </font>";
    print "<input class='filtro' type='text' name='filtro'"; ?> oninput="formatar_inteiro(this)"></div></div> <?php
    print "<table class='reset' style='margin: 0'>";
    print "<thead><div style='margin-left:5px;'>Colunas exibidas: </div></thead>";
    $registros = pg_query($dbcon, $sql);
    $columns = 0;

    //Com esse combobox o usuário consegue escolher os campos que serão exibidos na sua consulta.
    while ($rowCombo = pg_fetch_row($registros)) {
      print $columns % 5 == 0 ? "<tr class='reset'>" : "";
      print "<td class='reset'>";
      print "  <input type='checkbox' id='".$rowCombo[1]."' name=campos_exibidos[] value='".$rowCombo[2]."' checked/>";
      print "  <label for='".$rowCombo[1]."' style='padding-left: 10px'>".$rowCombo[2]."</label>";
      print "</td>";
      $columns += 1;
      print $columns % 5 == 0 ? "</tr>" : "";
    }

    print "</table>";
    print " </form>";

    // Tabela para visualizar todos os documentos
    $sql = "SELECT * FROM administrador.campos_tabela WHERE idtabela=".$idtabela. " ORDER BY ordem";
    $query = pg_query($dbcon, $sql);
    $rows = pg_fetch_all($query);
    $total_rows = count($rows);

    // Insere o cabeçalho da tabela
    print "<table id='consulta_inicial' class='acima_consulta'>";
    print "<thead>";
    print "<tr class='table100-head'>";
    for($i=0; $i<$total_rows; $i++){
        $row = $rows[$i];
        if($i < 1){
            print "<th class='column1'>".$row['rotulo']."</th>";
        } else {
            print "<th>".$row['rotulo']."</th>";
        }
    }
    print "</tr></thead>";

    // Insere corpo da tabela
    $sql = "SELECT * FROM public.".$tabela." ORDER BY id DESC";
    $query = pg_query($dbcon, $sql);

    print "<tbody>";
    while($row = pg_fetch_row($query)){
      print "<tr onclick='return popup3(\"geralRegistroVer.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'>";
      print "<td>" . $row[0] . "</td>";
      for($i=1; $i<$total_rows; $i++) {
        if ($rows[$i]['tipo'] == 'Combobox') {
          $sql = "SELECT ".$rows[$i]['propriedade'].' WHERE id=' . $row[$i];
          $qr2 = pg_query($dbcon, $sql);
          $res = pg_fetch_row($qr2);
          print "<td><div class='limitedHeight' title='".$res[1]."'>".$res[1]."</div></td>";
        }
        elseif ($rows[$i]['tipo'] == 'Boolean') {
          if ($row[$i] == 't') {
              print "<td><div class='limitedHeight'>SIM</div></td>";
          } else {
              print "<td><div class='limitedHeight'>NÃO</div></td>";
          }
        }
        elseif ($rows[$i]['tipo'] == 'DataBr' and !empty($row[$i])) {
            $aux = explode("-", $row[$i]);
            $data = $aux[2]."/".$aux[1]."/".$aux[0];
            print "<td><div class='limitedHeight'>".$data."</div></td>";
        }
        elseif ($rows[$i]['tipo'] == 'Decimal' and !empty($row[$i])) {
          print "<td><div name='dec' class='limitedHeight'>".$row[$i]."</div></td>";
        }
        elseif($rows[$i]['tipo'] == 'Multselect' and !empty($row[$i])){
          $row[$i] = str_replace('{', '', $row[$i]);
          $row[$i] = str_replace('}', '', $row[$i]);
          $row[$i] = explode(',', $row[$i]);
          $row[$i] = implode(', ', $row[$i]);
          $str = $rows[$i]['propriedade'];
          $pattern = "/id, /i";
          $busca = preg_replace($pattern, "", $str);
          $sql_aux = "SELECT ".$busca." where id IN (".$row[$i].")";
          $res_aux = pg_query($dbcon, $sql_aux);
          $cont_aux = 0;
          while ($line = pg_fetch_row($res_aux)){
            if ($cont_aux == 0) $campo_formatado = $line[0]; else $campo_formatado = $campo_formatado.", ".$line[0];  
            $cont_aux++;
          }
          print "<td><div class='limitedHeight' title='".$campo_formatado."'>".$campo_formatado."</div></td>";
        }
        else {
          print "<td><div class='limitedHeight' title='".$row[$i]."'>".$row[$i]."</div></td>";
        }
      }
      print "</tr>";
    }
    print "</tbody>";
    print "</table>";
  }

  // Consulta por coluna
  // fil - Valor do filtro;
  // gs - Campo (coluna) da busca;
  if(isset($_GET["campo_da_busca"])){
    $sqlt = "select * from administrador.tabelas where id='".$_GET['id']."'";
    $rest = pg_query($dbcon, $sqlt);
    while ($rowt = pg_fetch_row($rest)) { $nomenclatura=$rowt[2]; $tabela=$rowt[1]; }

    $sqlct = "select * from administrador.campos_tabela where idtabela='".$_GET['id']."' order by ordem";
    $resct = pg_query($dbcon, $sqlct); $i=0;

    while ($rowct = pg_fetch_row($resct)) { 
      $campos[$i][0]= $rowct[1];
      $campos[$i][1]= $rowct[2];
      $campos[$i][2]= $rowct[3];
      $campos[$i][3]= $rowct[4];
      $i=$i+1;
    }

    $sql = "SELECT campo, tipo, propriedade FROM administrador.campos_tabela WHERE idtabela=".$_GET['id']." AND campo='".$gs."';";
    $res = pg_query($dbcon, $sql);
    $row = pg_fetch_assoc($res);

    if (empty($fil) and $row['tipo'] != "DataBr" and $row['tipo'] != "Boolean"){
      $sql = "SELECT * FROM ".$tabela." ORDER BY id DESC;";
    }

    elseif (in_array($row["tipo"], ["Text", "Textlong", "Telefone", "CNPJ", "CPF"])) {
      if(is_numeric($fil)){
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." = '".$fil."' ORDER BY id DESC;";
      } else {
        $pesquisa_lower = strtolower($fil);
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." iLIKE '%".$pesquisa_lower."%' ORDER BY id DESC;";
      }
    }

    elseif (strcmp($row["tipo"], "Combobox") == 0){
      $campo = explode(" ", $row["propriedade"]);

      // Caso de Combobox com dados cruzados (busca de uma segunda tabela)
      if ($campo[1][0] == "(") {
        $campo = explode(")", explode("(", $row["propriedade"])[1]);
        $mid_query = explode(" ", $campo[0]);
        $campo_tabela = end(explode(" ", $campo[1]));

        $campo_2 = $mid_query[1];
        $campo_tabela_mid = $mid_query[3];
        $sql = "SELECT id FROM ".$campo_tabela_mid." WHERE ".$campo_2." iLIKE '%".$fil."%';";
        $res = pg_query($dbcon, $sql);

        $campos_externos = explode(".", explode("=", end($mid_query))[1]);
        $tabela_externa = $campos_externos[0];
        $coluna_externa = $campos_externos[1];

        $sql = "SELECT id FROM ".$tabela_externa." WHERE ".$coluna_externa." IN (";
        $cont = 0;
        while($id_row = pg_fetch_row($res)) {
          if ($cont == 0){
            $sql .= $id_row[0];
          } else {
            $sql .= ", ".$id_row[0];
          }
          $cont = $cont + 1;
        }
        $sql .= ") ORDER BY id DESC;";
        $res = pg_query($dbcon, $sql);

        $sql = "SELECT * FROM ".$tabela." WHERE ".$row["campo"]." IN (";
        $cont = 0;
        while($id_row = pg_fetch_row($res)) {
          if ($cont == 0){
            $sql .= $id_row[0];
          } else {
            $sql .= ", ".$id_row[0];
          }
          $cont = $cont + 1;
        }
        $sql .= ") ORDER BY id DESC;";
      }
      // Caso de Combobox simples (busca de uma única tabela)
      else {
        $campo_2 = $campo[1];
        $campo_tabela = $campo[3];
        $sql = "SELECT id FROM ".$campo_tabela." WHERE ".$campo_2." iLIKE '%".$fil."%';";
        $res = pg_query($dbcon, $sql);

        $sql = "SELECT * FROM ".$tabela." WHERE ".$row["campo"]." IN (";
        $cont = 0;
        while($id = pg_fetch_row($res)) {
          if ($cont == 0){
            $sql .= $id[0];
          } else {
            $sql .= ", ".$id[0];
          }
          $cont = $cont + 1;
        }
        $sql .= ") ORDER BY id DESC;";
      }
    }
    elseif (in_array($row["tipo"], ["Inteiro", "Decimal", "Porcentagem"])){
      $fil = str_replace(".", "", $fil);
      $fil = str_replace(",", ".", $fil);
      $sql = "SELECT * FROM ".$tabela." WHERE CONCAT(".$gs.", '') iLIKE '%".$fil."%' ORDER BY id DESC;";
    }

    elseif (strcmp($row["tipo"], "Boolean") == 0 ){
      if (isset($fil)){
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." = 't' ORDER BY id DESC;";
      } else {
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." = 'f' ORDER BY id DESC;";
      }
    }

    elseif (strcmp($row["tipo"], "DataBr") == 0 ) {
      if (empty($fil) and empty($_GET['filtro-data-fim'])){
        $sql = "SELECT * FROM ".$tabela." ORDER BY id DESC;";
      } elseif (empty($_GET['filtro-data-fim'])) {
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." >= '".$fil."' ORDER BY id DESC;";
      } elseif (empty($fil)) {
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." <= '".$_GET['filtro-data-fim']."' ORDER BY id DESC;";
      } else {
        $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." BETWEEN '".$fil."' AND '".$_GET['filtro-data-fim']."' ORDER BY id DESC;";
      }
    }

    elseif (strcmp($row["tipo"], "Multselect") == 0 ){
      # select possiveis_clientes from tb_lead where possiveis_clientes && array[5,142];  
      $campos_propriedade = explode(' ', $row['propriedade']);
      $campo_buscado = $campos_propriedade[1];
      $tabela_busca = $campos_propriedade[3];
      $sql = "SELECT id FROM ".$tabela_busca." WHERE ".$campo_buscado." iLIKE '%".$fil."%' ORDER BY id DESC;";
      $ids_result = [];
      $res = pg_query($dbcon, $sql);
      while($register = pg_fetch_row($res)){
        array_push($ids_result, $register[0]);
      }
      $ids_result_str = implode(', ', $ids_result);
      $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." && array[".$ids_result_str."] ORDER BY id DESC;";
    }

    else {
      $sql = "SELECT * FROM ".$tabela." WHERE ".$gs." = ".$fil." ORDER BY id DESC;";
    }

    $res = pg_query($dbcon, $sql);
    $record= pg_num_rows($res);

    if (empty($record)){
      $record = 0;
    }

    print "<b>Total de Registros Encontrados: " .$record. ".</b> ";
    print "<form name='TextForm2' method='get' target='_self'>";
    print "<input class='button' type='submit' value='Voltar'/>";
    print "<input type='hidden' name=id value=".$_GET["id"]." />";
    print "<input type='hidden' name=voltar value='1'/>";
    print "</form>";

    // Insere o cabeçalho da tabela
    if ($record > 0) {
      print "<table id='consulta'>";
      print "<thead>";
      print "<tr class='table100-head'>";
      $campos_exibidos = $_GET["campos_exibidos"];
      $i = pg_num_fields($res);

      for ($j = 0; $j < $i; $j++) {
      //Compara o campo da tabela contido em $campos[$j][1] é requerido pelo usuário em $_GET["campos_exibidos".$j] se não for não será exibido.
        if(in_array($campos[$j][1], $campos_exibidos)){       
          print "<th>".$campos[$j][1]."</th>";
        }
      }

      print "</tr>";
      print "</thead>";
      // Insere o corpo da tabela
      print "<tbody>";
      $sql1 = "select * from administrador.campos_tabela where idtabela='".$_GET['id']."' order by ordem";
      $res1 = pg_query($dbcon, $sql1);
      $rows = pg_fetch_all($res1);
      $total_rows = count($rowct); #consertar aqui (use fetch all)

      while ($row = pg_fetch_row($res)) {
        print "<tr onclick='return popup3(\"geralRegistroVer.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'>";
        for($j=0; $j<$i; $j++) {
          if ($rows[$j]['tipo'] == 'Combobox' and in_array($campos[$j][1], $campos_exibidos)) {
            $sql = "SELECT " . $rows[$j]['propriedade'] . ' WHERE id=' . $row[$j];
            $con = pg_query($dbcon, $sql);
            $res3 = pg_fetch_row($con);
              print "<td><div class='limitedHeight' title='".$res3[1]."'>".$res3[1]."</div></td>";
          }
          elseif ($rows[$j]['tipo'] == 'Boolean' and in_array($campos[$j][1], $campos_exibidos)) {
            if ($row[$j] == 't') {
              print "<td><div class='limitedHeight'>SIM</div></td>";
            } else {
              print "<td><div class='limitedHeight'>NÃO</div></td>";
            }
          }
          elseif ($rows[$j]['tipo'] == 'DataBr' and !empty($row[$j]) and in_array($campos[$j][1], $campos_exibidos)) {
            $aux = explode("-", $row[$j]);
            $data = $aux[2]."/".$aux[1]."/".$aux[0];
            print "<td><div class='limitedHeight'>".$data."</div></td>";
          } 
          elseif ($rows[$j]['tipo'] == 'Decimal' and !empty($row[$j]) and in_array($campos[$j][1], $campos_exibidos)) {
            print "<td><div name='dec' class='limitedHeightdec'>".$row[$j]."</div></td>";
          }
          elseif($rows[$j]['tipo'] == 'Multselect' and !empty($row[$j]) and in_array($campos[$j][1], $campos_exibidos)) {
            $row[$j] = str_replace('{', '', $row[$j]);
            $row[$j] = str_replace('}', '', $row[$j]);
            $row[$j] = explode(',', $row[$j]);
            $row[$j] = implode(', ', $row[$j]);
            $str = $rows[$j]['propriedade'];
            $pattern = "/id, /i";
            $busca = preg_replace($pattern, "", $str);
            $sql_aux = "SELECT ".$busca." where id IN (".$row[$j].")";
            $res_aux = pg_query($dbcon, $sql_aux);
            $cont_aux = 0;
            while ($line = pg_fetch_row($res_aux)){
              if ($cont_aux == 0) $campo_formatado = $line[0]; else $campo_formatado = $campo_formatado.", ".$line[0];  
              $cont_aux++;
            }
            print "<td><div class='limitedHeight' title='".$campo_formatado."'>".$campo_formatado."</div></td>";
          }
          elseif (in_array($campos[$j][1], $campos_exibidos)){
            print "<td><div class='limitedHeight' title='".$row[$j]."'>" .$row[$j]. "</div></td>";
          }
        }
      }
      print "</tr>";
      print "</tbody>";
      print "</table>";
      print "<div class='button-label'>Exportar: </div>";

      pg_close($dbcon);
    }
  }
require_once($_SERVER['DOCUMENT_ROOT']."/etice/pages/Foot.php");

?>

</html>
