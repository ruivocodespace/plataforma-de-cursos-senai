<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

$nome = $_SESSION["usuario_nome"];
$email = $_SESSION["usuario_email"];

// BUSCAR USUÁRIOS
$sqlUsuarios = "
SELECT 
    id,
    nome,
    email,
    tipo,
    criado_em
FROM usuarios 
ORDER BY id DESC
";

$resultUsuarios = mysqli_query($conexao, $sqlUsuarios);
$totalUsuarios = mysqli_num_rows($resultUsuarios);

// INICIALIZAR E CAPTURAR ALERTAS
$erro = "";
$sucesso = "";

if (isset($_GET["erro"])) {
    // Pega o texto exato que veio na URL, ou exibe um erro genérico
    $erro = $_GET["erro"] == "1" ? "Ocorreu um erro na operação." : htmlspecialchars($_GET["erro"]);
}

if (isset($_GET["sucesso"])) {
    $sucesso = $_GET["sucesso"] == "1" ? "Usuário excluído com sucesso!" : htmlspecialchars($_GET["sucesso"]);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários — Admin | EAD SENAI</title>
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

    <?php
    require_once "includes/menu.php";
    ?>

    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Usuários</h1>
                <p class="text-sm text-gray-500">Cadastre, edite e organize os usuários da plataforma</p>
            </div>
            <a href="usuario_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Usuário
            </a>
        </div>

        <div class="p-6 flex-1">

            <?php if (!empty($sucesso)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo $sucesso; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">#</th>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-left">E-mail</th>
                            <th class="px-4 py-3 text-center">Nível de Acesso</th>
                            <th class="px-4 py-3 text-center">Cadastrado em</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <?php while ($u = mysqli_fetch_assoc($resultUsuarios)): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <?php echo $u["id"]; ?>
                                </td>

                                <td class="px-4 py-3 font-medium text-gray-800">
                                    <?php echo $u["nome"]; ?>
                                </td>

                                <td class="px-4 py-3 text-gray-500">
                                    <?php echo $u["email"]; ?>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <?php if ($u["tipo"] === 'admin'): ?>
                                        <span class="bg-blue-100 text-senai-blue font-semibold px-2 py-1 rounded text-xs">
                                            Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-600 font-semibold px-2 py-1 rounded text-xs">
                                            Aluno
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3 text-center text-gray-500">
                                    <?php echo isset($u["criado_em"]) ? date("d/m/Y", strtotime($u["criado_em"])) : '--/--/----'; ?>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="usuario_form.php?editar=<?php echo $u["id"]; ?>"
                                            class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition">
                                            ✏ Editar
                                        </a>

                                        <a href="usuario_delete.php?id=<?php echo $u["id"]; ?>"
                                            onclick="return confirm('Deseja realmente excluir este usuário?')"
                                            class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition">
                                            🗑 Excluir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?php echo $totalUsuarios; ?> usuário(s)</p>
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