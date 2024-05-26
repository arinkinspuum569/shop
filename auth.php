<?php

require_once "common/page.php";
require_once "common/a_content.php";
require_once "common/db_helper.php";

enum error_type: string{
    case INVALID_LOGIN = 'Некорректный логин';
    case INVALID_PASSWORD = 'Некорректный пароль';
    case EMPTY_FIELDS = 'Заполните поля';
    case UNMATCHED_PASSWORDS = 'Пароли не совпадают';
}

class auth extends common\a_content{

    private error_type $error_type;

    function __construct()
    {
        parent::__construct();
        if (isset($_GET['exit'])){
            unset($_SESSION['login']);
        }
        unset($this->error_type);
        if (
            !isset($_POST['login']) ||
            !isset($_POST['password'])
        ) {
            return;
        }

        $login = trim(htmlspecialchars($_POST['login']));

        $password = $_POST['password'];

        // TODO: Выполнить остальные проверки


        $db = \common\db_helper::getInstance();
        $w = array();
        $w['login'] = $login;
        $data = $db->get_all_data(\common\tables::USER, $w);
        print_r($data);
        print($data[0]['password']);
        if (password_verify($password, $data[0]['password'])){
            $_SESSION['login'] = $login;
            header('Location: second.php');
        } else {
            $this->error_type = error_type::INVALID_PASSWORD;
        }

    }

    function show_content(): void
    {
        if (isset($this->error_type)) {
            echo $this->error_type->value;
        }
        ?>
        <div class="form-wrapper">
            <span>Авторизация</span>
            <form method="post" action="auth.php">
                <div class="input-wrapper">
                    <label for="login">Логин</label>
                    <input name="login" type="text" />
                </div>
                <div class="input-wrapper">
                    <label for="password">Пароль</label>
                    <input name="password" type="password"/>
                </div>
                <button type="submit">Войти</button>
            </form>
            <a href="reg.php">Регистрация</a>
        </div>
        <?php
    }
}

new \common\page(new auth());
?>
