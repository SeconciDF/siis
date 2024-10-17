<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasObrasController extends AppController {

  public function beforeFilter(Event $event) {
    $this->request->session()->write('Auth.User.MenuActive', 'associado');
  }

  public function index($id = null) {
    $this->loadModel('Empresas');
    $empresa = $this->Empresas->get($id);

    $option['EmpresasObras.empresas_id'] = $empresa['id'];
    if ($this->request->query('nome')) {
      $option['EmpresasObras.nome LIKE'] = "%{$this->request->query('nome')}%";
    }

    $this->paginate = [
      'fields' => [
        'EmpresasObras.id',
        'EmpresasObras.nome',
        'EmpresasObras.situacao',
      ],
      'conditions' => $option,
      'sortWhitelist'=> [
        'EmpresasObras.nome',
        'EmpresasObras.situacao',
      ],
      'order' => ['EmpresasObras.nome' => 'asc'],
    ];

    $this->set('empresa', $empresa);
    $this->set('obras', $this->paginate($this->EmpresasObras));
  }

  public function add($id = null) {
    $this->loadModel('Empresas');
    $empresa = $this->Empresas->get($id);

    $obra = $this->EmpresasObras->newEntity();
    if ($this->request->is('post')) {
      $obra = $this->EmpresasObras->patchEntity($obra, $this->request->data);
      if ($this->EmpresasObras->save($obra)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $obra)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $obra['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioEstados');
    $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla',])->toArray();

    $this->set(compact('obra', 'empresa', 'estados'));
  }

  public function edit($id = null) {
    $this->loadModel('Empresas');
    $obra = $this->EmpresasObras->get($id);
    $empresa = $this->Empresas->get($obra['empresas_id']);

    if ($this->request->is(['patch', 'post', 'put'])) {
      $obra = $this->EmpresasObras->patchEntity($obra, $this->request->data);
      if ($this->EmpresasObras->save($obra)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $obra)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'edit', $id]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioEstados');
    $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla',])->toArray();

    $this->set(compact('obra', 'empresa', 'estados'));
  }
}
