<?php
declare(strict_types=1);

namespace NW;

use NW\Model\User;

class AdminController
{
    protected ?User $user;
    protected \PDO $db;

    public function __construct(?User $user, \PDO $db)
    {
        if(!isset($user) || !$user->isAdmin()) {
            $this->notFound();
        }

        $this->user = $user;
        $this->db = $db;
    }

    public function notFound(): void
    {
        http_response_code(404);

        $view = new View();
        $view->setVar('user', $this->user ?? null);
        $view->display(VIEW_PATH . '/Error/NotFound.phtml');
        die();
    }
}