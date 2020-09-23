<?php
declare(strict_types=1);

namespace NW;

use DateTime;
use NW\Config\Enviroment;
use NW\Controller\Admin\Changelog as AdminChangelog;
use NW\Controller\Admin\Feature as AdminFeature;
use NW\Controller\Admin\Index;
use NW\Controller\Admin\Page as AdminPage;
use NW\Controller\Admin\User as AdminUser;
use NW\Controller\Api\User;
use NW\Controller\Changelog;
use NW\Controller\Feature;
use NW\Controller\Login;
use NW\Controller\Page;
use NW\Controller\Register;
use NW\Model\User as UserModel;
use PDO;

class Router
{
    private PDO $db;

    public function __construct()
    {
        $this->db = new PDO(Enviroment::$config['db_dsn']);
    }

    public function route(): void
    {
        //TODO: move the server status check to cron
        $q = $this->db->query('SELECT * FROM nw.server');

        foreach ($q->fetchAll() as $row) {
            $hostname = $row['hostname'];
            $port = (int)$row['port'];
            $timestamp = new DateTime($row['last_status_check']);

            if ($timestamp->getTimestamp() < time() - 5*60) {
                $status = (new POL())->serverStatus($hostname, $port);
                $q = $this->db->prepare(
                    'UPDATE nw.server 
                                SET last_status_check = NOW(), status = :status 
                                WHERE hostname = :hostname'
                );
                $q->execute(['status' => (int)$status, 'hostname' => $hostname]);
            }
        }

        //TODO: this should be a middleware
        $uri = $_SERVER['REQUEST_URI'];

        if(substr($uri, -1) !== '/') {
            header('Location: '. $uri . '/', true, 301);
            exit;
        }

        //TODO: abstract this
        $user = null;

        if(isset($_SESSION['login'])) {
            $q = $this->db->prepare('SELECT * FROM nw.user WHERE id = ? AND active = true AND approved = true');
            $q->execute([$_SESSION['login']]);
            if ($row = $q->fetch()) {
                $user = UserModel::withRow($row);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            switch($uri) {
                case '/admin/api/feature/add/': (new AdminFeature($user, $this->db))->add(); break;
                case '/admin/api/feature/update/': (new AdminFeature($user, $this->db))->update(); break;
                case '/admin/api/feature/delete/': (new AdminFeature($user, $this->db))->delete(); break;
                case '/admin/api/changelog/add/': (new AdminChangelog($user, $this->db))->add(); break;
                case '/admin/api/changelog/delete/': (new AdminChangelog($user, $this->db))->delete(); break;
                case '/admin/api/page/update/': (new AdminPage($user, $this->db))->update(); break;
                case '/admin/api/user/approve/': (new AdminUser($user, $this->db))->approve(); break;
                case '/admin/api/user/refuse/': (new AdminUser($user, $this->db))->refuse(); break;
                case '/api/change-password/': (new User($user, $this->db))->changePassword(); break;
                case '/api/login/': (new User($user, $this->db))->login(); break;
                case '/api/logout/': (new User($user, $this->db))->logout(); break;
                case '/api/register/': (new User($user, $this->db))->register(); break;
                default: http_response_code(404); break;
            }
        } else {

            //TODO: abstract this
            $q = $this->db->prepare('SELECT status FROM nw.server WHERE hostname = ?');
            $q->execute([Enviroment::$config['game_server_host']]);

            if ($row = $q->fetch()) {
                define('SERVER_STATUS', (bool)$row['status'] ? 'online' : 'offline');
            }

            switch($uri) {
                case '/admin/': (new Index($user, $this->db))->index(); break;
                case '/admin/changelog/': (new AdminChangelog($user, $this->db))->index(); break;
                case '/admin/features/': (new AdminFeature($user, $this->db))->list(); break;
                case '/admin/users/': (new AdminUser($user, $this->db))->list(); break;
                case '/admin/pages/': (new AdminPage($user, $this->db))->list(); break;
                case '/admin/profile/': (new AdminUser($user, $this->db))->changePassword(); break;
                case '/rekisterointi/': (new Register($user, $this->db))->index(); break;
                case '/ominaisuudet/': (new Feature($user, $this->db))->index(); break;
                case '/kirjautuminen/': (new Login($user, $this->db))->index(); break;
                default:

                    //TODO: make it more dynamic

                    if (strpos($uri, '/kehitysloki/') === 0) {
                        $array = explode('/', trim($uri, '/'));
                        (new Changelog($user, $this->db))->list((int)end($array));
                        return;
                    }

                    if (strpos($uri, '/ominaisuudet/') === 0) {
                        $array = explode('/', trim($uri, '/'));
                        (new Feature($user, $this->db))->list((int)end($array));
                        return;
                    }

                    if (strpos($uri, '/admin/features/') === 0) {
                        $array = explode('/', trim($uri, '/'));
                        (new AdminFeature($user, $this->db))->index((int)end($array));
                        return;
                    }

                    if (strpos($uri, '/admin/pages/') === 0) {
                        $array = explode('/', trim($uri, '/'));
                        (new AdminPage($user, $this->db))->index((int)end($array));
                        return;
                    }

                    (new Page($user, $this->db))->index($uri);
                    break;
            }
        }
    }
}