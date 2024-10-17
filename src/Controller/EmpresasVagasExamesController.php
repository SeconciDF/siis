<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasVagasExamesController extends AppController {

  public function beforeFilter(Event $event) {
      $this->request->session()->write('Auth.User.MenuActive', 'configuracao');
  }

  public function index() {
    $this->loadModel('VagaExame');
    $vagas = $this->VagaExame->vagasExames();
    $this->set(compact('vagas'));
  }

  public function add() {
    $this->loadModel('VagaExame');
    $this->loadModel('Consultas');

    if ($this->request->is(['patch', 'post', 'put'])) {
      $this->VagaExame->salvarVaga($this->request->data);
      if(isset($this->VagaExame->success)) {
        $this->Flash->success($this->VagaExame->success);
      }
      if(isset($this->VagaExame->error)) {
        $this->Flash->error($this->VagaExame->error);
      }
      return $this->redirect(['action' => 'index']);
    }

    $exames = $this->Consultas->getExames('17');
    $this->set(compact('exames'));
    $this->viewBuilder()->layout('ajax');
  }

  public function deletar($id) {
    $this->loadModel('VagaExame');
    if ($this->request->is(['get', 'put'])) {
      $this->VagaExame->deletarVaga($id);
      if(isset($this->VagaExame->success)) {
        $this->Flash->success($this->VagaExame->success);
      }
      if(isset($this->VagaExame->error)) {
        $this->Flash->error($this->VagaExame->error);
      }
    }
    return $this->redirect(['action' => 'index']);
  }
}
