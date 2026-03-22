<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ----------------------------------------------------------------
        // PERMISOS
        // ----------------------------------------------------------------
        // Secciones (acceso a cada módulo del sistema)
        $permisos = [
            // --- Sección: Mis Tickets ---
            'mis_tickets',            // acceso a sección
            'ticket.crear',    // crear ticket para sí mismo
            'ticket.conformidad',     // dar conformidad a un ticket resuelto

            // --- Sección: Mesa de Servicio ---
            'mesa_servicio',          // acceso a sección
            'ticket.crear_mesa',      // crear ticket por cualquier canal (presencial, correo, SGD, otro)
            'ticket.clasificar',      // clasificar tipo y prioridad
            'ticket.asignar',         // asignar profesional responsable

            // --- Acciones de Profesional (sin sección propia) ---
            'ticket.atender',         // registrar avances en la atención
            'ticket.solicitar_info',  // solicitar información adicional al usuario
            'ticket.transferir',      // transferir el ticket a otro profesional
            'ticket.resolver',        // marcar ticket como resuelto

            // --- Sección: Reportes ---
            'reportes',               // acceso a sección
            'reporte.generar',        // generar y exportar reportes

            // --- Sección: Administración ---
            'admin',                  // acceso a sección
            'usuario.gestionar',      // alta, edición y baja de usuarios y trabajadores
        ];

        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ----------------------------------------------------------------
        // ROLES Y SUS PERMISOS
        // ----------------------------------------------------------------
        $roles = [

            // Trabajador municipal que solicita servicios TI
            'usuario' => [
                'mis_tickets',
                'ticket.crear',
                'ticket.conformidad',
            ],

            // Personal TI que recibe y gestiona solicitudes en la mesa de servicio
            'mesa_servicio' => [
                'mis_tickets',
                'ticket.crear',
                'ticket.conformidad',
                'mesa_servicio',
                'ticket.crear_mesa',
                'ticket.clasificar',
                'ticket.asignar',
            ],

            // Técnico TI asignado para atender tickets
            'profesional' => [
                'mis_tickets',
                'ticket.crear',
                'ticket.conformidad',
                'ticket.atender',
                'ticket.solicitar_info',
                'ticket.transferir',
                'ticket.resolver',
            ],

            // Jefe / coordinador OTI: puede hacer todo lo anterior + reportes
            'coordinador' => [
                'mis_tickets',
                'ticket.crear',
                'ticket.conformidad',
                'mesa_servicio',
                'ticket.crear_mesa',
                'ticket.clasificar',
                'ticket.asignar',
                'ticket.atender',
                'ticket.solicitar_info',
                'ticket.transferir',
                'ticket.resolver',
                'reportes',
                'reporte.generar',
            ],

            // Administrador del sistema: acceso total
            'admin' => $permisos,
        ];

        foreach ($roles as $nombre => $permisosList) {
            $role = Role::findOrCreate($nombre, 'web');
            $role->syncPermissions($permisosList);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
