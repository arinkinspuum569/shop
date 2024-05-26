<?php

namespace common;
require_once "components/page.php";
require_once "components/a_content.php";

class second extends \common\a_content
{

    function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['login'])) {
            header('Location: auth.php');
        }
    }

    function show_content()
    {
        print("Привет, {$_SESSION['login']}!");
        print("<br/><a href='auth.php?exit=1'>Выход</a>");
    }
}

new \common\page(new second());
