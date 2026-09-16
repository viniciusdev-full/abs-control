<?php

require_once("./conexao.php");

$nome = $_POST['nome'];
$senha = $_POST['senha'];

$sql = "INSERT INTO admin (nome,senha)
        VALUES ('$nome','$senha')";

if (mysqli_query($conexao,$sql)) {

    echo "Administrador cadastrado com sucesso!";

} else {

    echo "Erro ao cadastrar: " . mysqli_error($conexao);

}

?>
