<?php 

session_start();
require_once("includes/logado.php");
require_once("includes/conexao.php");

$usuario_id = $_SESSION["usuario_id"];

 //// TOTAIS DO SISTEMA ////
$sql = "SELECT
(SELECT COUNT(*) FROM cursos) AS cursos,
(SELECT COUNT(*) FROM modulos) AS modulos,
(SELECT COUNT(*) FROM aulas) AS aulas
";

$result = mysqli_query($conexao, $sql);
$totais = mysqli_fetch_assoc($result);

$totalCursos = $totais['cursos'] ?? 0;
$totalModulos = $totais['modulos'] ?? 0;
$totalAulas = $totais['aulas'] ?? 0;

// BUSCAR CURSOS
$sqlCursos = "
SELECT 
c.id,
c.titulo,
c.descricao,
c.capa,

COUNT(DISTINCT m.id) AS total_modulos,
COUNT(DISTINCT a.id) AS total_aulas

FROM cursos c

LEFT JOIN modulos m 
ON m.curso_id = c.id

LEFT JOIN aulas a 
ON a.modulo_id = m.id

LEFT JOIN inscricoes i 
ON i.curso_id = c.id

GROUP BY c.id

ORDER BY c.id DESC
";
$resultCursos = mysqli_query($conexao, $sqlCursos);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Cursos — EAD SENAI</title>
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

    <!-- NAVBAR -->
    <nav class="bg-senai-blue shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center gap-6">
            <a href="index.php" class="flex items-center gap-2 text-white font-extrabold text-lg">🎓 EAD SENAI</a>
            <a href="cursos.php"   class="text-white text-sm font-semibold border-b-2 border-white pb-0.5">Cursos</a>
            <a href="meus_cursos.php" class="text-blue-200 hover:text-white text-sm transition">Meus Cursos</a>
            <div class="flex-1"></div>
            <span class="text-sm text-blue-200">Olá, <strong class="text-white"><?= htmlspecialchars($_SESSION["usuario_nome"]) ?> </strong></span>
            <a href="login.php" class="bg-senai-red text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-red-700 transition">Sair</a>
        </div>
    </nav>

    <!-- CABEÇALHO DA PÁGINA -->
    <div class="bg-white border-b border-gray-200 px-6 py-5">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl font-extrabold text-gray-800">Catálogo de Cursos</h1>
            <p class="text-sm text-gray-500 mt-1">Escolha um curso, inscreva-se e comece a aprender agora mesmo.</p>
        </div>
    </div>

    <!-- MENSAGEM DE SUCESSO (após inscrição) -->
    <?php if(isset($_GET['sucesso'])): ?>
    <div class="max-w-6xl mx-auto px-6 pt-5">
        <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 flex items-center gap-2 text-sm">
            <span class="font-bold text-lg">✓</span>
            <span>Inscrição realizada com sucesso! Acesse <a href="meus_cursos.php" class="underline font-semibold">Meus Cursos</a> para começar.</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- GRADE DE CURSOS -->
    <main class="max-w-6xl mx-auto px-6 py-8 flex-1">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Lista de cusros -->
                    <?php while ($u = mysqli_fetch_assoc($resultCursos)): ?>
                     <!-- LÓGICA DO USUARIO INCRITO -->
                     <?php
                        $usuario_id = $_SESSION["usuario_id"];
                        $sql_inscrito = "
                        SELECT id 
                        FROM inscricoes 
                        WHERE usuario_id = '$usuario_id'
                        AND curso_id = '".$u["id"]."'
                        ";
                        $resultInscrito = mysqli_query($conexao,$sql_inscrito);
                        
                        $inscrito = mysqli_num_rows($resultInscrito) > 0;

                        $sql_progresso = "
                        SELECT 
                        COUNT(DISTINCT a.id) AS total_aulas,
                        COUNT(DISTINCT p.aula_id) AS aulas_concluidas

                        FROM modulos m
                        JOIN aulas a ON a.modulo_id = m.id

                        LEFT JOIN progresso p 
                        ON p.aula_id = a.id 
                        AND p.usuario_id = '$usuario_id'

                        WHERE m.curso_id = '".$u["id"]."'
                        ";

                        $resultProgresso = mysqli_query($conexao,$sql_progresso);
                        $dados = mysqli_fetch_assoc($resultProgresso);

                        $total = $dados["total_aulas"];
                        $concluidas = $dados["aulas_concluidas"];

                        $progresso = 0;

                        if($total > 0){
                            $progresso = ($concluidas / $total) * 100;
                        }
                        ?>
                        

                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-700 h-36 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($u["capa"])): ?>
                                    <img style="width: 100%; height: 100%; object-fit: cover;" src="uploads/capas/<?= $u["capa"] ?>">

                                <?php else: ?>
                                    <span class="text-white">Sem capa</span>
                                <?php endif; ?>

                                </div>
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs text-gray-400"><?= $u["total_modulos"] ?> módulos · <?= $u["total_aulas"] ?> aulas</span>
                                </div>
                                <h3 class="font-bold text-gray-800 text-base mb-2"><?php echo $u["titulo"]; ?></h3>
                                <p class="text-sm text-gray-500 mb-4 flex-1"><?php echo $u["descricao"]; ?></p>

                            <!-- Progresso -->
                            <?php  if($inscrito): ?>
                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Seu progresso</span>
                                    <span class="text-senai-green font-semibold">
                                        <?= $concluidas ?> / <?= $total ?> aulas
                                    </span>
                                </div>

                                <div class="bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?= $progresso ?>%"></div>
                                </div>
                            </div>
                            
                            <a href="curso.php?curso_id=<?= $u["id"] ?>" class="block bg-senai-green text-white text-sm font-semibold py-2.5 rounded-lg text-center hover:bg-green-600 transition">
                                Continuar Curso →
                            </a>

                            <?php else: ?>
                            <a href="inscricao.php?curso_id=<?= $u["id"] ?>"class="bg-senai-blue text-white text-sm font-semibold py-2 rounded-lg text-center hover:bg-senai-blue-dark transition"
                            onclick="return confirm('Tem certeza que deseja se inscrever este curso?')"
                            class="bg-senai-red text-white text-xs px-3 py-2 rounded-md hover:bg-red-700 transition">
                                Inscrever-se Grátis
                            </a>
                            <?php endif; ?>
                            </div>
                    </div>
                    <?php endwhile; ?>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <?php require_once("includes/footer.php");?>

</body>
</html>
