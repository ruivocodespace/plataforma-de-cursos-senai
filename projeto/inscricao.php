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

$result = mysqli_query($conexao,$sql_verifica);

if(mysqli_num_rows($result) == 0){

    $sql = "
    INSERT INTO inscricoes (usuario_id, curso_id)
    VALUES ('$usuario_id','$curso_id')
    ";

    mysqli_query($conexao,$sql);
}

header("Location: cursos.php");
exit;