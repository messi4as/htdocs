<?php
require 'db_connect.php';

// funcoes_log.php

function registrarLogAuditoria($conn, $usuario_id, $tabela, $registro_id, $dados_anteriores, $dados_novos) {
    $operacao = "UPDATE"; // Como estamos registrando alterações
    $sql = "INSERT INTO log_auditoria (id_usuarios, operacao, nome_tabela, dados_antigos, dados_novos)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $usuario_id, $operacao, $tabela, $dados_anteriores, $dados_novos);

    if ($stmt->execute()) {
        $stmt->close();
        return true; // Log registrado com sucesso
    } else {
        error_log("Erro ao registrar log de auditoria: " . $stmt->error);
        $stmt->close();
        return false; // Falha ao registrar o log
    }
}

?>
