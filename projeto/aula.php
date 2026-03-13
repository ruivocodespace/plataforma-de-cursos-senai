<?php
session_start();
require_once "includes/logado.php";
require_once "includes/conexao.php";

$nome = $_SESSION["usuario_nome"];
$usuario_id = $_SESSION["usuario_id"];

$aula_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

// Busca os dados da aula, unindo com módulo e curso para o breadcrumb
$sqlAula = "SELECT a.*, m.titulo AS modulo_titulo, c.id AS curso_id, c.titulo AS curso_titulo 
            FROM aulas a 
            INNER JOIN modulos m ON a.modulo_id = m.id 
            INNER JOIN cursos c ON m.curso_id = c.id 
            WHERE a.id = $aula_id";
$resAula = mysqli_query($conexao, $sqlAula);

if (mysqli_num_rows($resAula) == 0) {
    header("Location: meus_cursos.php");
    exit;
}

$aula = mysqli_fetch_assoc($resAula);
$curso_id = $aula['curso_id'];

// Verifica se o aluno já concluiu esta aula
$sqlProgresso = "SELECT * FROM progresso WHERE usuario_id = $usuario_id AND aula_id = $aula_id AND concluido = 1";
$resProgresso = mysqli_query($conexao, $sqlProgresso);
$is_concluida = (mysqli_num_rows($resProgresso) > 0);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($aula['titulo']) ?> — EAD SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { senai: { red:'#C0392B', blue:'#34679A', 'blue-dark':'#2C5A85', orange:'#E67E22', green:'#27AE60' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <nav class="bg-senai-blue shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center gap-6">
            <a href="index.php" class="flex items-center gap-2 text-white font-extrabold text-lg">🎓 EAD SENAI</a>
            <a href="cursos.php"      class="text-blue-200 hover:text-white text-sm transition">Cursos</a>
            <a href="meus_cursos.php" class="text-blue-200 hover:text-white text-sm transition">Meus Cursos</a>
            <div class="flex-1"></div>
            <span class="text-sm text-blue-200">Olá, <strong class="text-white"> <?= htmlspecialchars($nome) ?> </strong></span>
            <a href="login.php" class="bg-senai-red text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-red-700 transition">Sair</a>
        </div>
    </nav>

    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-4xl mx-auto flex items-center gap-2 text-xs text-gray-500">
            <a href="meus_cursos.php" class="hover:text-senai-blue">Meus Cursos</a>
            <span>›</span>
            <a href="curso.php?id=<?= $curso_id ?>" class="hover:text-senai-blue"><?= htmlspecialchars($aula['curso_titulo']) ?></a>
            <span>›</span>
            <span class="text-gray-400"><?= htmlspecialchars($aula['modulo_titulo']) ?></span>
        </div>
    </div>

    <main class="max-w-4xl mx-auto px-6 py-8 flex-1 w-full">
        
        <div class="bg-black w-full aspect-video rounded-xl shadow-lg flex items-center justify-center mb-6 relative overflow-hidden">
            <?php if(!empty($aula['video_url'])): ?>
                <iframe src="<?= htmlspecialchars($aula['video_url']) ?>" class="w-full h-full absolute inset-0 border-0" allowfullscreen></iframe>
            <?php else: ?>
                <span class="text-gray-400 text-lg flex flex-col items-center gap-2">
                    <span class="text-4xl">▶</span>
                    Vídeo da aula não cadastrado
                </span>
            <?php endif; ?>
        </div>

        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800"><?= htmlspecialchars($aula['titulo']) ?></h1>
                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($aula['modulo_titulo']) ?></p>
            </div>
            
            <form action="concluir_aula.php" method="POST">
                <input type="hidden" name="aula_id" value="<?= $aula_id ?>">
                <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
                
                <?php if ($is_concluida): ?>
                    <button type="submit" name="desmarcar" class="bg-green-100 text-senai-green border border-senai-green px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-green-200 transition">
                        <span>✓</span> Concluída
                    </button>
                <?php else: ?>
                    <button type="submit" name="concluir" class="bg-senai-green text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-green-700 transition shadow-sm">
                        <span>○</span> Marcar como concluída
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Material de Apoio e Descrição</h3>
            <div class="text-gray-600 text-sm leading-relaxed max-w-none">
                <?= nl2br(htmlspecialchars($aula['descricao'])) ?>
            </div>
        </div>

        <div class="flex justify-between mt-8 border-t pt-6 border-gray-200">
            <a href="curso.php?id=<?= $curso_id ?>" class="text-gray-500 hover:text-senai-blue text-sm font-semibold flex items-center gap-2 transition">
                <span>←</span> Voltar ao Curso
            </a>
        </div>

    </main>

    <?php require_once("includes/footer.php");?>

</body>
</html>