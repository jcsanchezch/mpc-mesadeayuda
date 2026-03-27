<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametrosSeeder extends Seeder
{
    public function run(): void
    {
        // ── Estados ───────────────────────────────────────────────────────────
        $estados = [

['codigo' =>'REGISTRADO', 'label' => 'Registrado', 'labelSolicitante' => 'En Espera', 'text_color' => 'text-yellow-700', 'bg_color' => 'bg-yellow-200', 'es_inicio' => true, 'es_fin' => false, 'actor' => 'ti', 'activo' => true],

['codigo' =>'ASIGNADO', 'label' => 'Asignado', 'labelSolicitante' => 'Asignado', 'text_color' => 'text-purple-700', 'bg_color' => 'bg-purple-200', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'ti', 'activo' => true],

['codigo' =>'PROGRAMADO', 'label' => 'Programado', 'labelSolicitante' => 'Programado', 'text_color' => 'text-indigo-700', 'bg_color' => 'bg-indigo-200', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'ti', 'activo' => true],

['codigo' =>'ATENDIENDO', 'label' => 'Atendiendo', 'labelSolicitante' => 'Atendiendo', 'text_color' => 'text-blue-700', 'bg_color' => 'bg-blue-200', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'ti', 'activo' => true],

['codigo' =>'INFORMACION', 'label' => 'Información', 'labelSolicitante' => 'Brindar Información', 'text_color' => 'text-orange-700', 'bg_color' => 'bg-orange-200', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'solicitante', 'activo' => true],

['codigo' =>'ATENDIDO', 'label' => 'Atendido', 'labelSolicitante' => 'Atendido', 'text_color' => 'text-emerald-700', 'bg_color' => 'bg-emerald-200', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'solicitante', 'activo' => true],

['codigo' =>'CANCELADO', 'label' => 'Cancelado', 'labelSolicitante' => 'Cancelado', 'text_color' => 'text-red-700', 'bg_color' => 'bg-red-200', 'es_inicio' => false, 'es_fin' => true, 'actor' => null, 'activo' => true],

['codigo' =>'CERRADO', 'label' => 'Cerrado', 'labelSolicitante' => 'Cerrado', 'text_color' => 'text-gray-500', 'bg_color' => 'bg-gray-200', 'es_inicio' => false, 'es_fin' => true, 'actor' => null, 'activo' => true],
            /*
Estados más usados en sistemas reales

Este es un flujo profesional (muy parecido a Jira / ServiceNow):

🟢 1. Creación
Nuevo (New / Open)
→ Ticket recién creado, aún sin revisar
🔵 2. Evaluación
En revisión (Triaged / Under Review)
→ Se valida información, se clasifica
Asignado (Assigned)
→ Ya tiene responsable o equipo
🟡 3. Trabajo activo
En progreso (In Progress)
→ Se está trabajando en la solución
En espera (On Hold / Pending)
→ Bloqueado por:
usuario
proveedor externo
otra área
🟠 4. Resolución
Resuelto (Resolved)
→ Ya se solucionó, pero falta confirmación
🔴 5. Cierre
Cerrado (Closed)
→ Usuario validó o pasó tiempo sin respuesta
🧠 Estados adicionales (más maduros)

En sistemas más completos puedes ver:

Escalado (Escalated) → sube de nivel (L2, L3)
Reabierto (Reopened) → problema volvió
Cancelado (Cancelled) → ya no aplica
Rechazado (Rejected) → solicitud inválida
En validación (UAT / Verification) → usuario prueba solución
🔁 Flujo típico completo
Nuevo
  ↓
En revisión
  ↓
Asignado
  ↓
En progreso
  ↓
En espera (opcional)
  ↓
Resuelto
  ↓
Cerrado
🏢 Recomendación profesional (muy importante)

Para un sistema moderno de mesa de servicio:

Mantén entre 5 y 8 estados máximo
Evita complejidad innecesaria
Usa estados claros para métricas SLA:
tiempo de atención
tiempo de resolución
tiempos en espera
💡 Ejemplo ideal (balanceado)

Si estás diseñando tu sistema, te recomiendo este set:

New
Assigned
In Progress
Pending
Resolved
Closed
Reopened (opcional)

     Registro (Logged) → el ticket se crea
Clasificación (Categorized) → se define tipo, prioridad
Priorización (Prioritized) → impacto + urgencia
Asignación (Assigned) → se deriva a un grupo
Diagnóstico (In Progress) → análisis y solución
Resolución (Resolved) → ya tiene solución
Cierre (Closed) → validado con el usuario


New
Assigned
In Progress
Pending
Resolved
Closed
Reopened

            Roles (estructura ITSM real)
Usuario (Requester) → crea el ticket
Agente L1 (Mesa de servicio) → primer nivel
Soporte L2 → técnico especializado
Soporte L3 → expertos / desarrollo
Gestor / Supervisor → monitorea SLA y escalaciones


            3. Flujo con transiciones
New
 ↓ (L1 revisa)
Assigned
 ↓
In Progress
 ↓
Pending  ↔  In Progress
 ↓
Resolved
 ↓
Closed

Resolved → Reopened → Assigned


             */

        ];

        foreach ($estados as $e) {
            DB::table('estados')->updateOrInsert(['codigo' => $e['codigo']], $e);
        }

        // ── Canales ───────────────────────────────────────────────────────────
        $canales = [
            ['codigo' =>'APLICACION', 'label' => 'Aplicación Mesa de Ayuda', 'activo' => true, 'es_aplicacion' => true],
            ['codigo' =>'PRESENCIAL_MS', 'label' => 'Presencial en Mesa de Servicio', 'activo' => true],
            ['codigo' =>'CORREO', 'label' => 'Correo Electrónico', 'activo' => true],
            ['codigo' =>'SGD', 'label' => 'Sistema de Gestión Documental', 'activo' => true],
            ['codigo' =>'LLAMADA', 'label' => 'Llamada Telefónica', 'activo' => true],
            ['codigo' =>'JEFATURA', 'label' => 'Indicación de la Jefatura', 'activo' => true],
            ['codigo' =>'OTRO', 'label' => 'Otro Canal', 'activo' => true],
        ];

        foreach ($canales as $c) {
            DB::table('canales')->updateOrInsert(['codigo' => $c['codigo']], $c);
        }

        // ── Prioridades ───────────────────────────────────────────────────────
        $prioridades = [
            ['codigo' =>'NORMAL', 'label' => 'Normal', 'text_color' => 'text-blue-700', 'bg_color' => 'bg-blue-100', 'activo' => true],
            ['codigo' =>'URGENTE', 'label' => 'Urgente', 'text_color' => 'text-yellow-700', 'bg_color' => 'bg-yellow-100', 'activo' => true],
            ['codigo' =>'MUY_URGENTE', 'label' => 'Muy Urgente', 'text_color' => 'text-red-700', 'bg_color' => 'bg-red-100', 'activo' => true],
        ];

        foreach ($prioridades as $p) {
            DB::table('prioridades')->updateOrInsert(['codigo' => $p['codigo']], $p);
        }

        // ── Dificultades ──────────────────────────────────────────────────────
        $dificultades = [
            ['nivel' => 1, 'codigo' =>'TRIVIAL', 'label' => 'Trivial', 'color' => '#6b7280', 'activo' => true],
            ['nivel' => 2, 'codigo' =>'SIMPLE', 'label' => 'Simple', 'color' => '#22c55e', 'activo' => true],
            ['nivel' => 3, 'codigo' =>'MODERADO', 'label' => 'Moderado', 'color' => '#3b82f6', 'activo' => true],
            ['nivel' => 4, 'codigo' =>'COMPLEJO', 'label' => 'Complejo', 'color' => '#f97316', 'activo' => true],
            ['nivel' => 5, 'codigo' =>'MUY_COMPLEJO', 'label' => 'Muy Complejo', 'color' => '#ef4444', 'activo' => true],
        ];

        foreach ($dificultades as $d) {
            DB::table('dificultades')->updateOrInsert(['codigo' => $d['codigo']], $d);
        }

        // ── Niveles ITIL 4 ────────────────────────────────────────────────────
        $niveles = [
            [
                'nivel' => 0,
                'codigo' =>'N0',
                'label' => 'Autoservicio',
                'descripcion' => 'El usuario resuelve el incidente o solicitud por su propia cuenta mediante la base de conocimiento, FAQs o herramientas de autoservicio, sin intervención del equipo de TI.',
                'activo' => true,
            ],
            [
                'nivel' => 1,
                'codigo' =>'N1',
                'label' => 'Mesa de Servicios',
                'descripcion' => 'Primer punto de contacto. Atiende incidentes y solicitudes de baja complejidad: restablecimiento de contraseñas, consultas generales, enrutamiento y registro de tickets. Resuelve la mayoría de los casos.',
                'activo' => true,
            ],
            [
                'nivel' => 2,
                'codigo' =>'N2',
                'label' => 'Soporte Técnico',
                'descripcion' => 'Especialistas técnicos con conocimiento profundo en áreas específicas. Atiende los casos que N1 no pudo resolver: configuraciones, diagnósticos avanzados e incidentes de mediana complejidad.',
                'activo' => true,
            ],
            [
                'nivel' => 3,
                'codigo' =>'N3',
                'label' => 'Soporte Experto',
                'descripcion' => 'Expertos senior, arquitectos de soluciones o desarrolladores. Gestiona problemas de alta complejidad, errores de software/infraestructura, cambios estructurales y análisis de causa raíz (RCA).',
                'activo' => true,
            ],
            [
                'nivel' => 4,
                'codigo' =>'N4',
                'label' => 'Proveedor Externo',
                'descripcion' => 'Soporte brindado por fabricantes, proveedores de software o hardware, o terceros especializados. Se activa cuando la solución está fuera del alcance interno de la organización.',
                'activo' => true,
            ],
        ];

        foreach ($niveles as $n) {
            DB::table('niveles')->updateOrInsert(['codigo' => $n['codigo']], $n);
        }

        // ── Tipos ─────────────────────────────────────────────────────────────
        $tipos = [
            [
                'id'                      => 1,
                'codigo'                  =>'SOLICITUD',
                'label'                   => 'Solicitud',
                'descripcion'             => 'Petición formal de un usuario para recibir algo: información, acceso a un servicio, un recurso o una acción estándar. No representa una interrupción del servicio.',
                'disponible_al_solicitante' => true,
            ],
            [
                'id'                      => 2,
                'codigo'                  =>'INCIDENTE',
                'label'                   => 'Incidente',
                'descripcion'             => 'Interrupción no planificada o degradación de la calidad de un servicio. Requiere restauración rápida para minimizar el impacto en el negocio.',
                'disponible_al_solicitante' => true,
            ],
            [
                'id'                      => 3,
                'codigo'                  =>'CAMBIO',
                'label'                   => 'Cambio',
                'descripcion'             => 'Adición, modificación o eliminación de cualquier elemento que pueda afectar a los servicios de TI. Sigue un proceso de evaluación y aprobación para controlar el riesgo.',
                'disponible_al_solicitante' => false,
            ],
            [
                'id'                      => 4,
                'codigo'                  =>'PROBLEMA',
                'label'                   => 'Problema',
                'descripcion'             => 'Causa raíz de uno o más incidentes. Su gestión busca identificar y eliminar la causa subyacente para prevenir recurrencias futuras.',
                'disponible_al_solicitante' => false,
            ],
            [
                'id'                      => 5,
                'codigo'                  =>'EVENTO',
                'label'                   => 'Evento',
                'descripcion'             => 'Cambio de estado con relevancia para la gestión de un servicio o elemento de configuración. Puede ser informativo, de advertencia o de excepción, y puede originar incidentes o cambios.',
                'disponible_al_solicitante' => false,
            ],
        ];

        foreach ($tipos as $t) {
            DB::table('tipos')->updateOrInsert(['id' => $t['id']], $t);
        }
    }
}
