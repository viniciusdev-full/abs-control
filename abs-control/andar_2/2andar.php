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
    <link rel="stylesheet" href="./Css/pages.css">
</head>
<body>
   <style>
    /* abscontrol — tema rosa (página de setor / formulário) */

@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Manrope:wght@400;500;600;700&display=swap');

:root {
  --bg: #fbf1f0;
  --surface: #ffffff;
  --ink: #2b1b22;
  --muted: #8a7178;
  --accent: #8c2f4b;
  --accent-dark: #6e2039;
  --accent-soft: #f1c6d0;
  --line: #e8d3d6;

  --font-display: 'Fraunces', Georgia, serif;
  --font-body: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  min-height: 100vh;
  background: var(--bg);
  color: var(--ink);
  font-family: var(--font-body);
  font-size: 16px;
  line-height: 1.5;
  padding: 48px 20px 64px;
}

/* Título do setor */
h2 {
  max-width: 480px;
  margin: 0 auto;
  font-family: var(--font-display);
  font-weight: 500;
  font-size: clamp(1.5rem, 4vw, 2rem);
  letter-spacing: 0.01em;
  color: var(--accent-dark);
  text-align: center;
}

/* O <br><br> do autor cria o respiro entre título e formulário;
   reduzimos a altura de linha desses <br> soltos para não abrir
   um vão exagerado. */
h2 + br,
h2 + br + br {
  line-height: 0.6;
  display: block;
  content: '';
}

/* Formulário como um cartão sobre o fundo rosa */
form {
  max-width: 420px;
  margin: 24px auto 0;
  background: var(--surface);
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 28px 26px 32px;
  box-shadow: 0 18px 40px -24px rgba(140, 47, 75, 0.35);
}

form br {
  content: '';
  display: block;
  margin-top: 4px;
}

label {
  display: inline-block;
  font-family: var(--font-body);
  font-weight: 600;
  font-size: 0.85rem;
  color: var(--muted);
  margin-bottom: 6px;
}

input[type="number"],
textarea {
  width: 100%;
  font-family: var(--font-body);
  font-size: 1rem;
  color: var(--ink);
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 10px 12px;
  outline: none;
  transition: border-color 0.15s ease, background-color 0.15s ease;
}

input[type="number"] {
  max-width: 140px;
}

input[type="number"]:focus,
textarea:focus {
  border-color: var(--accent);
  background: var(--surface);
}

textarea {
  resize: vertical;
  min-height: 90px;
  font-family: var(--font-body);
}

textarea::placeholder {
  color: #b79aa1;
}

/* O botão tem cor verde fixa via style="" no HTML (inline styles
   sempre vencem o CSS), então usamos !important para trazê-lo
   para o tema rosa sem tocar no HTML. */
button[type="submit"] {
  background-color: var(--accent) !important;
  color: #ffffff !important;
  border: none;
  border-radius: 10px;
  font-family: var(--font-body);
  font-weight: 600;
  font-size: 0.95rem;
  letter-spacing: 0.01em;
  cursor: pointer;
  transition: background-color 0.15s ease, transform 0.15s ease;
}

button[type="submit"]:hover {
  background-color: var(--accent-dark) !important;
}

button[type="submit"]:active {
  transform: translateY(1px);
}

/* Link de volta ao painel principal */
a {
  display: block;
  max-width: 420px;
  margin: 28px auto 0;
  text-align: center;
  color: var(--muted);
  font-family: var(--font-body);
  font-weight: 500;
  font-size: 0.9rem;
  text-decoration: none;
}

a:hover {
  color: var(--accent);
}

/* Mensagens de sucesso / erro */
.mensagem {
  max-width: 420px;
  margin: 24px auto 0;
  padding: 14px 18px;
  border-radius: 10px;
  font-family: var(--font-body);
  font-weight: 600;
  font-size: 0.9rem;
  text-align: center;
}

.mensagem-sucesso {
  background: #e6f4ea;
  border: 1px solid #b7ddc2;
  color: #2f6b40;
}

.mensagem-erro {
  background: #fdeceb;
  border: 1px solid #f3b9b3;
  color: #9c3b34;
}

@media (max-width: 480px) {
  form {
    padding: 22px 18px 26px;
  }
}

    </style>

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