<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use App\Models\Dependencia;
use App\Models\Sector;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UsuarioPermisosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    // public function test_example(): void
    // {
    //  $this->assertTrue(true);
    // }
    /** @test */
    public function usuario_puede_tener_un_permiso()
    {
        $permiso = Permission::create(['name' => 'ver_dashboard']);

        $sector = Sector::create([
            'nombre_sector' => 'Sector Test',
        ]);

        $dependencia = Dependencia::create([
            'nombre_depen' => 'Dependencia Test',
            'id_sector' => $sector->id_sector,
            'email_depen' => 'dep@test.com',
            'extension_depen' => '101',
            'telefono_depen' => '9510000000',
            'calle_depen' => 'Calle Test',
            'numerocalle_depen' => '123',
            'colonia_depen' => 'Centro',
            'cp_depen' => '68000',
        ]);

        $usuario = Usuario::create([
            'usuario_usr' => 'user_perm',
            'nombre_usr' => 'User',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'Permiso',
            'email_usr' => 'perm@test.com',
            'password' => bcrypt('123456'),
            'id_depen' => $dependencia->id_depen,
            'estado_usr' => 'Activo',
            'telefono_usr' => '9513333333',
        ]);

        $usuario->givePermissionTo('ver_dashboard');

        $this->assertTrue($usuario->hasPermissionTo('ver_dashboard'));
    }
}
