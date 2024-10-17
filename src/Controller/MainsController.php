<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class MainsController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'mains');
    }

    public function index() {

    }
    
    public function cep($cep = null) {
        $cep = str_replace('.', '', $cep);
        $cep = str_replace('-', '', $cep);

        $homepage = file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
        echo $homepage;
        exit;
    }
}
