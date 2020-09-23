<?php
declare(strict_types=1);

namespace NW\Controller;

use DateTime;
use NW\Controller;
use NW\Model\Change;
use NW\Model\Feature as FeatureModel;

final class Feature extends Controller
{
    public function index(): void
    {
        $q = $this->db->query('SELECT * FROM nw.feature ORDER BY timestamp DESC LIMIT 1 OFFSET 0');

        $features = [];
        foreach ($q->fetchAll() as $feature) {
            $features[] = new FeatureModel(
                (int)$feature['id'],
                new DateTime($feature['timestamp']),
                $feature['title'],
                $feature['description']
            );
        }

        $q = $this->db->query('SELECT * FROM nw.change ORDER BY timestamp DESC LIMIT 20 OFFSET 0');

        $changelog = [];
        foreach ($q->fetchAll() as $change) {
            $changelog[] = new Change(
                (int)$change['id'],
                new DateTime($change['timestamp']),
                $change['type'],
                $change['description']
            );
        }

        $this->view->setVar('features', $features);
        $this->view->setVar('changelog', $changelog);
        $this->view->display(VIEW_PATH . '/Features.phtml');
    }

    public function list(int $page): void
    {
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $q = $this->db->prepare(
            'SELECT * FROM nw.feature ORDER BY `timestamp` DESC LIMIT :limit OFFSET :offset'
        );
        $q->bindParam('limit', $limit, \PDO::PARAM_INT);
        $q->bindParam('offset', $offset, \PDO::PARAM_INT);
        $q->execute();
        $result = $q->fetchAll();

        if(empty($result)) {
            $this->notFound();
        }

        $features = [];
        foreach ($result as $feature) {
            $features[] = new FeatureModel(
                (int)$feature['id'],
                new DateTime($feature['timestamp']),
                $feature['title'],
                $feature['description']
            );
        }

        $q = $this->db->query('SELECT COUNT(*) FROM nw.feature');
        $last = $page * $limit + 1 > $q->fetch()[0];

        $this->view->setVar('page', $page);
        $this->view->setVar('last', $last);
        $this->view->setVar('features', $features);
        $this->view->display(VIEW_PATH . '/FeatureList.phtml');
    }
}