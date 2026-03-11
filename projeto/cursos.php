<?php  
session_start();
// Incluir o arquivo de conexão com o banco
require_once "includes/logado.php";
require_once "includes/conexao.php";

$nome = $_SESSION["usuario_nome"];

// Busca TODOS os cursos, modulos e aulas cadastrados
$sql_cursos = "
SELECT 
    c.*,
    
    (SELECT COUNT(*) 
     FROM modulos m 
     WHERE m.curso_id = c.id) AS total_modulos,

    (SELECT COUNT(*) 
     FROM aulas a
     JOIN modulos m ON m.id = a.modulo_id
     WHERE m.curso_id = c.id) AS total_aulas

FROM cursos c
ORDER BY c.id DESC
";

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

    <!-- MENSAGEM DE SUCESSO (após inscrição) 
    <div class="max-w-6xl mx-auto px-6 pt-5">
        <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 flex items-center gap-2 text-sm">
            <span class="font-bold text-lg">✓</span>
            <span>Inscrição realizada com sucesso! Acesse <a href="meus_cursos.php" class="underline font-semibold">Meus Cursos</a> para começar.</span>
        </div>
    </div> -->

    <!-- GRADE DE CURSOS -->
    
    <main class="max-w-6xl mx-auto px-6 py-8 flex-1"> 
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php while ($curso = mysqli_fetch_assoc($resultado_cursos)): ?>
            <?php
            $usuario_id = $_SESSION["usuario_id"];

            $sql_inscrito = "
            SELECT id 
            FROM inscricoes 
            WHERE usuario_id = '$usuario_id'
            AND curso_id = '".$curso["id"]."'
            ";

            $result_inscrito = mysqli_query($conexao,$sql_inscrito);

            $inscrito = mysqli_num_rows($result_inscrito) > 0;
            ?>

            <!-- CURSO-->
            <div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden flex flex-col border-2 border-green-400">
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 h-40 flex items-center justify-center">
                        <span class="text-6xl">🌐</span>
                    </div>

                    <?php if($inscrito): ?>
                        <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        ✓ Inscrito
                        </span>
                    <?php endif; ?>

                </div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded"><?php echo $curso[$titulo];?></span>
                        <span class="text-xs text-gray-400"><?php echo $curso[$total_modulos];?> módulos · <?php echo $curso[$total_aulas];?> aulas </span>
                    </div>
                    <h3 class="font-bold text-gray-800 text-base mb-2"><?php echo htmlspecialchars($curso["titulo"]); ?></h3>
                    <p class="text-sm text-gray-500 mb-4 flex-1"><?php echo htmlspecialchars($curso["descricao"]);?></p>

                    <!-- Progresso -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Seu progresso</span>
                            <span class="text-senai-green font-semibold">3 / 9 aulas</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-senai-green h-2 rounded-full" style="width:33%"></div>
                        </div>
                    </div>
                    <?php if($inscrito): ?>
                        <a href="curso.php?id=<?php echo $curso["id"]; ?>" 
                        class="bg-senai-green text-white text-sm font-semibold py-2.5 rounded-lg text-center hover:bg-green-600 transition">
                        Continuar Curso →
                        </a>
                        <?php else: ?>

                        <a href="inscrever.php?curso_id=<?php echo $curso["id"]; ?>" 
                        class="bg-senai-blue text-white text-sm font-semibold py-2.5 rounded-lg text-center hover:bg-senai-blue-dark transition">
                        Inscrever-se Grátis
                        </a>
                        <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        
    </main>

    <!-- FOOTER -->
    <?php require_once("includes/footer.php");?>

</body>
</html>
