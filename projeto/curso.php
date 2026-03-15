<?php  
session_start();
require_once "includes/logado.php";
require_once "includes/conexao.php";

$nome = $_SESSION["usuario_nome"];
$usuario_id = (int) $_SESSION["usuario_id"];

// 1. CAPTURAR E VALIDAR O ID DO CURSO
$curso_id = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($curso_id === 0) {
    header("Location: meus_cursos.php");
    exit;
}

// 2. BUSCAR DADOS DO CURSO
$sqlCurso = "SELECT * FROM cursos WHERE id = $curso_id";
$resCurso = mysqli_query($conexao, $sqlCurso);
$curso = mysqli_fetch_assoc($resCurso);

if (!$curso) {
    die("Curso não encontrado.");
}

// 3. ESTATÍSTICAS GERAIS DO CURSO
$sqlModulosCount = "SELECT COUNT(*) as total FROM modulos WHERE curso_id = $curso_id";
$totalModulos = mysqli_fetch_assoc(mysqli_query($conexao, $sqlModulosCount))['total'];

$sqlAulasCount = "SELECT COUNT(a.id) as total FROM aulas a JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = $curso_id";
$totalAulas = mysqli_fetch_assoc(mysqli_query($conexao, $sqlAulasCount))['total'];

$sqlConcluidas = "SELECT COUNT(p.id) as total FROM progresso p 
                  JOIN aulas a ON p.aula_id = a.id 
                  JOIN modulos m ON a.modulo_id = m.id 
                  WHERE m.curso_id = $curso_id AND p.usuario_id = $usuario_id AND p.concluido = 1";
$aulasConcluidas = mysqli_fetch_assoc(mysqli_query($conexao, $sqlConcluidas))['total'];

$progressoGeral = ($totalAulas > 0) ? round(($aulasConcluidas / $totalAulas) * 100) : 0;

// 4. PREPARAR ARRAY DE MÓDULOS E AULAS (Para a lista central e sidebar)
$sqlModulos = "SELECT * FROM modulos WHERE curso_id = $curso_id ORDER BY id ASC";
$resModulos = mysqli_query($conexao, $sqlModulos);
$modulosComAulas = [];
$proximaAulaId = 0; // Para o botão "Continuar Aula" da sidebar

while ($mod = mysqli_fetch_assoc($resModulos)) {
    $mod_id = $mod['id'];
    
    // Buscar aulas e verificar progresso do aluno via LEFT JOIN
    $sqlAulas = "SELECT a.*, p.concluido 
                 FROM aulas a 
                 LEFT JOIN progresso p ON (a.id = p.aula_id AND p.usuario_id = $usuario_id) 
                 WHERE a.modulo_id = $mod_id ORDER BY a.id ASC";
    $resAulas = mysqli_query($conexao, $sqlAulas);
    
    $aulas = [];
    $aulasConcluidasMod = 0;
    
    while ($aula = mysqli_fetch_assoc($resAulas)) {
        if ($aula['concluido'] == 1) {
            $aulasConcluidasMod++;
        } else if ($proximaAulaId === 0) {
            // Guarda o ID da primeira aula não concluída para o botão "Continuar"
            $proximaAulaId = $aula['id'];
        }
        $aulas[] = $aula;
    }
    
    // Adicionar estatísticas ao módulo
    $mod['aulas'] = $aulas;
    $mod['total_aulas'] = count($aulas);
    $mod['aulas_concluidas'] = $aulasConcluidasMod;
    $mod['progresso'] = ($mod['total_aulas'] > 0) ? round(($aulasConcluidasMod / $mod['total_aulas']) * 100) : 0;
    
    $modulosComAulas[] = $mod;
}

// Se todas as aulas estiverem concluídas, o botão Continuar aponta para a primeira aula
if ($proximaAulaId === 0 && $totalAulas > 0) {
    $proximaAulaId = $modulosComAulas[0]['aulas'][0]['id'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($curso['titulo']) ?> — EAD SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { senai: { red:'#C0392B', blue:'#34679A', 'blue-dark':'#2C5A85', orange:'#E67E22', green:'#27AE60' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .modulo-header { cursor: pointer; }
        .modulo-body { display: block; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- NAVBAR -->
    <nav class="bg-senai-blue shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center gap-6">
            <a href="index.php" class="flex items-center gap-2 text-white font-extrabold text-lg">🎓 EAD SENAI</a>
            <a href="cursos.php"      class="text-blue-200 hover:text-white text-sm transition">Cursos</a>
            <a href="meus_cursos.php" class="text-blue-200 hover:text-white text-sm transition">Meus Cursos</a>
            <div class="flex-1"></div>
            <span class="text-sm text-blue-200">Olá, <strong class="text-white"><?= htmlspecialchars($_SESSION["usuario_nome"]) ?> </strong></span>
            <a href="login.php" class="bg-senai-red text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-red-700 transition">Sair</a>
        </div>
    </nav>

    <!-- BREADCRUMB + INFO DO CURSO -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                <a href="meus_cursos.php" class="hover:text-senai-blue">Meus Cursos</a>
                <span>›</span>
                <span class="text-gray-700 font-semibold"><?= htmlspecialchars($curso['titulo']) ?></span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-14 h-14 rounded-lg flex items-center justify-center">
                        <span class="text-3xl"><img src="uploads/capas/<?= htmlspecialchars($curso['capa']) ?>" alt="Capa do curso" class="w-full h-full object-cover"></span>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-gray-800"><?= htmlspecialchars($curso['titulo']) ?></h1>
                        <div class="flex gap-4 text-xs text-gray-500 mt-1">
                            <span>📚 <?= $totalModulos ?></span>
                            <span>🎬 <?= $totalAulas ?></span>
                            <span class="text-senai-green font-semibold">✓ <?= $aulasConcluidas ?> aulas concluídas</span>
                        </div>
                    </div>
                </div>
                <!-- Progresso geral -->
                <div class="min-w-48">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progresso geral</span>
                        <span class="font-semibold text-senai-green"><?= $progressoGeral ?>%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-3">
                        <div class="bg-senai-green h-3 rounded-full" style="width: <?= $progressoGeral ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="max-w-6xl mx-auto px-6 py-6 flex gap-6 flex-1 w-full">

        <div class="flex-1 space-y-4">
            
            <?php 
            if (count($modulosComAulas) > 0): 
                foreach ($modulosComAulas as $index => $modulo):
                    $numModulo = $index + 1;
                    $modConcluido = ($modulo['progresso'] == 100);
            ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="modulo-header flex items-center justify-between px-5 py-4 <?= $modConcluido ? 'bg-green-50' : 'bg-blue-50 border-l-4 border-senai-blue' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 <?= $modConcluido ? 'bg-senai-green' : 'bg-senai-blue' ?> rounded-full flex items-center justify-center text-white text-sm font-bold"><?= $numModulo ?></div>
                            <div>
                                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($modulo['titulo']) ?></h3>
                                <p class="text-xs text-gray-500"><?= $modulo['total_aulas'] ?> aulas &nbsp;·&nbsp; <span class="<?= $modConcluido ? 'text-senai-green' : 'text-senai-blue' ?>"><?= $modulo['aulas_concluidas'] ?> concluídas</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 hidden sm:flex">
                            <div class="bg-gray-200 rounded-full h-2 w-24">
                                <div class="<?= $modConcluido ? 'bg-senai-green' : 'bg-senai-blue' ?> h-2 rounded-full" style="width: <?= $modulo['progresso'] ?>%"></div>
                            </div>
                            <span class="text-xs text-gray-400 font-semibold"><?= $modulo['progresso'] ?>%</span>
                        </div>
                    </div>
                    
                    <div class="modulo-body divide-y divide-gray-100">
                        <?php 
                        if (count($modulo['aulas']) > 0):
                            foreach ($modulo['aulas'] as $aula):
                                $aulaConcluida = ($aula['concluido'] == 1);
                        ?>
                            <div class="flex items-center gap-4 px-5 py-3 <?= $aulaConcluida ? 'bg-green-50/30' : 'hover:bg-gray-50' ?>">
                                <div class="w-7 h-7 <?= $aulaConcluida ? 'bg-senai-green' : 'bg-senai-blue' ?> rounded-full flex items-center justify-center flex-shrink-0">
                                    <?php if ($aulaConcluida): ?>
                                        <span class="text-white text-xs font-bold">✓</span>
                                    <?php else: ?>
                                        <span class="text-white text-xs ml-0.5">▶</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($aula['titulo']) ?></p>
                                </div>
                                <?php if ($aulaConcluida): ?>
                                    <span class="text-xs text-senai-green font-semibold hidden sm:inline">Concluída</span>
                                    <a href="aula.php?id=<?= $aula['id'] ?>" class="bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded-lg hover:bg-gray-200 transition">Rever</a>
                                <?php else: ?>
                                    <a href="aula.php?id=<?= $aula['id'] ?>" class="bg-senai-blue text-white text-xs px-4 py-1.5 rounded-lg hover:bg-senai-blue-dark transition font-semibold">Assistir</a>
                                <?php endif; ?>
                            </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <div class="p-4 text-center text-sm text-gray-500">Nenhuma aula cadastrada neste módulo.</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                    Este curso ainda não possui módulos cadastrados.
                </div>
            <?php endif; ?>

        </div>

        <!-- SIDEBAR — NAVEGAÇÃO RÁPIDA -->
        <aside class="w-64 flex-shrink-0 hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-20">
                <h4 class="font-bold text-gray-700 text-sm mb-3">Navegação do Curso</h4>
                <ul class="space-y-1 text-xs">
                    <?php 
                    foreach ($modulosComAulas as $index => $modulo): 
                        $numModulo = $index + 1;
                    ?>
                    <li class="font-semibold text-senai-blue border-l-2 border-senai-blue pl-2">Módulo <?= $numModulo ?>: <?= htmlspecialchars($modulo['titulo']) ?></li>
                    <?php foreach ($modulo['aulas'] as $aula): ?>
                            <?php if ($aula['concluido'] == 1): ?>
                                <li class="text-senai-green pl-4 truncate">✓ <?= htmlspecialchars($aula['titulo']) ?></li>
                            <?php else: ?>
                                <li class="text-gray-500 pl-4 truncate">▶ <?= htmlspecialchars($aula['titulo']) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                    <?php endforeach; ?>
                </ul>
                <hr class="my-3 border-gray-200">
                <?php if ($proximaAulaId > 0): ?>
                <a href="aula.php?id=<?= $proximaAulaId ?>" class="block bg-senai-blue text-white text-xs font-bold py-2 rounded-lg text-center hover:bg-senai-blue-dark transition">
                    ▶ Continuar Aula
                </a>
                <?php endif; ?>
            </div>
        </aside>

    </main>

    <!-- FOOTER -->
    <?php require_once("includes/footer.php");?>

</body>
</html>
