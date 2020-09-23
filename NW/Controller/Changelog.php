<?php
declare(strict_types=1);

namespace NW\Controller;

use DateTime;
use NW\Controller;
use NW\Model\Change;

final class Changelog extends Controller
{
    public function list(int $page): void
    {
        $limit = 80;
        $offset = $page * $limit;

        $q = $this->db->prepare(
            'SELECT * FROM nw.change ORDER BY `timestamp` DESC LIMIT :limit OFFSET :offset'
        );
        $q->bindParam('limit', $limit, \PDO::PARAM_INT);
        $q->bindParam('offset', $offset, \PDO::PARAM_INT);
        $q->execute();
        $result = $q->fetchAll();

        if(empty($result)) {
            $this->notFound();
        }
        
        $changelog = [];
        foreach ($result as $change) {
            $changelog[] = new Change(
                (int)$change['id'],
                new DateTime($change['timestamp']),
                $change['type'],
                $change['description']
            );
        }

        $q = $this->db->query('SELECT COUNT(*) FROM nw.change');
        $last = ($page + 1) * $limit + 1 > $q->fetch()[0];

        $this->view->setVar('page', $page);
        $this->view->setVar('last', $last);
        $this->view->setVar('changelog', $changelog);
        $this->view->display(VIEW_PATH . '/Changelog.phtml');
    }
}