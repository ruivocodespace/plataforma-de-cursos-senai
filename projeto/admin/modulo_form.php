<?php
session_start();
require_once "../includes/logado.php";
require_once "../includes/conexao.php";

// Variáveis para mensagens
$sucesso = "";
$erro = "";
$editando = NULL;


if (isset($_GET["editar"])) {
    $id = $_GET["editar"];
    $sql = "SELECT * FROM aulas WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

if (isset($_GET["excluir"])) {
    $id = $_GET["excluir"];
    $sql = "UPDATE aulas SET ativo = 0 WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
}

// Verificar se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $modulo_id = $_POST["modulo_id"];
    $titulo  = $_POST["titulo"];
    $video_url = $_POST["video_url"];
    $duracao = $_POST["duracao"];
    $descricao = $_POST["descricao"];
    $ordem = $_POST["ordem"];

    // Verificar se o email já existe
    $sql = "SELECT * FROM aulas WHERE titulo = '$titulo'";
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) > 0 && !$editando) {
        $erro = "Esta aula já está cadastrado.";
    } else {
        if($id) {
            $sql = "UPDATE aulas SET
            modulo_id = '$modulo_id',
            titulo = '$titulo',
            video_url = '$video_url',
            duracao = '$duracao',
            descricao = '$descricao',
            ordem = '$ordem'
            WHERE id = $id
            ";
            $sucesso = "Aula atualizada com sucesso!";

            

        }else{
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO aulas (modulo_id, titulo, video_url, duracao, descricao, ordem) VALUES 
            ('$modulo_id', '$titulo', '$video_url', '$duracao', '$descricao', '$ordem')";
            $sucesso = "Aula cadastrada com sucesso!";
            
        }

        if (!mysqli_query($conexao, $sql)) {
            $erro = "Erro ao cadastrar aula.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Módulo — Admin | EAD SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { senai: { red:'#C0392B', blue:'#34679A', 'blue-dark':'#2C5A85', orange:'#E67E22', green:'#27AE60' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .nav-link { display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:6px; font-size:13px; cursor:pointer; transition:background .15s; color:#cbd5e1; }
        .nav-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .nav-link.active { background:rgba(255,255,255,.15); color:#fff; font-weight:600; }
        .form-input { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:10px 14px; font-size:14px; outline:none; transition:border .15s; }
        .form-input:focus { border-color:#34679A; box-shadow:0 0 0 3px rgba(52,103,154,.15); }
        .form-label { display:block; font-size:12px; font-weight:600; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!--SIDEBAR + TOPBAR -->
    <?php
    require_once "includes/menu.php";
    ?>>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                <span class="text-gray-700 font-semibold">Editar Módulo</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Editar Módulo</h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="modulos.php" method="post">
                    <input type="hidden" name="id" value="1">
                    <div class="mb-4">
                        <label class="form-label">Curso</label>
                        <select name="curso_id" class="form-input">
                            <option value="1" selected>HTML e CSS do Zero</option>
                            <option value="2">PHP para Iniciantes</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Título do Módulo *</label>
                        <input type="text" name="titulo" class="form-input" value="Introdução ao HTML">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descrição (opcional)</label>
                        <textarea name="descricao" rows="3" class="form-input resize-none">Fundamentos da linguagem HTML, estrutura de uma página e tags principais.</textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-input" value="1" min="1">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="modulos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
