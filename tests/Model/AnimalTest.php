<?php

declare(strict_types=1);

namespace BovinNet\Tests\Model;

use BovinNet\Model\Animal;
use PHPUnit\Framework\TestCase;

/**
 * Pruebas unitarias de Animal::tieneAreteValido(), la unica regla de
 * negocio que vive en el modelo. No requieren base de datos.
 */
final class AnimalTest extends TestCase
{
    private function crearAnimal(string $arete): Animal
    {
        return new Animal(
            0,
            $arete,
            'Estrella',
            'Hembra',
            180.5,
            '2026-01-15',
            1
        );
    }

    public function testAreteValidoDentroDelLimite(): void
    {
        $animal = $this->crearAnimal('0512');
        $this->assertTrue($animal->tieneAreteValido());
    }

    public function testAreteVacioNoEsValido(): void
    {
        $animal = $this->crearAnimal('');
        $this->assertFalse($animal->tieneAreteValido());
    }

    public function testAreteEnElLimiteExactoEsValido(): void
    {
        $arete = str_repeat('9', Animal::ARETE_LONGITUD_MAXIMA);
        $animal = $this->crearAnimal($arete);
        $this->assertTrue($animal->tieneAreteValido());
    }

    public function testAreteQueSuperaElLimiteNoEsValido(): void
    {
        $arete = str_repeat('9', Animal::ARETE_LONGITUD_MAXIMA + 1);
        $animal = $this->crearAnimal($arete);
        $this->assertFalse($animal->tieneAreteValido());
    }

    public function testLosGettersDevuelvenLosValoresDelConstructor(): void
    {
        $animal = new Animal(
            idAnimal: 7,
            arete: '0231',
            nombre: 'Lucero',
            sexo: 'Hembra',
            peso: 210.0,
            fechaNacimiento: '2025-03-10',
            idLote: 2,
            idAnimalMadre: 4
        );

        $this->assertSame(7, $animal->getIdAnimal());
        $this->assertSame('0231', $animal->getArete());
        $this->assertSame('Lucero', $animal->getNombre());
        $this->assertSame('Hembra', $animal->getSexo());
        $this->assertSame(210.0, $animal->getPeso());
        $this->assertSame('2025-03-10', $animal->getFechaNacimiento());
        $this->assertSame(2, $animal->getIdLote());
        $this->assertSame(4, $animal->getIdAnimalMadre());
    }

    public function testNombreYMadreOpcionalesPuedenSerNulos(): void
    {
        $animal = new Animal(0, '0304', null, 'Macho', null, null, 1);

        $this->assertNull($animal->getNombre());
        $this->assertNull($animal->getPeso());
        $this->assertNull($animal->getFechaNacimiento());
        $this->assertNull($animal->getIdAnimalMadre());
    }
}
