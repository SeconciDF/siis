<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasLotacoesController extends AppController {

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
        'EmpresasLotacoes.id',
        'EmpresasLotacoes.funcao',
        'EmpresasLotacoes.revezamento',
        'Ambiente.descricao',
        'Funcao.descricao',
        'Jornada.turno'
      ],
      'join' => [
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'INNER',
          'conditions' => 'Empresa.id = EmpresasLotacoes.empresas_id',
        ],
        [
          'table' => 'empresas_setores',
          'alias' => 'Setor',
          'type' => 'INNER',
          'conditions' => 'Setor.id = EmpresasLotacoes.empresas_setores_id',
        ],
        [
          'table' => 'apoio_ambientes',
          'alias' => 'Ambiente',
          'type' => 'INNER',
          'conditions' => 'Ambiente.id = Setor.apoio_ambientes_id',
        ],
        [
          'table' => 'apoio_funcoes',
          'alias' => 'Funcao',
          'type' => 'INNER',
          'conditions' => 'Funcao.id = EmpresasLotacoes.apoio_funcoes_id',
        ],
        [
          'table' => 'empresas_jornadas',
          'alias' => 'Jornada',
          'type' => 'INNER',
          'conditions' => 'Jornada.id = EmpresasLotacoes.empresas_jornadas_id',
        ],
      ],
      'sortWhitelist'=> [
        'EmpresasLotacoes.id',
        'EmpresasLotacoes.funcao',
        'EmpresasLotacoes.revezamento',
        'Ambiente.descricao',
        'Funcao.descricao',
        'Jornada.turno'
      ],
      'order' => ['Ambiente.descricao' => 'ASC', 'Funcao.descricao' => 'ASC']
    ];

    $this->set('empresa', $empresa);
    $this->set('lotacoes', $this->paginate($this->EmpresasLotacoes));
  }

  public function add($id = null) {
    $this->loadModel('Empresas');
    $empresa = $this->Empresas->get($id);

    $lotacao = $this->EmpresasLotacoes->newEntity();
    if ($this->request->is('post')) {
      $lotacao = $this->EmpresasLotacoes->patchEntity($lotacao, $this->request->data);
      if ($this->EmpresasLotacoes->save($lotacao)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $lotacao)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $lotacao['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('ApoioFuncoes');
    $funcoes = $this->ApoioFuncoes->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'order' => ['descricao' => 'ASC']])->toArray();

    $this->loadModel('EmpresasSetores');
    $setores = $this->EmpresasSetores->find('list',['keyField' => 'id', 'valueField' => 'Ambiente.descricao',
    'fields' => [
      'EmpresasSetores.id',
      'Ambiente.descricao'
    ],
    'conditions' => [
      'empresas_id' => $empresa['id']
    ],
    'join' => [
      [
        'table' => 'apoio_ambientes',
        'alias' => 'Ambiente',
        'type' => 'INNER',
        'conditions' => 'Ambiente.id = EmpresasSetores.apoio_ambientes_id',
      ]
    ],
    'order' => ['Ambiente.descricao' => 'ASC']
    ])->toArray();

    $this->loadModel('EmpresasJornadas');
    $jornadas = $this->EmpresasJornadas->find('list',['keyField' => 'id', 'valueField' => 'turno', 'conditions' => ['empresas_id' => $empresa['id']], 'order' => ['turno' => 'ASC']])->toArray();

    $this->set(compact('empresa', 'lotacao', 'funcoes', 'setores', 'jornadas'));
  }

  public function edit($id = null) {
    $this->loadModel('Empresas');

    $lotacao = $this->EmpresasLotacoes->get($id);
    $empresa = $this->Empresas->get($lotacao['empresas_id']);

    if ($this->request->is(['post', 'put'])) {
      $lotacao = $this->EmpresasLotacoes->patchEntity($lotacao, $this->request->data);
      if ($this->EmpresasLotacoes->save($lotacao)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $lotacao)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $lotacao['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }


    $this->loadModel('ApoioFuncoes');
    $funcoes = $this->ApoioFuncoes->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'order' => ['descricao' => 'ASC']])->toArray();

    $this->loadModel('EmpresasSetores');
    $setores = $this->EmpresasSetores->find('list',['keyField' => 'id', 'valueField' => 'Ambiente.descricao',
    'fields' => [
      'EmpresasSetores.id',
      'Ambiente.descricao'
    ],
    'conditions' => [
      'empresas_id' => $empresa['id']
    ],
    'join' => [
      [
        'table' => 'apoio_ambientes',
        'alias' => 'Ambiente',
        'type' => 'INNER',
        'conditions' => 'Ambiente.id = EmpresasSetores.apoio_ambientes_id',
      ]
    ],
    'order' => ['Ambiente.descricao' => 'ASC']
    ])->toArray();

    $this->loadModel('EmpresasJornadas');
    $jornadas = $this->EmpresasJornadas->find('list',['keyField' => 'id', 'valueField' => 'turno', 'conditions' => ['empresas_id' => $empresa['id']], 'order' => ['turno' => 'ASC']])->toArray();

    $this->set(compact('empresa', 'lotacao', 'funcoes', 'setores', 'jornadas'));
  }
}
