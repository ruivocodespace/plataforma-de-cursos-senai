<?php
// Verificar se o admin está logado

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION["usuario_tipo"] != "admin") {
    header("Location: login.php");
    exit;
}
