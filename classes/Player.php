<?php

class Player
{
    private int $id;
    private string $login;

    public function __construct(int $id, string $login)
    {
        $this->id = $id;
        $this->login = $login;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    // Pour compatibilité avec finish.php
    public function getUsername(): string
    {
        return $this->login;
    }
}
