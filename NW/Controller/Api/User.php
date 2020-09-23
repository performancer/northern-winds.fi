<?php
declare(strict_types=1);

namespace NW\Controller\Api;

use NW\ApiController;
use NW\PasswordGenerator;

final class User extends ApiController
{
    public function register(): void
    {
        if(isset($this->user)) {
            $this->notFound();
        }

        if (!isset($_POST['rules'], $_POST['single-account'])) {
            http_response_code(400);
            echo 'Et ole hyväksynyt shardin ehtoja.';
            return;
        }

        //TODO: changes when player accounts are available
        $email = $_POST['email'];
        $username = $_POST['account']; // $_POST['username']
        //$account = $_POST['account'];
        //this is just a default password that is never supposed to be used since player users are not yet allowed
        $password = $_POST['password'] ?? (new PasswordGenerator())->generate(8);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo 'Syötä oikea sähköposti';
            return;
        }

        if(strlen($username) < 4) {
            echo 'Käyttäjänimen tulee olla vähintään 4 merkkiä pitkä.';
            http_response_code(400);
            return;
        }

        if(strlen($username) > 15 ) {
            echo 'Käyttäjänimi ei saa olla pitempi kuin 15 merkkiä.';
            http_response_code(400);
            return;
        }

        if(preg_match('/[^A-Za-z0-9.#\\-$]/', $username)) {
            http_response_code(400);
            echo 'Käyttäjänimessä ei saa olla erikoismerkkejä.';
            return;
        }

        if(strlen($password) < 7) {
            http_response_code(400);
            echo 'Salasanan tulee olla vähintään 7 merkkiä pitkä.';
            return;
        }

        $password = password_hash($password, PASSWORD_BCRYPT);

        $q = $this->db->prepare(
            'INSERT INTO nw.user (email, username, account, password) VALUES (?, ?, ?, ?)'
        );
        if ($q->execute([$email, $username, $username, $password])) {
            echo 'Rekisteröinti onnistui, odotathan kunnes henkilökuntamme tarkistaa hakemuksesi.';
        } else {
            http_response_code(400);
            echo 'Sähköposti on jo käytössä tai käyttäjätunnus on jo olemassa.';
        }
    }

    public function changePassword(): void
    {
        if(!isset($this->user)) {
            $this->notFound();
        }

        $id = $this->user->id();
        $password = $_POST['old-password'];

        $q = $this->db->prepare('SELECT password FROM nw.user WHERE id = ? AND active = true AND approved = true');
        $q->execute([$id]);

        $result = $q->fetch();

        if($result && password_verify($password, $result['password'])) {

            $password = $_POST['new-password'];

            if(strlen($password) < 7) {
                http_response_code(400);
                echo 'Salasanan tulee olla vähintään 7 merkkiä pitkä.';
                return;
            }

            $password = password_hash($password, PASSWORD_BCRYPT);

            $q = $this->db->prepare('UPDATE nw.user SET password = ? WHERE id = ?');
            $q->execute([$password, $id]);

            echo 'Salasana vaihdettu onnistuneesti.';
        } else {
            http_response_code(400);
            echo 'Väärä salasana.';
        }
    }

    public function login(): void
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $q = $this->db->prepare(
            'SELECT id, password FROM nw.user WHERE username = ? AND active = true AND approved = true');
        $q->execute([$username]);
        $result = $q->fetch();

        if($result !== false && password_verify($password, $result['password'])) {
            $_SESSION = ["login" => $result['id']];
            echo 'Kirjautuminen onnistui.';
        } else {
            http_response_code(400);
            echo 'Väärä salasana tai käyttäjätunnus.';
        }
    }

    public function logout(): void
    {
        session_destroy();
        echo 'Kirjauduit ulos.';
    }
}