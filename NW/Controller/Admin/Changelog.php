<?php
declare(strict_types=1);

namespace NW\Controller\Admin;

use DateTime;
use NW\AdminController;
use NW\Model\Change;
use NW\View;

final class Changelog extends AdminController
{
    public function index(): void
    {
        $q = $this->db->query('SELECT * FROM nw.change ORDER BY timestamp DESC');

        $changelog = [];
        foreach ($q->fetchAll() as $change) {
            $changelog[] = new Change(
                (int)$change['id'],
                new DateTime($change['timestamp']),
                $change['type'],
                $change['description']
            );
        }

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('changelog', $changelog);
        $view->display(VIEW_PATH . '/Admin/Changelog.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function add(): void
    {
        $type = $_POST['type'];
        $description = trim(strip_tags($_POST['description']));

        if(strlen($description) < 3) {
            http_response_code(400);
            echo 'Yritä nyt vähän jotain edes kuvailla.';
            return;
        }

        $q = $this->db->prepare('INSERT INTO nw.change (type, description) VALUES (?, ?)');
        $q->execute([$type, $description]);

        echo 'Kehitysmerkintä lisätty!';
    }

    public function delete(): void
    {
        $id = (int)$_POST['id'];

        $q = $this->db->prepare('DELETE FROM nw.change WHERE id = ?');
        $q->execute([$id]);

        echo 'Kehitysmerkintä poistettu!';
    }
}