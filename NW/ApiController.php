<?php
declare(strict_types=1);

namespace NW;

use NW\Model\User;

class ApiController
{
    protected ?User $user;
    protected \PDO $db;

    public function __construct(?User $user, \PDO $db)
    {
        $this->user = $user;
        $this->db = $db;
    }

    public function notFound(): void
    {
        http_response_code(404);
        die();
    }
}