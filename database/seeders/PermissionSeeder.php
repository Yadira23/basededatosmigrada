<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            'ver_dashboard',
            'ver_formularios',
            'ver_anexos',
            'ver_indicadores',
            'gestionar_usuarios',
            'gestionar_dependencias',
            'gestionar_cargas',
            'ver_detalle_cargas',
        ];

        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(
                ['name' => $permiso, 'guard_name' => 'web']
            );
        }
    }
}
