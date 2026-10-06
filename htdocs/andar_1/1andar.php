<?php

require_once("../conexao.php");
require_once("../buscar_estoque.php");
/* =====================================================
   CONFIGURAÇÃO DESTE ANDAR
   Térreo = andar 0 (mesmo padrão usado no index.php
   e no buscar_estoque.php: $estoque_por_andar[0])
   ===================================================== */
$ANDAR_ATUAL = 1;

$mensagem = null;
$erro = null;

/* =====================================================
   Exibe mensagem vinda de um redirecionamento anterior
   (padrão Post/Redirect/Get, evita reenvio do form)
   ===================================================== */
if (isset($_GET['status']) && isset($_GET['msg'])) {
    if ($_GET['status'] === 'ok') {
        $mensagem = $_GET['msg'];
    } else {
        $erro = $_GET['msg'];
    }
}

/* =====================================================
   PROCESSAMENTO DO USO DE ABSORVENTE
   ===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantidade = isset($_POST['quantidade']) ? trim($_POST['quantidade']) : '';

    // Validação: precisa ser um número inteiro entre 1 e 15 (mesmo limite do input)
    if ($quantidade === '' || !ctype_digit($quantidade) || (int) $quantidade < 1 || (int) $quantidade > 15) {

        header("Location: 1andar.php?" . http_build_query([
            'status' => 'erro',
            'msg'    => 'Informe uma quantidade válida (entre 1 e 15).'
        ]));
        exit;
    }

    $quantidade = (int) $quantidade;

    // Verifica quanto tem em estoque para este andar antes de descontar
    $sqlVerifica = "SELECT qtd FROM estoque WHERE andar = ?";
    $stmt = mysqli_prepare($conexao, $sqlVerifica);
    mysqli_stmt_bind_param($stmt, "i", $ANDAR_ATUAL);
    mysqli_stmt_execute($stmt);
    $resultadoVerifica = mysqli_stmt_get_result($stmt);
    $linhaEstoque = mysqli_fetch_assoc($resultadoVerifica);
    mysqli_stmt_close($stmt);

    if (!$linhaEstoque) {

        header("Location: 1andar.php?" . http_build_query([
            'status' => 'erro',
            'msg'    => 'Este andar ainda não possui registro de estoque.'
        ]));
        exit;

    } elseif ((int) $linhaEstoque['qtd'] < $quantidade) {

        header("Location: 1andar.php?" . http_build_query([
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

        header("Location: 1andar.php?" . http_build_query([
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

    <h2> 1° andar </h2>

    <?php if ($mensagem) { ?>
        <div class="mensagem mensagem-sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php } ?>

    <?php if ($erro) { ?>
        <div class="mensagem mensagem-erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php } ?>

    <br><br>

    <!-- O formulário envia os dados para este mesmo arquivo processar -->
    <form method="POST" action="">
        <!-- Identifica que esta página é o 1andar (andar 1) -->
        <input type="hidden" name="andar" value="1">
        <?php echo isset($estoque_por_andar[1]) ? $estoque_por_andar[1] . " unidades" : "Sem dados"; ?>
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