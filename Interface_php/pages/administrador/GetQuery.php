<?php 
  session_start();  // acessar id e nome do usuario ativo atraves de $_SESSION['id_usuario']e $_SESSION['nome']
  error_reporting(0); 
?>
<html lang='pt-BR'>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="../styles/main.css">
<link rel="stylesheet" href="../styles/default.min.css">
<script src="../../libs/extjs/jquery-3.4.0.min.js"></script>
<script src="../../libs/extjs/jquery.dataTables.min.js"></script>
<script src="../../libs/extjs/dataTables.buttons.min.js"></script>
<script src="../../libs/extjs/jszip.min.js"></script>
<script src="../../libs/extjs/print.min.js"></script>
<script src="../../libs/extjs/vfs_fonts.js"></script>
<script src="../../libs/extjs/buttons.html5.min.js"></script>
<script src="../../libs/extjs/highlight.min.js"></script>
<script>hljs.highlightAll();</script>

<!--===============================================================================================-->

<script type="text/javascript">
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
    $('#query').DataTable( {
      dom: 'lrt<"abaixo_tabela"Bp>',
      "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tudo"] ],
      "language": {
        "lengthMenu": "Mostrar _MENU_ registos",
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
   })
  })
</script>
</head>
<?php
  require_once($_SERVER['DOCUMENT_ROOT']."/pages/Head.php");
  include ("../conexaoBD.php");
   
  print "<font face='arial' size='2' color='silver'><b>Consultar > Querys</b></font>";
  print "<br><br>";
  
  //Formulário inicial
  if(!isset($_GET["query"]) or isset($_GET['voltar'])){
    unset($_GET['voltar']);
    print "<form name='TextForm' method='get' target='_self'>";
    print "<b style='display: inline-block;vertical-align: middle;margin: 25px 0;'>Querys</b>";
    print "<input style='display:inline-block;vertical-align:middle;margin: 10px 0;' class='button' type='submit' value='Executar'>";
    print "<br><div><textarea required name='query' placeholder='Escreva sua Query...' style='font-size:20px;margin-top:10px; max-width: 100%;' cols='1000' rows='10'></textarea><div>";
    print " </form>";
  }
  
  if(isset($_GET["query"])){
    $query = $_GET["query"];
    $sql = pg_query($dbcon, $query);
    if ($sql) {
      $status = "Ok!";
    } else {
        $status = "Error!";
      }
    print "<form name='TextForm2' method='get' target='_self'>";
    print "<b style='display: inline-block;vertical-align: middle;margin: 25px 0;'>Status Query: " .$status. "</b> ";
    print "<input style='display:inline-block;vertical-align:middle;margin: 10px 0;' class='button' type='submit' value='Voltar'/>";
    print "<input type='hidden' name=voltar value='1'/>";
    print "</form>";

    print "SQL EXECUTADO:";
    print "<div><pre><code style='font-size:18;' class='language-sql'>".$query."</code></pre></div>";
  
    if ($sql) {
      print "<table id='query'>";
      print "<thead>";
      print "<tr class='table100-head'>";
      $all = pg_fetch_assoc($sql);
      $col_names = array_keys($all);
      $len = count($col_names);
      foreach ($col_names as $column){
        print "<th>".$column."</th>";
      }
      print "</tr>";
      print "</thead>";
      // Insere o corpo da tabela
      print "<tbody>";
      $sql2 = pg_query($dbcon, $query);
      while ($row = pg_fetch_row($sql2)) {
        print "<tr>";
        for($j=0; $j<$len; $j++) {
          print "<td><div class='limitedHeight'>" .$row[$j]. "</div></td>";
        }
      }
      print "</tr>";
      print "</tbody>";
      print "</table>";
      print "<div class='button-label'>Exportar: </div>";
    
      print "<script type='text/javascript'>Richtag.doTableLayout('sample')</script>";

    }
    else {
      $error = pg_last_error($dbcon);
      print "<div style='margin: 10px 10px;'>$error</div>";
    }  
    pg_close($dbcon);
  }  
require_once($_SERVER['DOCUMENT_ROOT']."/etice/pages/Foot.php");
  
?>

</html>
