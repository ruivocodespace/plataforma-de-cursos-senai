<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

// Variáveis para mensagens
$sucesso = "";
$erro = "";
$editando = NULL;
$id = "";

// Buscar dados se estiver editando
if (isset($_GET["editar"])) {
    $id = $_GET["editar"];
    $sql = "SELECT * FROM usuarios WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id    = $_POST["id"];
    $nome  = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $tipo  = $_POST["tipo"];

    // Verificar se o email já existe (ignorando o próprio usuário se for edição)
    $sql_verifica = "SELECT * FROM usuarios WHERE email = '$email'";
    if (!empty($id)) {
        $sql_verifica .= " AND id != $id";
    }

    $resultado = mysqli_query($conexao, $sql_verifica);

    if (mysqli_num_rows($resultado) > 0) {
        $erro = "Este email já está cadastrado por outro usuário.";
    } else {
        // ATUALIZAR (UPDATE)
        if ($id) {
            if (!empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', senha = '$senhaHash', tipo = '$tipo' WHERE id = $id";
            } else {
                $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', tipo = '$tipo' WHERE id = $id";
            }
            $sucesso = "Usuário atualizado com sucesso!";

            // Atualiza os dados na tela
            $editando['nome'] = $nome;
            $editando['email'] = $email;
            $editando['tipo'] = $tipo;

            // CADASTRAR (INSERT)
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senhaHash', '$tipo')";
            $sucesso = "Usuário cadastrado com sucesso!";
        }

        if (!mysqli_query($conexao, $sql)) {
            $erro = "Erro ao salvar usuário.";
            $sucesso = "";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editando ? 'Editar Usuário' : 'Adicionar Usuário' ?> — Admin | EAD SENAI</title>
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

        .form-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            outline: none;
            transition: border .15s;
        }

        .form-input:focus {
            border-color: #34679A;
            box-shadow: 0 0 0 3px rgba(52, 103, 154, .15);
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <?php
    require_once "includes/menu.php";
    ?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="usuarios.php" class="hover:text-senai-blue">Usuários</a> ›
                <span class="text-gray-700 font-semibold"><?= $editando ? 'Editar Usuário' : 'Adicionar Usuário' ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?= $editando ? 'Editar Usuário' : 'Adicionar Usuário' ?></h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">
            <div class="bg-white rounded-xl shadow-sm p-6">

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

                <form action="usuario_form.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="mb-4">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" name="nome" class="form-input" required value="<?= $editando ? $editando['nome'] : '' ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" required value="<?= $editando ? $editando['email'] : '' ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nível de Acesso *</label>
                        <select name="tipo" class="form-input" required>
                            <?php $tipoAtual = $editando ? $editando['tipo'] : 'aluno'; ?>
                            <option value="aluno" <?= $tipoAtual == 'aluno' ? 'selected' : '' ?>>Aluno</option>
                            <option value="admin" <?= $tipoAtual == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Senha <?= !$editando ? '*' : '(Deixe em branco para manter a atual)' ?></label>
                        <input type="password" name="senha" class="form-input" <?= !$editando ? 'required' : '' ?> placeholder="<?= $editando ? 'Nova senha...' : 'Digite a senha...' ?>">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="usuarios.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>