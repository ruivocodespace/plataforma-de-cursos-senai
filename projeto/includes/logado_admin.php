<?php
// Verificar se o usuario está logado
//session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;

} 
if ($_SESSION["usuario_tipo"] != "admin" ){
    header("Location: login.php");
    exit;

}

