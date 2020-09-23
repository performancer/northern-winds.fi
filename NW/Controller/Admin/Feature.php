<?php
declare(strict_types=1);

namespace NW\Controller\Admin;

use DateTime;
use NW\AdminController;
use NW\Model\Feature as FeatureModel;
use NW\View;

final class Feature extends AdminController
{
    public function index(int $id): void
    {
        $q = $this->db->prepare('SELECT * FROM nw.feature WHERE id = ?');
        $q->execute([$id]);

        if(!($feature = $q->fetch())) {
            $this->notFound();
        }

        $feature = new FeatureModel(
            (int)$feature['id'],
            new DateTime($feature['timestamp']),
            $feature['title'],
            $feature['description']
        );

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('feature', $feature);
        $view->display(VIEW_PATH . '/Admin/FeatureEditor.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function list(): void
    {
        $q = $this->db->query('SELECT * FROM nw.feature ORDER BY timestamp DESC');

        $features = [];
        foreach ($q->fetchAll() as $feature) {
            $features[] = new FeatureModel(
                (int)$feature['id'],
                new DateTime($feature['timestamp']),
                $feature['title'],
                $feature['description']
            );
        }

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('features', $features);
        $view->display(VIEW_PATH . '/Admin/Features.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function add(): void
    {
        $title = trim(strip_tags($_POST['title']));
        $allowed_tags = array("b", "i", "li", "p", "ul");
        $description = trim(strip_tags($_POST['description'], $allowed_tags));

        if(strlen($title) < 3) {
            http_response_code(400);
            echo 'Ominaisuudella pitää olla otsikko.';
        }

        if(strlen($description) < 3) {
            http_response_code(400);
            echo 'Yritä nyt vähän jotain edes kuvailla.';
        }

        $q = $this->db->prepare('INSERT INTO nw.feature (title, description) VALUES (?, ?)');
        $q->execute([$title, $description]);

        echo 'Ominaisuus lisätty!';
    }

    public function update(): void
    {
        $id = (int)$_POST['id'];
        $title = trim(strip_tags($_POST['title']));
        $allowed_tags = array("b", "i", "li", "p", "ul");
        $description = trim(strip_tags($_POST['description'], $allowed_tags));

        $q = $this->db->prepare('UPDATE nw.feature SET title = ?, description = ? WHERE id = ?');
        $q->execute([$title, $description, $id]);

        echo 'Ominaisuus päivitetty';
    }

    public function delete(): void
    {
        $id = (int)$_POST['id'];

        $q = $this->db->prepare('DELETE FROM nw.feature WHERE id = ?');
        $q->execute([$id]);

        echo 'Ominaisuus poistettu!';
    }
}