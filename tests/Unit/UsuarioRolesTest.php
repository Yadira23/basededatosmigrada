<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use App\Models\Dependencia;
use App\Models\Sector;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsuarioRolesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    //public function test_example(): void
    //{
      //  $this->assertTrue(true);
    //}
    /** @test */
    public function usuario_puede_tener_un_rol()
    {
        $role = Role::create(['name' => 'admin']);

        $sector = Sector::create([
            'nombre_sector' => 'Sector Test'
        ]);

        $dependencia = Dependencia::create([
            'nombre_depen'        => 'Dependencia Test',
            'id_sector'           => $sector->id_sector,
            'email_depen'         => 'dep@test.com',
            'extension_depen'     => '101',
            'telefono_depen'      => '9510000000',
            'calle_depen'         => 'Calle Test',
            'numerocalle_depen'   => '123',
            'colonia_depen'       => 'Centro',
            'cp_depen'            => '68000',
        ]);

        $usuario = Usuario::create([
            'usuario_usr'        => 'admin1',
            'nombre_usr'         => 'Admin',
            'apellido_paterno'   => 'Root',
            'apellido_materno'   => 'System',
            'email_usr'          => 'admin@test.com',
            'password'           => bcrypt('123456'),
            'id_depen'           => $dependencia->id_depen,
            'estado_usr'         => 'Activo',
            'telefono_usr'       => '9512222222',
        ]);

        $usuario->assignRole('admin');

        $this->assertTrue($usuario->hasRole('admin'));
    }
}
