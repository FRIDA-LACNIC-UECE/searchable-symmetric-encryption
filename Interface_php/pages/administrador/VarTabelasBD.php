<?php
    //Identificação das colunas:
    //0-nome do campo, 1-rótulo do campo, 2-tipo de dado no form, 3-propriedade do campo.

    //campos e propriedades da tabela administrador.grupo
    $grupo[0][0]="idgrupo"; $grupo[0][1]="Id."; $grupo[0][2]="Text"; $grupo[0][3]=" size='10'";
    $grupo[1][0]="descricao"; $grupo[1][1]="Descrição"; $grupo[1][2]="Text"; $grupo[1][3]="";
	
 	//campos e propriedades da tabela administrador.unidade
    $unidade[0][0]="id"; $unidade[0][1]="Id."; $unidade[0][2]="Text"; $unidade[0][3]=" size='10'";
    $unidade[1][0]="descricao"; $unidade[1][1]="Descrição"; $unidade[1][2]="Text"; $unidade[1][3]="";
 
    //campos e propriedades da tabela administrador.usuario
    $usuario[0][0]="idusuario"; $usuario[0][1]="Id."; $usuario[0][2]="Text"; $usuario[0][3]=" size='10'";
	$usuario[1][0]="nome"; $usuario[1][1]="Nome"; $usuario[1][2]="Text"; $usuario[1][3]="";
    $usuario[2][0]="conta"; $usuario[2][1]="Conta"; $usuario[2][2]="Text"; $usuario[2][3]=" size='8'";
    $usuario[3][0]="senha"; $usuario[3][1]="Senha (Máx. 15 caracteres)"; $usuario[3][2]="Password"; $usuario[3][3]="";
    $usuario[4][0]="email"; $usuario[4][1]="E-mail"; $usuario[4][2]="Text"; $usuario[4][3]=" size='30' maxlength='40'";
    $usuario[5][0]="idgrupo"; $usuario[5][1]="Grupo"; $usuario[5][2]="Combobox"; $usuario[5][3]="idgrupo, descricao from administrador.grupo order by descricao";
    $usuario[6][0]="celular"; $usuario[6][1]="Celular"; $usuario[6][2]="Text"; $usuario[6][3]=" size='11' maxlength='11'";
	$usuario[7][0]="idunidade"; $usuario[7][1]="Unidade"; $usuario[7][2]="Combobox"; $usuario[7][3]="id, descricao from administrador.unidade order by id";
	$usuario[8][0]="dica"; $usuario[8][1]="Dica para Senha"; $usuario[8][2]="Text"; $usuario[8][3]=" size='30' maxlength='30'";

    //campos e propriedades da tabela administrador.usuario
    $perbotoes[0][0]="id"; $perbotoes[0][1]="Id."; $perbotoes[0][2]="Text"; $perbotoes[0][3]=" size='10'";
	$perbotoes[1][0]="inserir"; $perbotoes[1][1]="Botão Inserir"; $perbotoes[1][2]="Text"; $perbotoes[1][3]=" size='10'";
    $perbotoes[2][0]="atualizar"; $perbotoes[2][1]="Botão Atualizar"; $perbotoes[2][2]="Text"; $perbotoes[2][3]=" size='10'";
    $perbotoes[3][0]="deletar"; $perbotoes[3][1]="Botão Deletar"; $perbotoes[3][2]="Text"; $perbotoes[3][3]=" size='10'";
    $perbotoes[4][0]="idgrupo"; $perbotoes[4][1]="Grupo"; $perbotoes[4][2]="Combobox"; $perbotoes[4][3]="idgrupo, descricao from administrador.grupo order by descricao";
    	
    function  dadosTabela($tab, $matriz, $numCampos){
       for ($i=0; $i<$numCampos; $i++)
         for ($j=0; $j<=3; $j++)
            $campo[$i][$j]=$matriz[$i][$j];
       return $campo;
    }

    switch ($tabela) {
       Case "grupo": $campos=dadosTabela($tabela, $grupo, 2); break;
       Case "usuario": $campos=dadosTabela($tabela, $usuario, 9); break;
	   Case "unidade": $campos=dadosTabela($tabela, $unidade, 2); break;
	   Case "perbotoes": $campos=dadosTabela($tabela, $perbotoes, 5); break;
    }

?>

