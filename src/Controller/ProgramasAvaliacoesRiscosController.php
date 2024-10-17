<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ProgramasAvaliacoesRiscosController extends AppController {

  public function beforeFilter(Event $event) {
      parent::beforeFilter($event);
      $this->request->session()->write('Auth.User.MenuActive', 'sst');
  }

  public function index($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $this->paginate = [
      'conditions' => [
        'ProgramasAvaliacoesRiscos.programas_id' => $programa['id']
      ],
      'fields' => [
        'ProgramasAvaliacoesRiscos.id',
        'Perigo.possivel_dano',
        'Risco.descricao',
        'Ambiente.descricao',
        'FatorRisco.descricao',
        'FatorRisco.codigo',
        'Grupo.nome',
        'Agente.descricao',
        'Processo.processo',
        'processos' => 'GROUP_CONCAT(Processo.processo)'
      ],
      'join' => [
        [
          'table' => 'ambientes',
          'alias' => 'a',
          'type' => 'INNER',
          'conditions' => 'a.id = ProgramasAvaliacoesRiscos.ambientes_id',
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
          'conditions' => 'Grupo.id = ProgramasAvaliacoesRiscos.ambientes_grupos_id',
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
          'table' => 'programas_perigos_danos',
          'alias' => 'Perigo',
          'type' => 'INNER',
          'conditions' => 'Perigo.id = ProgramasAvaliacoesRiscos.perigos_danos_id',
        ],
        [
          'table' => 'apoio_agentes_tipos',
          'alias' => 'Agente',
          'type' => 'INNER',
          'conditions' => 'Agente.id = Perigo.agentes_tipos_id',
        ],
        [
          'table' => 'apoio_riscos',
          'alias' => 'Risco',
          'type' => 'INNER',
          'conditions' => 'Risco.id = ProgramasAvaliacoesRiscos.apoio_riscos_id',
        ],
        [
          'table' => 'apoio_fatores_riscos',
          'alias' => 'FatorRisco',
          'type' => 'INNER',
          'conditions' => 'FatorRisco.id = Perigo.apoio_fatores_riscos_id',
        ]
      ],
      'sortWhitelist'=> [
        'ProgramasAvaliacoesRiscos.id',
        'Perigo.possivel_dano',
        'Risco.descricao',
        'Ambiente.descricao',
        'Grupo.nome',
        'Agente.descricao',
        'Processo.processo'
      ],
      'group' => ['ProgramasAvaliacoesRiscos.id'],
      'order' => ['Ambiente.descricao' => 'ASC', 'Grupo.nome' => 'ASC']
    ];

    $this->set('programa', $programa);
    $this->set('medidas', $this->paginate($this->ProgramasAvaliacoesRiscos));
  }

  public function add($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $entity = $this->ProgramasAvaliacoesRiscos->newEntity();
    if ($this->request->is('post')) {
      $entity = $this->ProgramasAvaliacoesRiscos->patchEntity($entity, $this->request->data);
      if ($this->ProgramasAvaliacoesRiscos->save($entity)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $entity)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $entity['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('ApoioRiscos');
    $riscos = $this->ApoioRiscos->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'order' => ['descricao']
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

    $this->loadModel('ProgramasPerigosDanos');
    $perigos = $this->ProgramasPerigosDanos->find('list',[
      'keyField' => 'id',
      'valueField' => function($row) {
        return "{$row['Agente']['descricao']} - {$row['Risco']['codigo']} {$row['Risco']['descricao']} - {$row['possivel_dano']}";
      },
      'groupField' => function($row) {
        return "{$row['Ambiente']['descricao']} - {$row['Grupo']['nome']}";
      },
      'conditions' => [
        'ProgramasPerigosDanos.programas_id' => $programa['id'],
      ],
      'fields' => [
        'Agente.descricao',
        'Ambiente.descricao',
        'Grupo.nome',
        'Risco.codigo',
        'Risco.descricao',
        'ProgramasPerigosDanos.possivel_dano',
        'ProgramasPerigosDanos.id'
      ],
      'join' => [
        [
          'table' => 'ambientes',
          'alias' => 'a',
          'type' => 'INNER',
          'conditions' => 'a.id = ProgramasPerigosDanos.ambientes_id',
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
          'conditions' => 'Grupo.id = ProgramasPerigosDanos.ambientes_grupos_id',
        ],
        [
          'table' => 'apoio_agentes_tipos',
          'alias' => 'Agente',
          'type' => 'INNER',
          'conditions' => 'Agente.id = ProgramasPerigosDanos.agentes_tipos_id',
        ],
        [
          'table' => 'apoio_fatores_riscos',
          'alias' => 'Risco',
          'type' => 'INNER',
          'conditions' => 'Risco.id = ProgramasPerigosDanos.apoio_fatores_riscos_id',
        ]
      ],
    ])->toArray();

    $this->set(compact('entity', 'programa', 'empresa', 'perigos', 'ambientes', 'grupos', 'riscos'));
  }

  public function edit($id = null) {
    $this->loadModel('Programas');

    $entity = $this->ProgramasAvaliacoesRiscos->get($id);
    $programa = $this->Programas->get($entity['programas_id']);

    if ($this->request->is(['post','put'])) {
      $entity = $this->ProgramasAvaliacoesRiscos->patchEntity($entity, $this->request->data);
      if ($this->ProgramasAvaliacoesRiscos->save($entity)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $entity)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $entity['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('ApoioRiscos');
    $riscos = $this->ApoioRiscos->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'order' => ['descricao']
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
        'ambientes_grupos_id' => $entity['ambientes_grupos_id']
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

    $this->loadModel('ProgramasPerigosDanos');
    $perigos = $this->ProgramasPerigosDanos->find('list',[
      'keyField' => 'id',
      'valueField' => function($row) {
        return "{$row['Agente']['descricao']} - {$row['Risco']['codigo']} {$row['Risco']['descricao']} - {$row['possivel_dano']}";
      },
      'groupField' => function($row) {
        return "{$row['Ambiente']['descricao']} - {$row['Grupo']['nome']}";
      },
      'conditions' => [
        'ProgramasPerigosDanos.programas_id' => $programa['id'],
      ],
      'fields' => [
        'Agente.descricao',
        'Ambiente.descricao',
        'Grupo.nome',
        'Risco.codigo',
        'Risco.descricao',
        'ProgramasPerigosDanos.possivel_dano',
        'ProgramasPerigosDanos.id'
      ],
      'join' => [
        [
          'table' => 'ambientes',
          'alias' => 'a',
          'type' => 'INNER',
          'conditions' => 'a.id = ProgramasPerigosDanos.ambientes_id',
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
          'conditions' => 'Grupo.id = ProgramasPerigosDanos.ambientes_grupos_id',
        ],
        [
          'table' => 'apoio_agentes_tipos',
          'alias' => 'Agente',
          'type' => 'INNER',
          'conditions' => 'Agente.id = ProgramasPerigosDanos.agentes_tipos_id',
        ],
        [
          'table' => 'apoio_fatores_riscos',
          'alias' => 'Risco',
          'type' => 'INNER',
          'conditions' => 'Risco.id = ProgramasPerigosDanos.apoio_fatores_riscos_id',
        ]
      ],
    ])->toArray();

    $this->set(compact('entity', 'programa', 'empresa', 'perigos', 'ambientes', 'grupos', 'riscos', 'processos'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $entity = $this->ProgramasAvaliacoesRiscos->get($id);
    if ($this->ProgramasAvaliacoesRiscos->delete($entity)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $entity['programas_id']]);
  }

}
