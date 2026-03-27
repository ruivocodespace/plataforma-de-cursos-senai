<?php
// criar_usuario.php — Envia dados para a API criar um usuario

$dados = json_encode([
    "nome"    => "Leandro",
    "email" => "Eleandro@email.com"
]);

$ch = curl_init("http://localhost:8012/ead_senai/projeto/api/usuarios.php");

curl_setopt($ch, CURLOPT_POST, true);                    // Verbo POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);             // Body com JSON
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"                      // Avisa: estou enviando JSON
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);    // 201 = criado!
curl_close($ch);

$resultado = json_decode($resposta, true);
echo "Status: $http_code | ID: " . $resultado['id'];
