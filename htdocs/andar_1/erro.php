<?php $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "Erro desconhecido."; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Erro</title>
</head>
<body style="font-family: Arial; margin: 50px; text-align: center;">
    <h2 style="color: red;">❌ Não foi possível realizar a retirada</h2>
    <p><?php echo $msg; ?></p>
    <br><br>
    <a href="javascript:history.back()" style="padding: 10px 20px; background: #ff4081; color: white; text-decoration: none; border-radius: 4px;">Voltar e Corrigir</a>
</body>
</html>
