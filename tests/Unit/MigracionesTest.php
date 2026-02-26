<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigracionesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    // public function test_example(): void
    // {
    // $this->assertTrue(true);
    // }

    public function test_existe_tabla_sectores()
    {
        $this->assertTrue(Schema::hasTable('sectores'));
    }

    public function test_existe_tabla_dependencias()
    {
        $this->assertTrue(Schema::hasTable('dependencias'));
    }

    public function test_existe_tabla_usuarios()
    {
        $this->assertTrue(Schema::hasTable('usuarios'));
    }

    public function test_existe_tabla_regiones()
    {
        $this->assertTrue(Schema::hasTable('regiones'));
    }

    public function test_existe_tabla_municipios()
    {
        $this->assertTrue(Schema::hasTable('municipios'));
    }

    public function test_existe_tabla_indicadores()
    {
        $this->assertTrue(Schema::hasTable('indicadores'));
    }

    public function test_existe_tabla_formularios()
    {
        $this->assertTrue(Schema::hasTable('formularios'));
    }

    public function test_existe_tabla_anexos()
    {
        $this->assertTrue(Schema::hasTable('anexos'));
    }

    public function test_existe_tabla_cargas()
    {
        $this->assertTrue(Schema::hasTable('cargas'));
    }

    public function test_existe_tabla_detallecargas()
    {
        $this->assertTrue(Schema::hasTable('detallecargas'));
    }

    public function test_existe_tabla_bitacoras()
    {
        $this->assertTrue(Schema::hasTable('bitacoras'));
    }

    public function test_existe_tabla_permissions()
    {
        $this->assertTrue(Schema::hasTable('permissions'));
    }
}
