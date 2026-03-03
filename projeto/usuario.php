<?php
session_start();
require_once "conexao.php";
require_once "includes/menu.php";

$sucesso = "";
$erro = "";
$editando = NULL;

?>
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Usuarios</h1>
                <p class="text-sm text-gray-500">Cadastre, edite e organize os usuarios da plataforma</p>
            </div>
            <div>
                <a href="usuario_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2" style="margin: 10px;">
                    + Novo Admin
                </a>
            </div>
        </div>

        <div class="p-6 flex-1">
            <!-- TABELA DE USUARIOS -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-center">Email</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php 
                        $sql = "SELECT id, nome, email, tipo FROM usuarios ORDER BY id DESC";
                        $usuario = mysqli_query($conexao, $sql);
                        while ($u = mysqli_fetch_assoc($usuario)): 
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?=$u["nome"];?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?=$u["email"];?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?=$u["tipo"];?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="modulos.html" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver Módulos">📦 Módulos</a>
                                    <a href="usuario_form.php?editar=<?=$u["id"]; ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏ Editar</a>
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?=$u["id"]; ?>">Excluir</a>
                                </div>
                            </td>
                        </tr>

                        <?php endwhile; ?>
                     

                    </tbody>
                </table>

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