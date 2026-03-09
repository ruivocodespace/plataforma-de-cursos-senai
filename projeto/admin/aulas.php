<?php  

session_start();
require_once "../includes/logado.php";
require_once "../includes/conexao.php";

$nome = $_SESSION["usuario_nome"];
$email = $_SESSION["usuario_email"];

// BUSCAR AULAS
if(!isset($_GET["modulo_id"])){
    header("Location: modulos.php");
    exit;
}

$modulo_id = $_GET["modulo_id"];

$sqlModulo = "
SELECT m.*, c.titulo AS curso
FROM modulos m
INNER JOIN cursos c ON c.id = m.curso_id
WHERE m.id = '$modulo_id'
";

$resultModulo = mysqli_query($conexao, $sqlModulo);
$modulo = mysqli_fetch_assoc($resultModulo);

$sqlAulas = "
SELECT *
FROM aulas
WHERE modulo_id = '$modulo_id'
ORDER BY ordem ASC
";

$resultAulas = mysqli_query($conexao, $sqlAulas);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Aulas — Admin | EAD SENAI</title>
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
                    <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                    <span class="text-gray-700 font-semibold">
                        <?php echo $modulo["titulo"]; ?>
                    </span>
                    <span>Aulas</span>
                </div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Aulas</h1>
            </div>
            <a href="aula_form.php?modulo_id=<?php echo $modulo_id; ?>" 
                class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition">+ Nova Aula</a>
            </div>

        <div class="p-6 flex-1">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

               <!-- LISTA DE MÓDULOS -->
               <div class="space-y-3">
                    
               <?php if(mysqli_num_rows($resultAulas) == 0){
                    ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                        <p class="text-xs text-gray-400">Nenhuma aula cadastrada.</p>
                    </div>
                <?php }else{
                    $index = 1;
                    while($aula = mysqli_fetch_assoc($resultAulas)){
                    ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                <?php echo $index; ?>
                            </div>

                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">
                                    <?php echo $aula["titulo"]; ?>
                                </p>

                                <p class="text-xs text-gray-400">
                                    <?php echo $aula["descricao"]; ?>
                                </p>

                                <p class="text-xs text-gray-400">
                                    ⏱ <?php echo $aula["duracao"]; ?>
                                </p>
                            </div>
                            <div class="flex gap-1.5">

                                <a href="aula_form.php?editar=<?php echo $aula['id']; ?>" 
                                class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md">
                                ✏ Editar
                                </a>

                                <a href="aula_delete.php?id=<?php echo $aula['id']; ?>&modulo_id=<?php echo $modulo_id; ?>" 
                                class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md"
                                onclick="return confirm('Deseja excluir esta aula?')">
                                🗑 Excluir
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php
                    $index++;
                    }
                    }
                    ?>
                    </div>
                <!-- FORMULÁRIO RÁPIDO DE AULA -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="font-bold text-gray-700 text-sm mb-4">Adicionar Nova Aula</h2>
                    <form action="aula_form.php" method="post">
                        <input type="hidden" name="modulo_id" value="<?php echo $modulo_id; ?>">
                        <div class="mb-4">
                            <label class="form-label">Título da Aula *</label>
                            <input type="text" name="titulo" class="form-input" placeholder="Ex: Introdução às Tabelas HTML">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">URL do Vídeo</label>
                            <input type="url" name="video_url" class="form-input" placeholder="https://www.youtube.com/embed/...">
                            <p class="text-xs text-gray-400 mt-1">Use a URL de incorporação (embed) do YouTube ou Vimeo.</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Duração</label>
                            <input type="text" name="duracao" class="form-input" placeholder="Ex: 12:30">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Descrição (opcional)</label>
                            <textarea name="descricao" rows="3" class="form-input resize-none" placeholder="Breve descrição do conteúdo da aula..."></textarea>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="ordem" class="form-input" value="4" min="1">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">
                                Salvar Aula
                            </button>
                            <a href="modulos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
