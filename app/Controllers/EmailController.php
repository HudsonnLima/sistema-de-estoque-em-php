<?php
namespace App\Controllers;
use App\Models\EmailModel;

class EmailController {
    private $model;

    public function __construct($pdo) {
        $this->model = new EmailModel($pdo);
    }

    public function getUserByEmail($email) {
        return $this->model->checkEmailExists($email);
    }
}
