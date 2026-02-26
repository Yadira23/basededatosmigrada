<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use App\Models\Dependencia;
use App\Models\Sector;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    // public function test_example(): void
    // {
    // $this->assertTrue(true);
    // }
    /** @test */
    public function se_puede_crear_un_usuario()
    {
        $sector = Sector::create([
            'nombre_sector' => 'Sector Test',
        ]);

        $dependencia = Dependencia::create([
            'nombre_depen' => 'Dependencia Test',
            'id_sector' => $sector->id_sector,
            'email_depen' => 'dependencia@test.com',
            'extension_depen' => '101',
            'telefono_depen' => '9511234567',
            'calle_depen' => 'Calle Test',
            'numerocalle_depen' => '123',
            'colonia_depen' => 'Centro',
            'cp_depen' => '68000',
        ]);

        $usuario = Usuario::create([
            'usuario_usr' => 'juan123',
            'nombre_usr' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'López',
            'email_usr' => 'juan@test.com',
            'password' => bcrypt('password123'),
            'id_depen' => $dependencia->id_depen,
            'estado_usr' => 'Activo',
            'telefono_usr' => '1234567890',
        ]);

        $this->assertDatabaseHas('usuarios', [
            'usuario_usr' => 'juan123',
            'email_usr' => 'juan@test.com',
        ]);
    }
}
