<?php
session_start();
require_once "../includes/logado_admin.php";
require_once "../includes/conexao.php";

// Variáveis para mensagens e controle
$sucesso = "";
$erro = "";
$editando = null;
$id_edicao = null; // Variável dedicada para guardar o ID que estamos editando

// Verifica se estamos editando uma aula (se veio ?editar=ID na URL)
if (isset($_GET["editar"])) {
    $id_edicao = $_GET["editar"];
    $sql = "SELECT * FROM aulas WHERE id = '$id_edicao'";
    $res = mysqli_query($conexao, $sql);

    // Se encontrou a aula, guarda os dados em $editando para preencher o formulário. Se não, mostra erro.
    if (mysqli_num_rows($res) > 0) {
        $editando = mysqli_fetch_assoc($res);
    } else {
        $erro = "Aula não encontrada!";
    }
}

// Verifica se o formulário foi submetido via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Captura os dados do formulário
    $id_post   = $_POST["id"]; // Pega o ID que veio escondido no form
    $modulo_id = $_POST["modulo_id"];
    $titulo    = $_POST["titulo"];
    $video_url = $_POST["video_url"];
    $duracao   = $_POST["duracao"];
    $descricao = $_POST["descricao"];
    $ordem     = $_POST["ordem"];

    // Verifica se já existe OUTRA aula com este mesmo título
    // Usa != para ignorar a própria aula que estamos editando
    $sql_verifica = "SELECT * FROM aulas WHERE titulo = '$titulo' AND id != '$id_post'";
    $resultado = mysqli_query($conexao, $sql_verifica);

    if (mysqli_num_rows($resultado) > 0) {
        $erro = "Já existe outra aula cadastrada com este título.";

        // Mantém os dados no formulário para o usuário não perder o que digitou
        $editando = $_POST;
        $editando['id'] = $id_post;
    } else {
        // Se existe ID no POST, faz UPDATE. Se não, faz INSERT.
        if (!empty($id_post)) {
            $sql = "UPDATE aulas SET
                    modulo_id = '$modulo_id',
                    titulo = '$titulo',
                    video_url = '$video_url',
                    duracao = '$duracao',
                    descricao = '$descricao',
                    ordem = '$ordem'
                    WHERE id = '$id_post'";

            $sucesso = "Aula atualizada com sucesso!";
        } else {
            $sql = "INSERT INTO aulas (modulo_id, titulo, video_url, duracao, descricao, ordem) VALUES 
                    ('$modulo_id', '$titulo', '$video_url', '$duracao', '$descricao', '$ordem')";

            $sucesso = "Aula cadastrada com sucesso!";
        }

        // Executa a query
        if (mysqli_query($conexao, $sql)) {
            $sucesso;

            // Se foi UPDATE, busca os dados novos para atualizar a tela
            if (!empty($id_post)) {
                $res_novo = mysqli_query($conexao, "SELECT * FROM aulas WHERE id = '$id_post'");
                $editando = mysqli_fetch_assoc($res_novo);
            }
            // Se não mostra mensagem de erro
        } else {
            $erro = "Erro ao salvar aula no banco de dados.";
        }
    }
}

// Busca todos os módulos com select para preencher o dropdown do formulário
$sqlModulos = "SELECT * FROM modulos ORDER BY titulo";
$resultadoModulos = mysqli_query($conexao, $sqlModulos);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($editando) ? 'Editar' : 'Nova' ?> Aula — Admin | EAD SENAI</title>
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

    <!--SIDEBAR + TOPBAR -->
    <?php
    require_once "includes/menu.php";
    ?>

    <main class="flex-1 flex flex-col">
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

        <!-- Breadcrumbs e título -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                <a href="aulas.php" class="hover:text-senai-blue">Aulas</a> ›
                <span class="text-gray-700 font-semibold">Editar Aula</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Editar Aula</h1>
        </div>

        <!-- Formulário de cadastro/edição -->
        <div class="p-6 flex-1 max-w-xl">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="aula_form.php" method="post">
                    <input type="hidden" value="<?= $editando['id'] ?? "" ?>" name="id" />
                    <div class="mb-4">
                        <label class="form-label">Módulo *</label>
                        <select name="modulo_id" class="form-input">
                            <?php while ($modulo = mysqli_fetch_assoc($resultadoModulos)): ?>
                                <option value="<?php echo $modulo['id']; ?>"
                                    <?php if ($editando && $editando['modulo_id'] == $modulo['id']) echo "selected"; ?>>
                                    <?php echo $modulo['titulo']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Título da Aula *</label>
                        <input type="text" name="titulo" value="<?= $editando['titulo'] ?? "" ?>" class="form-input" value="Tags Essenciais do HTML">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">URL do Vídeo (embed)</label>
                        <input type="url" name="video_url" value="<?= $editando['video_url'] ?? "" ?>" class="form-input" value="https://www.youtube.com/embed/exemplo3" placeholder="https://www.youtube.com/embed/...">
                        <p class="text-xs text-gray-400 mt-1">Use a URL de incorporação do YouTube ou Vimeo.</p>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Duração</label>
                        <input type="text" name="duracao" value="<?= $editando['duracao'] ?? "" ?>" class="form-input" value="15:10" placeholder="Ex: 15:10">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descrição (opcional)</label>
                        <textarea name="descricao" rows="4" class="form-input resize-none" <?= $editando['descricao'] ?? "" ?>></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" value="<?= $editando['ordem'] ?? "" ?>" class="form-input" min="1">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar Aula</button>
                        <a href="aulas.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>