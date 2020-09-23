<?php
declare(strict_types=1);

namespace NW\Model;

use DateTime;

final class User
{
    private int $id;
    private DateTime $time;
    private int $access;
    private string $email;
    private string $username;
    private string $account;
    private ?string $discord;
    private bool $active;
    private bool $approved;

    public function __construct(
        int $id,
        DateTime $time,
        int $access,
        string $email,
        string $username,
        string $account,
        ?string $discord,
        bool $active,
        bool $approved
    ) {
        $this->id = $id;
        $this->time = $time;
        $this->access = $access;
        $this->email = $email;
        $this->username = $username;
        $this->account = $account;
        $this->discord = $discord;
        $this->active = $active;
        $this->approved = $approved;
    }

    public static function withRow( array $row): User
    {
        return new User(
            (int)$row['id'],
            new DateTime($row['timestamp']),
            (int)$row['access'],
            $row['email'],
            $row['username'],
            $row['account'],
            $row['discord'],
            (bool)$row['active'],
            (bool)$row['approved'],
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

    public function email(): string
    {
        return $this->email;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function account(): string
    {
        return $this->account;
    }

    public function discord(): ?string
    {
        return $this->discord;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function approved(): bool
    {
        return $this->approved;
    }

    public function isAdmin(): bool
    {
        return $this->access > 0;
    }
}