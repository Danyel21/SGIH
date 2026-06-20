<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilizadores - SGI Hospitalar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-800 text-white flex-shrink-0">
            <div class="p-4 text-xl font-bold border-b border-blue-700">SGI Hospitalar</div>
            <nav class="mt-4">
                <a href="../../dashboard/dashboard_admin.html" class="block py-2.5 px-4 bg-blue-900 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="../../views/equipamentos/equipamentos_lista.html" class="block py-2.5 px-4 hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-microscope mr-2"></i> Equipamentos
                </a>
                <a href="../../views/manutencao/manutencao_lista.html" class="block py-2.5 px-4 hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-tools mr-2"></i> Manutenção
                </a>
                <a href="../../views/fornecedor/fornecedores_lista.html" class="block py-2.5 px-4 hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-truck mr-2"></i> Fornecedores
                </a>
                <a href="../../views/localizacao/localizacoes_lista.html" class="block py-2.5 px-4 hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-map-marker-alt mr-2"></i> Localizações
                </a>
                <a href="../../views/garantias/garantias_lista.html" class="block py-2.5 px-4 hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-shield-alt mr-2"></i> Garantias
                </a>
                <div class="border-t border-blue-700 my-2"></div>
                <a href="../../views/funcionario/utilizadores_lista.html" class="block py-2.5 px-4 hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-users mr-2"></i> Utilizadores
                </a>
                <a href="../../login.html" class="block py-2.5 px-4 hover:bg-red-600 transition duration-200 mt-10">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                </a>
            </nav>
        </aside>


        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
          <header>
            <div class="header-title">
                <h1>Lista de Utilizadores</h1>
                <p>Gerencie os utilizadores do hospital</p>
            </div>
        <div class="dropdown user-profile">
            <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">JD</div>
                <span style="font-weight: 600; font-size: 0.875rem;">João Duarte</span>
                <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--text-muted);"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>
        </header>

            <div class="p-6">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Função</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">João Silva</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">joao.silva@hospital.pt</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Administrador</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">TI / Engenharia Clínica</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Ativo</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-edit"></i></button>
                                    <button class="text-red-600 hover:text-red-900"><i class="fas fa-user-slash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
