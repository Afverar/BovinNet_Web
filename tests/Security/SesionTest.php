<?php

declare(strict_types=1);

namespace BovinNet\Tests\Security;

use BovinNet\Security\Sesion;
use PHPUnit\Framework\TestCase;

/**
 * Pruebas unitarias del token anti-CSRF (Sesion::token() y
 * Sesion::validarToken()). Estas dos funciones solo leen y escriben el
 * arreglo $_SESSION, sin necesitar una sesion HTTP real ni una base de
 * datos, por lo que se pueden probar de forma aislada simulando
 * $_SESSION como un arreglo comun.
 */
final class SesionTest extends TestCase
{
    protected function setUp(): void
    {
        // Simula el inicio de una sesion vacia antes de cada prueba,
        // para que ninguna prueba dependa del resultado de otra.
        $_SESSION = [];
    }

    public function testTokenGeneraUnaCadenaHexadecimalDe64Caracteres(): void
    {
        $token = Sesion::token();

        $this->assertSame(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $token);
    }

    public function testTokenDevuelveSiempreElMismoValorDentroDeLaMismaSesion(): void
    {
        $primero = Sesion::token();
        $segundo = Sesion::token();

        $this->assertSame($primero, $segundo);
    }

    public function testValidarTokenConElValorCorrectoEsValido(): void
    {
        $token = Sesion::token();

        $this->assertTrue(Sesion::validarToken($token));
    }

    public function testValidarTokenConUnValorIncorrectoNoEsValido(): void
    {
        Sesion::token();

        $this->assertFalse(Sesion::validarToken('un-token-que-no-corresponde'));
    }

    public function testValidarTokenSinNingunTokenEnSesionNoEsValido(): void
    {
        // setUp() ya dejo $_SESSION vacio, sin llamar a Sesion::token().
        $this->assertFalse(Sesion::validarToken('cualquier-valor'));
    }

    public function testValidarTokenConValorNoTextualNoEsValido(): void
    {
        Sesion::token();

        $this->assertFalse(Sesion::validarToken(null));
        $this->assertFalse(Sesion::validarToken(['no', 'es', 'texto']));
    }
}
