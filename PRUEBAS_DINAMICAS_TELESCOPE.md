# Pruebas Dinámicas con Telescope - Flujo de Creación de Facturas

## Índice
1. [Configuración de Telescope](#configuración-de-telescope)
2. [Flujo de Pruebas Principal](#flujo-de-pruebas-principal)
3. [Casos de Error Críticos](#casos-de-error-críticos)
4. [Monitoreo en Telescope](#monitoreo-en-telescope)
5. [Detección de Problemas](#detección-de-problemas)
6. [Reportes de Resultados](#reportes-de-resultados)

## Configuración de Telescope

### 1. Verificación de Instalación
Acceder a Telescope para verificar que esté funcionando:
```
URL: http://localhost:8000/telescope
```

### 2. Watchers Necesarios
Verificar que estén habilitados en `config/telescope.php`:
- **QueryWatcher**: Monitorear consultas SQL
- **RequestWatcher**: Tracking de requests HTTP
- **ExceptionWatcher**: Capturar errores y excepciones
- **ModelWatcher**: Operaciones Eloquent (creación/actualización)

### 3. Configuración del Ambiente
```env
TELESCOPE_ENABLED=true
TELESCOPE_PATH=telescope
```

## Flujo de Pruebas Principal

### 🔄 PRUEBA PRINCIPAL: Flujo Completo de Creación de Factura

**Duración estimada:** 10-15 minutos  
**Objetivo:** Validar todo el proceso desde registro de cliente hasta factura finalizada

#### **FASE 1: Registro/Verificación de Cliente**

**Paso 1: Preparar datos del cliente**
```
Datos de prueba:
- Nombre: "Juan Pérez Martínez"
- Email: "juan.perez@email.com"
- Teléfono: "0999123456" (opcional)
```

**Paso 2: Verificar si el cliente ya existe**
- Ir a listado de usuarios/clientes
- Buscar por nombre o email
- Si no existe, registrar nuevo cliente

**Monitoreo en Telescope:**
```
▶️ Queries: Búsqueda de cliente existente
▶️ Models: Creación de User si es nuevo cliente
▶️ Performance: Tiempo de consulta/creación
```

#### **FASE 2: Iniciar Creación de Factura**

**Paso 3: Acceder al formulario de creación**
- Login como vendedor
- Navegar a `/invoices/create`
- Verificar carga del formulario

**Paso 4: Seleccionar cliente**
- Buscar y seleccionar el cliente registrado
- Verificar autocompletado de datos

**Monitoreo en Telescope:**
```
▶️ Requests: GET /invoices/create
▶️ Queries: Carga de productos disponibles y clientes
▶️ Performance: Tiempo de carga del formulario
```

#### **FASE 3: Configuración de Productos**

**Paso 5: Agregar primer producto**
```
Producto 1:
- Seleccionar: [Cualquier producto con stock > 5]
- Cantidad: 2
- IVA: 15%
```

**Paso 6: Agregar segundo producto**
```
Producto 2:
- Seleccionar: [Producto diferente con stock > 3]
- Cantidad: 1
- IVA: 0% (exento)
```

**Paso 7: Verificar cálculos automáticos**
- Confirmar que subtotales se calculen correctamente
- Verificar cálculo de IVA
- Validar total general

**Monitoreo en Telescope:**
```
▶️ Queries: Verificación de stock de productos
▶️ JavaScript: Cálculos del lado cliente
▶️ Performance: Tiempo de respuesta al agregar productos
```

#### **FASE 4: Validación y Creación**

**Paso 8: Enviar formulario**
- Revisar todos los datos
- Hacer clic en "Crear Factura"
- Observar proceso de validación

**Paso 9: Verificar transacción completa**
- Confirmar creación de factura
- Verificar reducción de stock
- Validar cálculos finales

**Monitoreo en Telescope:**
```
▶️ Requests: POST /invoices
▶️ Queries: Transacción completa (INSERT factura, INSERT items, UPDATE stock)
▶️ Models: Creación de Invoice e InvoiceItem
▶️ Performance: Tiempo total de procesamiento
```

#### **FASE 5: Verificación Final**

**Paso 10: Validar resultado**
- Verificar redirección a vista de factura
- Confirmar datos mostrados
- Validar número de factura generado

**Monitoreo en Telescope:**
```
▶️ Requests: GET /invoices/{id}
▶️ Queries: Carga de factura con relaciones
▶️ Models: Carga de datos relacionados
```

## Casos de Error Críticos

### ❌ PRUEBA DE ERROR 1: Stock Insuficiente

**Objetivo:** Verificar manejo de stock insuficiente

**Pasos:**
1. Identificar producto con stock bajo (ej: stock = 1)
2. Intentar crear factura con cantidad = 5
3. Observar manejo del error

**Resultado esperado:**
- Error de validación antes de crear factura
- Stock no debe modificarse
- Mensaje claro al usuario

**Monitoreo en Telescope:**
```
▶️ Exceptions: ValidationException capturada
▶️ Queries: No debe haber UPDATE en stock
▶️ Logs: Mensaje de error registrado
```

### ❌ PRUEBA DE ERROR 2: Datos Inválidos

**Objetivo:** Validar protección contra datos maliciosos

**Datos de prueba:**
```
Cliente: "<script>alert('XSS')</script>"
Cantidad: -5
Email: "email_invalido"
```

**Resultado esperado:**
- Validación del lado servidor
- Datos sanitizados antes de almacenar
- Formulario rechazado

**Monitoreo en Telescope:**
```
▶️ Requests: Request con datos inválidos
▶️ Exceptions: Validation errors
▶️ Queries: No debe ejecutarse INSERT con datos inválidos
```

### ❌ PRUEBA DE ERROR 3: Concurrencia de Stock

**Objetivo:** Detectar problemas de concurrencia

**Pasos:**
1. Abrir 2 pestañas del navegador
2. Producto con stock = 3
3. Simultáneamente crear 2 facturas con cantidad = 2 cada una
4. Solo una debe exitosa

**Resultado esperado:**
- Una factura se crea exitosamente
- La segunda falla por stock insuficiente
- Stock final = 1 (no negativo)

**Monitoreo en Telescope:**
```
▶️ Queries: Múltiples UPDATE simultáneos
▶️ Exceptions: Error de stock en segunda transacción
▶️ Performance: Manejo de locks de BD
```

## Monitoreo en Telescope

### 📊 Métricas a Observar

#### **1. Performance General**
```
✅ Tiempo total flujo completo: < 5 segundos
✅ Carga formulario: < 1 segundo
✅ Creación factura: < 2 segundos
✅ Verificación final: < 1 segundo
```

#### **2. Queries de Base de Datos**
```
✅ Total queries por flujo: < 15
✅ Queries lentas: 0 (todas < 500ms)
✅ Queries N+1: 0
✅ Uso de índices: Verificar en EXPLAIN
```

#### **3. Integridad de Datos**
```
✅ Factura creada con número único
✅ Items asociados correctamente
✅ Stock reducido exactamente
✅ Totales calculados correctamente
```

### 🔍 Puntos de Verificación en Telescope

#### **En sección "Requests":**
- `GET /invoices/create` - Carga formulario
- `POST /invoices` - Creación factura
- `GET /invoices/{id}` - Vista final

#### **En sección "Queries":**
```sql
-- Verificar estas consultas aparezcan:
SELECT * FROM products WHERE stock > 0
SELECT * FROM users WHERE role = 'cliente'
INSERT INTO invoices (...)
INSERT INTO invoice_items (...)
UPDATE products SET stock = stock - ? WHERE id = ?
```

#### **En sección "Models":**
- `Invoice created` - Factura creada
- `InvoiceItem created` - Items creados (múltiples)
- `Product updated` - Stock actualizado

#### **En sección "Exceptions":**
- No debe haber excepciones en flujo exitoso
- Validar excepciones en casos de error

## Detección de Problemas

### 🚨 Indicadores de Problemas

#### **Problemas de Performance:**
```
❌ Request > 3 segundos
❌ Query > 1000ms
❌ Más de 20 queries por flujo
❌ Uso memoria > 128MB
```

#### **Problemas de Integridad:**
```sql
-- Ejecutar después de pruebas para verificar:

-- Stock negativo (CRÍTICO)
SELECT * FROM products WHERE stock < 0;

-- Facturas sin items (ERROR)
SELECT * FROM invoices i 
LEFT JOIN invoice_items ii ON i.id = ii.invoice_id 
WHERE ii.id IS NULL;

-- Items huérfanos (ERROR)
SELECT * FROM invoice_items ii 
LEFT JOIN invoices i ON ii.invoice_id = i.id 
WHERE i.id IS NULL;

-- Totales incorrectos (ERROR)
SELECT id, subtotal, tax, total,
       (subtotal + tax) as calculated_total
FROM invoices 
WHERE ABS(total - (subtotal + tax)) > 0.01;
```

#### **Problemas de Seguridad:**
```
❌ Datos no sanitizados almacenados
❌ Queries sin prepared statements
❌ Tokens CSRF faltantes
❌ Acceso sin autenticación
```

## Reportes de Resultados

### 📋 Template de Reporte de Prueba

```markdown
## Reporte de Prueba - Flujo Creación Factura
**Fecha:** {FECHA}
**Tester:** {NOMBRE}
**Duración:** {TIEMPO_TOTAL}

### ✅ Resultados del Flujo Principal
- [ ] Cliente registrado/seleccionado correctamente
- [ ] Productos agregados sin errores
- [ ] Cálculos correctos (subtotal, IVA, total)
- [ ] Factura creada exitosamente
- [ ] Stock reducido correctamente
- [ ] Redirección y vista final ok

### ❌ Errores Encontrados
**Error 1:**
- Descripción: {DESCRIPCION_ERROR}
- Paso donde ocurrió: {PASO}
- Telescope Request ID: {REQUEST_ID}
- Severidad: Critical|High|Medium|Low

### 📊 Métricas de Performance
- Tiempo total: {TIEMPO}s
- Queries ejecutadas: {NUM_QUERIES}
- Query más lenta: {TIEMPO_QUERY}ms
- Uso memoria pico: {MEMORIA}MB

### 🔍 Observaciones de Telescope
- Requests monitoreados: {NUM_REQUESTS}
- Excepciones capturadas: {NUM_EXCEPTIONS}
- Warnings de performance: {NUM_WARNINGS}

### ✅ Verificación de Integridad
- [ ] Sin stock negativo
- [ ] Sin facturas huérfanas
- [ ] Totales calculados correctos
- [ ] Datos sanitizados correctamente
```

### 🎯 Criterios de Aprobación

#### **Flujo APROBADO si:**
```
✅ Tiempo total < 5 segundos
✅ 0 excepciones en flujo normal
✅ Stock siempre >= 0
✅ Todos los cálculos correctos
✅ Datos almacenados sin caracteres maliciosos
✅ Transacciones atómicas (todo o nada)
```

#### **Flujo RECHAZADO si:**
```
❌ Cualquier error crítico
❌ Stock negativo generado
❌ Datos corruptos almacenados
❌ Tiempo > 10 segundos
❌ Pérdida de datos en transacción
```

### 📝 Acciones Post-Prueba

1. **Limpiar datos de prueba:**
   ```sql
   -- Eliminar factura de prueba y restaurar stock
   DELETE FROM invoice_items WHERE invoice_id = {TEST_INVOICE_ID};
   DELETE FROM invoices WHERE id = {TEST_INVOICE_ID};
   -- Restaurar stock manualmente si es necesario
   ```

2. **Documentar problemas encontrados**
3. **Reportar a equipo de desarrollo**
4. **Programar nueva prueba después de correcciones**
