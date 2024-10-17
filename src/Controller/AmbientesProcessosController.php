<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AmbientesProcessosController extends AppController {

  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->request->session()->write('Auth.User.MenuActive', 'sst');
  }

  public function index($id = null, $ambiente = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');
    $programa = $this->Programas->get($id);
    $ambiente = $this->Ambientes->get($ambiente);

    $this->paginate = [
      'conditions' => [
        'AmbientesProcessos.programas_id' => $programa['id'],
        'AmbientesProcessos.ambientes_id' => $ambiente['id']
      ],
      'fields' => [
        'AmbientesProcessos.id',
        'AmbientesProcessos.sequencia',
        'AmbientesProcessos.processo',
        'AmbientesProcessos.descricao',
        'Ambiente.descricao'
      ],
      'join' => [
        [
          'table' => 'ambientes',
          'alias' => 'a',
          'type' => 'INNER',
          'conditions' => 'a.id = AmbientesProcessos.ambientes_id',
        ],
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = a.apoio_ambientes_id',
        ]
      ],
      'sortWhitelist'=> [
        'AmbientesProcessos.id',
        'AmbientesProcessos.sequencia',
        'AmbientesProcessos.processo',
        'AmbientesProcessos.descricao',
        'Ambiente.descricao'
      ],
    ];

    $this->set('processos', $this->paginate($this->AmbientesProcessos));
    $this->set(compact('programa', 'ambiente'));
  }

  public function add($id = null, $ambiente = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');
    $programa = $this->Programas->get($id);
    $ambiente = $this->Ambientes->get($ambiente);

    $processo = $this->AmbientesProcessos->newEntity();
    if ($this->request->is('post')) {
      $processo = $this->AmbientesProcessos->patchEntity($processo, $this->request->data);
      if ($this->AmbientesProcessos->save($processo)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $processo)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $processo['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('Ambientes');
    $ambientes = $this->Ambientes->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'fields' => [
        'id','descricao'=>'Ambiente.descricao',
      ],
      'conditions' => [
        'Ambientes.empresas_id' => $programa['empresas_id']
      ],
      'join' => [
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = Ambientes.apoio_ambientes_id',
        ]
      ],
    ])->toArray();

    $this->set(compact('programa', 'ambiente', 'empresa', 'processo', 'ambientes'));
  }

  public function edit($id = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');

    $processo = $this->AmbientesProcessos->get($id);
    $programa = $this->Programas->get($processo['programas_id']);
    $ambiente = $this->Ambientes->get($processo['ambientes_id']);

    if ($this->request->is(['post','put'])) {
      $processo = $this->AmbientesProcessos->patchEntity($processo, $this->request->data);
      if ($this->AmbientesProcessos->save($processo)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $processo)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $processo['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('Ambientes');
    $ambientes = $this->Ambientes->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'fields' => [
        'id','descricao'=>'Ambiente.descricao',
      ],
      'conditions' => [
        'Ambientes.empresas_id' => $programa['empresas_id']
      ],
      'join' => [
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = Ambientes.apoio_ambientes_id',
        ]
      ],
    ])->toArray();

    $this->set(compact('programa', 'ambiente', 'empresa', 'processo', 'ambientes'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $processo = $this->AmbientesProcessos->get($id);
    if ($this->AmbientesProcessos->delete($processo)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $processo['programas_id'], $processo['ambientes_id']]);
  }
}
