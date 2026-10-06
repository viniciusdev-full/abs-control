    <?php

    require_once("../conexao.php");

    $erroRecarga = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recarregar_id'])) {

        $id = (int) $_POST['recarregar_id'];
        $quantidade = isset($_POST['quantidade_recarga']) ? trim($_POST['quantidade_recarga']) : '';

        if ($quantidade === '' || !ctype_digit($quantidade) || (int) $quantidade <= 0) {
            $erroRecarga = "Informe uma quantidade válida (número inteiro maior que zero) para recarregar.";
        } else {
            $quantidade = (int) $quantidade;

            $sqlUpdate = "UPDATE estoque SET qtd = qtd + ? WHERE id = ?";
            $stmt = mysqli_prepare($conexao, $sqlUpdate);
            mysqli_stmt_bind_param($stmt, "ii", $quantidade, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descarregar_id'])) {

        $id = (int) $_POST['descarregar_id'];
        $quantidade = isset($_POST['quantidade_descarga']) ? trim($_POST['quantidade_descarga']) : '';

        if ($quantidade === '' || !ctype_digit($quantidade) || (int) $quantidade <= 0) {
            $erroRecarga = "Informe uma quantidade válida (número inteiro maior que zero) para retirar.";
        } else {
            $quantidade = (int) $quantidade;

            $sqlUpdate = "UPDATE estoque SET qtd = qtd - ? WHERE id = ?";
            $stmt = mysqli_prepare($conexao, $sqlUpdate);
            mysqli_stmt_bind_param($stmt, "ii", $quantidade, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Estoque
    $sql = "SELECT * FROM estoque ORDER BY id";
    $resultado = mysqli_query($conexao, $sql);

    $listaEstoque = [];
    $totalGeralAbsorventes = 0;

    while ($linha = mysqli_fetch_assoc($resultado)) {
        $listaEstoque[] = $linha;
        $totalGeralAbsorventes += (int) $linha['qtd'];
    }

    $totalRegistros = count($listaEstoque);

    // Histórico de uso
    $sqlHistorico = "
        SELECT
            historico_uso.andar AS andar,
            historico_uso.estoque AS estoque_usado,
            historico_uso.data AS data_uso,
            estoque.qtd AS qtd_atual
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
        <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <link rel="stylesheet" href="../css/painel.css">
        
    </head>
    <body>

    <div class="wrapper">

        <!-- Cabeçalho -->
        <div class="topo">

            <div class="topo-titulo">
                <div class="icone-topo">
                    <i class="fa-solid fa-user-gear"></i>
                </div>

                <div>
                    <span class="eyebrow">Área administrativa</span>
                    <h2>Painel Administrativo</h2>
                </div>
            </div>

            <div class="topo-acoes">
                <a href="cadastro_admin.html" class="botao botao-secundario">
                    <i class="fa-solid fa-user-plus"></i>
                    Cadastrar Administrador
                </a>

                <a href="../index.html" class="botao botao-primario">
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

        <!-- Resumo -->
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

        <!-- Estoque -->
        <div class="card">

            <div class="card-header">
                <h3>Estoque por andar</h3>

                <span class="contador">
                    <?php echo $totalRegistros; ?> registros
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
                            <td>
                                <?php echo htmlspecialchars($estoque['id']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($estoque['andar']); ?>
                            </td>

                            <td>
                                <?php if (!empty($estoque['qtd'])) { ?>
                                    <?php echo htmlspecialchars($estoque['qtd']); ?>
                                <?php } else { ?>
                                    <span class="status-vazio">Não enviado</span>
                                <?php } ?>
                            </td>

                            <td>
                                <form method="POST" class="form-recarga"
                                    onsubmit="return confirmarRecarga(this, '<?php echo htmlspecialchars($estoque['andar']); ?>');">

                                    <input type="hidden" name="recarregar_id"
                                        value="<?php echo htmlspecialchars($estoque['id']); ?>">

                                    <input type="number"
                                        name="quantidade_recarga"
                                        class="input-recarga"
                                        placeholder="Qtd"
                                        min="1"
                                        step="1"
                                        required>

                                    <button type="submit" class="botao-recarga">
                                        <i class="fa-solid fa-plus"></i>
                                        Adicionar
                                    </button>
                                </form>
                            </td>

                            <td>
                                <form method="POST" class="form-descarga"
                                    onsubmit="return confirmarDescarga(this, '<?php echo htmlspecialchars($estoque['andar']); ?>');">

                                    <input type="hidden" name="descarregar_id"
                                        value="<?php echo htmlspecialchars($estoque['id']); ?>">

                                    <input type="number"
                                        name="quantidade_descarga"
                                        class="input-descarga"
                                        placeholder="Qtd"
                                        min="1"
                                        step="1"
                                        required>

                                    <button type="submit" class="botao-descarga">
                                        <i class="fa-solid fa-minus"></i>
                                        Remover
                                    </button>
                                </form>
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>
        </div>

        <!-- Histórico -->
        <div class="card">

            <div class="card-header">
                <h3>Histórico de uso</h3>

                <span class="contador">
                    <?php echo mysqli_num_rows($resultadoHistorico); ?> registros
                </span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ANDAR</th>
                        <th>ABSORVENTES USADOS</th>
                        <th>DATA</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($uso = mysqli_fetch_assoc($resultadoHistorico)) { ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($uso['andar']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($uso['estoque_usado']); ?>
                            </td>

                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($uso['data_uso'])); ?>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>

        <!-- Ações finais -->
        <div class="rodape-acoes">

            <a href="cadastro_admin.html">
                <i class="fa-solid fa-user-plus"></i>
                Cadastrar Administrador
            </a>

            <a href="../index.html" class="sair">
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

            return confirm('Adicionar mais ' + quantidade + ' unidade(s) em ' + andar);
        }

        function confirmarDescarga(form, andar) {
            const input = form.querySelector('.input-descarga');
            const quantidade = input.value;

            if (!quantidade || parseInt(quantidade, 10) <= 0) {
                alert('Informe uma quantidade válida para retirar.');
                return false;
            }

            return confirm('Retirar mais ' + quantidade + ' unidade(s) em ' + andar);
        }

    </script>

    </body>
    </html>