<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - BarEspe VentasPro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Bienvenido, {{ auth()->user()->name }}</h3>
                    <p class="mb-6">Tu rol: <span class="font-bold text-blue-600">{{ ucfirst(auth()->user()->getRoleNames()->first()) }}</span></p>
                    
                    {{-- Estadísticas rápidas para admin --}}
                    @if(auth()->user()->hasRole('admin'))
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800">Total Usuarios</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ \App\Models\User::count() }}</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800">Total Productos</h4>
                                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Product::count() }}</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-800">Facturas Hoy</h4>
                                <p class="text-2xl font-bold text-purple-600">{{ \App\Models\Invoice::today()->count() }}</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-yellow-800">Ventas Hoy</h4>
                                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Sale::today()->count() }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Estadísticas para ventas --}}
                    @if(auth()->user()->hasRole('ventas'))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-purple-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-800">Mis Facturas Hoy</h4>
                                <p class="text-2xl font-bold text-purple-600">{{ \App\Models\Invoice::today()->where('user_id', auth()->id())->count() }}</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800">Total Vendido Hoy</h4>
                                <p class="text-2xl font-bold text-green-600">${{ number_format(\App\Models\Invoice::today()->where('user_id', auth()->id())->sum('total'), 2) }}</p>
                            </div>
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800">Productos Disponibles</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ \App\Models\Product::where('stock', '>', 0)->count() }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Estadísticas para bodega --}}
                    @if(auth()->user()->hasRole('bodega'))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-green-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800">Total Productos</h4>
                                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Product::count() }}</p>
                            </div>
                            <div class="bg-red-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-red-800">Sin Stock</h4>
                                <p class="text-2xl font-bold text-red-600">{{ \App\Models\Product::where('stock', 0)->count() }}</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg">
                                <h4 class="font-semibold text-yellow-800">Stock Bajo</h4>
                                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Product::whereBetween('stock', [1, 10])->count() }}</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Menú de navegación según roles --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        {{-- Gestión de Usuarios (admin, secre) --}}
                        @if(auth()->user()->hasAnyRole(['admin', 'secre']))
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-semibold text-blue-800 mb-2">👥 Gestión de Usuarios</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('users.index') }}" class="block text-blue-600 hover:text-blue-800">
                                        📋 Ver Usuarios
                                    </a>
                                    <a href="{{ route('users.create') }}" class="block text-blue-600 hover:text-blue-800">
                                        ➕ Crear Usuario
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Gestión de Inventario (admin, bodega) --}}
                        @if(auth()->user()->hasAnyRole(['admin', 'bodega']))
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-semibold text-green-800 mb-2">📦 Inventario</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('categories.index') }}" class="block text-green-600 hover:text-green-800">
                                        📁 Categorías
                                    </a>
                                    <a href="{{ route('products.index') }}" class="block text-green-600 hover:text-green-800">
                                        🛒 Productos
                                    </a>
                                    <a href="{{ route('categories.create') }}" class="block text-green-600 hover:text-green-800">
                                        ➕ Nueva Categoría
                                    </a>
                                    <a href="{{ route('products.create') }}" class="block text-green-600 hover:text-green-800">
                                        ➕ Nuevo Producto
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Facturación (admin, ventas) --}}
                        @if(auth()->user()->hasAnyRole(['admin', 'ventas']))
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <h4 class="font-semibold text-purple-800 mb-2">🧾 Facturación</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('invoices.index') }}" class="block text-purple-600 hover:text-purple-800">
                                        📄 Ver Facturas
                                    </a>
                                    <a href="{{ route('invoices.create') }}" class="block text-purple-600 hover:text-purple-800">
                                        ➕ Nueva Factura
                                    </a>
                                    <a href="{{ route('sales.index') }}" class="block text-purple-600 hover:text-purple-800">
                                        ⚡ Ventas Rápidas
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Solo para clientes --}}
                        @if(auth()->user()->hasRole('cliente'))
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <h4 class="font-semibold text-yellow-800 mb-2">🛍️ Mi Cuenta</h4>
                                <div class="space-y-2">
                                    <p class="text-yellow-600">Perfil de Cliente</p>
                                    <p class="text-sm text-yellow-500">Acceso limitado al sistema</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Productos con stock bajo (para bodega y admin) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'bodega']))
                        @php
                            $lowStockProducts = \App\Models\Product::with('category')->where('stock', '<=', 10)->orderBy('stock')->take(5)->get();
                        @endphp
                        
                        @if($lowStockProducts->count() > 0)
                            <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <h4 class="font-semibold text-red-800 mb-3">⚠️ Productos con Stock Bajo</h4>
                                <div class="space-y-2">
                                    @foreach($lowStockProducts as $product)
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-red-700">{{ $product->name }}</span>
                                            <span class="text-red-600 font-bold">
                                                Stock: {{ $product->stock }}
                                                @if($product->stock == 0)
                                                    <span class="text-xs bg-red-200 px-2 py-1 rounded">SIN STOCK</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <a href="{{ route('products.index') }}" class="mt-3 inline-block text-red-600 hover:text-red-800 font-medium">
                                    Ver todos los productos →
                                </a>
                            </div>
                        @endif
                    @endif

                    {{-- Últimas facturas (para ventas) --}}
                    @if(auth()->user()->hasRole('ventas'))
                        @php
                            $recentInvoices = \App\Models\Invoice::with('customer')
                                ->where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @if($recentInvoices->count() > 0)
                            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-3">📄 Mis Últimas Facturas</h4>
                                <div class="space-y-2">
                                    @foreach($recentInvoices as $invoice)
                                        <div class="flex justify-between items-center py-1">
                                            <div>
                                                <span class="text-blue-700 font-medium">{{ $invoice->invoice_number }}</span>
                                                <span class="text-blue-600 ml-2">{{ $invoice->customer_name }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-blue-600 font-bold">${{ number_format($invoice->total, 2) }}</span>
                                                <br>
                                                <span class="text-xs text-blue-500">{{ $invoice->created_at->format('d/m H:i') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <a href="{{ route('invoices.index') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 font-medium">
                                    Ver todas las facturas →
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>