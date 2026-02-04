<?php

namespace LRSDA\Server\Models;

/**
 * Représente un acteur dans un Statement xAPI (version simplifiée)
 */

class StatementAuthority
{
    private string $objectType;
    private string $name;
    private StatementAccount $account;

    public function __construct(string $objectType, string $name, StatementAccount $account)
    {
        $this->objectType = $objectType;
        $this->name = $name;
        $this->account = $account;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }
    
    public function setObjectType(string $objectType): void
    {
        $this->objectType = $objectType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAccount(): StatementAccount
    {
        return $this->account;
    }

    public function setAccount(StatementAccount $account): void
    {
        $this->account = $account;
    }
}