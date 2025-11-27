<?php
class CardGot
{
    private string $image;
    private bool $reveled = false;

    public function __construct(string $image)
    {
        $this->image = $image;
    }

    public function getValue(): string
    {
        return $this->image;
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
        return "/memory/assets/img/got/" . $this->image;
    }
}
