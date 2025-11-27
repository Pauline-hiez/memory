<?php

class Card
{
    private int|string $value;
    private bool $reveled = false;

    // $value peut être un int (Vikings) ou un string (GOT)
    public function __construct(int|string $value)
    {
        $this->value = $value;
    }

    public function getValue(): int|string
    {
        return $this->value;
    }

    public function isReveled(): bool
    {
        return $this->reveled;
    }

    public function hide(): void
    {
        $this->reveled = false;
    }

    public function reveal(): void
    {
        $this->reveled = true;
    }

    public function getImagePath(): string
    {
        return "/memory/assets/img/vikings/" . $this->getImageName();
    }

    private function getImageName()
    {
        $map = [
            1 => "bjorn.jpg",
            2 => "ragnar.jpg",
            3 => "lagertha.jpg",
            4 => "ivar.jpg",
            5 => "floki.jpg"
        ];
        return $map[$this->value] ?? "carte.jpg";
    }
}
