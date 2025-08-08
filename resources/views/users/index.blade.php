<!-- filepath: c:\Users\condo\Documents\Universidad\7mo\SW seguro\sws-projects\ventas-pry-p1\resources\views\users\index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Encabezado con botón de crear --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Listado de Usuarios</h3>
                        <div class="flex space-x-3">
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('users.trash') }}" 
                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Papelera
                                </a>
                            @endif
                            <a href="{{ route('users.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Usuario
                            </a>
                        </div>
                    </div>

                    {{-- Mensajes --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    {{-- Controles de tabla: búsqueda y paginación --}}
                    <x-table-controls 
                        :search="$search" 
                        :per-page="$perPage" 
                        :route="route('users.index')" 
                        placeholder="Buscar por nombre, email o rol..." 
                    />

                    {{-- Tabla de usuarios --}}
                    <div class="overflow-x-auto">
                        @if(session('token_message'))
                            <div class="mt-2 text-sm text-gray-600">
                                {{ session('token_message') }}
                            </div>
                        @endif

                        {{-- Mostrar token Sanctum generado --}}
                        @if(session('api_token'))
                            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">🔑 Token Sanctum Generado (Seguro - Hasheado)</h3>
                                <p class="text-sm text-blue-700 mb-3">
                                    Copia este token ahora, no se volverá a mostrar:
                                </p>
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           id="sanctum-token" 
                                           value="{{ session('api_token') }}" 
                                           readonly 
                                           class="flex-1 px-3 py-2 bg-white border border-blue-300 rounded-md text-sm font-mono text-blue-800">
                                    <button onclick="copiarToken('sanctum-token')" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                        📋 Copiar
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Mostrar token en texto plano generado --}}
                        @if(session('plain_text_token'))
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-red-800 mb-2">⚠️ Token en Texto Plano Generado (INSEGURO)</h3>
                                <p class="text-sm text-red-700 mb-3">
                                    <strong>ADVERTENCIA:</strong> Este token se almacena en texto plano y es inseguro. Solo para desarrollo/educación:
                                </p>
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           id="plain-text-token" 
                                           value="{{ session('plain_text_token') }}" 
                                           readonly 
                                           class="flex-1 px-3 py-2 bg-white border border-red-300 rounded-md text-sm font-mono text-red-800">
                                    <button onclick="copiarToken('plain-text-token')" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                        📋 Copiar
                                    </button>
                                </div>
                            </div>
                        @endif

                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rol
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha Creación
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-xs text-blue-600">(Tú)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($role->name === 'admin') bg-red-100 text-red-800
                                                    @elseif($role->name === 'secre') bg-blue-100 text-blue-800
                                                    @elseif($role->name === 'bodega') bg-green-100 text-green-800
                                                    @elseif($role->name === 'ventas') bg-purple-100 text-purple-800
                                                    @elseif($role->name === 'cliente') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                @if($user->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Activo
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactivo
                                                    </span>
                                                @endif
                                                
                                                @if(!$user->canBeDeleted())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800" 
                                                          title="{{ $user->getDeletionBlockReason() }}">
                                                        🔒 En uso
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                {{-- Botón Editar --}}
                                                @php
                                                    $canEdit = auth()->user()->hasRole('admin') || 
                                                              (auth()->user()->hasRole('secre') && !$user->hasRole('admin'));
                                                @endphp
                                                
                                                @if($canEdit)
                                                    <a href="{{ route('users.edit', $user) }}" 
                                                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Editar
                                                    </a>
                                                @endif

                                                {{-- Botón Eliminar --}}
                                                @php
                                                    $canDelete = $user->id !== auth()->id() && 
                                                               (auth()->user()->hasRole('admin') || 
                                                               (auth()->user()->hasRole('secre') && !$user->hasRole('admin')));
                                                @endphp
                                                
                                                @if($canDelete)
                                                    @if($user->canBeDeleted())
                                                        <button type="button" 
                                                                onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                            Eliminar
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="bg-gray-400 text-gray-700 font-bold py-1 px-3 rounded text-xs cursor-not-allowed"
                                                                disabled
                                                                title="{{ $user->getDeletionBlockReason() }}">
                                                            Eliminar
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('users.crearTokenAcceso', $user) }}">
                                                crear token de acceso
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            @if($search)
                                                No se encontraron usuarios que coincidan con "{{ $search }}"
                                                <div class="mt-2">
                                                    <a href="{{ route('users.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Limpiar búsqueda
                                                    </a>
                                                </div>
                                            @else
                                                No hay usuarios registrados
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Información de paginación --}}
                    <x-pagination-info :items="$users" />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar usuario -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Eliminar Usuario</h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que deseas eliminar a <span id="deleteUserName" class="font-medium"></span>?
                    </p>
                    <p class="text-sm text-yellow-600 mt-2">
                        El usuario será enviado a la papelera y podrá ser restaurado más tarde.
                    </p>
                </div>
                <form id="deleteForm" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 text-left mb-2">
                            Razón de eliminación *
                        </label>
                        <textarea name="deletion_reason" 
                                  placeholder="Describe la razón por la cual se elimina este usuario..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                  rows="3"
                                  required></textarea>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" 
                                onclick="closeDeleteModal()"
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(userId, userName) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteForm').action = `/users/${userId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteForm').reset();
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }

        // Función para copiar tokens
        function copiarToken(inputId) {
            const input = document.getElementById(inputId);
            input.select();
            input.setSelectionRange(0, 99999); // Para dispositivos móviles
            
            try {
                document.execCommand('copy');
                // Mostrar confirmación
                const button = input.nextElementSibling;
                const originalText = button.innerHTML;
                button.innerHTML = '✅ Copiado';
                button.classList.add('bg-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600');
                }, 2000);
            } catch (err) {
                alert('Error al copiar el token');
            }
        }
    </script>
</x-app-layout>