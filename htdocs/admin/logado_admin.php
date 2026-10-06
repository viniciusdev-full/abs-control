<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("./conexao.php");

$nome = trim($_POST['nome'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($nome) || empty($senha)) {
    $_SESSION['msg'] = "<p>Nome e senha são obrigatórios!</p>";
    header("Location: login.html");
    exit;
}

// Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Cadastra o administrador
$sql_usuario = "INSERT INTO admin (nome, senha)
                VALUES ('$nome', '$senha_hash')";

$result_usuario = mysqli_query($conexao, $sql_usuario);

if ($result_usuario) {

    $id_usuario = mysqli_insert_id($conexao);

    $_SESSION['msg'] = "<p>Usuário cadastrado com sucesso!</p>";

} else {

    $_SESSION['msg'] = "<p>Falha ao cadastrar: ".mysqli_error($conexao)."</p>";
}

header("Location: login.html");
exit;

?>
