<?php  

session_start();
require_once "../includes/logado.php";
require_once "../includes/conexao.php";

$nome = $_SESSION["usuario_nome"];
$email = $_SESSION["usuario_email"];

// BUSCAR CURSOS
$sqlCursos = "SELECT * FROM cursos ORDER BY id DESC";
$resultCursos = mysqli_query($conexao, $sqlCursos);

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos — Admin | EAD SENAI</title>
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
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!--SIDEBAR + TOPBAR -->
    <?php
    require_once "includes/menu.php";
    ?>
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Cursos</h1>
                <p class="text-sm text-gray-500">Cadastre, edite e organize os cursos da plataforma</p>
            </div>
            <a href="curso_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Curso
            </a>
        </div>

        <div class="p-6 flex-1">

            <!-- MENSAGEM DE SUCESSO -->
            <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">✓</span>
                <span>Curso excluído com sucesso!</span>
                <button class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
            </div>

            <!-- TABELA DE CURSOS -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">#</th>
                            <th class="px-4 py-3 text-left">Curso</th>
                            <th class="px-4 py-3 text-center">Módulos</th>
                            <th class="px-4 py-3 text-center">Aulas</th>
                            <th class="px-4 py-3 text-center">Inscrições</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Cadastrado em</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

<?php while($curso = mysqli_fetch_assoc($resultCursos)): ?>

<tr class="hover:bg-gray-50 transition">

<td class="px-4 py-3 text-gray-400 font-mono text-xs">
    <?= $curso["id"] ?>
</td>

<td class="px-4 py-3">
<div class="flex items-center gap-3">

<div class="w-10 h-10 bg-senai-blue rounded-lg flex items-center justify-center flex-shrink-0">
<span class="text-lg">📚</span>
</div>

<div>
<p class="font-semibold text-gray-800">
<?= $curso["nome"] ?>
</p>

<p class="text-xs text-gray-400 mt-0.5">
<?= $curso["descricao"] ?>
</p>

</div>
</div>
</td>

<td class="px-4 py-3 text-center text-gray-600 font-semibold">-</td>
<td class="px-4 py-3 text-center text-gray-600 font-semibold">-</td>
<td class="px-4 py-3 text-center text-gray-600 font-semibold">-</td>

<td class="px-4 py-3 text-center">

<?php if($curso["ativo"] == 1): ?>

<span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">
Ativo
</span>

<?php else: ?>

<span class="bg-gray-100 text-gray-500 text-xs font-bold px-2.5 py-1 rounded-full">
Inativo
</span>

<?php endif; ?>

</td>

<td class="px-4 py-3 text-center text-xs text-gray-400">
<?= date("d/m/Y", strtotime($curso["criado_em"])) ?>
</td>

<td class="px-4 py-3 text-center">

<div class="flex items-center justify-center gap-1.5">

<a href="modulos.php?curso=<?= $curso["id"] ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition">
📦 Módulos
</a>

<a href="curso_form.php?id=<?= $curso["id"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition">
✏ Editar
</a>

<a onclick="return confirm('Excluir este curso?')" href="curso_excluir.php?id=<?= $curso["id"] ?>" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition">
🗑
</a>

</div>

</td>

</tr>

<?php endwhile; ?>

</tbody>
                </table>

                <!-- RODAPÉ DA TABELA -->
                <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo 3 de 3 cursos</p>
                    <div class="flex gap-1">
                        <button class="px-3 py-1 text-xs border border-gray-300 rounded bg-white text-gray-500">← Anterior</button>
                        <button class="px-3 py-1 text-xs border border-senai-blue rounded bg-senai-blue text-white font-semibold">1</button>
                        <button class="px-3 py-1 text-xs border border-gray-300 rounded bg-white text-gray-500">Próxima →</button>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
