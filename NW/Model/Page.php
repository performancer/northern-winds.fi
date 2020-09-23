<?php
declare(strict_types=1);

namespace NW\Model;

use DateTime;

final class Page
{
    private int $id;
    private DateTime $time;
    private string $url;
    private string $content;

    public function __construct(int $id, DateTime $time, string $url, string $content)
    {
        $this->id = $id;
        $this->time = $time;
        $this->url = $url;
        $this->content = $content;
    }

    public static function withRow( array $row): Page
    {
        return new Page(
            (int)$row['id'],
            new DateTime($row['timestamp']),
            $row['url'],
            $row['content']
        );
    }


    public function id(): int
    {
        return $this->id;
    }

    public function time(): DateTime
    {
        return $this->time;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function content(): string
    {
        return $this->content;
    }
}