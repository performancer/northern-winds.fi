<?php
declare(strict_types=1);

namespace NW\Model;

use DateTime;

final class Feature
{
    private int $id;
    private DateTime $time;
    private string $title;
    private string $description;

    public function __construct(int $id, DateTime $time, string $title, string $description)
    {
        $this->id = $id;
        $this->time = $time;
        $this->title = $title;
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

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }
}