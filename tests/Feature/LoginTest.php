<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Sector;
use App\Models\Dependencia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoginTest extends TestCase
{ 
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    //public function test_example(): void
    //{
      //  $response = $this->get('/');

      //  $response->assertStatus(200);
    //}

    /** @test */
    public function usuario_con_permiso_puede_acceder_al_dashboard()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $usuario = Usuario::factory()->create();
        $usuario->assignRole($adminRole);

        $this->actingAs($usuario)
             ->get('/admin/dashboard')
             ->assertStatus(200);
    }

    /** @test */
    public function usuario_no_puede_acceder_al_dashboard_admin()
    {
        $usuarioRole = Role::firstOrCreate(['name' => 'usuario']);

        $usuario = Usuario::factory()->create();
        $usuario->assignRole($usuarioRole);

        $this->actingAs($usuario)
             ->get('/admin/dashboard')
             ->assertStatus(403);
    }
}