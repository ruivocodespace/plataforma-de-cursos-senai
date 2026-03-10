<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

// Variáveis para mensagens
$sucesso = "";
$erro = "";
$editando = NULL;
$id = "";


if (isset($_GET["editar"])) {
    $id = $_GET["editar"];
    $sql = "SELECT * FROM modulos WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

// Verificar se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $curso_id = $_POST["curso_id"];
    $titulo = $_POST["titulo"];
    $descricao = $_POST["descricao"];
    $ordem = $_POST["ordem"];

    if($id) {
    $sql = "UPDATE modulos SET
    curso_id = '$curso_id',
    titulo = '$titulo',
    descricao = '$descricao',
    ordem = '$ordem'
    WHERE id = $id";
        $sucesso = "Módulo atualizado com sucesso!";
    }else{
        $sql = "INSERT INTO modulos (curso_id, titulo, descricao, ordem) VALUES 
            ('$curso_id', '$titulo', '$descricao', '$ordem')";
            $sucesso = "Módulo cadastrado com sucesso!";
        }
    if (!mysqli_query($conexao, $sql)) {
        $erro = "Erro ao cadastrar módulo.";
    }
}

/* BUSCAR CURSOS */

$sqlCursos = "SELECT * FROM cursos ORDER BY titulo";
$resultadoCursos = mysqli_query($conexao,$sqlCursos);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Módulo — Admin | EAD SENAI</title>
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
    ?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                <span class="text-gray-700 font-semibold">Adicionar Módulo</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Adicionar Módulo</h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Mensagem de sucesso -->
                <?php if (!empty($sucesso)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            <?php echo $sucesso; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Mensagem de erro -->
                    <?php if (!empty($erro)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>
                <form action="modulo_form.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="mb-4">
                        <label class="form-label">Curso</label>
                        <select name="curso_id" class="form-input">
                                <?php while($curso = mysqli_fetch_assoc($resultadoCursos)): ?>
                            <option value="<?php echo $curso['id']; ?>"
                                <?php if($editando && $editando['curso_id'] == $curso['id']) echo "selected"; ?>>
                                <?php echo $curso['titulo']; ?>
                            </option>
                                <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Título do Módulo *</label>
                        <input type="text" name="titulo" class="form-input" value="<?= $editando ? $editando['titulo'] : '' ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descrição (opcional)</label>
                        <textarea name="descricao" rows="3" class="form-input resize-none"><?= $editando ? $editando['descricao'] : '' ?></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-input" value="<?= $editando ? $editando['ordem'] : '' ?>" min="1">
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
