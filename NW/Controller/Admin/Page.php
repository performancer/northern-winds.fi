<?php
declare(strict_types=1);

namespace NW\Controller\Admin;

use DateTime;
use NW\AdminController;
use NW\Model\Page as PageModel;
use NW\View;

final class Page extends AdminController
{
    public function index(int $id): void
    {
        $q = $this->db->prepare('SELECT * FROM nw.page WHERE id = ?');
        $q->execute([$id]);

        if(!$row = $q->fetch()) {
            $this->notFound();
        }

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('page', PageModel::withRow($row));
        $view->display(VIEW_PATH . '/Admin/PageEditor.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function list(): void
    {
        $q = $this->db->query('SELECT id, timestamp, url FROM nw.page ORDER BY url');

        $pages = [];
        foreach($q as $row) {
            $pages[] = new PageModel((int)$row['id'],  new DateTime($row['timestamp']), $row['url'], '');
        }

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('pages', $pages);
        $view->display(VIEW_PATH . '/Admin/PageList.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function update(): void
    {
        $id = (int)$_POST['id'];
        $allowed_tags = array("html", "body", "b", "br", "em", "hr", "i", "li", "ol", "p", "s", "div", "span", "table",
            "tr", "td", "u", "ul", "h1", "h2", "h3", "h4", "a", "img");
        $content = strip_tags($_POST['pageContent'], $allowed_tags);

        $q = $this->db->prepare('UPDATE nw.page SET content = ?, timestamp = CURRENT_TIMESTAMP WHERE id = ?');
        $q->execute([$content, $id]);

        echo 'Sivu päivitetty';
    }
}