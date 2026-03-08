    <!-- SIDEBAR ADMIN -->
    <aside class="w-56 bg-gray-900 min-h-screen flex flex-col flex-shrink-0">
        <!-- Logo -->
        <div class="px-4 py-5 border-b border-gray-700">
            <p class="text-white font-extrabold text-base">🎓 EAD SENAI</p>
            <p class="text-gray-500 text-xs mt-0.5">Painel Administrativo</p>
        </div>
        <!-- Info admin -->
        <div class="px-4 py-3 border-b border-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-xs font-bold">A</div>
                <div>
                    <p class="text-white text-xs font-semibold"><?= htmlspecialchars($_SESSION["usuario_nome"]) ?></p>
                    <p class="text-gray-500 text-xs"><?= htmlspecialchars($_SESSION["usuario_email"]) ?></p>
                </div>
            </div>
        </div>
        <!-- Menu -->
        <nav class="flex-1 p-3 space-y-1">
            <a href="index.php"      class="nav-link active">📊 <span>Dashboard</span></a>
            <a href="cursos.php"     class="nav-link">📚 <span>Cursos</span></a>
            <a href="modulos.php"    class="nav-link">📦 <span>Módulos</span></a>
            <a href="aulas.php"      class="nav-link">🎬 <span>Aulas</span></a>
            <div class="pt-2 border-t border-gray-700 mt-2">
                <a href="../meus_cursos.php" class="nav-link">👁 <span>Ver site</span></a>
                <a href="../index.php"       class="nav-link text-red-400 hover:text-red-300">🚪 <span>Sair</span></a>
            </div>
        </nav>
    </aside>