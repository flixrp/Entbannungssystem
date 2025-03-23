<?php

trait Alerts {
    protected function alertSuccess($message) {
        $this->addNextPageActionErr('<div class="alert alert-success">' . $message . '</div>');
    }

    protected function alertDanger($message) {
        $this->addNextPageActionErr('<div class="alert alert-danger">' . $message . '</div>');
    }

    protected function alertInfo($message) {
        $this->addNextPageActionErr('<div class="alert alert-info">' . $message . '</div>');
    }

    protected function alertWarn($message) {
        $this->addNextPageActionErr('<div class="alert alert-warning">' . $message . '</div>');
    }
}