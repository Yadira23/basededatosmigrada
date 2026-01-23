<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Usuario;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Permisos oficiales del sistema
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
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }

        // 2️⃣ Roles (coinciden con middleware)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $usuario = Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);

        // 3️⃣ Asignación de permisos
        $admin->syncPermissions($permisos);

        $usuario->syncPermissions([
            'ver_dashboard',
            'ver_formularios',
            'ver_anexos',
            'ver_indicadores',
        ]);
    }
}
