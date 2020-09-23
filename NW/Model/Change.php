<?php
declare(strict_types=1);

namespace NW\Model;

use DateTime;

final class Change
{
    private int $id;
    private DateTime $time;
    private string $type;
    private string $description;

    public function __construct(int $id, DateTime $time, string $type, $description)
    {
        $this->id = $id;
        $this->time = $time;
        $this->type = $type;
        $this->description = $description;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function time(): DateTime
    {
        return $this->time;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function description(): string
    {
        return $this->description;
    }
}