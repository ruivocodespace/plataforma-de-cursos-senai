<?php
session_start();

require_once "includes/conexao.php";
// Variável para armazenar mensagem de erro
$erro = "";

// Verificar se o formulário foi enviado (method POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Receber os dados do formulário
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Buscar o usuário no banco pelo email
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);

    // Verificar se encontrou o usuário
    if ($usuario = mysqli_fetch_assoc($resultado)) {

        // Verificar se a senha está correta
        if (password_verify($senha, $usuario["senha"])) {
            // Guardar dados do usuário na sessão
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            $_SESSION["usuario_email"] = $usuario["email"];
            $_SESSION["usuario_tipo"] = $usuario["tipo"];

            if($_SESSION["usuario_tipo"] == 'admin'){
                // Redirecionar para o dashboard
                header("Location: admin/index.php");
                exit;
            }elseif($_SESSION["usuario_tipo"] == 'aluno'){
                // Redirecionar para o dashboard
                header("Location: meus_cursos.php");
                exit;
            }
        } else {
            $erro = "Email ou senha incorretos.";
        }
    } else {
        $erro = "Email ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — EAD SENAI</title>
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

    <!-- NAVBAR MÍNIMA -->
    <nav class="bg-senai-blue shadow-md">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-white font-extrabold text-lg">
                🎓 <span>EAD SENAI</span>
            </a>
            <a href="cadastro.php" class="text-blue-200 hover:text-white text-sm transition">
                Não tem conta? <span class="underline font-semibold">Cadastre-se</span>
            </a>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <!-- CARD DE LOGIN -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

                <!-- Topo colorido -->
                <div class="bg-senai-blue px-8 py-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-3xl">🔐</span>
                    </div>
                    <h1 class="text-white font-extrabold text-xl">Entrar na Plataforma</h1>
                    <p class="text-blue-200 text-sm mt-1">Informe suas credenciais para acessar</p>
                </div>

                <div class="px-8 py-6">
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

                    <!-- FORMULÁRIO -->
                    <form action="login.php" method="post">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">E-mail</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">✉</span>
                                <input
                                    name="email"
                                    type="email"
                                    placeholder="seu@email.com"
                                    class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-senai-blue focus:border-transparent"
                                >
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Senha</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔒</span>
                                <input
                                    name="senha"
                                    type="password"
                                    placeholder="••••••••"
                                    class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-senai-blue focus:border-transparent"
                                >
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-senai-blue hover:bg-senai-blue-dark text-white font-bold py-3 rounded-lg transition text-sm">
                            Entrar na Plataforma
                        </button>
                    </form>

                    <div class="relative my-5">
                        <div class="border-t border-gray-200"></div>
                        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3 text-xs text-gray-400">ou</span>
                    </div>

                    <a href="cadastro.php" class="block w-full border-2 border-senai-blue text-senai-blue font-bold py-2.5 rounded-lg text-sm text-center hover:bg-blue-50 transition">
                        Criar nova conta
                    </a>

                </div>
            </div>

            <!-- INFO ADMIN -->
            <div class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-3 text-xs text-gray-600 text-center">
                <strong>Admin?</strong> Use <span class="font-mono bg-white px-1 rounded">admin@ead.com</span> /
                <span class="font-mono bg-white px-1 rounded">admin123</span> →
                <a href="admin/login.php" class="text-senai-blue underline font-semibold">Painel Admin</a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-5">
                <a href="index.php" class="hover:text-senai-blue transition">← Voltar à página inicial</a>
            </p>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-senai-blue text-blue-200 text-center text-xs py-3">
        SENAI — Sistema EAD &nbsp;|&nbsp; Todos os direitos reservados
    </footer>

</body>
</html>
