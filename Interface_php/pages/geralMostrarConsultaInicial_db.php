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

  $idtabela=$_GET["id"];
  $user_id=$_SESSION['id_usuario'];

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
  print "    <input class='button' type='button' value='Inserir' onClick='inserirForm3(this.form);'>";
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
    if($i == 0 or $i == 6 or $i == 8){
      continue;
    } else {
      print "<th>".$row['rotulo']."</th>";
    }
  }
  print "<th>Status</th>";
  print "</tr></thead>";

  // Insere corpo da tabela
  print "<tbody>";
  while($row = $stmt2->fetch(PDO::FETCH_BOTH)){
    print "<tr onclick='return popup3(\"geralRegistroConsultar_db.php?tabela=" . $tabela . "&id=" . $row[0] . "\")'>";
    for($i=1; $i<$total_rows; $i++) {
      if ($i == 6){
        continue;
      }
      if ($rows[$i]['tipo'] == 'Combobox') {
        $sql = "SELECT " . $rows[$i]['propriedade'] . ' WHERE id=' . $row[$i];
        $stmt4 = Conexao::getConn()->prepare($sql);
        $stmt4->execute();
        $res = $stmt4->fetchAll();
        print "<td><div class='limitedHeight' title='".$res[0][1]."'>".$res[0][1]."</div></td>";
      }
      elseif ($i == 7){
        $row[$i] = str_replace('{', '', $row[$i]);
        $row[$i] = str_replace('}', '', $row[$i]);
        $row[$i] = explode(',', $row[$i]);
        $row[$i] = implode(', ', $row[$i]);
        print "<td><div class='limitedHeight' title='".$row[$i]."'>".$row[$i]."</div></td>";
      }
      else {
        print "<td><div class='limitedHeight' title='".$row[$i]."'>".$row[$i]."</div></td>";
      }
    }
    print test_connection($user_id, $row[0], $tabela)[0];
    print "</tr>";
  }
  print "</tbody>";
  print "</table>";

  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Foot.php");

?>
</html>