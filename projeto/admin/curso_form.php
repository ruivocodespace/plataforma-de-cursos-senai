<?php
session_start();
require_once "../includes/logado.php";
require_once "../includes/conexao.php";

// Variáveis para mensagens
$sucesso = "";
$erro = "";
$editando = NULL;


if (isset($_GET["editar"])) {
    $id = $_GET["editar"];
    $sql = "SELECT * FROM cursos WHERE id = '$id'";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST["id"] ?? null;
    $titulo  = $_POST["titulo"] ?? "";
    $descricao  = $_POST["descricao"] ?? "";
    $ativo = $_POST["ativo"] ?? 1;

    // LÓGICA DE UPLOAD DA IMAGEM DE CAPA
    // Mantém a capa antiga se estiver editando
    $nome_capa = isset($editando['capa']) ? $editando['capa'] : "";

    // Caminho da pasta de uploads
    $pasta_destino = "../uploads/cursos/";

    // Verifica se foi enviada uma nova imagem
    if (isset($_FILES['capa']) && $_FILES['capa']['size'] > 0) {

        // Pega extensão
        $extensao = strtolower(pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg','jpeg','png','webp'];

        if (!in_array($extensao, $extensoes_permitidas)) {
            $erro = "Formato de imagem inválido.";
        } else {
            // Gera nome único para evitar conflitos
            $novo_nome_imagem = uniqid() . "." . $extensao;

            // Move imagem
            if (move_uploaded_file($_FILES['capa']['tmp_name'], $pasta_destino . $novo_nome_imagem)) {
                $nome_capa = $novo_nome_imagem;
            } else {
                $erro = "Erro ao salvar a imagem.";
            }
        }
    }

    // Se não houve erro no upload
    if (empty($erro)) {

        // Verificar se já existe curso com mesmo título
        if ($id) {
            $sql_busca = "SELECT * FROM cursos WHERE titulo = '$titulo' AND id != '$id'";
        } else {
            $sql_busca = "SELECT * FROM cursos WHERE titulo = '$titulo'";
        }

        $resultado_busca = mysqli_query($conexao, $sql_busca);

        if (mysqli_num_rows($resultado_busca) > 0) {
            $erro = "Já existe um curso cadastrado com este título.";

        } else {

            if ($id) {
                // UPDATE
                $sql_salvar = "UPDATE cursos SET 
                               titulo = '$titulo', 
                               descricao = '$descricao',
                               capa = '$nome_capa',
                               ativo = '$ativo'
                               WHERE id = '$id'";

            } else {
                // INSERT
                $sql_salvar = "INSERT INTO cursos (titulo, descricao, capa, ativo) 
                VALUES ('$titulo', '$descricao', '$nome_capa', '$ativo')";
            }

            if (mysqli_query($conexao, $sql_salvar)) {
                header("Location: cursos.php");
                exit;

            } else {
                $erro = "Erro ao salvar no banco: " . mysqli_error($conexao);

            }
        }
    }
}

$modulos = [];

if($editando){
    $curso_id = $editando["id"];

    $sqlModulos = "
    SELECT 
    m.id,
    m.titulo,
    COUNT(a.id) as total_aulas

    FROM modulos m

    LEFT JOIN aulas a
    ON a.modulo_id = m.id

    WHERE m.curso_id = $curso_id

    GROUP BY m.id
    ORDER BY m.id
    ";

    $resModulos = mysqli_query($conexao,$sqlModulos);

    while($m = mysqli_fetch_assoc($resModulos)){
        $modulos[] = $m;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso — Admin | EAD SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { senai: { red:'#C0392B', blue:'#34679A', 'blue-dark':'#2C5A85', orange:'#E67E22', green:'#27AE60' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .nav-link { display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:6px; font-size:13px; cursor:pointer; transition:background .15s; color:#cbd5e1; }
        .nav-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .nav-link.active { background:rgba(255,255,255,.15); color:#fff; font-weight:600; }
        .form-input { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:10px 14px; font-size:14px; outline:none; transition:border .15s, box-shadow .15s; }
        .form-input:focus { border-color:#34679A; box-shadow: 0 0 0 3px rgba(52,103,154,.15); }
        .form-label { display:block; font-size:12px; font-weight:600; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!--SIDEBAR -->
    <?php
    require_once "includes/menu.php";
    ?>

    <!-- CONTEÚDO -->
    <main class="flex-1 flex flex-col">
        <!-- TOPBAR -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="cursos.php" class="hover:text-senai-blue">Cursos</a>
                <span>›</span>
                <span class="text-gray-700 font-semibold"><?= $editando ? "Editar Curso" : "Cadastrar Novo Curso"; ?></span>
            </div>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-extrabold text-gray-800"><?= $editando ? "Editar Curso" : "Cadastrar Novo Curso"; ?></h1>
                <a href="cursos.php" class="text-sm text-gray-500 hover:text-senai-blue flex items-center gap-1 transition">← Voltar para Cursos</a>
            </div>
        </div>

        <div class="p-6 flex-1">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- FORMULÁRIO PRINCIPAL -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6">

                        <form action="curso_form.php" method="post" enctype="multipart/form-data">
                            <!-- Campo oculto: id do curso (edição) -->
                            <input type="hidden" name="id" value="<?= $editando ? $editando['id'] : '' ?>">

                            <!-- TÍTULO -->
                            <div class="mb-5">
                                <label class="form-label">Título do Curso *</label>
                                <input
                                    type="text"
                                    name="titulo"
                                    class="form-input"
                                    placeholder="Ex: HTML e CSS do Zero"
                                    value="<?= $editando ? $editando['titulo'] : '' ?>"
                                >
                                <p class="text-xs text-gray-400 mt-1">Use um título claro e direto. Máx. 150 caracteres.</p>
                            </div>

                            <!-- DESCRIÇÃO -->
                            <div class="mb-5">
                                <label class="form-label">Descrição *</label>
                                <textarea
                                    name="descricao"
                                    rows="4"
                                    class="form-input resize-none"
                                    placeholder="Descreva o curso, o que o aluno vai aprender..."
                                ><?= $editando ? $editando['descricao'] : '' ?></textarea>
                                <p class="text-xs text-gray-400 mt-1">Seja claro sobre o conteúdo e o público-alvo do curso.</p>
                            </div>

                            <!-- IMAGEM DE CAPA -->
                            <div class="mb-5">
                                <label class="form-label">Imagem de Capa</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-senai-blue transition cursor-pointer bg-gray-50">
                                    <!-- Preview da capa atual -->
                                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-32 h-20 rounded-lg mx-auto mb-3 flex items-center justify-center">
                                        <span class="text-3xl">🌐</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">Capa atual. Clique para alterar.</p>
                                    <input type="file" name="capa" accept="image/*" class="hidden" id="input-capa">
                                    <label for="input-capa" class="bg-white border border-gray-300 text-gray-600 text-xs font-semibold px-4 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                        Selecionar nova imagem
                                    </label>
                                    <p class="text-xs text-gray-400 mt-2">PNG, JPG ou WEBP. Máx. 2MB. Proporção recomendada: 16:9</p>
                                </div>
                            </div>

                            <!-- STATUS -->
                            <div class="mb-6">
                                <label class="form-label">Status do Curso</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="ativo" value="1" <?= $editando && $editando['ativo'] ? 'checked' : '' ?> class="accent-senai-green">
                                        <span class="text-sm text-gray-700">Ativo — Visível para os alunos</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="ativo" value="0" <?= $editando && !$editando['ativo'] ? 'checked' : '' ?> class="accent-gray-400">
                                        <span class="text-sm text-gray-500">Inativo — Oculto para os alunos</span>
                                    </label>
                                </div>
                            </div>

                            <!-- BOTÕES -->
                            <div class="flex gap-3 pt-2 border-t border-gray-100">
                                <button type="submit" class="bg-senai-blue hover:bg-senai-blue-dark text-white font-bold px-6 py-2.5 rounded-lg text-sm transition flex items-center gap-2">
                                    💾 Salvar Alterações
                                </button>
                                <a href="cursos.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                                    Cancelar
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- PAINEL LATERAL -->
                <div class="space-y-4">
                    <?php if($editando): ?>
                    <div class="space-y-4">
                        <div class="bg-white rounded-xl shadow-sm p-5">
                            <h3 class="font-bold text-gray-700 text-sm mb-3">Módulos deste Curso</h3>
                            <ul class="space-y-2 text-sm">
                                <?php if(empty($modulos)): ?>
                                    <p class="text-xs text-gray-400">Nenhum módulo cadastrado.</p>
                                <?php else: ?>

                                <?php foreach($modulos as $m): ?>
                                    <li class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                        <span class="text-gray-700">
                                            <?= $m["titulo"] ?>
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <?= $m["total_aulas"] ?> aulas
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                            <a href="modulos.php?curso_id=<?=$editando['id']?>"
                            class="block mt-3 text-center border border-senai-blue text-senai-blue text-xs font-semibold py-2 rounded-lg hover:bg-blue-50 transition">
                            Gerenciar Módulos
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    

                    <!-- Dicas -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="font-bold text-senai-blue text-sm mb-2">💡 Dicas</h4>
                        <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4">
                            <li>Use títulos claros e atrativos</li>
                            <li>A capa deve ter boa resolução (min. 800×450px)</li>
                            <li>Cursos inativos não aparecem para alunos</li>
                            <li>Cadastre os módulos após criar o curso</li>
                        </ul>
                    </div>

                    <!-- Aviso exclusão -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <h4 class="font-bold text-senai-red text-sm mb-2">⚠ Zona de Perigo</h4>
                        <p class="text-xs text-gray-600 mb-3">Excluir o curso também remove todos os módulos, aulas e inscrições vinculadas.</p>
                        <?php if ($editando) { ?>
                        <a href="curso_delete.php?id=<?php echo $editando['id']; ?>"
                        onclick="return confirm('Tem certeza que deseja excluir este curso?')"
                        class="bg-senai-red text-white text-xs px-3 py-2 rounded-md hover:bg-red-700 transition">
                        🗑 Excluir curso
                        </a>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </main>

</body>
</html>
