# Mi App - Contexto del Proyecto

## Qué es
App de creación de tickets de mesa de ayuda para servicios de TI, basada en ITIL.

## Frameworks
- Laravel 12
- Vue 3
- Vite
- Inertia.js
- Ziggy (rutas en el frontend vía `route()` de `ziggy-js`)

## Arquitectura
- `/app/Http/Controllers` → controladores
- `/app/Models` → modelos de datos
- `/app/Traits` → traits
- `/resources/js/Components` → componentes de UI
- `/resources/js/Layouts` → layouts
- `/resources/js/Pages` → vistas UI
- `/resources/js/Pages/Solicitante` → rol Solicitante
- `/resources/js/Pages/MesaServicio` → rol Mesa de Servicio
- `/resources/js/Pages/Especialista` → rol Especialista TI
- `/routes/web.php` → rutas

## Roles y prefijos de ruta
| Rol | Prefijo | Nombre base |
|-----|---------|-------------|
| Solicitante | `/mt` | `solicitante.` |
| Mesa de Servicio | `/mds` | `mesadeservicio.` |

## Modelos y relaciones clave
- `Ticket` → `solicitante()` (→ Trabajador), `dependencia()`, `local()`, `canal()`, `servicio.categoria`, `prioridad()`, `historial.estadoNuevo/Anterior/user`, `archivos.archivo/user`
- `tickets.solicitante_id` es FK a `trabajadores`, no a `users`
- El usuario autenticado tiene su trabajador en `Auth::user()->trabajador`

## Parámetros desde la BD (nunca hardcodear en el frontend)
Los estados, prioridades, dificultades, etc. tienen `text_color` y `bg_color` almacenados en la BD.
Siempre pasarlos desde el controller como prop y construir un `computed` map en el componente:

```php
// Controller
$estados     = Estado::where('activo', true)->get(['codigo', 'label', 'text_color', 'bg_color']);
$prioridades = Prioridad::where('activo', true)->get(['codigo', 'label', 'text_color', 'bg_color']);
```

```js
// Vue
const estadoMap = computed(() => Object.fromEntries(props.estados.map(e => [e.codigo, e])))
const estadoLabel = (codigo) => estadoMap.value[codigo]?.label ?? codigo
const estadoClase = (codigo) => {
    const e = estadoMap.value[codigo]
    return `${e?.text_color ?? 'text-gray-500'} ${e?.bg_color ?? 'bg-gray-100'}`
}
```

## Referencia visual: MesaServicio/Tickets/Ver.vue
Es la vista de referencia para el diseño de páginas de detalle. Estructura estándar:
1. Botón volver
2. Card principal: header (código, estado, prioridad) → sección Solicitante → sección Ticket (asunto, descripción, archivos) → sección Servicio (categoría, servicio)
3. Card historial con línea vertical y burbujas de estado

## Canales en la BD
El canal para tickets creados desde la app es `codigo = 'APLICACION'`.

## Convenciones de código
- Estilos con Tailwind, sin CSS propio
- Commits en español
- Rutas nombradas, nunca URLs hardcodeadas
- Vistas de detalle como páginas completas (no modales), salvo acciones simples como confirmaciones

## Comandos frecuentes
- `npm run dev` → desarrollo local Vite
- `php artisan serve` → desarrollo local servidor PHP
- `php artisan route:list --path=mt` → listar rutas de un prefijo
- `php artisan tinker` → verificar datos en BD
