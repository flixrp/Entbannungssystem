<?php
require_once "include/Session.php";
require_once "include/Recaptcha.php";

Session::create();
Recaptcha::generateImage();