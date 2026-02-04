<?php

namespace LRSDA\Server\Models;

/**
 * Représente un compte dans un Statement xAPI (version simplifiée)
 */

class StatementAccount
{
    private string $name;
    private string $homePage;

    public function __construct(string $name, string $homePage)
    {
        $this->name = $name;
        $this->homePage = $homePage;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHomePage(): string
    {
        return $this->homePage;
    }

    public function setHomePage(string $homePage): void
    {
        $this->homePage = $homePage;
    }
}