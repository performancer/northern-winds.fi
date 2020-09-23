<?php
declare(strict_types=1);

namespace NW\Controller;

use NW\Controller;

final class Register extends Controller
{
    public function index(): void
    {
        if ($this->user) {
            header("Location: /", true, 302);
            exit;
        }

        $this->view->display(VIEW_PATH . '/Register.phtml');
    }
}