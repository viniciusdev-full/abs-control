<?php

require_once("../conexao.php");
require_once("../buscar_estoque.php");

$ANDAR_ATUAL = 2;

$mensagem = null;
$erro = null;


if (isset($_GET['status']) && isset($_GET['msg'])) {
    if ($_GET['status'] === 'ok') {
        $mensagem = $_GET['msg'];
    } else {
        $erro = $_GET['msg'];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantidade = isset($_POST['quantidade']) ? trim($_POST['quantidade']) : '';

    
    if ($quantidade === '' || !ctype_digit($quantidade) || (int) $quantidade < 1 || (int) $quantidade > 15) {

        header("Location: 2andar.php?" . http_build_query([
            'status' => 'erro',
            'msg'    => 'Informe uma quantidade válida (entre 1 e 15).'
        ]));
        exit;
    }

    $quantidade = (int) $quantidade;

    
    $sqlVerifica = "SELECT qtd FROM estoque WHERE andar = ?";
    $stmt = mysqli_prepare($conexao, $sqlVerifica);
    mysqli_stmt_bind_param($stmt, "i", $ANDAR_ATUAL);
    mysqli_stmt_execute($stmt);
    $resultadoVerifica = mysqli_stmt_get_result($stmt);
    $linhaEstoque = mysqli_fetch_assoc($resultadoVerifica);
    mysqli_stmt_close($stmt);

    if (!$linhaEstoque) {

        header("Location: 2andar.php?" . http_build_query([
            'status' => 'erro',
            'msg'    => 'Este andar ainda não possui registro de estoque.'
        ]));
        exit;

    } elseif ((int) $linhaEstoque['qtd'] < $quantidade) {

        header("Location: 2andar.php?" . http_build_query([
            'status' => 'erro',
            'msg'    => 'Estoque insuficiente. Restam apenas ' . (int) $linhaEstoque['qtd'] . ' unidades.'
        ]));
        exit;

    } else {

        // Desconta a quantidade usada do estoque deste andar
        $sqlUpdate = "UPDATE estoque SET qtd = qtd - ? WHERE andar = ?";
        $stmt = mysqli_prepare($conexao, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, "ii", $quantidade, $ANDAR_ATUAL);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Registra o uso no histórico
        $sqlHistorico = "INSERT INTO historico_uso (andar, estoque, data) VALUES (?, ?, NOW())";
        $stmt = mysqli_prepare($conexao, $sqlHistorico);
        mysqli_stmt_bind_param($stmt, "ii", $ANDAR_ATUAL, $quantidade);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: 2andar.php?" . http_build_query([
            'status' => 'ok',
            'msg'    => 'Uso registrado com sucesso! Obrigado.'
        ]));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1° andar</title>
    <link rel="icon" type="image/x-icon" href="../img/entre_ciclos.jfif">
    <link rel="stylesheet" href="../css/pages.css">
</head>
<body>

    <h2> 2° andar </h2>

    <?php if ($mensagem) { ?>
        <div class="mensagem mensagem-sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php } ?>

    <?php if ($erro) { ?>
        <div class="mensagem mensagem-erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php } ?>

    <br><br>

    <!-- O formulário envia os dados para este mesmo arquivo processar -->
    <form method="POST" action="">
        <!-- Identifica que esta página é o térreo (andar 2) -->
        <input type="hidden" name="andar" value="2">
        <?php echo isset($estoque_por_andar[2]) ? $estoque_por_andar[2] . " unidades" : "Sem dados"; ?>
        <br><br>
        <label for="quantidade">Quantidade a usar:</label><br>
        <input type="number" name="quantidade" id="quantidade" min="1" max="15" required>

        <br><br>

        <button type="submit"
         style="background-color: green; color: white; padding: 10px 20px;">Usar Produto</button>
    </form>

<a href="../index.html">⬅ Voltar ao Painel Principal</a>


</body>
</html>