<?php

function validarInjecaoXSS($nome, $email, $senha, $captcha){
    if(substr_count($nome, "<") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } elseif(substr_count($nome, ">") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } elseif(substr_count($nome, " ") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($nome, "&lt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($nome, "&gt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($nome, "&nbsp;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }
    
    if(substr_count($senha, "<") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } elseif(substr_count($senha, ">") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } if(substr_count($senha, " ") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($senha, "&lt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($senha, "&gt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($senha, "&nbsp;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }

    if(substr_count($captcha, "<") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } elseif(substr_count($captcha, ">") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } if(substr_count($captcha, " ") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($captcha, "&lt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($captcha, "&gt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($captcha, "&nbsp;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }

    if(substr_count($email, "<") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } elseif(substr_count($email, ">") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    } if(substr_count($email, " ") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($email, "&lt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($email, "&gt;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }elseif(substr_count($email, "&nbsp;") >= 1){
        header("Location: index.php?CharInvalido=true");
        exit;
    }
}


?>
