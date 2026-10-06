<?php

require_once("conexao.php");

$estoque_por_andar = [];

$sqlEstoque = "SELECT andar, qtd FROM estoque";
$resultadoEstoque = mysqli_query($conexao, $sqlEstoque);

if ($resultadoEstoque) {
    while ($linha = mysqli_fetch_assoc($resultadoEstoque)) {
        $andar = (int) $linha['andar'];
        $estoque_por_andar[$andar] = (int) $linha['qtd'];
    }
}

?>
