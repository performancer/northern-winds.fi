<?php
declare(strict_types=1);

namespace NW\Controller\Admin;

use NW\AdminController;
use NW\Model\User as UserModel;
use NW\PasswordGenerator;
use NW\POL;
use NW\View;

final class User extends AdminController
{
    public function changePassword(): void
    {
        $view = new View();
        $view->setVar('user', $this->user);
        $view->display(VIEW_PATH . '/Admin/ChangePassword.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function list(): void
    {
        $q = $this->db->query('SELECT * FROM nw.user WHERE approved = true ORDER BY id');

        $users = [];
        foreach($q as $row) {
            $users[] = UserModel::withRow($row);
        }

        $view = new View();
        $view->setVar('user', $this->user);
        $view->setVar('users', $users);
        $view->display(VIEW_PATH . '/Admin/UserList.phtml');
        echo '<script type="module" src="/assets/js/Admin.js"></script>';
    }

    public function approve(): void
    {
        $q = $this->db->prepare('SELECT * FROM nw.user WHERE id = ? AND approved = false');
        $q->execute([(int)$_POST['id']]);

        if (!($row = $q->fetch())) {
            echo 'Jotakin meni mönkään.';
            return;
        }

        $user = UserModel::withRow($row);
        $account = $user->account();
        $password = (new PasswordGenerator())->generate(4);

        if ((new POL())->createAccount($account, $password, $user->email(), $this->user->username())) {
            $message = "Hakemuksesi on hyväksytty!\n\n
            Käyttäjä: $account \n
            Salasana: $password\n\n
            Tervetuloa mukaan ropeltamaan. Jos jokin asia jäi askarruttamaan, voit etsiä apua Northern Windsin 
            Discord-kanavalta. Siellä voit myöskin tutustua muihin pelitovereihisi.\n\n
            Mukavia pelihetkiä Northern Windsin parissa!\n\n
            https://northern-winds.fi";

            $headers = 'From: info@northern-winds.fi' . "\r\n" . 'X-Mailer: PHP/' . PHP_VERSION;

            if (mail($user->email(), 'Pelitilisi Northern Winds Shardille', $message, $headers)) {
                echo sprintf('Ilmoitus lähetetty sähköpostiin %s <br><br>', $user->email());
            } else {
                echo sprintf('<span class="red">Ilmoituksen lähetys epäonnistui: %s</span><br><br>',
                    $user->email());
            }

            $q = $this->db->prepare('UPDATE nw.user SET approved = true WHERE id = ?');
            $q->execute([$user->id()]);

            echo 'Tili ' . $account . ' luotu onnistuneesti.';
            return;
        }

        echo 'Pelitilin luonti epäonnistui! Yritä myöhemmin uudelleen.';
    }

    public function refuse(): void
    {
        $id = (int)$_POST['id'];

        $q = $this->db->prepare('SELECT * FROM nw.user WHERE id = ? AND approved = false');
        $q->execute([(int)$_POST['id']]);

        if (!($row = $q->fetch())) {
            echo 'Jotakin meni mönkään.';
            return;
        }

        $user = UserModel::withRow($row);

        $q = $this->db->prepare('DELETE FROM nw.user  WHERE id = ? AND approved = false');
        $q->execute([$id]);

        $message = "Hakemuksesi on hylätty.\n\n
        Yleisimmät syyt hylkäämiselle:\n
        1. Kenttien täyttö oli epäasiallista.\n
        2. Sinulla  on jo entuudestaan pelitili.\n\n
        Huomattuasi missä kohtaa olet toiminut väärin, tee uusi sääntöjen mukainen hakemus. Jos et mielestäsi ole 
        tehnyt mitään väärää, ota yhteyttä Northern Windsin henkilökuntaan Discordin kautta. \n\n
        https://northern-winds.fi";

        $headers = 'From: info@northern-winds.fi' . "\r\n" . 'X-Mailer: PHP/' . PHP_VERSION;

        if (mail($user->email(), 'Pelitilisi Northern Winds Shardille', $message, $headers)) {
            echo sprintf('Ilmoitus lähetetty sähköpostiin %s <br><br>', $user->email());
        } else {
            echo sprintf('<span class="red">Ilmoituksen lähetys epäonnistui: %s</span><br><br>', $user->email());
        }

        echo sprintf('Käyttäjän "%s" hakemus hylätty.', $user->username());
    }
}