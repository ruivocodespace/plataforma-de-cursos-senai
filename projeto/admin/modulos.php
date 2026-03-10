<?php  

session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

$sucesso = "";
$erro = "";

if(isset($_GET["sucesso"])){
    $sucesso = "Módulo excluído com sucesso!";
}

if(isset($_GET["erro"])){
    $erro = "Erro ao excluir módulo.";
}

$nome = $_SESSION["usuario_nome"];
$tipo = $_SESSION["usuario_tipo"];
$email = $_SESSION["usuario_email"];

$curso_id = $_GET["curso_id"] ?? 0;

if($curso_id > 0){
    $sql = "SELECT * FROM modulos WHERE curso_id = $curso_id ORDER BY ordem ASC";
    $sqlCurso = "SELECT * FROM cursos WHERE id = $curso_id";
    $resultCurso = mysqli_query($conexao,$sqlCurso);
    $curso = mysqli_fetch_assoc($resultCurso);
}else{
    $sql = "SELECT * FROM modulos ORDER BY ordem ASC";
    $curso = ["titulo" => "Todos os Cursos"];
}

$resultado = mysqli_query($conexao,$sql);

$modulos = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $modulos[] = $row;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $curso_id = $_POST["curso_id"];
    $titulo = $_POST["titulo"];
    $descricao = $_POST["descricao"];
    $ordem = $_POST["ordem"];

    $sql = "INSERT INTO modulos (curso_id, titulo, descricao, ordem)
            VALUES ('$curso_id','$titulo','$descricao','$ordem')";

    mysqli_query($conexao,$sql);

    header("Location: modulos.php");
    exit;
}

$sqlCursos = "SELECT id, titulo FROM cursos ORDER BY titulo ASC";
$resultCursos = mysqli_query($conexao, $sqlCursos);

$cursos = [];

while($row = mysqli_fetch_assoc($resultCursos)){
    $cursos[] = $row;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Módulos — Admin | EAD SENAI</title>
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
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                    <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                    <span class="text-gray-700 font-semibold">
                        <?php echo $curso["titulo"] ?? 'Todos os cursos'; ?>
                    </span> ›
                    <span>Módulos</span>
                </div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Módulos</h1>
            </div>
            <div class="mt-2">
            <select onchange="location = 'modulos.php?curso_id=' + this.value"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="0">Todos os cursos</option>
                <?php foreach($cursos as $c): ?>
                    <option 
                        value="<?php echo $c['id']; ?>"
                        <?php if($curso_id == $c['id']) echo "selected"; ?>>
                        <?php echo $c['titulo']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            </div>
            <a href="modulo_form.php?curso_id=<?php echo $curso_id; ?>" 
                class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition">
                + Novo Módulo
            </a>
        </div>

        <div class="p-6 flex-1">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- LISTA DE MÓDULOS -->
                <div class="space-y-3">
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
                    
                <?php if(empty($modulos)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-xs text-gray-400">Nenhum módulo cadastrado.</p>
                    </div>

                    <?php else: ?>
                    <?php foreach($modulos as $index => $modulo): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                <?php echo $index + 1; ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">
                                    <?php echo $modulo["titulo"]; ?>
                                </p>
                                <p class="text-xs text-gray-400">
                                    <?php echo $modulo["descricao"]; ?>
                                </p>
                            </div>
                            <div class="flex gap-1.5">
                                <a href="aulas.php?modulo_id=<?php echo $modulo['id']; ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md">🎬 Aulas</a>
                                <a href="modulo_form.php?editar=<?php echo $modulo['id']; ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md">✏ Editar</a>
                                <a href="modulo_delete.php?id=<?php echo $modulo['id']; ?>" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md">🗑 Excluir</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- FORMULÁRIO RÁPIDO -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="font-bold text-gray-700 text-sm mb-4">Adicionar Novo Módulo</h2>
                    <form action="modulos.php" method="post">
                        <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                        <div class="mb-4">
                            <label class="form-label">Título do Módulo *</label>
                            <input type="text" name="titulo" class="form-input" placeholder="Ex: Introdução ao HTML">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Descrição (opcional)</label>
                            <textarea name="descricao" rows="3" class="form-input resize-none" placeholder="Breve descrição do módulo..."></textarea>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="ordem" class="form-input" value="4" min="1">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">
                                Salvar Módulo
                            </button>
                            <a href="cursos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
