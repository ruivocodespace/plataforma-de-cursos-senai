<?php
session_start();

session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

if (isset($_GET["id"])) {

    $id = intval($_GET["id"]);

    // Soft delete (apenas desativa)
    $sql = "UPDATE cursos SET ativo = 0 WHERE id = '$id'";

    mysqli_query($conexao, $sql);
}

header("Location: cursos.php");
exit;