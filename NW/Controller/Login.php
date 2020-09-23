<?php
declare(strict_types=1);

namespace NW\Controller;

use NW\Controller;

final class Login extends Controller
{
    public function index(): void
    {
        if (isset($_SESSION['login'])) {
            header("Location: /");
            return;
        }

        $this->view->display(VIEW_PATH . '/Login.phtml');
    }
}