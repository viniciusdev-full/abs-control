<?php

require_once("conexao.php");

/* =====================================================
   AÇÃO: RECARREGAR ESTOQUE DE UM ANDAR
   Agora a admin escolhe a quantidade exata a ser
   adicionada, em vez de um valor fixo.
   ===================================================== */

$erroRecarga = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recarregar_id'])) {

    $id = (int) $_POST['recarregar_id'];
    $quantidade = isset($_POST['quantidade_recarga']) ? trim($_POST['quantidade_recarga']) : '';

    // Validação: precisa ser um número inteiro positivo
    if ($quantidade === '' || !ctype_digit($quantidade) || (int) $quantidade <= 0) {

        $erroRecarga = "Informe uma quantidade válida (número inteiro maior que zero) para recarregar.";

    } else {

        $quantidade = (int) $quantidade;

        $sqlUpdate = "UPDATE estoque SET qtd = qtd + ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, "ii", $quantidade, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Evita reenvio do formulário ao atualizar a página (padrão Post/Redirect/Get)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

/* =====================================================
   ESTOQUE — busca em array para reaproveitar nos
   cards de resumo e na tabela principal
   ===================================================== */

$sql = "SELECT * FROM estoque ORDER BY andar";

$resultado = mysqli_query($conexao, $sql);

$listaEstoque = [];
$totalGeralAbsorventes = 0;

while ($linha = mysqli_fetch_assoc($resultado)) {
    $listaEstoque[] = $linha;
    $totalGeralAbsorventes += (int) $linha['qtd'];
}

$totalRegistros = count($listaEstoque);


/* =====================================================
   HISTÓRICO DE USO — relacionado com estoque pelo
   campo "andar", trazendo também o estoque atual
   daquele andar
   ===================================================== */

$sqlHistorico = "
    SELECT
        historico_uso.andar   AS andar,
        historico_uso.estoque AS estoque_usado,
        historico_uso.data    AS data_uso,
        estoque.qtd           AS qtd_atual
    FROM historico_uso
    LEFT JOIN estoque ON estoque.andar = historico_uso.andar
    ORDER BY historico_uso.data DESC
";

$resultadoHistorico = mysqli_query($conexao, $sqlHistorico);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Administrativo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>
<style>
 
        /* =====================================================
           TOKENS — TEMA FEMININO (rosa / lilás / dourado suave)
        ===================================================== */
 
        :root {
            --bg-0: #fffaFC;
            --ink: #3a2740;
            --ink-soft: #9b7fa3;
            --line: #f0d9ea;
            --accent: #d6588f;
            --accent-soft: #fbe4ef;
            --accent-2: #b98add;
            --card-bg: #ffffff;
            --ok: #c98a3e;
            --ok-soft: #fdf1e2;
        }
 
 
        /* =====================================================
           RESET
        ===================================================== */
 
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
 
        /* =====================================================
           BODY
        ===================================================== */
 
        body {
            font-family: 'Inter Tight', Arial, Helvetica, sans-serif;
 
            background:
                radial-gradient(
                    circle at 50% 0%,
                    #f6d9ef 0%,
                    #fbeaf6 30%,
                    var(--bg-0) 60%
                );
 
            background-attachment: fixed;
 
            color: var(--ink);
 
            min-height: 100vh;
 
            padding: 48px 30px 70px;
 
            -webkit-font-smoothing: antialiased;
        }
 
 
        /* =====================================================
           WRAPPER
        ===================================================== */
 
        .wrapper {
 
            max-width: 1180px;
 
            margin: 0 auto;
        }
 
 
        /* =====================================================
           CABEÇALHO
        ===================================================== */
 
        .topo {
 
            display: flex;
 
            align-items: center;
 
            justify-content: space-between;
 
            flex-wrap: wrap;
 
            gap: 20px;
 
            margin-bottom: 34px;
        }
 
 
        .topo-titulo {
 
            display: flex;
 
            align-items: center;
 
            gap: 16px;
        }
 
 
        .icone-topo {
 
            width: 52px;
 
            height: 52px;
 
            border-radius: 16px;
 
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
 
            color: #ffffff;
 
            display: flex;
 
            align-items: center;
 
            justify-content: center;
 
            flex-shrink: 0;
 
            box-shadow: 0 8px 20px rgba(214, 88, 143, 0.35);
        }
 
 
        .icone-topo i {
 
            font-size: 22px;
        }
 
 
        .eyebrow {
 
            display: inline-flex;
 
            align-items: center;
 
            gap: 8px;
 
            font-size: 12px;
 
            font-weight: 700;
 
            letter-spacing: 2px;
 
            text-transform: uppercase;
 
            color: var(--accent);
 
            margin-bottom: 4px;
        }
 
 
        .eyebrow::before {
 
            content: "";
 
            width: 6px;
 
            height: 6px;
 
            border-radius: 50%;
 
            background: var(--accent);
        }
 
 
        h2 {
 
            font-size: 28px;
 
            font-weight: 800;
 
            letter-spacing: -0.8px;
 
            color: var(--ink);
        }
 
 
        .topo-acoes {
 
            display: flex;
 
            gap: 12px;
 
            flex-wrap: wrap;
        }
 
 
        .botao {
 
            display: inline-flex;
 
            align-items: center;
 
            gap: 10px;
 
            height: 46px;
 
            padding: 0 20px;
 
            border-radius: 12px;
 
            font-family: 'Inter Tight', Arial, sans-serif;
 
            font-size: 14px;
 
            font-weight: 600;
 
            text-decoration: none;
 
            border: 1px solid var(--accent);
 
            cursor: pointer;
 
            transition:
                background 0.25s ease,
                color 0.25s ease,
                border-color 0.25s ease,
                transform 0.25s ease,
                box-shadow 0.25s ease;
        }
 
 
        .botao i {
 
            font-size: 13px;
        }
 
 
        .botao-primario {
 
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
 
            color: #ffffff;
 
            border-color: transparent;
        }
 
 
        .botao-primario:hover {
 
            transform: translateY(-2px);
 
            box-shadow: 0 10px 22px rgba(214, 88, 143, 0.4);
        }
 
 
        .botao-secundario {
 
            background: #ffffff;
 
            color: var(--accent);
 
            border-color: var(--line);
        }
 
 
        .botao-secundario:hover {
 
            border-color: var(--accent);
 
            background: var(--accent-soft);
 
            transform: translateY(-2px);
        }
 
 
        /* formulário de recarga: input + botão lado a lado */
 
        .form-recarga {
 
            display: flex;
 
            align-items: center;
 
            gap: 8px;
 
            margin: 0;
        }
 
 
        .input-recarga {
 
            width: 72px;
 
            height: 38px;
 
            padding: 0 10px;
 
            border-radius: 10px;
 
            border: 1px solid var(--line);
 
            font-family: 'Inter Tight', Arial, sans-serif;
 
            font-size: 13px;
 
            font-weight: 600;
 
            color: var(--ink);
 
            background: #ffffff;
 
            transition: border-color 0.2s ease;
        }
 
 
        .input-recarga:focus {
 
            outline: none;
 
            border-color: var(--accent);
        }
 
 
        /* remove as setas do input number no Chrome/Safari */
 
        .input-recarga::-webkit-outer-spin-button,
        .input-recarga::-webkit-inner-spin-button {
 
            -webkit-appearance: none;
 
            margin: 0;
        }
 
 
        /* botão pequeno de recarga, usado dentro da tabela */
 
        .botao-recarga {
 
            display: inline-flex;
 
            align-items: center;
 
            gap: 8px;
 
            height: 38px;
 
            padding: 0 16px;
 
            border-radius: 10px;
 
            font-family: 'Inter Tight', Arial, sans-serif;
 
            font-size: 13px;
 
            font-weight: 600;
 
            border: none;
 
            cursor: pointer;
 
            color: #ffffff;
 
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
 
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }
 
 
        .botao-recarga:hover {
 
            transform: translateY(-2px);
 
            box-shadow: 0 8px 18px rgba(214, 88, 143, 0.4);
        }
 
 
        .botao-recarga i {
 
            font-size: 12px;
        }
 
 
        /* =====================================================
           ALERTA DE ERRO (validação da recarga)
        ===================================================== */
 
        .alerta-erro {
 
            display: flex;
 
            align-items: center;
 
            gap: 10px;
 
            background: #fdeceb;
 
            border: 1px solid #f3b9b3;
 
            color: #9c3b34;
 
            font-size: 14px;
 
            font-weight: 600;
 
            padding: 14px 18px;
 
            border-radius: 12px;
 
            margin-bottom: 24px;
        }
 
 
        /* =====================================================
           CARDS DE RESUMO (quantidade por andar / total geral)
        ===================================================== */
 
        .resumo-grid {
 
            display: grid;
 
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
 
            gap: 16px;
 
            margin-bottom: 30px;
        }
 
 
        .mini-card {
 
            background: var(--card-bg);
 
            border: 1px solid var(--line);
 
            border-radius: 16px;
 
            padding: 18px 20px;
 
            box-shadow: 0 10px 24px rgba(214, 88, 143, 0.06);
 
            display: flex;
 
            flex-direction: column;
 
            gap: 6px;
        }
 
 
        .mini-card-label {
 
            font-size: 12px;
 
            font-weight: 700;
 
            text-transform: uppercase;
 
            letter-spacing: 0.5px;
 
            color: var(--ink-soft);
 
            display: flex;
 
            align-items: center;
 
            gap: 6px;
        }
 
 
        .mini-card-valor {
 
            font-size: 26px;
 
            font-weight: 800;
 
            color: var(--ink);
 
            letter-spacing: -0.5px;
        }
 
 
        .mini-card-total {
 
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
 
            border-color: transparent;
        }
 
 
        .mini-card-total .mini-card-label,
        .mini-card-total .mini-card-valor {
 
            color: #ffffff;
        }
 
 
        /* =====================================================
           CARD
        ===================================================== */
 
        .card {
 
            background: var(--card-bg);
 
            border: 1px solid var(--line);
 
            border-radius: 20px;
 
            padding: 32px;
 
            box-shadow:
                0 14px 34px rgba(214, 88, 143, 0.08);
 
            overflow-x: auto;
        }
 
 
        .card + .card {
 
            margin-top: 26px;
        }
 
 
        .card-header {
 
            display: flex;
 
            align-items: center;
 
            justify-content: space-between;
 
            flex-wrap: wrap;
 
            gap: 12px;
 
            margin-bottom: 22px;
        }
 
 
        h3 {
 
            font-size: 19px;
 
            font-weight: 700;
 
            letter-spacing: -0.3px;
 
            color: var(--ink);
        }
 
 
        .contador {
 
            font-size: 13px;
 
            font-weight: 600;
 
            color: var(--accent);
 
            background: var(--accent-soft);
 
            padding: 5px 12px;
 
            border-radius: 20px;
        }
 
 
        /* =====================================================
           TABELA
        ===================================================== */
 
        table {
 
            width: 100%;
 
            border-collapse: collapse;
 
            min-width: 900px;
        }
 
 
        thead th {
 
            text-align: left;
 
            font-size: 12px;
 
            font-weight: 700;
 
            text-transform: uppercase;
 
            letter-spacing: 0.4px;
 
            color: var(--ink-soft);
 
            padding: 12px 14px;
 
            border-bottom: 2px solid var(--line);
 
            white-space: nowrap;
        }
 
 
        tbody td {
 
            font-size: 14px;
 
            color: var(--ink);
 
            padding: 16px 14px;
 
            border-bottom: 1px solid var(--line);
 
            vertical-align: middle;
        }
 
 
        tbody tr:hover {
 
            background: var(--accent-soft);
        }
 
 
        tbody tr:last-child td {
 
            border-bottom: none;
        }
 
 
        /* link de visualizar arquivo */
 
        td a {
 
            display: inline-flex;
 
            align-items: center;
 
            gap: 6px;
 
            font-size: 13px;
 
            font-weight: 600;
 
            color: var(--accent);
 
            text-decoration: none;
 
            padding: 6px 12px;
 
            border: 1px solid var(--line);
 
            border-radius: 10px;
 
            transition:
                border-color 0.2s ease,
                background 0.2s ease;
 
            white-space: nowrap;
        }
 
 
        td a:hover {
 
            border-color: var(--accent);
 
            background: var(--accent-soft);
        }
 
 
        /* status "não enviado" */
 
        .status-vazio {
 
            font-size: 13px;
 
            font-weight: 500;
 
            color: var(--ink-soft);
 
            font-style: italic;
        }
 
 
        /* =====================================================
           RODAPÉ DE AÇÕES
        ===================================================== */
 
        .rodape-acoes {
 
            display: flex;
 
            align-items: center;
 
            justify-content: space-between;
 
            flex-wrap: wrap;
 
            gap: 14px;
 
            margin-top: 30px;
        }
 
 
        .rodape-acoes a {
 
            color: var(--ink-soft);
 
            font-size: 14px;
 
            font-weight: 600;
 
            text-decoration: none;
        }
 
 
        .rodape-acoes a:hover {
 
            color: var(--accent);
        }
 
 
        .sair {
 
            color: var(--accent) !important;
        }
 
 
        /* =====================================================
           RESPONSIVO
        ===================================================== */
 
        @media (max-width: 700px) {
 
            body {
 
                padding: 36px 16px 50px;
            }
 
 
            .topo {
 
                flex-direction: column;
 
                align-items: flex-start;
            }
 
 
            .topo-acoes {
 
                width: 100%;
            }
 
 
            .botao {
 
                flex: 1;
 
                justify-content: center;
            }
 
 
            .card {
 
                padding: 20px;
 
                border-radius: 14px;
            }
 
 
            h2 {
 
                font-size: 24px;
            }
        }
 
    </style>
<body>

<div class="wrapper">


    <!-- CABEÇALHO -->

    <div class="topo">

        <div class="topo-titulo">

            <div class="icone-topo">
                <i class="fa-solid fa-user-gear"></i>
            </div>

            <div>

                <span class="eyebrow">
                    Área administrativa
                </span>

                <h2>Painel Administrativo</h2>

            </div>

        </div>


        <div class="topo-acoes">

            <a href="cadastro_admin.html"
               class="botao botao-secundario">

                <i class="fa-solid fa-user-plus"></i>

                Cadastrar Administrador

            </a>


            <a href="../index.php"
               class="botao botao-primario">

                <i class="fa-solid fa-arrow-right-from-bracket"></i>

                Sair

            </a>

        </div>

    </div>


    <?php if ($erroRecarga) { ?>

        <div class="alerta-erro">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php echo htmlspecialchars($erroRecarga); ?>
        </div>

    <?php } ?>


    <!-- CARDS DE RESUMO: QUANTIDADE POR ANDAR + TOTAL GERAL -->

    <div class="resumo-grid">

        <?php foreach ($listaEstoque as $itemEstoque) { ?>

            <div class="mini-card">

                <span class="mini-card-label">
                    <i class="fa-solid fa-building"></i>
                    <?php echo htmlspecialchars($itemEstoque['andar']); ?>
                </span>

                <span class="mini-card-valor">
                    <?php echo (int) $itemEstoque['qtd']; ?>
                </span>

            </div>

        <?php } ?>

        <div class="mini-card mini-card-total">

            <span class="mini-card-label">
                <i class="fa-solid fa-layer-group"></i>
                Total geral
            </span>

            <span class="mini-card-valor">
                <?php echo $totalGeralAbsorventes; ?>
            </span>

        </div>

    </div>


    <!-- TABELA DE ESTOQUE -->

    <div class="card">

        <div class="card-header">

            <h3>Estoque por andar</h3>

            <span class="contador">

                <?php echo $totalRegistros; ?>

                registros

            </span>

        </div>


        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>ANDAR</th>

                    <th>QTD</th>

                    <th>AÇÃO</th>

                </tr>

            </thead>


            <tbody>


         <?php foreach ($listaEstoque as $estoque) { ?>

    <tr>
        <!-- ID -->
        <td>
            <?php echo htmlspecialchars($estoque['id']); ?>
        </td>

        <!-- ANDAR -->
        <td>
            <?php echo htmlspecialchars($estoque['andar']); ?>
        </td>

        <!-- QUANTIDADE -->
        <td>
            <?php if (!empty($estoque['qtd'])) { ?>
                <?php echo htmlspecialchars($estoque['qtd']); ?>
            <?php } else { ?>
                <span class="status-vazio">
                    Não enviado
                </span>
            <?php } ?>
        </td>

        <!-- BOTÃO DE RECARGA -->
        <td>
            <form method="POST" class="form-recarga" onsubmit="return confirmarRecarga(this, '<?php echo htmlspecialchars($estoque['andar']); ?>');">
                <input type="hidden" name="recarregar_id" value="<?php echo htmlspecialchars($estoque['id']); ?>">
                <input type="number"
                       name="quantidade_recarga"
                       class="input-recarga"
                       placeholder="Qtd"
                       min="1"
                       step="1"
                       required>
                <button type="submit" class="botao-recarga">
                    <i class="fa-solid fa-rotate"></i>
                    Recarregar
                </button>
            </form>
        </td>
    </tr>

<?php } ?>


            </tbody>

        </table>

    </div>


    <!-- HISTÓRICO DE USO -->

    <div class="card">

        <div class="card-header">

            <h3>Histórico de uso</h3>

            <span class="contador">

                <?php echo mysqli_num_rows($resultadoHistorico); ?>

                registros

            </span>

        </div>


        <table>

            <thead>

                <tr>

                    <th>ANDAR</th>

                    <th>ABSORVENTES USADOS</th>

                    <th>DATA</th>

                    <th>ESTOQUE ATUAL DO ANDAR</th>

                </tr>

            </thead>


            <tbody>

                <?php while ($uso = mysqli_fetch_assoc($resultadoHistorico)) { ?>

                    <tr>

                        <!-- ANDAR -->
                        <td>
                            <?php echo htmlspecialchars($uso['andar']); ?>
                        </td>

                        <!-- QUANTIDADE USADA NAQUELE REGISTRO -->
                        <td>
                            <?php echo htmlspecialchars($uso['estoque_usado']); ?>
                        </td>

                        <!-- DATA DO USO -->
                        <td>
                            <?php echo date('d/m/Y H:i', strtotime($uso['data_uso'])); ?>
                        </td>

                        <!-- ESTOQUE ATUAL DAQUELE ANDAR (via JOIN com a tabela estoque) -->
                        <td>
                            <?php if ($uso['qtd_atual'] !== null) { ?>
                                <?php echo (int) $uso['qtd_atual']; ?>
                            <?php } else { ?>
                                <span class="status-vazio">
                                    Andar não cadastrado no estoque
                                </span>
                            <?php } ?>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>


    <!-- AÇÕES FINAIS -->

    <div class="rodape-acoes">

        <a href="cadastro_admin.html">

            <i class="fa-solid fa-user-plus"></i>

            Cadastrar Administrador

        </a>


        <a href="../index.php" class="sair">

            Sair

            <i class="fa-solid fa-arrow-right-from-bracket"></i>

        </a>

    </div>


</div>

<script>

    function confirmarRecarga(form, andar) {

        const input = form.querySelector('.input-recarga');
        const quantidade = input.value;

        if (!quantidade || parseInt(quantidade, 10) <= 0) {
            alert('Informe uma quantidade válida para recarregar.');
            return false;
        }

        return confirm('Recarregar o andar ' + andar + ' com ' + quantidade + ' unidades?');
    }

</script>

</body>

</html>