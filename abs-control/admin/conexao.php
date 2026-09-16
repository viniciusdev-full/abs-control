<?php
//criação das variaveis
$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname = "test";

//criar uma conexão
$conexao=mysqli_connect($servidor,$usuario,$senha,$dbname);

if ($conexao->connect_error) {
    die("Erro na conexão.");
}



?>