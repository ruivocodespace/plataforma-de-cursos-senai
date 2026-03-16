<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

// Verifica se o ID foi passado na URL e se é um número válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $email_logado = $_SESSION["usuario_email"] ?? "";

    // Buscar os dados do usuário que será excluído
    $sql_busca = "SELECT email FROM usuarios WHERE id = $id";
    $res_busca = mysqli_query($conexao, $sql_busca);
    $usuario_alvo = mysqli_fetch_assoc($res_busca);

    // Se o usuário existir, verifica se o email dele é igual ao de quem está logado
    if ($usuario_alvo && $usuario_alvo['email'] === $email_logado) {
        // Redireciona avisando que não pode excluir a si próprio
        header("Location: usuarios.php?erro=Você não pode excluir sua própria conta.");
        exit;
    }

    // Executar a exclusão no banco de dados
    $sql_delete = "DELETE FROM usuarios WHERE id = $id";

    if (mysqli_query($conexao, $sql_delete)) {
        // Sucesso
        header("Location: usuario.php?sucesso=1");
    } else {
        // Erro no banco de dados (ex: chave estrangeira impedindo exclusão)
        header("Location: usuario.php?erro=Erro ao tentar excluir o usuário.");
    }
} else {
    // Se não passou ID nenhum, só volta pra tela de usuários
    header("Location: usuario.php");
}

exit;
