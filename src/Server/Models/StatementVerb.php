<?php

namespace LRSDA\Server\Models;

/**
 * Représente un acteur dans un Statement xAPI (version simplifiée)
 */

class StatementVerb
{
    private string $id;
    private string $display;

    public function __construct(string $id, string $display)
    {
        $this->id = $id;
        $this->display = $display;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function setDisplay(string $display): void
    {
        $this->display = $display;
    }
}