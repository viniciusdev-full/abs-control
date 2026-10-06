<?php
require_once("../conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $andar = intval($_POST['andar']);
    $qtd = intval($_POST['qtd']);
   

    if ($quantidade_usar > 0 && !empty($descricao)) {
        
        $sql_busca = "SELECT qtd FROM estoque WHERE andar = $andar";
        $resultado = $conexao->query($sql_busca);
        $linha = $resultado->fetch_assoc();
        $estoque_atual = $linha['qtd'];

        if ($estoque_atual >= $qtd) {
            $novo_estoque = $estoque_atual - $qtd;
            
            $sql_atualiza = "UPDATE estoque SET qtd = $novo_estoque WHERE andar = $andar";
            
            if ($conexao->query($sql_atualiza)) {
                $sql_historico = "INSERT INTO historico_uso(andar,estoque,data) VALUES ('$andar','$qtd','$data')";
                $conexao->query($sql_historico);

                $status = "normal";
                if ($novo_estoque <= 10 && $novo_estoque > 0) { $status = "acabando"; }
                elseif ($novo_estoque == 0) { $status = "zerado"; }

                header("Location: sucesso.php?andar=$andar&restante=$novo_estoque&status=$status");
                exit();
            } else {
                header("Location: erro.php?msg=Erro ao atualizar o banco de dados.");
                exit();
            }
        } else {
            header("Location: erro.php?msg=Estoque insuficiente! Disponível: $estoque_atual unidades.");
            exit();
        }
    } else {
        header("Location: andar" . $andar . ".php");
        exit();
    }
}
?>
