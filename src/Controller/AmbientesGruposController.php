<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AmbientesGruposController extends AppController {

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
        'AmbientesGrupos.programas_id' => $programa['id'],
        'AmbientesGrupos.ambientes_id' => $ambiente['id']
      ],
      'fields' => [
        'AmbientesGrupos.id',
        'AmbientesGrupos.numero',
        'AmbientesGrupos.nome',
        'AmbientesGrupos.codigo',
        'Ambiente.descricao'
      ],
      'join' => [
        [
          'table' => 'ambientes',
          'alias' => 'a',
          'type' => 'INNER',
          'conditions' => 'a.id = AmbientesGrupos.ambientes_id',
        ],
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = a.apoio_ambientes_id',
        ]
      ],
      'sortWhitelist'=> [
        'AmbientesGrupos.id',
        'AmbientesGrupos.numero',
        'AmbientesGrupos.nome',
        'AmbientesGrupos.codigo',
        'Ambiente.descricao'
      ],
    ];

    $this->set('grupos', $this->paginate($this->AmbientesGrupos));
    $this->set(compact('programa', 'ambiente'));
  }

  public function add($id = null, $ambiente = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');
    $programa = $this->Programas->get($id);
    $ambiente = $this->Ambientes->get($ambiente);

    $grupo = $this->AmbientesGrupos->newEntity();
    if ($this->request->is('post')) {
      $grupo = $this->AmbientesGrupos->patchEntity($grupo, $this->request->data);
      if ($this->AmbientesGrupos->save($grupo)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $grupo)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        /**
         * Add AmbientesGruposProcessos
         */
        $this->loadModel('AmbientesGruposProcessos');
        if(is_array($this->request->data['processos'])) {
            foreach ($this->request->data['processos'] as $key => $value) {
                $funcoes = $this->AmbientesGruposProcessos->patchEntity(
                  $this->AmbientesGruposProcessos->newEntity(), [
                      'ambientes_grupos_id' => $grupo['id'],
                      'ambientes_processos_id' => $value
                  ]);
                $this->AmbientesGruposProcessos->save($funcoes);
            }
        }
        /**
         * End AmbientesGruposProcessos
         */

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $grupo['id']]);
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

    $this->loadModel('AmbientesProcessos');
    $processos = $this->AmbientesProcessos->find('list',[
      'keyField' => 'id',
      'valueField' => 'processo',
      'conditions' => ['programas_id' => $programa['id'], 'ambientes_id' => $ambiente['id']],
      'order' => ['processo']
    ])->toArray();

    $this->set(compact('programa', 'ambiente', 'empresa', 'grupo', 'processos', 'ambientes'));
  }

  public function edit($id = null) {
    $this->loadModel('Programas');
    $this->loadModel('Ambientes');

    $grupo = $this->AmbientesGrupos->get($id);
    $programa = $this->Programas->get($grupo['programas_id']);
    $ambiente = $this->Ambientes->get($grupo['ambientes_id']);

    if ($this->request->is(['post','put'])) {
      $grupo = $this->AmbientesGrupos->patchEntity($grupo, $this->request->data);
      if ($this->AmbientesGrupos->save($grupo)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $grupo)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        /**
         * Add AmbientesGruposProcessos
         */
        $this->loadModel('AmbientesGruposProcessos');
        $this->AmbientesGruposProcessos->deleteAll(array("ambientes_grupos_id = {$grupo['id']}"));
        if(is_array($this->request->data['processos'])) {
            foreach ($this->request->data['processos'] as $key => $value) {
                $funcoes = $this->AmbientesGruposProcessos->patchEntity(
                  $this->AmbientesGruposProcessos->newEntity(), [
                      'ambientes_grupos_id' => $grupo['id'],
                      'ambientes_processos_id' => $value
                  ]);
                $this->AmbientesGruposProcessos->save($funcoes);
            }
        }
        /**
         * End AmbientesGruposProcessos
         */

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $grupo['id']]);
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

    $this->loadModel('AmbientesProcessos');
    $processos = $this->AmbientesProcessos->find('list',[
      'keyField' => 'id',
      'valueField' => 'processo',
      'conditions' => ['programas_id' => $programa['id'], 'ambientes_id' => $ambiente['id']],
      'order' => ['processo']
    ])->toArray();

    $this->loadModel('AmbientesGruposProcessos');
    $selecionados = $this->AmbientesGruposProcessos->find('list',['keyField' => 'ambientes_processos_id', 'valueField' => 'ambientes_processos_id', 'conditions' => ['ambientes_grupos_id' => $grupo['id']]])->toArray();

    $this->set(compact('programa', 'ambiente', 'empresa', 'grupo', 'processos', 'ambientes', 'selecionados'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $grupo = $this->AmbientesGrupos->get($id);
    if ($this->AmbientesGrupos->delete($grupo)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $grupo['programas_id'], $grupo['ambientes_id']]);
  }

}
