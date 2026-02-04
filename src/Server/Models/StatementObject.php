<?php

namespace LRSDA\Server\Models;

/**
 * Représente un acteur dans un Statement xAPI (version simplifiée)
 */

class StatementObject
{
    private string $objectType;
    private string $id;
    private string $definition;

    public function __construct(string $objectType, string $id, string $definition)
    {
        $this->objectType = $objectType;
        $this->id = $id;
        $this->definition = $definition;
    }

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
}