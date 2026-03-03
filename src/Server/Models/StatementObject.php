<?php

namespace LRSDA\Server\Models;

/**
 * Représente un objet dans un Statement xAPI
 */

class StatementObject
{
    private string $objectType;
    private string $id;
    private string $definition;
    private string $name;

    public function __construct(string $objectType, string $id, string $definition, string $name)
    {
        $this->objectType = $objectType;
        $this->id = $id;
        $this->definition = $definition;
        $this->name = $name;
    }

    // --------
    // Getters & Setters
    // --------

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function setObjectType(string $objectType): void
    {
        $this->objectType = $objectType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function setDefinition(string $definition): void
    {
        $this->definition = $definition;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}