<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

// Verificar se o ID do curso foi fornecido
if (isset($_GET["id"])) {

    $id = intval($_GET["id"]);

    // Soft delete (apenas desativa)
    $sql = "UPDATE cursos SET ativo = 0 WHERE id = '$id'";

    mysqli_query($conexao, $sql);
}
// Redirecionar de volta para a página de cursos
header("Location: cursos.php");
exit;