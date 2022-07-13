<?php 
    function validarInjecaoSQL($conta, $senha){
        if(substr_count($conta, "'") >= 1){
            header("Location: index.php?CharInvalido=true");
            exit;
        }
        
        if(substr_count($senha, "'") >= 1){
            header("Location: index.php?CharInvalido=true");
            exit;
        }
    }
?>
