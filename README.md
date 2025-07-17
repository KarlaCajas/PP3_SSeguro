# BarEspe VentasPro - Sistema de Gestión de Ventas

Sistema completo desarrollado en Laravel para la gestión de usuarios, productos, categorías y ventas con control de acceso basado en roles usando Spatie Laravel-Permission.

## 🚀 Características del Sistema

- **Gestión de Usuarios**: CRUD completo de usuarios con diferentes roles
- **Gestión de Categorías**: CRUD completo de categorías de productos (solo bodega)
- **Gestión de Productos**: CRUD completo de productos con control de stock (solo bodega)
- **Registro de Ventas**: CRUD completo de ventas con actualización automática de stock (solo cajera)
- **Control de Acceso**: Sistema de roles y permisos con Spatie
- **Seguridad**: Protección de rutas con middleware personalizado
- **Validaciones**: Validaciones frontend y backend completas
- **Interfaz Dinámica**: Cálculos automáticos y actualizaciones en tiempo real

## 👥 Roles del Sistema

| Rol | Permisos |
|-----|----------|
| **admin** | CRUD completo de usuarios con cualquier rol |
| **secre** | CRUD de usuarios (excepto admin), Ver todos los usuarios |
| **bodega** | CRUD completo de categorías y productos |
| **cajera** | CRUD completo de ventas propias (limitado a día actual para edición/eliminación) |

## 🛠️ Tecnologías Utilizadas

- Laravel 11
- Laravel Breeze (Autenticación)
- Spatie Laravel-Permission (Roles y Permisos)
- Blade Templates
- Tailwind CSS
- MySQL/SQLite

## 📋 Requisitos Previos

- PHP >= 8.2
- Composer
- Node.js >= 16
- MySQL/SQLite
- Git

## 🚀 Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd ventas-pry-p1
```

### 2. Instalar Dependencias de PHP 

```bash
composer install
```

#### 2.1 Instalar Dependencias de PHP sin paquetes de desarrollo (Opcional)
```bash
composer install --no-dev
```

### 3. Instalar Dependencias de Node.js

```bash
npm install
```

### 4. Configurar Variables de Entorno

```bash
# Copiar el archivo de configuración
copy .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 5. Configurar Base de Datos

Editar el archivo `.env` con los datos de tu base de datos:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ventas_pry_p1
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Ejecutar Migraciones y Seeders (Importante)

```bash
# Ejecutar migraciones y poblar datos iniciales
php artisan migrate:fresh --seed
```

Este comando creará:
- Tablas del sistema (users, roles, categories, products, sales)
- Roles del sistema (admin, secre, bodega, cajera)
- Usuario administrador inicial
- Categorías y productos de prueba

### 7. Compilar Assets

```bash
# Compilar archivos CSS y JS
npm run build
```

### 8. Iniciar el Servidor

```bash
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

## 🔑 Credenciales de Acceso Inicial

Después de ejecutar los seeders, podrás acceder con:

- **Email**: admin@barespe.com
- **Contraseña**: password123
- **Rol**: Administrador

## 📖 Guía de Uso

### Primer Acceso

1. Acceder a `http://localhost:8000`
2. Hacer clic en "Log in"
3. Usar las credenciales del administrador
4. Crear usuarios adicionales desde el panel de administración

### Crear Usuarios con Diferentes Roles

Como administrador:

1. Ir a **Gestión de Usuarios** → **Crear Usuario**
2. Llenar el formulario con los datos requeridos
3. Seleccionar el rol apropiado
4. Guardar usuario

### Roles Sugeridos para Pruebas

```
Secretaria:
- Email: secretaria@barespe.com
- Rol: secre

Bodega:
- Email: bodega@barespe.com  
- Rol: bodega

Cajera:
- Email: cajera@barespe.com
- Rol: cajera
```

### Flujo de Trabajo Típico

1. **Admin/Secre**: Crear usuarios del sistema
2. **Bodega**: Crear categorías y productos
3. **Cajera**: Registrar ventas de productos disponibles

## � Funcionalidades CRUD Implementadas

### **Usuarios** (Admin, Secre)
- ✅ **Create**: Formulario con validaciones y restricciones de roles
- ✅ **Read**: Listado con información de roles, fechas y estadísticas
- ✅ **Update**: Edición completa con validaciones, cambio de roles y contraseñas opcionales
- ✅ **Delete**: Eliminación con restricciones (no auto-eliminación, verificar ventas)

### **Categorías** (Bodega)
- ✅ **Create**: Formulario con validación de nombres únicos
- ✅ **Read**: Listado con contador de productos asociados
- ✅ **Update**: Edición con visualización de productos asociados
- ✅ **Delete**: Eliminación con verificación de productos asociados

### **Productos** (Bodega)
- ✅ **Create**: Formulario con categorías, precios y stock
- ✅ **Read**: Listado con estados de stock, estadísticas y filtros
- ✅ **Update**: Edición con historial de ventas y validaciones de stock
- ✅ **Delete**: Eliminación con verificación de ventas registradas

### **Ventas** (Cajera)
- ✅ **Create**: Formulario dinámico con cálculos automáticos en tiempo real
- ✅ **Read**: Listado personal con estadísticas, resúmenes y filtros
- ✅ **Update**: Edición limitada a ventas del día actual con gestión inteligente de stock
- ✅ **Delete**: Eliminación con restauración automática de stock

## �📁 Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/          # Controladores del sistema
│   └── Middleware/           # Middleware personalizado
├── Models/                   # Modelos Eloquent
database/
├── migrations/              # Migraciones de base de datos
└── seeders/                # Seeders para datos iniciales
resources/
└── views/                  # Vistas Blade
routes/
└── web.php                 # Rutas del sistema
```

## 🔧 Comandos Útiles

```bash
# Resetear base de datos y datos
php artisan migrate:fresh --seed

# Ver rutas del sistema
php artisan route:list

# Limpiar cache
php artisan cache:clear
php artisan config:clear

# Compilar assets en desarrollo
npm run dev

# Compilar assets para producción
npm run build
```

## 🚨 Troubleshooting

### Error: "Class 'Spatie\Permission\Models\Role' not found"

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Error: "SQLSTATE[42S02]: Base table or directory not found"

```bash
php artisan migrate:fresh --seed
```

### Error: "Vite manifest not found"

```bash
npm install
npm run build
```

### Error de permisos en storage/

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 🔐 Características de Seguridad

- **Middleware de roles** en todas las rutas
- **Verificaciones de permisos** en controladores
- **Restricciones temporales** para edición de ventas
- **Validaciones de integridad** para eliminaciones
- **Control de stock** en tiempo real
- **Protección contra auto-eliminación** de usuarios
- **Aislamiento de datos** por usuario (ventas)

## ⚡ Características Avanzadas

- **Cálculos automáticos** en formularios de ventas
- **Gestión inteligente de stock** con updates/rollbacks automáticos
- **Interfaces dinámicas** con JavaScript vanilla
- **Validaciones en tiempo real** client-side y server-side
- **Estadísticas en tiempo real** en dashboards
- **Notificaciones de estado** contextuales
- **Restricciones de eliminación** para mantener integridad
- **Historial de cambios** visible en interfaces de edición

## 📝 Notas Adicionales

- El sistema implementa **CRUD completo** en todos los módulos
- Las ventas registran el precio del producto **al momento de la venta**
- Cada cajera **solo ve y puede editar sus propias ventas**
- El stock se actualiza **automáticamente** con cada venta, edición y eliminación
- Todas las rutas están **protegidas por middleware** de autenticación y roles
- **Solo se pueden editar/eliminar ventas del día actual** para mantener integridad
- **Restricciones de eliminación** evitan eliminar registros con dependencias
- **Validaciones frontend y backend** aseguran integridad de datos


**Desarrollado para el curso de Software Seguro - Universidad de las fuerzas armadas ESPE**