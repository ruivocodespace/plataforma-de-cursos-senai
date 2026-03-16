<?php
session_start();
require_once 'includes/logado.php';
require_once 'includes/conexao.php';

$nome = $_SESSION['usuario_nome'];
$usuario_id = $_SESSION['usuario_id'];

// Pega o ID da URL
if (isset($_GET['id'])) {
    $aula_id = $_GET['id'];
} else {
    header('Location: meus_cursos.php');
    exit();
}

// Pega os dados da Aula atual
$sql_aula = "SELECT * FROM aulas WHERE id = $aula_id";
$resultado_aula = mysqli_query($conexao, $sql_aula);
$aula = mysqli_fetch_assoc($resultado_aula);
$modulo_id = $aula['modulo_id'];

function converterParaEmbed($url)
{
    if (empty($url)) {
        return '';
    } // Se vier vazio, retorna vazio

    // Procura o ID do YouTube
    preg_match(
        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
        $url,
        $match,
    );

    // Se achar que é YouTube, monta o embed. Se não for (ex: Vimeo ou link já em embed), devolve como estava!
    return isset($match[1]) ? 'https://www.youtube.com/embed/' . $match[1] : $url;
}

// Pega os dados do Módulo desta aula
$sql_modulo = "SELECT * FROM modulos WHERE id = $modulo_id";
$resultado_modulo = mysqli_query($conexao, $sql_modulo);
$modulo = mysqli_fetch_assoc($resultado_modulo);

$curso_id = $modulo['curso_id'];

// Pega os dados do Curso
$sql_curso = "SELECT * FROM cursos WHERE id = $curso_id";
$resultado_curso = mysqli_query($conexao, $sql_curso);
$curso = mysqli_fetch_assoc($resultado_curso);

// Verifica se já concluiu esta aula
$sql_progresso = "SELECT * FROM progresso WHERE usuario_id = $usuario_id AND aula_id = $aula_id AND concluido = 1";
$resultado_progresso = mysqli_query($conexao, $sql_progresso);

$ta_concluida = false;
$data_que_concluiu = '';

if (mysqli_num_rows($resultado_progresso) > 0) {
    $ta_concluida = true;
    $dados_progresso = mysqli_fetch_assoc($resultado_progresso);
    $data_que_concluiu = $dados_progresso['data_conclusao'];
}

// =================================
// PRÓXIMA AULA E ANTERIOR
// =================================
$sql_proxima = "SELECT a.id FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = $curso_id AND a.id > $aula_id ORDER BY a.id ASC LIMIT 1";
$resultado_proxima = mysqli_query($conexao, $sql_proxima);
$tem_proxima = false;
$proxima_id = '';
if (mysqli_num_rows($resultado_proxima) > 0) {
    $tem_proxima = true;
    $proxima_id = mysqli_fetch_assoc($resultado_proxima)['id'];
}

$sql_anterior = "SELECT a.id FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = $curso_id AND a.id < $aula_id ORDER BY a.id DESC LIMIT 1";
$resultado_anterior = mysqli_query($conexao, $sql_anterior);
$tem_anterior = false;
$anterior_id = '';
if (mysqli_num_rows($resultado_anterior) > 0) {
    $tem_anterior = true;
    $anterior_id = mysqli_fetch_assoc($resultado_anterior)['id'];
}

// ==============================
// CÁLCULO DA BARRA DE PROGRESSO
// ==============================
// Conta o total de aulas deste curso
$sql_total = "SELECT COUNT(a.id) as total FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = $curso_id";
$res_total = mysqli_query($conexao, $sql_total);
$total_aulas_curso = mysqli_fetch_assoc($res_total)['total'];

// Conta quantas aulas o usuário concluiu neste curso
$sql_conc = "SELECT COUNT(p.id) as concluidas FROM progresso p INNER JOIN aulas a ON p.aula_id = a.id INNER JOIN modulos m ON a.modulo_id = m.id WHERE p.usuario_id = $usuario_id AND m.curso_id = $curso_id AND p.concluido = 1";
$res_conc = mysqli_query($conexao, $sql_conc);
$aulas_concluidas = mysqli_fetch_assoc($res_conc)['concluidas'];

// Calcula a porcentagem final
$porcentagem = $total_aulas_curso > 0 ? round(($aulas_concluidas / $total_aulas_curso) * 100) : 0;
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($curso['titulo']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        senai: {
                            red: '#C0392B',
                            blue: '#34679A',
                            'blue-dark': '#2C5A85',
                            orange: '#E67E22',
                            green: '#27AE60'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 min-h-screen flex flex-col">

    <!-- NAVBAR ESCURA (modo aula) -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-4">
            <a href="index.php" class="flex items-center gap-1.5 text-white font-extrabold text-base">🎓 EAD SENAI</a>
            <span class="text-gray-600">/</span>
            <a href="curso.php?id=<?= $curso_id ?>" class="text-gray-400 hover:text-white text-sm transition"><?= $curso['titulo'] ?></a>
            <span class="text-gray-600">/</span>
            <span class="text-gray-300 text-sm"><?= $modulo['titulo'] ?></span>
            <div class="flex-1"></div>
            <a href="meus_cursos.php" class="text-gray-400 hover:text-white text-xs transition">← Meus Cursos</a>
            <a href="logout.php" class="bg-senai-red text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-red-700 transition ml-2">Sair</a>
        </div>
    </nav>

    <!-- LAYOUT PRINCIPAL -->
    <div class="flex flex-1 max-w-7xl mx-auto w-full">

        <aside class="w-72 bg-gray-800 border-r border-gray-700 flex-shrink-0 overflow-y-auto hidden lg:block" style="height: calc(100vh - 44px); position: sticky; top: 44px;">
            <div class="p-4">
                <h3 class="text-white font-bold text-sm mb-1"><?= $curso['titulo'] ?></h3>

                <div class="flex items-center gap-2 mb-4">
                    <div class="flex-1 bg-gray-700 rounded-full h-1.5">
                        <div class="bg-senai-green h-1.5 rounded-full" style="width:<?= $porcentagem ?>%"></div>
                    </div>
                    <span class="text-xs text-gray-400"><?= $porcentagem ?>%</span>
                </div>

                <?php
                $sql_pega_modulos = "SELECT * FROM modulos WHERE curso_id = $curso_id ORDER BY ordem ASC";
                $lista_modulos = mysqli_query($conexao, $sql_pega_modulos);
                $numero_mod = 1;

                while ($mod = mysqli_fetch_assoc($lista_modulos)) {

                    $id_deste_modulo = $mod['id'];
                    // Se o módulo da lista for diferente do módulo da aula atual, deixa um pouco opaco
                    $opacidade = $id_deste_modulo == $modulo_id ? '' : 'opacity-60';
                    $cor_icone_mod =
                        $id_deste_modulo == $modulo_id ? 'bg-senai-blue' : 'bg-gray-600';
                ?>
                    <div class="mb-4 <?= $opacidade ?>">
                        <div class="flex items-center gap-2 text-xs font-bold text-white mb-2 uppercase tracking-wide">
                            <span class="w-5 h-5 <?= $cor_icone_mod ?> rounded-full flex items-center justify-center text-xs"><?= $numero_mod ?></span>
                            <?= $mod['titulo'] ?>
                        </div>

                        <ul class="space-y-1 pl-2">
                            <?php
                            $sql_pega_aulas = "SELECT * FROM aulas WHERE modulo_id = $id_deste_modulo ORDER BY ordem ASC";
                            $lista_aulas = mysqli_query($conexao, $sql_pega_aulas);

                            while ($aul = mysqli_fetch_assoc($lista_aulas)) {
                                $id_desta_aula = $aul['id'];
                                $duracao_txt = !empty($aul['duracao']) ? $aul['duracao'] : '--:--';

                                // Verifica status desta aula
                                $chk_aula = mysqli_query(
                                    $conexao,
                                    "SELECT id FROM progresso WHERE usuario_id = $usuario_id AND aula_id = $id_desta_aula AND concluido = 1",
                                );
                                $aula_feita = mysqli_num_rows($chk_aula) > 0;

                                if ($id_desta_aula == $aula_id) {
                                    // Aula Atual
                                    echo '<a href="aula.php?id=' .
                                        $id_desta_aula .
                                        '" class="flex items-center gap-2 py-1.5 px-2 rounded bg-senai-blue text-xs text-white cursor-pointer block">';
                                    echo '<span class="w-4 h-4 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0" style="font-size:9px">▶</span>';
                                    echo '<span class="font-semibold">' .
                                        $aul['titulo'] .
                                        '</span>';
                                    echo '<span class="ml-auto text-blue-200">' .
                                        $duracao_txt .
                                        '</span>';
                                    echo '</a>';
                                } elseif ($aula_feita) {
                                    // Aula Concluída
                                    echo '<a href="aula.php?id=' .
                                        $id_desta_aula .
                                        '" class="flex items-center gap-2 py-1.5 px-2 rounded text-xs text-green-400 cursor-pointer hover:bg-gray-700 block">';
                                    echo '<span class="w-4 h-4 bg-senai-green rounded-full flex items-center justify-center flex-shrink-0 text-white" style="font-size:9px">✓</span>';
                                    echo '<span>' . $aul['titulo'] . '</span>';
                                    echo '<span class="ml-auto text-gray-500">' .
                                        $duracao_txt .
                                        '</span>';
                                    echo '</a>';
                                } else {
                                    // Aula Pendente
                                    echo '<a href="aula.php?id=' .
                                        $id_desta_aula .
                                        '" class="flex items-center gap-2 py-1.5 px-2 rounded text-xs text-gray-400 hover:bg-gray-700 block">';
                                    echo '<span class="w-4 h-4 bg-gray-700 rounded-full flex-shrink-0"></span>';
                                    echo '<span>' . $aul['titulo'] . '</span>';
                                    echo '<span class="ml-auto text-gray-600">' .
                                        $duracao_txt .
                                        '</span>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                <?php $numero_mod++;
                }
                ?>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto">

            <div class="bg-black aspect-video flex items-center justify-center max-h-96 lg:max-h-[550px] w-full relative">
                <?php
                // Garante que temos a variável convertida (usando a função que você colocou lá no topo do PHP)
                $link_correto = converterParaEmbed($aula['video_url']);

                // Verifica se o link correto não está vazio
                if (!empty($link_correto)) { ?>
                    <iframe src="<?= $link_correto ?>" class="w-full h-full absolute inset-0 border-0" allowfullscreen></iframe>
                <?php } else { ?>
                    <div class="text-center text-gray-500 p-8">
                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-4xl text-gray-600 ml-1">▶</span>
                        </div>
                        <p class="text-sm text-gray-400">Vídeo não cadastrado</p>
                    </div>
                <?php }
                ?>
            </div>

            <div class="bg-white p-6 lg:p-8">

                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">
                                <?= $modulo['titulo'] ?>
                            </span>
                            <span class="text-gray-400 text-xs">⏱ <?= !empty($aula['duracao'])
                                                                        ? $aula['duracao']
                                                                        : '--:--' ?></span>
                        </div>
                        <h1 class="text-2xl font-extrabold text-gray-800"><?= $aula['titulo'] ?></h1>
                    </div>

                    <?php if ($ta_concluida) { ?>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0">
                            ✓ Concluída
                        </span>
                    <?php } else { ?>
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0">
                            Em andamento
                        </span>
                    <?php } ?>
                </div>

                <?php if (!empty($aula['descricao'])) { ?>
                    <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm text-gray-600 leading-relaxed">
                        <?= nl2br($aula['descricao']) ?>
                    </div>
                <?php } ?>

                <?php if ($ta_concluida) { ?>
                    <div class="mb-6 bg-green-50 border border-green-300 rounded-xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-senai-green rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold">✓</span>
                        </div>
                        <div>
                            <p class="font-bold text-green-800 text-sm">Aula concluída!</p>
                            <p class="text-xs text-green-700">Concluída em <?= $data_que_concluiu ?>.</p>
                        </div>
                    </div>
                <?php } ?>

                <div class="flex items-center gap-3 flex-wrap">
                    <?php if ($tem_anterior) { ?>
                        <a href="aula.php?id=<?= $anterior_id ?>" class="flex items-center gap-1.5 bg-gray-100 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-200 transition">
                            ← Aula Anterior
                        </a>
                    <?php } ?>

                    <form action="aula_concluida.php" method="POST" class="inline">
                        <input type="hidden" name="aula_id" value="<?= $aula_id ?>">
                        <?php if ($ta_concluida) { ?>
                            <button type="submit" class="flex items-center gap-1.5 bg-red-50 text-red-600 border border-red-200 text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-red-100 transition">
                                ✗ Desmarcar Conclusão
                            </button>
                        <?php } else { ?>
                            <button type="submit" class="flex items-center gap-1.5 bg-senai-green text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-green-600 transition shadow">
                                ✓ Marcar como Concluída
                            </button>
                        <?php } ?>
                    </form>

                    <?php if ($tem_proxima) { ?>
                        <a href="aula.php?id=<?= $proxima_id ?>" class="flex items-center gap-1.5 bg-senai-blue text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-senai-blue-dark transition ml-auto">
                            Próxima Aula →
                        </a>
                    <?php } ?>
                </div>

                <hr class="my-8 border-gray-200">

                <div>
                    <h3 class="font-bold text-gray-700 text-sm mb-3">Outras aulas deste módulo</h3>
                    <div class="space-y-2">
                        <?php
                        // Busca novamente as aulas deste módulo para a lista de baixo
                        $lista_aulas_rodape = mysqli_query(
                            $conexao,
                            "SELECT * FROM aulas WHERE modulo_id = $modulo_id ORDER BY ordem ASC",
                        );

                        while ($aul_rodape = mysqli_fetch_assoc($lista_aulas_rodape)) {
                            $id_r = $aul_rodape['id'];
                            $dur = !empty($aul_rodape['duracao'])
                                ? $aul_rodape['duracao']
                                : '--:--';

                            $chk_r = mysqli_query(
                                $conexao,
                                "SELECT id FROM progresso WHERE usuario_id = $usuario_id AND aula_id = $id_r AND concluido = 1",
                            );
                            $feita = mysqli_num_rows($chk_r) > 0;

                            if ($id_r == $aula_id) {
                                // Aula atual na lista de baixo
                                echo '<div class="flex items-center gap-3 p-3 bg-blue-50 border border-senai-blue rounded-lg">';
                                echo '<span class="w-6 h-6 bg-senai-blue rounded-full flex items-center justify-center text-white text-xs flex-shrink-0">▶</span>';
                                echo '<span class="text-sm font-semibold text-gray-800">' .
                                    $aul_rodape['titulo'] .
                                    '</span>';
                                echo '<span class="ml-auto text-xs text-gray-400">' .
                                    $dur .
                                    '</span>';
                                echo '<span class="text-xs text-senai-blue font-semibold ml-2">Atual</span>';
                                echo '</div>';
                            } elseif ($feita) {
                                // Concluída
                                echo '<div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">';
                                echo '<span class="w-6 h-6 bg-senai-green rounded-full flex items-center justify-center text-white text-xs flex-shrink-0">✓</span>';
                                echo '<span class="text-sm text-gray-600">' .
                                    $aul_rodape['titulo'] .
                                    '</span>';
                                echo '<span class="ml-auto text-xs text-gray-400">' .
                                    $dur .
                                    '</span>';
                                echo '<a href="aula.php?id=' .
                                    $id_r .
                                    '" class="text-xs text-senai-blue underline ml-2">Rever</a>';
                                echo '</div>';
                            } else {
                                // Pendente
                                echo '<div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-transparent hover:border-gray-200">';
                                echo '<span class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-xs flex-shrink-0">○</span>';
                                echo '<span class="text-sm text-gray-600">' .
                                    $aul_rodape['titulo'] .
                                    '</span>';
                                echo '<span class="ml-auto text-xs text-gray-400">' .
                                    $dur .
                                    '</span>';
                                echo '<a href="aula.php?id=' .
                                    $id_r .
                                    '" class="text-xs text-senai-blue underline ml-2">Assistir</a>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>

</html>