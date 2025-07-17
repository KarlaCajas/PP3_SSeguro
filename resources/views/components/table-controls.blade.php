@props([
    'search' => '',
    'perPage' => 10,
    'route' => '',
    'placeholder' => 'Buscar...'
])

<div class="mb-6">
    {{-- Barra de búsqueda y controles en esquinas superiores --}}
    <div class="flex  sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">

        {{-- Barra de búsqueda (esquina izquierda superior) --}}
        <div class="w-full sm:w-auto sm:max-w-sm">
            <form method="GET" action="{{ $route }}" class="flex items-center gap-2">
                {{-- Mantener parámetros existentes --}}
                @foreach(request()->except(['search', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
        
                <input type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="{{ $placeholder }}"
                    class="block w-full py-2 px-4 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        
                {{-- Icono de búsqueda --}}
                <button type="submit" class="ml-1 text-gray-500 hover:text-indigo-600 flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
        
                {{-- Botón limpiar --}}
                @if($search)
                    <a href="{{ request()->url() }}{{ request()->has('per_page') ? '?per_page=' . request('per_page') : '' }}" 
                       class="ml-1 text-gray-400 hover:text-gray-600 flex items-center justify-center"
                       title="Limpiar búsqueda">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                @endif
            </form>
        </div>

        {{-- Selector de elementos por página (esquina derecha superior) --}}
        <div class="flex items-center space-x-2 self-start">
            <label for="per_page" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                Mostrar:
            </label>
            <form method="GET" action="{{ $route }}" id="perPageForm">
                {{-- Mantener parámetros existentes --}}
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                
                <select name="per_page" 
                        id="per_page"
                        onchange="document.getElementById('perPageForm').submit()"
                        class="border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </form>
            <span class="text-sm text-gray-700 whitespace-nowrap">por página</span>
        </div>
    </div>
</div>
