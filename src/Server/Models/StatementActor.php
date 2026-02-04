<?php

namespace LRSDA\Server\Models;

/**
 * Représente un acteur dans un Statement xAPI (version simplifiée)
 */

class StatementActor
{
    private string $ObjectType;
    private StatementAccount $account;

    public function __construct(string $ObjectType, StatementAccount $account)
    {
        $this->ObjectType = $ObjectType;
        $this->account = $account;
    }

    public function getObjectType(): string
    {
        return $this->ObjectType;
    }

    public function setObjectType(string $ObjectType): void
    {
        $this->ObjectType = $ObjectType;
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