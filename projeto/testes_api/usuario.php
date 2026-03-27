<?php
// consumir_usuarios.php — Buscar todos os usuarios via API

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost:8012/ead_senai/projeto/api/usuarios.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($ch);
curl_close($ch);

$usuarios = json_decode($resposta, true);

echo '<pre>';
print_r($usuarios);
die;

// Exibir os usuarios na tela
foreach ($usuarios as $usuario) {
    echo "<div class='bg-white rounded-lg shadow p-4'>";
    echo "  <h3>" . $usuario['nome'] . "</h3>";
    echo "  <p>" . $usuario['email'] . "</p>";
    echo "  <a href='usuario_front.php?id=" . $usuario['id'] . "'>Acessar</a>";
    echo "</div>";
}
