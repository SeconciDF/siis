<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AmbientesQuimicosController extends AppController {

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
          'AmbientesQuimicos.programas_id' => $programa['id'],
          'AmbientesQuimicos.ambientes_id' => $ambiente['id']
        ],
        'fields' => [
          'AmbientesQuimicos.id',
          'AmbientesQuimicos.produto_quimico',
          'AmbientesQuimicos.substancia_ativa',
          'AmbientesQuimicos.forma_fisica_contaminante',
          'Ambiente.descricao'
        ],
        'join' => [
          [
            'table' => 'ambientes',
            'alias' => 'a',
            'type' => 'INNER',
            'conditions' => 'a.id = AmbientesQuimicos.ambientes_id',
          ],
          [
            'table' => 'apoio_ambientes',
            'alias' => 'Ambiente',
            'type' => 'INNER',
            'conditions' => 'Ambiente.id = a.apoio_ambientes_id',
          ]
        ],
        'sortWhitelist'=> [
          'AmbientesQuimicos.id',
          'AmbientesQuimicos.produto_quimico',
          'AmbientesQuimicos.substancia_ativa',
          'AmbientesQuimicos.forma_fisica_contaminante',
          'Ambiente.descricao'
        ],
      ];

      $this->set('quimicos', $this->paginate($this->AmbientesQuimicos));
      $this->set(compact('programa', 'ambiente'));
    }

    public function add($id = null, $ambiente = null) {
      $this->loadModel('Programas');
      $this->loadModel('Ambientes');
      $programa = $this->Programas->get($id);
      $ambiente = $this->Ambientes->get($ambiente);

      $quimico = $this->AmbientesQuimicos->newEntity();
      if ($this->request->is('post')) {
        $quimico = $this->AmbientesQuimicos->patchEntity($quimico, $this->request->data);
        if ($this->AmbientesQuimicos->save($quimico)) {
          $this->loadComponent('Log');
          if(!$this->Log->save('add', $quimico)) {
            $this->Flash->error($this::MSG_ERRO_LOG);
          }

          $this->Flash->success(__($this::MSG_SUCESSO_ADD));
          return $this->redirect(['action' => 'edit', $quimico['id']]);
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

      $this->set(compact('programa', 'ambiente', 'empresa', 'quimico', 'ambientes'));
    }

    public function edit($id = null) {
      $this->loadModel('Programas');
      $this->loadModel('Ambientes');

      $quimico = $this->AmbientesQuimicos->get($id);
      $programa = $this->Programas->get($quimico['programas_id']);
      $ambiente = $this->Ambientes->get($quimico['ambientes_id']);

      if ($this->request->is(['post','put'])) {
        $quimico = $this->AmbientesQuimicos->patchEntity($quimico, $this->request->data);
        if ($this->AmbientesQuimicos->save($quimico)) {
          $this->loadComponent('Log');
          if(!$this->Log->save('edit', $quimico)) {
            $this->Flash->error($this::MSG_ERRO_LOG);
          }

          $this->Flash->success(__($this::MSG_SUCESSO_ADD));
          return $this->redirect(['action' => 'edit', $quimico['id']]);
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

      $this->set(compact('programa', 'ambiente', 'empresa', 'quimico', 'ambientes'));
    }

    public function delete($id) {
      $this->request->allowMethod(['post', 'delete']);
      $quimico = $this->AmbientesQuimicos->get($id);
      if ($this->AmbientesQuimicos->delete($quimico)) {
        $this->Flash->success($this::MSG_SUCESSO_DEL);
      }
      return $this->redirect(['action' => 'index', $quimico['programas_id'], $quimico['ambientes_id']]);
    }
}
