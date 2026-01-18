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
        // 1️⃣ Crear permisos
        $permisos = [
            'view formularios',
            'cargar indicadores',
            'view anexos',
            'manage users',   // solo Admin
            'manage cargas',  // solo Admin
            'manage roles',   // solo Admin
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // 2️⃣ Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $capturistaRole = Role::firstOrCreate(['name' => 'Capturista', 'guard_name' => 'web']);

        // 3️⃣ Asignar permisos a roles
        $adminRole->syncPermissions(Permission::all()); // Admin tiene todos
        $capturistaRole->syncPermissions(['view formularios', 'cargar indicadores', 'view anexos']);

        // 4️⃣ Asignar roles a usuarios existentes
        $adminUser = Usuario::where('id_usuario', 1)->first();
        if ($adminUser) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        $capturistaUser = Usuario::where('id_usuario', 2)->first();
        if ($capturistaUser) {
            $capturistaUser->roles()->syncWithoutDetaching([$capturistaRole->id]);
        }

        $this->command->info('Roles, permisos y asignaciones completadas correctamente.');
    }
}
