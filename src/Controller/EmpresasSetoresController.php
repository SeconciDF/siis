<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasSetoresController extends AppController {

  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->request->session()->write('Auth.User.MenuActive', 'associado');
  }

  public function index($id = null) {
    $this->loadModel('Empresas');
    $empresa = $this->Empresas->get($id);

    $this->paginate = [
      'conditions' => [
        'Empresa.id' => $empresa['id']
      ],
      'fields' => [
        'EmpresasSetores.id',
        'EmpresasSetores.local',
        'EmpresasSetores.descricao',
        'EmpresasSetores.identificacao',
        'EmpresasSetores.tipo_identificacao',
        'Ambiente.id',
        'Ambiente.descricao'
      ],
      'join' => [
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'INNER',
          'conditions' => 'Empresa.id = EmpresasSetores.empresas_id',
        ],
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = EmpresasSetores.apoio_ambientes_id',
        ]
      ],
      'sortWhitelist'=> [
        'EmpresasSetores.id',
        'EmpresasSetores.local',
        'EmpresasSetores.descricao',
        'EmpresasSetores.identificacao',
        'EmpresasSetores.tipo_identificacao',
        'Ambiente.id',
        'Ambiente.descricao'
      ],
      'order' => ['Ambiente.descricao' => 'ASC']
    ];

    $this->set('empresa', $empresa);
    $this->set('setores', $this->paginate($this->EmpresasSetores));
  }

  public function add($id = null) {
    $this->loadModel('Empresas');
    $empresa = $this->Empresas->get($id);

    $setor = $this->EmpresasSetores->newEntity();
    if ($this->request->is('post')) {
      $setor = $this->EmpresasSetores->patchEntity($setor, $this->request->data);
      if ($this->EmpresasSetores->save($setor)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $setor)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $setor['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioAmbientes');
    $ambientes = $this->ApoioAmbientes->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

    $this->set(compact('empresa', 'setor', 'ambientes'));
  }

  public function edit($id = null) {
    $this->loadModel('Empresas');

    $setor = $this->EmpresasSetores->get($id);
    $empresa = $this->Empresas->get($setor['empresas_id']);

    if ($this->request->is(['post', 'put'])) {
      $setor = $this->EmpresasSetores->patchEntity($setor, $this->request->data);
      if ($this->EmpresasSetores->save($setor)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $setor)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $setor['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioAmbientes');
    $ambientes = $this->ApoioAmbientes->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

    $this->set(compact('empresa', 'setor', 'ambientes'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $setor = $this->EmpresasSetores->get($id);
    if ($this->EmpresasSetores->delete($setor)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $setor['empresas_id']]);
  }
}
