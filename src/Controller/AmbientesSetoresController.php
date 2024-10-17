<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AmbientesSetoresController extends AppController {

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
        'AmbientesSetores.programas_id' => $programa['id'],
        'AmbientesSetores.ambientes_id' => $ambiente['id']
      ],
      'fields' => [
        'AmbientesSetores.id',
        'AmbientesSetores.descricao',
        'Ambiente.descricao'
      ],
      'join' => [
        [
          'table' => 'ambientes',
          'alias' => 'a',
          'type' => 'INNER',
          'conditions' => 'a.id = AmbientesSetores.ambientes_id',
        ],
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = a.apoio_ambientes_id',
        ]
      ],
      'sortWhitelist'=> [
        'AmbientesSetores.id',
        'AmbientesSetores.descricao',
        'Ambiente.descricao'
      ],
    ];

    $this->set('setores', $this->paginate($this->AmbientesSetores));
    $this->set(compact('programa', 'ambiente'));
  }

  public function add($id = null, $ambiente = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');
    $programa = $this->Programas->get($id);
    $ambiente = $this->Ambientes->get($ambiente);

    $setor = $this->AmbientesSetores->newEntity();
    if ($this->request->is('post')) {
      $setor = $this->AmbientesSetores->patchEntity($setor, $this->request->data);
      if ($this->AmbientesSetores->save($setor)) {
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

    $this->set(compact('programa', 'ambiente', 'empresa', 'setor', 'ambientes'));
  }

  public function edit($id = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');

    $setor = $this->AmbientesSetores->get($id);
    $programa = $this->Programas->get($setor['programas_id']);
    $ambiente = $this->Ambientes->get($setor['ambientes_id']);

    if ($this->request->is(['post','put'])) {
      $setor = $this->AmbientesSetores->patchEntity($setor, $this->request->data);
      if ($this->AmbientesSetores->save($setor)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $setor)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $setor['id']]);
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

    $this->set(compact('programa', 'ambiente', 'empresa', 'setor', 'ambientes'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $setor = $this->AmbientesSetores->get($id);
    if ($this->AmbientesSetores->delete($setor)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $setor['programas_id'], $setor['ambientes_id']]);
  }
}
