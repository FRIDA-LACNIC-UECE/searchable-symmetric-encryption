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
        "emptyTable": "Tabela sem registros encontrados."
      },
      "ordering": false
  } )
  });
</script>
</head>
<?php
  session_start(); // acessar id e nome do usuario ativo atraves de $_SESSION['id_usuario']e $_SESSION['nome']
  error_reporting(0);
  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");

  $idtabela=$_GET["id"];

  include($_SERVER['DOCUMENT_ROOT']."/BD/scripts/Conexao.php");
  $sql = "SELECT * FROM administrador.tabelas WHERE id=".$idtabela;
  $stmt = Conexao::getConn()->prepare($sql);
  $stmt->execute();
  $row = $stmt->fetch();
  $rotulo = $row['rotulo']; // Rótulo da tabela ex: Cadastro>Grupo
  $tabela = $row['nome']; // Nome da tabela

  print "<font face='arial' size='2' color='silver'><b>".$rotulo."</b></font>";
  print "<br><br>";
  $sql = "SELECT * FROM public.".$tabela." ORDER BY id DESC";
  $stmt2 = Conexao::getConn()->prepare($sql);
  $stmt2->execute();
  $record = $stmt2->rowCount();
  print "<b>Total de Registros Encontrados: " .$record. ".</b> ";
  print "Clique na linha para editar, deletar ou consultar com detalhes o registro.";

  // Insere na página o botão INSERIR
  print "  <form method='get'>";
  print "    <input class='button' type='button' value='Inserir' onClick='inserirForm2(this.form);'>";
  print "    <button class='button' type='button' onClick='reload_();'><img height='25px' src='images/refresh.png' alt='Recarregar'></button>";
  print "    <input type=hidden name=tabela value='" .$tabela. "'>";
  print "  </form>";


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
    if($i < 1){
      print "<th class='column1'>".$row['rotulo']."</th>";
    } else {
      print "<th>".$row['rotulo']."</th>";
    }
  }
  print "</tr></thead>";

  // Insere corpo da tabela
  print "<tbody>";
  while($row = $stmt2->fetch(PDO::FETCH_BOTH)){
    print "<tr onclick='return popup3(\"geralRegistroConsultar.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'><td>";
    print $row[0] . "</td>";
    for($i=1; $i<$total_rows; $i++) {
      if ($rows[$i]['tipo'] == 'Combobox') {
        $sql = "SELECT " . $rows[$i]['propriedade'] . ' WHERE id=' . $row[$i];
        $stmt4 = Conexao::getConn()->prepare($sql);
        $stmt4->execute();
        $res = $stmt4->fetchAll();
        print "<td><div class='limitedHeight' title='".$res[0][1]."'>".$res[0][1]."</div></td>";
      }
      elseif ($rows[$i]['tipo'] == 'Boolean') {
        if ($row[$i] == 1) {
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
        $res_aux = Conexao::getConn()->prepare($sql_aux);
        $res_aux->execute();
        $cont_aux = 0;
        while ($line = $res_aux->fetch()){
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

  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");

?>
</html>