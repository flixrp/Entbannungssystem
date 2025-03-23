<?php

abstract class AbstractBase {
    protected $context = [];
    protected $template = "";

    public function run($action) {
        $this->addContext('action', $action);
        $this->addContext('actionErr', $this->getLastPageActionErr());

        $methodName = $action . 'Action';
        $this->setTemplate($methodName);

        if (method_exists($this, $methodName)) {
            $this->$methodName();
        } else {
            redirect($_SERVER["SCRIPT_NAME"]);
        }

        $this->render();
    }

    protected function setTemplate($template, $controller = null) {
        if (empty($controller)) {
            $controller = get_class($this);
        }

        $this->template = ucfirst($controller) . "/" . $template . ".tpl.php";
    }

    protected function getTemplate(): string {
        return $this->template;
    }

    protected function addContext(string $key, $value) {
        $this->context[$key] = $value;
    }

    /**
     * Shows a message on the next loaded page
     * @param string $message
     */
    protected function addNextPageActionErr(string $message) {
        if (isset($_SESSION["actionErr"])) {
            $_SESSION["actionErr"] .= $message;
        } else {
            $_SESSION["actionErr"] = $message;
        }
    }

    /**
     * Gets the actionErr from the last page out of the session
     * @return string
     */
    private function getLastPageActionErr(): string {
        if (isset($_SESSION["actionErr"])) {
            $actionErr = $_SESSION["actionErr"];
            unset($_SESSION["actionErr"]);
            return $actionErr;
        }

        return "";
    }

    protected function render() {
        global $user;
        extract($this->context);
        $template = $this->getTemplate();
        if (is_file("templates/" . $template)) {
            require 'templates/layout.tpl.php';
        } else {
            redirect($_SERVER["SCRIPT_NAME"] . "?controller=" . CONTROLLER);
        }
    }
}
