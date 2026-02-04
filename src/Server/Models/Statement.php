<?php

namespace LRSDA\Server\Models;

use DateTime;

/**
 * Représente un Statement xAPI (version simplifiée)
 */

class Statement
{
    private string $id;
    private StatementActor $actor;
    private StatementVerb $verb;
    private DateTime $timestamp;
    private StatementObject $object;
    private DateTime $stored;
    private StatementAuthority $authority;
    private string $version;

    public function __construct(
        string $id,
        StatementActor $actor,
        StatementVerb $verb,
        DateTime $timestamp,
        StatementObject $object,
        DateTime $stored,
        StatementAuthority $authority,
        string $version
    ) {
        $this->id = $id;
        $this->actor = $actor;
        $this->verb = $verb;
        $this->timestamp = $timestamp;
        $this->object = $object;
        $this->stored = $stored;
        $this->authority = $authority;
        $this->version = $version;
    }

    // --------
    // Getters 
    // --------

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getActor(): StatementActor
    {
        return $this->actor;
    }

    public function setActor(StatementActor $actor): void
    {
        $this->actor = $actor;
    }


    public function getVerb(): StatementVerb
    {
        return $this->verb;
    }

    public function setVerb(StatementVerb $verb): void
    {
        $this->verb = $verb;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getObject(): StatementObject
    {
        return $this->object;
    }

    public function setObject(StatementObject $object): void
    {
        $this->object = $object;
    }

    public function getStored(): DateTime
    {
        return $this->stored;
    }

    public function setStored(DateTime $stored): void
    {
        $this->stored = $stored;
    }

    public function getAuthority(): StatementAuthority
    {
        return $this->authority;
    }

    public function setAuthority(StatementAuthority $authority): void
    {
        $this->authority = $authority;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
