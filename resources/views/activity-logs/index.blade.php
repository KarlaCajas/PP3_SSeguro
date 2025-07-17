<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Logs de Actividad del Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    

                    
                    <!-- Filtros -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
                        <form method="GET" action="{{ route('activity-logs.index') }}">
                            {{-- Mantener parámetros de búsqueda --}}
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            @if($perPage != 20)
                                <input type="hidden" name="per_page" value="{{ $perPage }}">
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                <!-- Filtro por tipo de modelo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                    <select name="model_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Todos</option>
                                        @foreach($modelTypes as $key => $value)
                                            <option value="{{ $key }}" {{ request('model_type') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filtro por acción -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Acción</label>
                                    <select name="action" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Todas</option>
                                        @foreach($actions as $key => $value)
                                            <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filtro por usuario -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Usuario</label>
                                    <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Todos</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filtro por fecha desde -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Desde</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <!-- Filtro por fecha hasta -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hasta</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <!-- Botones -->
                                <div class="mt-6 flex space-x-2">
                                    <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                        Filtrar
                                    </button>
                                    <a href="{{ route('activity-logs.index') }}{{ $search ? '?search=' . $search : '' }}{{ $search && $perPage != 20 ? '&per_page=' . $perPage : (!$search && $perPage != 20 ? '?per_page=' . $perPage : '') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                                        Limpiar Filtros
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Controles de tabla: búsqueda y paginación --}}
                    <x-table-controls 
                        :search="$search" 
                        :per-page="$perPage" 
                        :route="route('activity-logs.index')" 
                        placeholder="Buscar por usuario, acción, tipo de modelo..." 
                    />

                    <!-- Estadísticas -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900">Total de Logs</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ $logs->total() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-green-900">Creaciones</h4>
                            <p class="text-2xl font-bold text-green-600">{{ $logs->where('action', 'create')->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-yellow-900">Actualizaciones</h4>
                            <p class="text-2xl font-bold text-yellow-600">{{ $logs->where('action', 'update')->count() }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-red-900">Eliminaciones</h4>
                            <p class="text-2xl font-bold text-red-600">{{ $logs->whereIn('action', ['soft_delete', 'hard_delete'])->count() }}</p>
                        </div>
                    </div>

                    <!-- Tabla de logs -->
                    @if($logs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha/Hora
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usuario
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acción
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Razón
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($logs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $log->user ? $log->user->name : 'Sistema' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $log->user ? $log->user->email : 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $actionColors = [
                                                        'create' => 'bg-green-100 text-green-800',
                                                        'update' => 'bg-blue-100 text-blue-800',
                                                        'soft_delete' => 'bg-yellow-100 text-yellow-800',
                                                        'hard_delete' => 'bg-red-100 text-red-800',
                                                        'restore' => 'bg-purple-100 text-purple-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $log->action_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $log->model_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $log->model_id }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate" title="{{ $log->reason }}">
                                                    {{ $log->reason ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <form action="{{ route('activity-logs.show', $log) }}" method="GET" class="inline">
                                                    <button type="submit"
                                                        class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded transition-colors duration-150">
                                                        Ver detalles
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Información de paginación --}}
                        <x-pagination-info :items="$logs" />
                    @else
                        <!-- Estado vacío -->
                        <div class="text-center py-12">
                            @if($search)
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron logs</h3>
                                <p class="mt-1 text-sm text-gray-500">No hay logs que coincidan con "{{ $search }}"</p>
                                <div class="mt-4">
                                    <a href="{{ route('activity-logs.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                        Limpiar búsqueda
                                    </a>
                                </div>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay logs de actividad</h3>
                                <p class="mt-1 text-sm text-gray-500">Los logs aparecerán aquí cuando se realicen acciones en el sistema.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
