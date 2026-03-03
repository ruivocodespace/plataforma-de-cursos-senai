<?php
// Verificar se o usuario está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>