<?php

session_start();
require_once("includes/conexao.php");
require_once("includes/logado.php");

$usuario_id = $_SESSION["usuario_id"];
$curso_id = $_GET["curso_id"];

// verifica se já está inscrito
$sql_verifica = "
SELECT id 
FROM inscricoes 
WHERE usuario_id = '$usuario_id'
AND curso_id = '$curso_id'
";

$result = mysqli_query($conexao, $sql_verifica);

if (mysqli_num_rows($result) == 0) {
    // Não está inscrito, faz o insert
    $sql = "
    INSERT INTO inscricoes (usuario_id, curso_id)
    VALUES ('$usuario_id','$curso_id')
    ";

    mysqli_query($conexao, $sql);

    // CRIA A MENSAGEM DE SUCESSO NA SESSÃO
    $_SESSION['msg_texto'] = "Inscrição realizada com sucesso! Bons estudos.";
    $_SESSION['msg_tipo'] = "sucesso";
} else {
    // Já estava inscrito
    // CRIA A MENSAGEM DE AVISO NA SESSÃO
    $_SESSION['msg_texto'] = "Você já está inscrito neste curso!";
    $_SESSION['msg_tipo'] = "aviso";
}

header("Location: cursos.php");
exit;
