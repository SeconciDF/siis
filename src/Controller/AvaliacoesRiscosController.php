<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AvaliacoesRiscosController extends AppController {

  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->request->session()->write('Auth.User.MenuActive', 'sst');
  }

  public function index($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $this->paginate = [
        'conditions' => [
          'AvaliacoesRiscos.programas_id' => $programa['id']
        ],
        'fields' => [
          'AvaliacoesRiscos.id',
          'Ambiente.descricao',
          'Grupo.nome',
          'Processo.processo',
          'Agente.descricao',
          'processos' => 'GROUP_CONCAT(Processo.processo)'
        ],
        'join' => [
          [
            'table' => 'ambientes',
            'alias' => 'a',
            'type' => 'INNER',
            'conditions' => 'a.id = AvaliacoesRiscos.ambientes_id',
          ],
          [
            'table' => 'apoio_ambientes',
            'alias' => 'Ambiente',
            'type' => 'INNER',
            'conditions' => 'Ambiente.id = a.apoio_ambientes_id',
          ],
          [
            'table' => 'ambientes_grupos',
            'alias' => 'Grupo',
            'type' => 'INNER',
            'conditions' => 'Grupo.id = AvaliacoesRiscos.ambientes_grupos_id',
          ],
          [
            'table' => 'ambientes_grupos_processos',
            'alias' => 'GrupoProcesso',
            'type' => 'INNER',
            'conditions' => 'Grupo.id = GrupoProcesso.ambientes_grupos_id',
          ],
          [
            'table' => 'ambientes_processos',
            'alias' => 'Processo',
            'type' => 'INNER',
            'conditions' => 'Processo.id = GrupoProcesso.ambientes_processos_id',
          ],
          [
            'table' => 'apoio_agentes_tipos',
            'alias' => 'Agente',
            'type' => 'INNER',
            'conditions' => 'Agente.id = AvaliacoesRiscos.agentes_tipos_id',
          ]
        ],
        'sortWhitelist'=> [
          'AvaliacoesRiscos.id',
          'Ambiente.descricao',
          'Grupo.nome',
          'Processo.processo',
          'Agente.descricao'
        ],
        'group' => ['AvaliacoesRiscos.id'],
        'order' => ['Ambiente.descricao' => 'ASC', 'Grupo.nome' => 'ASC']
    ];

    $this->set('programa', $programa);
    $this->set('riscos', $this->paginate($this->AvaliacoesRiscos));
  }

  public function add($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $risco = $this->AvaliacoesRiscos->newEntity();
    if ($this->request->is('post')) {
      $risco = $this->AvaliacoesRiscos->patchEntity($risco, $this->request->data);
      if ($this->AvaliacoesRiscos->save($risco)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $risco)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $risco['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('ApoioAgentesTipos');
    $agentes = $this->ApoioAgentesTipos->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
    ])->toArray();

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
      'order' => ['Ambiente.descricao']
    ])->toArray();

    $this->loadModel('AmbientesGrupos');
    $grupos = $this->AmbientesGrupos->find('list',[
      'keyField' => 'id',
      'valueField' => 'nome',
      'groupField' => 'grupo',
      'fields' => [
        'AmbientesGrupos.id',
        'AmbientesGrupos.nome',
        'grupo' => 'Ambiente.descricao',
      ],
      'conditions' => [
        'programas_id' => $programa['id']
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
      'order' => ['Ambiente.descricao', 'AmbientesGrupos.nome']
    ])->toArray();

    $this->set(compact('programa', 'empresa', 'risco', 'agentes', 'ambientes', 'grupos'));
  }

  public function edit($id = null) {
    $this->loadModel('Programas');

    $risco = $this->AvaliacoesRiscos->get($id);
    $programa = $this->Programas->get($risco['programas_id']);

    if ($this->request->is(['post','put'])) {
      $risco = $this->AvaliacoesRiscos->patchEntity($risco, $this->request->data);
      if ($this->AvaliacoesRiscos->save($risco)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $risco)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $risco['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('ApoioAgentesTipos');
    $agentes = $this->ApoioAgentesTipos->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
    ])->toArray();

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
      'order' => ['Ambiente.descricao']
    ])->toArray();

    $this->loadModel('AmbientesGrupos');
    $grupos = $this->AmbientesGrupos->find('list',[
      'keyField' => 'id',
      'valueField' => 'nome',
      'groupField' => 'grupo',
      'fields' => [
        'AmbientesGrupos.id',
        'AmbientesGrupos.nome',
        'grupo' => 'Ambiente.descricao',
      ],
      'conditions' => [
        'programas_id' => $programa['id']
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
      'order' => ['Ambiente.descricao', 'AmbientesGrupos.nome']
    ])->toArray();

    $this->loadModel('AmbientesGruposProcessos');
    $processos = $this->AmbientesGruposProcessos->find('list',[
      'keyField' => 'Processo.id',
      'valueField' => 'Processo.processo',
      'fields' => [
        'Processo.id',
        'Processo.processo'
      ],
      'conditions' => [
        'programas_id' => $programa['id'],
        'ambientes_grupos_id' => $risco['ambientes_grupos_id']
      ],
      'join' => [
        [
          'table' => 'ambientes_processos',
          'alias' => 'Processo',
          'type' => 'INNER',
          'conditions' => 'Processo.id = AmbientesGruposProcessos.ambientes_processos_id',
        ]
      ],
      'order' => ['Processo.processo']
    ])->toArray();

    $this->set(compact('programa', 'empresa', 'risco', 'agentes', 'ambientes', 'grupos', 'processos'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $risco = $this->AvaliacoesRiscos->get($id);
    if ($this->AvaliacoesRiscos->delete($risco)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $risco['programas_id']]);
  }
}
