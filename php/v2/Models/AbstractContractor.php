<?php

namespace App\Models;

/**
 * @property Seller $Seller
 */
abstract class AbstractContractor
{
    private int $id;
    private ?string $mobile;
    private ?string $type;
    private ?string $email;
    private ?string $name;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function getById(int $resellerId): ?self
    {
        return new static($resellerId); // fakes the getById method
    }

    public function getFullName(): string
    {
        return trim($this->getName() . ' ' . $this->getId());
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}