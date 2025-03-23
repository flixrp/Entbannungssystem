<?php
// display errors
/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
*/
define("_EXEC", true);

// include basic help functions
require_once "../include/functions.inc.php";
require_once "inc/functions.inc.php";
require_once "inc/Login.php";

require_once "../include/appeals/Appeals.php";
require_once "../include/register/Register.php";

// autoloader
spl_autoload_register("autoloadClass");
function autoloadClass($className) {
    $file = '../include/' . $className . '.php';
    if (is_file($file)) {
        require_once $file;
    }
    $file = 'src/Controllers/' . $className . '.php';
    if (is_file($file)) {
        require_once $file;
    }
    $file = 'src/Traits/' . $className . '.php';
    if (is_file($file)) {
        require_once $file;
    }
}


Session::create();

$user = new Login();
$user->login();


define("CONTROLLER", isset($_GET['controller']) ? (string)$_GET['controller'] : 'appeal');
define("ACTION", isset($_GET['action']) ? (string)$_GET['action'] : 'index');

$controllerName = ucfirst(CONTROLLER) . 'Controller';
$controllerFile = 'src/Controllers/' . $controllerName . '.php';

if (is_file($controllerFile)) {

    require_once $controllerFile;
    $requestController = new $controllerName();
    $requestController->run(ACTION);

} else {
    redirect($_SERVER["SCRIPT_NAME"]);
}