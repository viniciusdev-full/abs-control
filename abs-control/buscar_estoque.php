<?php

require_once("conexao.php");

/* =====================================================
   BUSCA ESTOQUE POR ANDAR
   Monta um array associativo no formato:
   $estoque_por_andar[andar] = quantidade
   Ex.: $estoque_por_andar[3] = 45;
   ===================================================== */

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