<?php
session_start();
require_once("includes/conexao.php");

// Verifica se o usuário está logado e se o formulário enviou o ID da aula
if (!isset($_SESSION["usuario_id"]) || !isset($_POST['aula_id'])) {
    die("Acesso negado.");
}

// Convertendo para números inteiros por segurança (evita SQL Injection)
$usuario_id = (int) $_SESSION["usuario_id"];
$aula_id = (int) $_POST['aula_id'];

// 1. Verifica se já existe um registro dessa aula para esse usuário
$sqlVerifica = "SELECT id, concluido FROM progresso WHERE usuario_id = $usuario_id AND aula_id = $aula_id";
$resVerifica = mysqli_query($conexao, $sqlVerifica);

if (mysqli_num_rows($resVerifica) > 0) {
    // O registro já existe! Vamos ver o status atual e inverter.
    $linha = mysqli_fetch_assoc($resVerifica);

    // Se estiver 1 (concluído), vira 0. Se estiver 0, vira 1.
    $novo_status = ($linha['concluido'] == 1) ? 0 : 1;

    // Atualiza o status e a data para o momento exato do clique
    $sqlUpdate = "UPDATE progresso 
                  SET concluido = $novo_status, data_conclusao = CURRENT_TIMESTAMP 
                  WHERE usuario_id = $usuario_id AND aula_id = $aula_id";
    mysqli_query($conexao, $sqlUpdate);
} else {
    // O registro não existe (primeira vez clicando). Vamos inserir!
    // Como sua tabela já tem DEFAULT 1 e CURRENT_TIMESTAMP, só precisamos enviar os IDs.
    $sqlInsert = "INSERT INTO progresso (usuario_id, aula_id) VALUES ($usuario_id, $aula_id)";
    mysqli_query($conexao, $sqlInsert);
}

// Redireciona o usuário de volta para a página da aula (ou para a home, se falhar)
$pagina_anterior = $_SERVER['HTTP_REFERER'] ?? 'meus_cursos.php';
header("Location: " . $pagina_anterior);
exit;
