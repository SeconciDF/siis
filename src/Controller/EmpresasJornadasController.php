<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasJornadasController extends AppController {

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
        'EmpresasJornadas.id',
        'EmpresasJornadas.horas',
        'EmpresasJornadas.turno'
      ],
      'join' => [
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'INNER',
          'conditions' => 'Empresa.id = EmpresasJornadas.empresas_id',
        ],
      ],
      'sortWhitelist'=> [
        'EmpresasJornadas.id',
        'EmpresasJornadas.horas',
        'EmpresasJornadas.turno'
      ],
      'order' => ['EmpresasJornadas.turno' => 'ASC']
    ];

    $this->set('empresa', $empresa);
    $this->set('jornadas', $this->paginate($this->EmpresasJornadas));
  }

  public function add($id = null) {
    $this->loadModel('Empresas');
    $empresa = $this->Empresas->get($id);

    $jornada = $this->EmpresasJornadas->newEntity();
    if ($this->request->is('post')) {
      $jornada = $this->EmpresasJornadas->patchEntity($jornada, $this->request->data);
      if ($this->EmpresasJornadas->save($jornada)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $jornada)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $jornada['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioAmbientes');
    $ambientes = $this->ApoioAmbientes->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

    $this->set(compact('empresa', 'jornada', 'ambientes'));
  }

  public function edit($id = null) {
    $this->loadModel('Empresas');

    $jornada = $this->EmpresasJornadas->get($id);
    $empresa = $this->Empresas->get($jornada['empresas_id']);

    if ($this->request->is(['post', 'put'])) {
      $jornada = $this->EmpresasJornadas->patchEntity($jornada, $this->request->data);
      if ($this->EmpresasJornadas->save($jornada)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $jornada)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $jornada['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioAmbientes');
    $ambientes = $this->ApoioAmbientes->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

    $this->set(compact('empresa', 'jornada', 'ambientes'));
  }
}
