<?php
$andar = isset($_GET['andar']) ? intval($_GET['andar']) : 0;
$restante = isset($_GET['restante']) ? intval($_GET['restante']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'normal';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Retirada Concluída</title>
</head>
<body style="font-family: Arial; margin: 50px; text-align: center;">
    <h2 style="color: green;">✔ Retirada realizada com sucesso!</h2>
    <p>Andar: <strong><?php echo $andar; ?>º Andar</strong> | Restam no dispensador: <strong><?php echo $restante; ?></strong> absorventes.</p>

    <?php if ($status == 'acabando'): ?>
        <h3 style="color: orange;">⚠️ ATENÇÃO: O estoque deste andar está acabando!</h3>
    <?php elseif ($status == 'zerado'): ?>
        <h3 style="color: red;">🚨 CRÍTICO: O estoque deste andar ZEROU!</h3>
    <?php endif; ?>

    <br><br>
    <a href="index.html" style="padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px;">Voltar ao Painel</a>
</body>
</html>
