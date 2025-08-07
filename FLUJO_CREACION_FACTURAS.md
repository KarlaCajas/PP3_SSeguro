# Flujo Completo de Creación de Facturas

## Índice
1. [Descripción General](#descripción-general)
2. [Flujo Paso a Paso](#flujo-paso-a-paso)
3. [Arquitectura del Sistema](#arquitectura-del-sistema)
4. [Flujo Detallado de Creación](#flujo-detallado-de-creación)
5. [Validaciones de Seguridad](#validaciones-de-seguridad)
6. [Gestión de Stock](#gestión-de-stock)
7. [Cálculos Financieros](#cálculos-financieros)
8. [Manejo de Errores](#manejo-de-errores)
9. [Estados de Factura](#estados-de-factura)

## Descripción General

El sistema de facturación implementa un flujo completo que incluye:
- Creación de facturas con múltiples productos
- Validación de stock en tiempo real
- Cálculo automático de impuestos (IVA 15% o exento)
- Transacciones atómicas para garantizar consistencia
- Auditoría completa con Telescope

## Flujo Paso a Paso

### Fase 1: Preparación del Sistema

#### Paso 1: El vendedor inicia sesión en el sistema
- El usuario vendedor accede al sistema con sus credenciales
- El sistema valida la autenticación y autorización
- Se verifica que el usuario tenga permisos para crear facturas
- Se carga la información del perfil del vendedor

#### Paso 2: El vendedor accede al módulo de facturación
- Desde el menú principal, selecciona "Facturas"
- El sistema muestra el listado de facturas existentes
- El vendedor hace clic en "Crear Nueva Factura"
- Se redirige al formulario de creación de facturas

### Fase 2: Configuración de la Factura

#### Paso 3: El sistema prepara los datos para la factura
- Se cargan todos los productos que tienen stock disponible (stock > 0)
- Se obtiene la lista de clientes activos registrados en el sistema
- Se inicializa el formulario con campos en blanco
- Se genera automáticamente el próximo número de factura

#### Paso 4: El vendedor selecciona o registra al cliente
**Opción A: Cliente existente**
- El vendedor busca al cliente en la lista desplegable
- Selecciona el cliente deseado
- El sistema autocompleta el nombre y email del cliente

**Opción B: Cliente nuevo (venta al público)**
- El vendedor ingresa manualmente el nombre del cliente
- Opcionalmente ingresa el email del cliente
- Deja el campo "Cliente registrado" sin seleccionar
- El sistema procesa la venta como cliente ocasional

#### Paso 5: El vendedor añade productos a la factura
- Hace clic en "Agregar Producto"
- Selecciona un producto del catálogo disponible
- El sistema muestra automáticamente:
  - Nombre del producto
  - Precio unitario actual
  - Stock disponible
- El vendedor ingresa la cantidad deseada
- Selecciona la tasa de IVA (0% exento o 15% gravado)
- El sistema calcula automáticamente el subtotal del item

#### Paso 6: El vendedor revisa y ajusta los productos
- Puede agregar múltiples productos repitiendo el paso anterior
- Para cada producto adicional, verifica que hay stock suficiente
- Puede modificar cantidades de productos ya agregados
- Puede eliminar productos de la lista si es necesario
- El sistema recalcula los totales automáticamente con cada cambio

### Fase 3: Validación y Cálculos

#### Paso 7: El sistema valida la información ingresada
- Verifica que todos los campos obligatorios estén completos
- Valida que las cantidades sean números positivos
- Confirma que hay al menos un producto en la factura
- Valida el formato del email si fue proporcionado
- Verifica que todos los productos seleccionados aún existan

#### Paso 8: El sistema verifica la disponibilidad de stock
- Para cada producto en la factura, consulta el stock actual
- Compara la cantidad solicitada con el stock disponible
- Si algún producto no tiene stock suficiente:
  - Muestra un mensaje de error específico
  - Indica la cantidad disponible
  - Impide continuar con el proceso

#### Paso 9: El sistema calcula los totales financieros
- Para cada producto calcula:
  - Subtotal = Cantidad × Precio unitario
  - Impuesto = Subtotal × (Tasa IVA / 100)
  - Total del item = Subtotal + Impuesto
- Para la factura completa calcula:
  - Subtotal general = Suma de todos los subtotales
  - IVA total = Suma de todos los impuestos
  - Total a pagar = Subtotal general + IVA total

### Fase 4: Procesamiento de la Factura

#### Paso 10: El vendedor confirma la creación de la factura
- Revisa todos los datos ingresados
- Verifica que los totales sean correctos
- Hace clic en "Guardar Factura"
- El sistema inicia el proceso de creación

#### Paso 11: El sistema inicia una transacción atómica
- Comienza una transacción de base de datos
- Esto garantiza que todos los cambios se hagan completamente o no se hagan
- Protege contra errores que dejen el sistema en estado inconsistente

#### Paso 12: El sistema genera el número de factura único
- Obtiene la fecha actual en formato YYYYMMDD
- Cuenta cuántas facturas se han creado en el día
- Genera el número secuencial con formato: FAC-20250805-0001
- Asigna este número único a la nueva factura

#### Paso 13: El sistema crea el registro principal de la factura
- Guarda en la base de datos:
  - Número de factura generado
  - ID del vendedor que la crea
  - Información del cliente (ID si es registrado, nombre y email)
  - Totales calculados (subtotal, IVA, total)
  - Fecha y hora de creación
  - Estado inicial: "activa"

#### Paso 14: El sistema procesa cada producto de la factura
Para cada producto en la lista:
- Crea un registro de "item de factura" con:
  - Referencia a la factura creada
  - ID y nombre del producto
  - Cantidad vendida
  - Precio unitario al momento de la venta
  - Tasa de IVA aplicada
  - Cálculos individuales (subtotal, impuesto, total)

#### Paso 15: El sistema actualiza el inventario
Para cada producto vendido:
- Reduce el stock disponible por la cantidad vendida
- Utiliza operaciones atómicas para evitar problemas de concurrencia
- Si hay error en este paso, revierte toda la transacción

### Fase 5: Finalización y Confirmación

#### Paso 16: El sistema confirma la transacción
- Si todos los pasos anteriores fueron exitosos:
  - Confirma todos los cambios en la base de datos
  - La factura queda oficialmente creada
- Si hubo algún error:
  - Revierte todos los cambios
  - Restaura el stock a su estado original
  - Muestra mensaje de error al usuario

#### Paso 17: El sistema registra la actividad en el log
- Guarda un registro de auditoría de la operación
- Incluye quién, qué, cuándo y detalles de la transacción
- Esta información es visible en Telescope para monitoreo

#### Paso 18: El sistema muestra la confirmación al vendedor
- Redirige a la vista de detalle de la factura creada
- Muestra un mensaje de éxito
- Presenta la factura completa con todos sus detalles
- Ofrece opciones para imprimir o enviar por email

#### Paso 19: El vendedor puede realizar acciones adicionales
**Opciones disponibles:**
- Ver el detalle completo de la factura
- Imprimir la factura para entrega al cliente
- Enviar la factura por email (si el cliente proporcionó email)
- Crear una nueva factura
- Volver al listado de facturas

### Flujo Alternativo: Edición de Facturas

#### Paso A1: El vendedor decide editar una factura del día actual
- Accede al listado de facturas
- Busca la factura que desea editar
- Hace clic en "Editar" (solo disponible para facturas del día actual)

#### Paso A2: El sistema verifica si la edición es permitida
- Confirma que la factura esté en estado "activa"
- Verifica que la factura fue creada en el día actual
- Comprueba que el usuario tenga permisos para editar

#### Paso A3: El vendedor modifica los datos necesarios
- Puede cambiar información del cliente
- Puede agregar, quitar o modificar productos
- Puede cambiar cantidades (respetando stock disponible)

#### Paso A4: El sistema procesa la edición
- Revierte el stock de los productos originales
- Aplica las validaciones como en una nueva factura
- Reduce el stock según los nuevos productos/cantidades
- Actualiza todos los cálculos y totales

### Flujo Alternativo: Cancelación de Facturas

#### Paso B1: El vendedor decide cancelar una factura
- Localiza la factura a cancelar
- Hace clic en "Cancelar Factura"
- El sistema verifica que tenga permisos (creador o administrador)

#### Paso B2: El sistema solicita confirmación
- Muestra un formulario de confirmación
- Requiere la contraseña actual del usuario
- Solicita una razón obligatoria para la cancelación

#### Paso B3: El vendedor confirma la cancelación
- Ingresa su contraseña
- Proporciona una razón detallada (ej: "Error en cantidad", "Cliente canceló pedido")
- Confirma la acción

#### Paso B4: El sistema procesa la cancelación
- Verifica la contraseña ingresada
- Inicia una transacción atómica
- Restaura el stock de todos los productos de la factura
- Cambia el estado de la factura a "cancelada"
- Registra quién canceló y cuándo
- Guarda la razón de cancelación

#### Paso B5: El sistema confirma la cancelación
- Muestra mensaje de confirmación
- La factura aparece marcada como cancelada
- El stock queda restaurado
- Se registra la actividad en el log de auditoría

## Arquitectura del Sistema

### Modelos Involucrados
```
Invoice (Factura Principal)
├── InvoiceItem (Items de la factura)
├── User (Usuario vendedor)
├── User (Cliente - opcional)
└── Product (Productos)
```

### Controlador Principal
- `InvoiceController` - Maneja todas las operaciones CRUD de facturas

### Rutas del Sistema
```php
GET  /invoices              - Listado de facturas
GET  /invoices/create       - Formulario de creación
POST /invoices              - Almacenar nueva factura
GET  /invoices/{id}         - Ver factura específica
GET  /invoices/{id}/edit    - Editar factura
PUT  /invoices/{id}         - Actualizar factura
```

## Flujo Detallado de Creación

### 1. Acceso al Formulario de Creación
**Ruta:** `GET /invoices/create`

**Proceso:**
1. Verificación de autenticación del usuario
2. Carga de productos con stock disponible (`stock > 0`)
3. Carga de clientes activos con rol 'cliente'
4. Renderizado de vista con datos necesarios

**Código relevante:**
```php
public function create()
{
    $products = Product::where('stock', '>', 0)->get();
    $customers = User::role('cliente')->active()->get();
    
    return view('invoices.create', compact('products', 'customers'));
}
```

### 2. Validación de Datos del Formulario
**Ruta:** `POST /invoices`

**Validaciones implementadas:**
- `customer_name`: Requerido, string, máximo 255 caracteres
- `customer_email`: Opcional, formato email válido
- `customer_id`: Opcional, debe existir en tabla users
- `items`: Requerido, array con mínimo 1 elemento
- `items.*.product_id`: Requerido, debe existir en tabla products
- `items.*.product_name`: Requerido, string, máximo 255 caracteres
- `items.*.quantity`: Requerido, entero, mínimo 1
- `items.*.tax_rate`: Requerido, solo valores 0 o 15

### 3. Procesamiento en Transacción Atómica

**Inicio de transacción:**
```php
return DB::transaction(function () use ($request) {
    // Todo el procesamiento ocurre aquí
});
```

#### 3.1 Validación de Stock
```php
foreach ($request->items as $itemData) {
    $product = Product::findOrFail($itemData['product_id']);
    
    if ($product->stock < $itemData['quantity']) {
        throw ValidationException::withMessages([
            'items' => "Stock insuficiente para {$product->name}. Stock disponible: {$product->stock}"
        ]);
    }
}
```

#### 3.2 Cálculos Financieros
Para cada producto:
```php
$quantity = $itemData['quantity'];
$unitPrice = $product->price;
$taxRate = $itemData['tax_rate'];

$itemSubtotal = $quantity * $unitPrice;
$itemTaxAmount = $itemSubtotal * ($taxRate / 100);
$itemTotalWithTax = $itemSubtotal + $itemTaxAmount;

$subtotal += $itemSubtotal;
$totalTax += $itemTaxAmount;
```

#### 3.3 Generación de Número de Factura
```php
public static function generateInvoiceNumber(): string
{
    $date = now()->format('Ymd');
    $sequence = static::whereDate('created_at', now())->count() + 1;
    return "FAC-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}
```

#### 3.4 Creación de Factura Principal
```php
$invoice = Invoice::create([
    'user_id' => Auth::id(),
    'customer_id' => $request->customer_id,
    'customer_name' => $request->customer_name,
    'customer_email' => $request->customer_email,
    'subtotal' => $subtotal,
    'tax' => $totalTax,
    'total' => $subtotal + $totalTax,
]);
```

#### 3.5 Creación de Items y Actualización de Stock
```php
foreach ($items as $itemData) {
    $itemData['invoice_id'] = $invoice->id;
    InvoiceItem::create($itemData);
    
    // Reducir stock atómicamente
    Product::find($itemData['product_id'])->decrement('stock', $itemData['quantity']);
}
```

### 4. Respuesta y Redirección
```php
return redirect()->route('invoices.show', $invoice)
    ->with('success', 'Factura creada exitosamente.');
```

## Validaciones de Seguridad

### 1. Autenticación
- Solo usuarios autenticados pueden crear facturas
- Verificación mediante middleware `auth`

### 2. Autorización
- Usuarios deben tener permisos para crear facturas
- Verificación de roles y permisos

### 3. Validación de Datos
- Sanitización de entrada
- Validación de tipos de datos
- Verificación de existencia de productos y clientes

### 4. Protección CSRF
- Token CSRF en formularios
- Validación automática por Laravel

## Gestión de Stock

### Control de Inventario
1. **Verificación previa:** Stock disponible antes de procesar
2. **Reducción atómica:** Uso de `decrement()` para evitar condiciones de carrera
3. **Reversión en errores:** Rollback automático si falla la transacción

### Algoritmo de Reducción
```php
// Verificación
if ($product->stock < $quantity) {
    throw ValidationException::withMessages([...]);
}

// Reducción atómica
Product::find($product_id)->decrement('stock', $quantity);
```

## Cálculos Financieros

### Estructura de Precios
```
Subtotal = Quantity × Unit_Price
Tax_Amount = Subtotal × (Tax_Rate / 100)
Total_Price = Subtotal + Tax_Amount

Invoice_Subtotal = Σ(Item_Subtotals)
Invoice_Tax = Σ(Item_Tax_Amounts)
Invoice_Total = Invoice_Subtotal + Invoice_Tax
```

### Tasas de Impuesto Permitidas
- **0%**: Productos exentos de IVA
- **15%**: Productos con IVA normal

### Precisión Decimal
- Todos los cálculos usan precisión de 2 decimales
- Casting automático en modelos Eloquent

## Manejo de Errores

### Tipos de Errores Posibles

1. **Errores de Validación**
   - Datos faltantes o inválidos
   - Formatos incorrectos
   - Valores fuera de rango

2. **Errores de Negocio**
   - Stock insuficiente
   - Producto no disponible
   - Cliente inactivo

3. **Errores del Sistema**
   - Fallos de base de datos
   - Problemas de conectividad
   - Errores de memoria

### Estrategias de Manejo

1. **Transacciones Atómicas**
   ```php
   DB::transaction(function () {
       // Operaciones críticas
   });
   ```

2. **Validación en Capas**
   - Frontend (JavaScript)
   - Backend (Laravel Validation)
   - Base de datos (Constraints)

3. **Logging y Monitoreo**
   - Telescope para debugging
   - Logs de aplicación
   - Métricas de performance

## Estados de Factura

### Estados Disponibles
- **active**: Factura válida y activa
- **cancelled**: Factura cancelada (con reversión de stock)

### Transiciones de Estado
```
[Creación] → active
active → cancelled (solo si cumple condiciones)
```

### Reglas de Cancelación
1. Solo el creador o administrador puede cancelar
2. Requiere contraseña de confirmación
3. Debe proporcionar razón de cancelación
4. Stock se restaura automáticamente

## Flujo de Cancelación

### Proceso de Cancelación
```php
public function cancel(User $cancelledBy, string $reason): bool
{
    return DB::transaction(function () use ($cancelledBy, $reason) {
        // Reversar stock de todos los productos
        foreach ($this->items as $item) {
            $product = $item->product;
            $product->increment('stock', $item->quantity);
        }

        // Marcar factura como cancelada
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy->id,
            'cancellation_reason' => $reason,
        ]);

        return true;
    });
}
```

### Validaciones para Cancelación
1. Verificación de permisos
2. Validación de contraseña
3. Razón obligatoria de cancelación
4. Estado actual debe ser 'active'

## Auditoría y Trazabilidad

### Información Registrada
- Timestamp de creación
- Usuario que creó la factura
- Cliente asociado
- Detalles de productos y cantidades
- Cálculos financieros
- Cambios de estado
- Usuario que canceló (si aplica)
- Razón de cancelación (si aplica)

### Telescope Integration
- Monitoreo de queries de base de datos
- Tracking de requests HTTP
- Logging de excepciones
- Métricas de performance
- Debugging de procesos

## Consideraciones de Performance

### Optimizaciones Implementadas
1. **Eager Loading:** Carga anticipada de relaciones
2. **Índices de Base de Datos:** En campos de búsqueda frecuente
3. **Paginación:** Para listados grandes
4. **Caching:** De consultas repetitivas

### Puntos de Monitoreo
1. Tiempo de respuesta en creación de facturas
2. Queries N+1 en listados
3. Uso de memoria en procesamiento masivo
4. Locks de base de datos en operaciones concurrentes
