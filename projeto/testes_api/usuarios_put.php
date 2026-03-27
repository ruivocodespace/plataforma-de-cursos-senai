<?php
// atualizar_usuario.php — Atualiza o usuario ID 2 via API

$id = 2;
$dados = json_encode([
    "nome"    => "Peterson Ruivo",
    "email" => "peperuivo@hotmail.com"
]);

$ch = curl_init("http://localhost:8012/ead_senai/projeto/api/usuarios.php?id=$id"); //precisa inserir o id na url

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");          // Verbo PUT
curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resultado = json_decode($resposta, true);
echo $resultado['mensagem'];  // "usuario atualizado"