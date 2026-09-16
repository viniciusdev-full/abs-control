<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("conexao.php");

$nome = $_POST['nome'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM admin WHERE NOME='$nome' AND SENHA='$senha'";

$resultado = mysqli_query($conexao, $sql);

if (mysqli_num_rows($resultado) > 0) {

    header("Location:painel_admin.php");
    exit;

} else {

    echo "Usuário ou senha incorretos.";

}

?>