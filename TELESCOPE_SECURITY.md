# Configuración de Seguridad para Laravel Telescope

## Descripción
Este documento describe la configuración de seguridad implementada para restringir el acceso a Laravel Telescope únicamente a usuarios administradores autenticados y activos.

## Medidas de Seguridad Implementadas

### 1. Gate de Autorización (`TelescopeServiceProvider`)
- **Ubicación**: `app/Providers/TelescopeServiceProvider.php`
- **Función**: Define quién puede acceder a Telescope usando Gates de Laravel
- **Validaciones**:
  - Usuario debe estar autenticado
  - Usuario debe estar activo (`is_active = true`)
  - Usuario debe tener el rol de 'admin'

### 2. Middleware Personalizado (`TelescopeAuth`)
- **Ubicación**: `app/Http/Middleware/TelescopeAuth.php`
- **Función**: Capa adicional de protección antes de acceder a Telescope
- **Validaciones**:
  - Verificación de autenticación
  - Verificación de estado activo del usuario
  - Verificación de rol de administrador
  - Registro de accesos en logs para auditoría

### 3. Configuración de Middleware en Telescope
- **Ubicación**: `config/telescope.php`
- **Middleware aplicados**:
  - `web`: Sesiones y CSRF
  - `telescope.auth`: Nuestro middleware personalizado
  - `Authorize`: Gate de autorización de Laravel Telescope

### 4. Acceso desde Dashboard
- **Ubicación**: `resources/views/dashboard.blade.php`
- **Función**: Enlace directo a Telescope solo visible para administradores
- **Características**:
  - Solo visible para usuarios con rol 'admin'
  - Abre en nueva pestaña para mejor experiencia
  - Interfaz clara y profesional

## Configuración de Roles

### Roles Definidos en el Sistema
- `admin`: Acceso completo al sistema, incluyendo Telescope
- `secre`: Secretaria con permisos limitados
- `bodega`: Gestión de inventario
- `ventas`: Gestión de ventas y facturación
- `cliente`: Acceso limitado como cliente

### Acceso a Telescope
- **Permitido**: Solo usuarios con rol `admin`
- **Denegado**: Todos los demás roles

## Logs de Seguridad

### Eventos Registrados
- Accesos exitosos a Telescope
- Intentos de acceso denegados
- Información del usuario (ID, email)
- Información de la sesión (IP, User-Agent)
- Timestamp del acceso

### Ubicación de Logs
- **Archivo**: `storage/logs/laravel.log`
- **Formato**: JSON estructurado para fácil análisis

## Rutas y URLs

### Acceso a Telescope
- **URL**: `/telescope`
- **Método**: GET
- **Protección**: Múltiples capas de middleware

### Acceso desde Dashboard
- **Ruta**: Dashboard principal (`/dashboard`)
- **Sección**: "Herramientas de Administración"
- **Visibilidad**: Solo para administradores

## Recomendaciones de Seguridad

### Entorno de Producción
1. **Desactivar Telescope en producción**:
   ```env
   TELESCOPE_ENABLED=false
   ```

2. **Limitar acceso por IP** (opcional):
   ```php
   // En TelescopeServiceProvider
   Gate::define('viewTelescope', function ($user) {
       $allowedIPs = ['192.168.1.100', '10.0.0.5'];
       return $user && 
              $user->is_active && 
              $user->hasRole('admin') &&
              in_array(request()->ip(), $allowedIPs);
   });
   ```

3. **Configurar HTTPS obligatorio**
4. **Implementar rate limiting**
5. **Monitorear logs regularmente**

### Auditoría y Monitoreo
- Revisar logs de acceso regularmente
- Implementar alertas para accesos sospechosos
- Mantener registro de usuarios administradores
- Revisar permisos de usuarios periódicamente

## Verificación de Implementación

### Tests de Seguridad
1. **Usuario no autenticado**: Debe redirigir a login
2. **Usuario sin rol admin**: Debe mostrar error 403
3. **Usuario inactivo**: Debe denegar acceso
4. **Usuario admin activo**: Debe permitir acceso

### Comandos de Verificación
```bash
# Verificar configuración de Telescope
php artisan telescope:status

# Verificar roles de usuarios
php artisan tinker
>>> App\Models\User::with('roles')->get(['id', 'name', 'email'])

# Verificar logs
tail -f storage/logs/laravel.log | grep "Telescope"
```

## Troubleshooting

### Problemas Comunes
1. **Error 403 para admin**: Verificar que el usuario tenga el rol correcto
2. **Middleware no funciona**: Verificar registro en `bootstrap/app.php`
3. **Telescope no carga**: Verificar configuración en `config/telescope.php`
4. **Logs no aparecen**: Verificar permisos de escritura en `storage/logs`

### Comandos de Diagnóstico
```bash
# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Verificar middleware registrados
php artisan route:list --middleware=telescope.auth
```

---

**Fecha de implementación**: 5 de agosto de 2025
**Versión**: 1.0
**Responsable**: Administrador del Sistema
