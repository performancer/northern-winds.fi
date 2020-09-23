<?php
declare(strict_types=1);

namespace NW;

use NW\Model\User;

class Controller
{
    protected View $view;
    protected ?User $user;
    protected \PDO $db;

    public function __construct(?User $user, \PDO $db)
    {
        $this->view = new View();
        $this->user = $user;
        $this->db = $db;

        $this->view->setVar('user', $this->user);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view->display(VIEW_PATH . '/Error/NotFound.phtml');
        die();
    }
}