<?php

declare(strict_types=1);

namespace BovinNet\Model;

/**
 * Representa un animal del hato registrado en BovinNet.
 */
class Animal
{
    public const ARETE_LONGITUD_MAXIMA = 10;

    private int $idAnimal;
    private string $arete;
    private ?string $nombre;
    private string $sexo;
    private ?float $peso;
    private ?string $fechaNacimiento;
    private int $idLote;
    private ?int $idAnimalMadre;

    public function __construct(
        int $idAnimal,
        string $arete,
        ?string $nombre,
        string $sexo,
        ?float $peso,
        ?string $fechaNacimiento,
        int $idLote,
        ?int $idAnimalMadre = null
    ) {
        $this->idAnimal        = $idAnimal;
        $this->arete           = $arete;
        $this->nombre          = $nombre;
        $this->sexo            = $sexo;
        $this->peso            = $peso;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->idLote          = $idLote;
        $this->idAnimalMadre   = $idAnimalMadre;
    }

    /**
     * Indica si el arete cumple el formato esperado por el negocio.
     */
    public function tieneAreteValido(): bool
    {
        return $this->arete !== ''
            && strlen($this->arete) <= self::ARETE_LONGITUD_MAXIMA;
    }

    public function getIdAnimal(): int           { return $this->idAnimal; }
    public function getArete(): string           { return $this->arete; }
    public function getNombre(): ?string         { return $this->nombre; }
    public function getSexo(): string            { return $this->sexo; }
    public function getPeso(): ?float            { return $this->peso; }
    public function getFechaNacimiento(): ?string{ return $this->fechaNacimiento; }
    public function getIdLote(): int             { return $this->idLote; }
    public function getIdAnimalMadre(): ?int     { return $this->idAnimalMadre; }
}
