<?php
declare(strict_types=1);

namespace NW\Controller\Admin;

use NW\AdminController;
use NW\Model\User as UserModel;
use NW\View;

final class Index extends AdminController
{
    public function index(): void
    {
        $q = $this->db->query('SELECT * FROM nw.user WHERE approved = false ORDER BY timestamp');

        $users = [];
        foreach($q as $row) {
            $users[] = UserModel::withRow($row);
        }

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('users', $users);
        $view->display(VIEW_PATH . '/Admin/UserApplications.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }
}