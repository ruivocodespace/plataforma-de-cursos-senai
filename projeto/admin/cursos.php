<?php

session_start();
require_once '../includes/logado_admin.php';
require_once '../includes/conexao.php';

$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['usuario_email'];

// BUSCAR CURSOS
$sqlCursos = "
SELECT 
c.id,
c.titulo,
c.descricao,
c.ativo,
c.criado_em,

COUNT(DISTINCT m.id) AS total_modulos,
COUNT(DISTINCT a.id) AS total_aulas,
COUNT(DISTINCT i.id) AS total_inscricoes

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
    <title>Gerenciar Cursos — Admin | EAD SENAI</title>
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

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: background .15s;
            color: #cbd5e1;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, .15);
            color: #fff;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-56 bg-gray-900 min-h-screen flex flex-col flex-shrink-0">
        <div class="px-4 py-5 border-b border-gray-700">
            <p class="text-white font-extrabold text-base">🎓 EAD SENAI</p>
            <p class="text-gray-500 text-xs mt-0.5">Painel Administrativo</p>
        </div>
        <div class="px-4 py-3 border-b border-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-xs font-bold">A</div>
                <div>
                    <p class="text-white text-xs font-semibold"><?= htmlspecialchars($nome) ?></p>
                    <p class="text-gray-500 text-xs"><?= htmlspecialchars($email) ?></p>
                </div>
            </div>
        </div>
        <nav class="flex-1 p-3 space-y-1">
            <a href="index.php" class="nav-link">📊 <span>Dashboard</span></a>
            <a href="cursos.php" class="nav-link active">📚 <span>Cursos</span></a>
            <a href="modulos.php" class="nav-link">📦 <span>Módulos</span></a>
            <a href="aulas.php" class="nav-link">🎬 <span>Aulas</span></a>
            <div class="pt-2 border-t border-gray-700 mt-2">
                <a href="../meus_cursos.php" class="nav-link">👁 <span>Ver site</span></a>
                <a href="../login.php" class="nav-link text-red-400 hover:text-red-300">🚪 <span>Sair</span></a>
            </div>
        </nav>
    </aside>

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

            <!-- MENSAGEM DE SUCESSO
            <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">✓</span>
                <span></span>
                <button class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
            </div> -->

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
                        <?php while ($u = mysqli_fetch_assoc($resultCursos)): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <!-- ID -->
                                <td class="px-4 py-3">
                                    <?php echo $u['id']; ?>
                                </td>

                                <!-- Titulo -->
                                <td class="px-4 py-3">
                                    <?php echo $u['titulo']; ?>
                                </td>

                                <!-- Modulos -->
                                <td class="px-4 py-3 text-center">
                                    <?php echo $u['total_modulos']; ?>
                                </td>

                                <!-- Aulas -->
                                <td class="px-4 py-3 text-center">
                                    <?php echo $u['total_aulas']; ?>
                                </td>

                                <!-- Inscrições -->
                                <td class="px-4 py-3 text-center">
                                    <?php echo $u['total_inscricoes']; ?>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3 text-center">
                                    <?php if ($u['ativo']): ?>
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                            Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                                            Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Data -->
                                <td class="px-4 py-3 text-center">
                                    <?php echo date('d/m/Y', strtotime($u['criado_em'])); ?>
                                </td>

                                <!-- Ações -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <a href="modulos.php?curso_id=<?php echo $u['id']; ?>"
                                            class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition">
                                            📦 Módulos
                                        </a>

                                        <a href="curso_form.php?editar=<?php echo $u['id']; ?>"
                                            class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition">
                                            ✏ Editar
                                        </a>

                                        <a href="curso_delete.php?id=<?php echo $u['id']; ?>"
                                            onclick="return confirm('Excluir este curso?')"
                                            class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition">
                                            🗑
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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