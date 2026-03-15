<?php 
session_start();
require_once("includes/logado.php");
require_once("includes/conexao.php");

$usuario_id = (int) $_SESSION["usuario_id"];

// 1. TOTAIS DO SISTEMA
$sql = "SELECT
(SELECT COUNT(*) FROM cursos) AS cursos,
(SELECT COUNT(*) FROM modulos) AS modulos,
(SELECT COUNT(*) FROM aulas) AS aulas,
(SELECT COUNT(*) FROM inscricoes WHERE usuario_id = $usuario_id) AS inscricoes,
(SELECT COUNT(*) FROM progresso WHERE usuario_id = $usuario_id AND concluido = 1) AS aulas_concluidas
";

$result = mysqli_query($conexao, $sql);
$totais = mysqli_fetch_assoc($result);

$totalCursos = $totais['cursos'] ?? 0;
$totalModulos = $totais['modulos'] ?? 0;
$totalAulas = $totais['aulas'] ?? 0;
$totalInscricoes = $totais['inscricoes'] ?? 0;
$totalAulasConcluidas = $totais['aulas_concluidas'] ?? 0;

// 2. BUSCAR CURSOS INSCRITOS
$sqlCursos = "SELECT c.* FROM cursos c
              JOIN inscricoes i ON i.curso_id = c.id
              WHERE i.usuario_id = $usuario_id";
$resultCursos = mysqli_query($conexao, $sqlCursos);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Cursos — EAD SENAI</title>
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
            <a href="meus_cursos.php" class="text-white text-sm font-semibold border-b-2 border-white pb-0.5">Meus Cursos</a>
            <div class="flex-1"></div>
            <span class="text-sm text-blue-200">
                Olá, <strong class="text-white">
                    <?= htmlspecialchars($_SESSION["usuario_nome"] ?? 'Aluno') ?>
                </strong>
            </span>
            <a href="logout.php" class="bg-senai-red text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-red-700 transition">Sair</a>
        </div>
    </nav>

    <div class="bg-white border-b border-gray-200 px-6 py-5">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">Meus Cursos</h1>
                <p class="text-sm text-gray-500 mt-1">Bem-vindo de volta, continue de onde parou.</p>
            </div>
            <a href="cursos.php" class="border-2 border-senai-blue text-senai-blue text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-50 transition">
                + Explorar mais cursos
            </a>
        </div>
    </div>

    <main class="max-w-6xl mx-auto px-6 py-8 flex-1 w-full">

        <?php
        // Calcular Progresso Geral
        $progressoGeral = 0;
        
        $sqlTotalInscritas = "SELECT COUNT(a.id) AS total 
                              FROM aulas a 
                              JOIN modulos m ON a.modulo_id = m.id 
                              JOIN inscricoes i ON m.curso_id = i.curso_id 
                              WHERE i.usuario_id = $usuario_id";
        $resTotalInscritas = mysqli_query($conexao, $sqlTotalInscritas);
        $totalAulasInscritas = mysqli_fetch_assoc($resTotalInscritas)['total'] ?? 0;

        if ($totalAulasInscritas > 0) {
            $progressoGeral = round(($totalAulasConcluidas / $totalAulasInscritas) * 100);
        }
        ?>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-senai-blue"><?= $totalInscricoes ?></p>
                <p class="text-xs text-gray-500 mt-1">Cursos inscritos</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-senai-green"><?= $totalAulasConcluidas ?></p>
                <p class="text-xs text-gray-500 mt-1">Aulas concluídas</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-senai-orange"><?= $progressoGeral ?>%</p>
                <p class="text-xs text-gray-500 mt-1">Progresso geral</p>
            </div>
        </div>

        <h2 class="font-bold text-gray-700 mb-4">Cursos em Andamento</h2>
        <div class="space-y-4">

            <?php if (mysqli_num_rows($resultCursos) > 0): ?>
                
                <?php while ($curso = mysqli_fetch_assoc($resultCursos)): 
                    $curso_id = (int) $curso['id'];

                    // Total de aulas do curso
                    $sqlTotalAulas = "SELECT COUNT(a.id) AS total FROM aulas a 
                                      JOIN modulos m ON a.modulo_id = m.id 
                                      WHERE m.curso_id = $curso_id";
                    $resTotal = mysqli_query($conexao, $sqlTotalAulas);
                    $total_aulas_curso = mysqli_fetch_assoc($resTotal)['total'] ?? 0;

                    // Aulas concluídas por este aluno (verificando concluido = 1)
                    $sqlConcluidas = "SELECT COUNT(p.id) AS concluidas FROM progresso p 
                                      JOIN aulas a ON p.aula_id = a.id 
                                      JOIN modulos m ON a.modulo_id = m.id 
                                      WHERE m.curso_id = $curso_id 
                                      AND p.usuario_id = $usuario_id 
                                      AND p.concluido = 1";
                    $resConcluidas = mysqli_query($conexao, $sqlConcluidas);
                    $aulas_concluidas_curso = mysqli_fetch_assoc($resConcluidas)['concluidas'] ?? 0;

                    // Calcular porcentagem do curso
                    $porcentagem = 0;
                    if ($total_aulas_curso > 0) {
                        $porcentagem = round(($aulas_concluidas_curso / $total_aulas_curso) * 100);
                    }
                ?>
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100 flex gap-5 p-5 items-center">
                        
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-24 h-20 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden relative">
                            <?php if (!empty($curso["capa"])): ?>
                                <img src="uploads/capas/<?= htmlspecialchars($curso["capa"]) ?>" class="w-full h-full object-cover absolute inset-0" alt="Capa do curso">
                            <?php else: ?>
                                <span class="text-white text-xs font-semibold text-center px-2">Sem capa</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-base mt-1">
                                        <?= htmlspecialchars($curso['titulo'] ?? 'Sem Título') ?>
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <?= $porcentagem == 100 ? 'Concluído' : 'Em andamento' ?>
                                    </p>
                                </div>
                                <a href="curso.php?curso_id=<?= $curso['id'] ?>" class="bg-senai-blue text-white text-xs font-bold px-5 py-2.5 rounded-lg hover:bg-senai-blue-dark transition flex-shrink-0">
                                    Continuar →
                                </a>
                            </div>
                            
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                    <span>Progresso</span>
                                    <span class="<?= $porcentagem == 100 ? 'text-senai-green' : 'text-gray-500' ?> font-semibold">
                                        <?= $aulas_concluidas_curso ?> / <?= $total_aulas_curso ?> aulas
                                    </span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-2.5">
                                    <div class="<?= $porcentagem == 100 ? 'bg-senai-green' : 'bg-senai-blue' ?> h-2.5 rounded-full transition-all" style="width:<?= $porcentagem ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1"><?= $porcentagem ?>% concluído</p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                    <span class="text-4xl block mb-2">📭</span>
                    <p>Você ainda não está inscrito em nenhum curso.</p>
                </div>
            <?php endif; ?>

        </div>

        <div class="mt-10 bg-senai-blue rounded-2xl p-6 text-white text-center">
            <h3 class="font-extrabold text-lg mb-1">Quer aprender mais?</h3>
            <p class="text-blue-200 text-sm mb-4">Temos outros cursos disponíveis no catálogo.</p>
            <a href="cursos.php" class="inline-block bg-white text-senai-blue font-bold px-6 py-2.5 rounded-lg text-sm hover:bg-blue-50 transition">
                Ver todos os cursos
            </a>
        </div>

    </main>

    <?php require_once("includes/footer.php");?>

</body>
</html>