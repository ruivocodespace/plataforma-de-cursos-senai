<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

if (isset($_GET["id"])) {

    $id = intval($_GET["id"]);

    $sql = "DELETE FROM modulos WHERE id = $id";
    mysqli_query($conexao, $sql);

    header("Location: modulos.php?sucesso=1");
    exit;
} else {

    header("Location: modulos.php?erro=1");
    exit;
}
