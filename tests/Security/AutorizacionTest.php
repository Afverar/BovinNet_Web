<?php

declare(strict_types=1);

namespace BovinNet\Tests\Security;

use BovinNet\Security\Autorizacion;
use PHPUnit\Framework\TestCase;

/**
 * Pruebas unitarias de la matriz de permisos por rol
 * (Autorizacion::permite). No dependen de sesion ni de base de datos.
 */
final class AutorizacionTest extends TestCase
{
    public function testAdministradorTieneAccesoATodosLosModulos(): void
    {
        $modulos = ['dashboard', 'bovinos', 'nacimientos', 'muertes', 'vacunacion', 'reportes', 'cuenta'];
        foreach ($modulos as $modulo) {
            $this->assertTrue(
                Autorizacion::permite('Administrador', $modulo),
                "El Administrador deberia poder entrar a '$modulo'"
            );
        }
    }

    public function testVeterinarioNoTieneAccesoANacimientos(): void
    {
        $this->assertFalse(Autorizacion::permite('Veterinario', 'nacimientos'));
    }

    public function testVeterinarioSiTieneAccesoAVacunacionYMuertes(): void
    {
        $this->assertTrue(Autorizacion::permite('Veterinario', 'vacunacion'));
        $this->assertTrue(Autorizacion::permite('Veterinario', 'muertes'));
    }

    public function testOperarioNoTieneAccesoAMuertesNiReportes(): void
    {
        $this->assertFalse(Autorizacion::permite('Operario', 'muertes'));
        $this->assertFalse(Autorizacion::permite('Operario', 'reportes'));
    }

    public function testOperarioSiTieneAccesoABovinosYNacimientos(): void
    {
        $this->assertTrue(Autorizacion::permite('Operario', 'bovinos'));
        $this->assertTrue(Autorizacion::permite('Operario', 'nacimientos'));
    }

    public function testUnRolDesconocidoNoTieneAccesoANingunModulo(): void
    {
        $this->assertFalse(Autorizacion::permite('RolInexistente', 'dashboard'));
        $this->assertFalse(Autorizacion::permite('RolInexistente', 'bovinos'));
    }

    public function testTodosLosRolesPuedenEntrarACuenta(): void
    {
        foreach (['Administrador', 'Veterinario', 'Operario'] as $rol) {
            $this->assertTrue(Autorizacion::permite($rol, 'cuenta'));
        }
    }
}
