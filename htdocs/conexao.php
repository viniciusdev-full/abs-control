<?php
$servidor = "sql304.infinityfree.com";
$usuario = "if0_42268767";
$senha = "yEGK1sNxMwyqo";
$dbname = "if0_42268767_test";

$conexao=mysqli_connect($servidor,$usuario,$senha,$dbname);

date_default_timezone_set('America/Sao_Paulo');

if ($conexao->connect_error) {
    die("Erro na conexão.");
}

?>