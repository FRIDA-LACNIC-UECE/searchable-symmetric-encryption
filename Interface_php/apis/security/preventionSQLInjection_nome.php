<?php 
    function validarInjecaoSQL($nome, $senha, $email){
        if(substr_count($nome, "'") >= 1){
            header("Location: index.php?CharInvalido=true");
            exit;
        }
        
        if(substr_count($senha, "'") >= 1){
            header("Location: index.php?CharInvalido=true");
            exit;
        }

        if(substr_count($email, "'") >= 1){
            header("Location: index.php?CharInvalido=true");
            exit;
        }
    }
?>
