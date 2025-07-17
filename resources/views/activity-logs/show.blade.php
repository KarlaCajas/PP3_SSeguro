<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Log de Actividad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header con navegación -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <a href="{{ route('activity-logs.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Volver a Logs
                            </a>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-medium text-gray-900">Log #{{ $activityLog->id }}</h3>
                            <p class="text-sm text-gray-500">{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <!-- Información general -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Información General</h4>
                            <dl class="space-y-1">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Acción:</dt>
                                    <dd class="text-sm text-gray-900">
                                        @php
                                            $actionColors = [
                                                'create' => 'bg-green-100 text-green-800',
                                                'update' => 'bg-blue-100 text-blue-800',
                                                'soft_delete' => 'bg-yellow-100 text-yellow-800',
                                                'hard_delete' => 'bg-red-100 text-red-800',
                                                'restore' => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $actionColors[$activityLog->action] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $activityLog->action_name }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipo de Modelo:</dt>
                                    <dd class="text-sm text-gray-900">{{ $activityLog->model_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID del Modelo:</dt>
                                    <dd class="text-sm text-gray-900">{{ $activityLog->model_id }}</dd>
                                </div>
                                @if($activityLog->reason)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Razón:</dt>
                                        <dd class="text-sm text-gray-900">{{ $activityLog->reason }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Usuario y Contexto</h4>
                            <dl class="space-y-1">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Usuario:</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $activityLog->user ? $activityLog->user->name : 'Sistema' }}
                                    </dd>
                                </div>
                                @if($activityLog->user)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                        <dd class="text-sm text-gray-900">{{ $activityLog->user->email }}</dd>
                                    </div>
                                @endif
                                @if($activityLog->ip_address)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">IP:</dt>
                                        <dd class="text-sm text-gray-900">{{ $activityLog->ip_address }}</dd>
                                    </div>
                                @endif
                                @if($activityLog->user_agent)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">User Agent:</dt>
                                        <dd class="text-sm text-gray-900 break-all">{{ $activityLog->user_agent }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Cambios realizados -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($activityLog->old_values)
                            <div class="bg-red-50 p-4 rounded-lg">
                                <h4 class="font-medium text-red-900 mb-2">Valores Anteriores</h4>
                                <div class="bg-white p-3 rounded border">
                                    <pre class="text-sm text-red-800 whitespace-pre-wrap">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif

                        @if($activityLog->new_values)
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-900 mb-2">Valores Nuevos</h4>
                                <div class="bg-white p-3 rounded border">
                                    <pre class="text-sm text-green-800 whitespace-pre-wrap">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!$activityLog->old_values && !$activityLog->new_values)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Cambios</h4>
                            <p class="text-sm text-gray-500">No se registraron cambios específicos para esta acción.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
