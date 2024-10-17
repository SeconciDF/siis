<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ProgramasAvaliacoesQuantitativasController extends AppController {

  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->request->session()->write('Auth.User.MenuActive', 'sst');
  }

  public function index($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $this->paginate = [
        'conditions' => [
          'ProgramasAvaliacoesQuantitativas.programas_id' => $programa['id']
        ],
        // 'fields' => [
        //   'ProgramasPerigosDanos.id',
        //   'Ambiente.descricao',
        //   'Risco.descricao',
        //   'Risco.codigo',
        //   'Grupo.nome',
        //   'Processo.processo',
        //   'Agente.descricao',
        //   'processos' => 'GROUP_CONCAT(Processo.processo)'
        // ],
        // 'join' => [
        //   [
        //     'table' => 'ambientes',
        //     'alias' => 'a',
        //     'type' => 'INNER',
        //     'conditions' => 'a.id = ProgramasPerigosDanos.ambientes_id',
        //   ],
        //   [
        //     'table' => 'apoio_ambientes',
        //     'alias' => 'Ambiente',
        //     'type' => 'INNER',
        //     'conditions' => 'Ambiente.id = a.apoio_ambientes_id',
        //   ],
        //   [
        //     'table' => 'ambientes_grupos',
        //     'alias' => 'Grupo',
        //     'type' => 'INNER',
        //     'conditions' => 'Grupo.id = ProgramasPerigosDanos.ambientes_grupos_id',
        //   ],
        //   [
        //     'table' => 'ambientes_grupos_processos',
        //     'alias' => 'GrupoProcesso',
        //     'type' => 'INNER',
        //     'conditions' => 'Grupo.id = GrupoProcesso.ambientes_grupos_id',
        //   ],
        //   [
        //     'table' => 'ambientes_processos',
        //     'alias' => 'Processo',
        //     'type' => 'INNER',
        //     'conditions' => 'Processo.id = GrupoProcesso.ambientes_processos_id',
        //   ],
        //   [
        //     'table' => 'apoio_agentes_tipos',
        //     'alias' => 'Agente',
        //     'type' => 'INNER',
        //     'conditions' => 'Agente.id = ProgramasPerigosDanos.agentes_tipos_id',
        //   ],
        //   [
        //     'table' => 'apoio_fatores_riscos',
        //     'alias' => 'Risco',
        //     'type' => 'INNER',
        //     'conditions' => 'Risco.id = ProgramasPerigosDanos.apoio_fatores_riscos_id',
        //   ]
        // ],
        // 'sortWhitelist'=> [
        //   'ProgramasPerigosDanos.id',
        //   'Ambiente.descricao',
        //   'Risco.descricao',
        //   'Grupo.nome',
        //   'Processo.processo',
        //   'Agente.descricao'
        // ],
        // 'group' => ['ProgramasPerigosDanos.id'],
        // 'order' => ['Ambiente.descricao' => 'ASC', 'Grupo.nome' => 'ASC']
    ];

    $this->set('programa', $programa);
    $this->set('avaliacoes', $this->paginate($this->ProgramasAvaliacoesQuantitativas));
  }

  public function add($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $avaliacao = $this->ProgramasAvaliacoesQuantitativas->newEntity();
    if ($this->request->is('post')) {
      $avaliacao = $this->ProgramasAvaliacoesQuantitativas->patchEntity($avaliacao, $this->request->data);
      if ($this->ProgramasAvaliacoesQuantitativas->save($avaliacao)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $avaliacao)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        /**
         * Add ProgramasGhGhe
         */
        $this->loadModel('ProgramasGhGhe');
        if(is_array($this->request->data['ghe'])) {
            foreach ($this->request->data['ghe'] as $key => $value) {
                $ghghe = $this->ProgramasGhGhe->patchEntity(
                  $this->ProgramasGhGhe->newEntity(), [
                      'ambientes_grupos_id' => $value,
                      'avaliacoes_quantitativas_id' => $avaliacao['id']
                  ]);
                $this->ProgramasGhGhe->save($ghghe);
            }
        }
        /**
         * End ProgramasGhGhe
         */

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $avaliacao['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('ApoioAvaliacoes');
    $avaliacoes = $this->ApoioAvaliacoes->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'order' => ['descricao']
    ])->toArray();

    $this->loadModel('ApoioExposicoes');
    $exposicoes = $this->ApoioExposicoes->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'order' => ['descricao']
    ])->toArray();

    $this->loadModel('Beneficiarios');
    $beneficiarios = $this->Beneficiarios->find('list',[
      'keyField' => 'id',
      'valueField' => 'nome',
      'conditions' => [
        'Empresa.empresas_id' => $empresa['id']
      ],
      'join' => [
        [
          'table' => 'beneficiarios_empresas',
          'alias' => 'Empresa',
          'type' => 'INNER',
          'conditions' => 'Beneficiarios.id = Empresa.beneficiarios_id',
        ]
      ],
      'order' => ['nome']
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

    $this->set(compact('programa', 'empresa', 'avaliacao', 'ambientes', 'grupos', 'avaliacoes', 'exposicoes', 'beneficiarios'));
  }

  public function edit($id = null) {
    $this->loadModel('Programas');

    $avaliacao = $this->ProgramasAvaliacoesQuantitativas->get($id);
    $programa = $this->Programas->get($avaliacao['programas_id']);

    if ($this->request->is(['post','put'])) {
      $avaliacao = $this->ProgramasAvaliacoesQuantitativas->patchEntity($avaliacao, $this->request->data);
      if ($this->ProgramasAvaliacoesQuantitativas->save($avaliacao)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $avaliacao)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        /**
         * Add ProgramasGhGhe
         */
        $this->loadModel('ProgramasGhGhe');
        $this->ProgramasGhGhe->deleteAll(array("avaliacoes_quantitativas_id = {$avaliacao['id']}"));
        if(is_array($this->request->data['ghe'])) {
            foreach ($this->request->data['ghe'] as $key => $value) {
                $ghghe = $this->ProgramasGhGhe->patchEntity(
                  $this->ProgramasGhGhe->newEntity(), [
                      'ambientes_grupos_id' => $value,
                      'avaliacoes_quantitativas_id' => $avaliacao['id']
                  ]);
                $this->ProgramasGhGhe->save($ghghe);
            }
        }
        /**
         * End ProgramasGhGhe
         */

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $avaliacao['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $this->loadModel('ApoioAvaliacoes');
    $avaliacoes = $this->ApoioAvaliacoes->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'order' => ['descricao']
    ])->toArray();

    $this->loadModel('ApoioExposicoes');
    $exposicoes = $this->ApoioExposicoes->find('list',[
      'keyField' => 'id',
      'valueField' => 'descricao',
      'order' => ['descricao']
    ])->toArray();

    $this->loadModel('Beneficiarios');
    $beneficiarios = $this->Beneficiarios->find('list',[
      'keyField' => 'id',
      'valueField' => 'nome',
      'conditions' => [
        'Empresa.empresas_id' => $empresa['id']
      ],
      'join' => [
        [
          'table' => 'beneficiarios_empresas',
          'alias' => 'Empresa',
          'type' => 'INNER',
          'conditions' => 'Beneficiarios.id = Empresa.beneficiarios_id',
        ]
      ],
      'order' => ['nome']
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

    $this->loadModel('ProgramasGhGhe');
    $selecionadas = $this->ProgramasGhGhe->find('list',['keyField' => 'ambientes_grupos_id', 'valueField' => 'ambientes_grupos_id', 'conditions' => ['avaliacoes_quantitativas_id' => $avaliacao['id']]])->toArray();

    $this->set(compact('programa', 'empresa', 'avaliacao', 'ambientes', 'grupos', 'avaliacoes', 'exposicoes', 'beneficiarios', 'selecionadas'));
  }

  public function delete($id) {
    $this->request->allowMethod(['post', 'delete']);
    $avaliacao = $this->ProgramasAvaliacoesQuantitativas->get($id);
    if ($this->ProgramasAvaliacoesQuantitativas->delete($avaliacao)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
    }
    return $this->redirect(['action' => 'index', $avaliacao['programas_id']]);
  }
}
