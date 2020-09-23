<?php
declare(strict_types=1);

namespace NW\Controller;

use DateTime;
use NW\Controller;

final class Page extends Controller
{
    public function index($url): void
    {
        $q = $this->db->prepare('SELECT timestamp, content FROM nw.page WHERE url = ?');
        $q->execute([$url]);

        if(!$row = $q->fetch()) {
            $this->notFound();
        }

        $this->view->setVar('content', $row['content']);
        $this->view->setVar('time', new DateTime($row['timestamp']));
        $this->view->display(VIEW_PATH . '/Page.phtml');
    }
}