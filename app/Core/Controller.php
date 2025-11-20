<?php

/*
class Controller {
    public function model($model) {
        require_once '../app/Models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        require_once '../app/views/' . $view . '.php';
    }
}
*/

class Controller {
    public function model($model) {
        require_once MODELS . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        require_once VIEWS . $view . '.php';
    }
}

