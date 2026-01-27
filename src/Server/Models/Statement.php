<?php

namespace LRSDA\Server\Models;

/**
 * Représente un statement xAPI (version simplifiée)
 */
class Statement
{
    private string $id;
    // private string $actorName;
    // private string $actorMbox;
    private string $verbId;
    private string $objectId;

    public function __construct(
        string $id,
        // string $actorName,
        // string $actorMbox,
        string $verbId,
        string $objectId
    ) {
        $this->id = $id;
        // $this->actorName = $actorName;
        // $this->actorMbox = $actorMbox;
        $this->verbId = $verbId;
        $this->objectId = $objectId;
    }

    // --------
    // Getters 
    // --------

    public function getId(): string
    {
        return $this->id;
    }

    // public function getActorName(): string
    // {
    //     return $this->actorName;
    // }

    // public function getActorMbox(): string
    // {
    //     return $this->actorMbox;
    // }

    public function getVerbId(): string
    {
        return $this->verbId;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }
}
