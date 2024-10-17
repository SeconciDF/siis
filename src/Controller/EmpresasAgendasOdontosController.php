<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasAgendasOdontosController extends AppController {

  public function beforeFilter(Event $event) {
    $this->request->session()->write('Auth.User.MenuActive', 'empresa');
  }

  public function index() {
    $this->loadModel('AtendimentosFilas');
    $this->loadModel('Beneficiarios');

    $option = [
      'AtendimentosFilas.situacao' => 'AA',
      'AtendimentosFilas.data_hora_registro > "2015-12-31"',
      'AtendimentosFilas.empresas_id' => $this->request->session()->read('Auth.User.empresas_id')
    ];

    $prontuario = $this->Beneficiarios->prontuario($this->request->query('id'));
    if ($this->request->query('id') && $prontuario['beneficiario']) {
      $option['Beneficiario.id'] = $prontuario['beneficiario'];
    }
    if ($this->request->query('id') && $prontuario['dependente']) {
      $option['Dependente.id'] = $prontuario['dependente'];
    } else if($this->request->query('id')) {
      $option[] = 'Dependente.id IS NULL';
    }

    if ($this->request->query('cpf')) {
      $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
      $option['OR'][] = "Beneficiario.cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
      $option['OR'][] = "Dependente.cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
    }
    if ($this->request->query('nome')) {
      $option['OR']['Beneficiario.nome LIKE'] = "%{$this->request->query('nome')}%";
      $option['OR']['Dependente.nome LIKE'] = "%{$this->request->query('nome')}%";
    }

    if($this->request->query('especialidade')) {
      $option['Especialidade.id'] = $this->request->query('especialidade');
    }
    if($this->request->query('unidade')) {
      $option['Unidade.id'] = $this->request->query('unidade');
    }
    if($this->request->query('paciente')) {
      if($this->request->query('paciente') == '1') {
        $option[] = "Dependente.id IS NULL";
      }
      if($this->request->query('paciente') == '2') {
        $option[] = "Dependente.id IS NOT NULL";
      }
    }


    $this->paginate = [
      'conditions' => $option,
      'fields' => [
        'AtendimentosFilas.id',
        'AtendimentosFilas.data_hora_registro',
        'AtendimentosFilas.nome_solicitante',
        'AtendimentosFilas.telefone_solicitante',
        'AtendimentosFilas.observacao',
        'Especialidade.descricao',
        'Especialidade.id',
        'Beneficiario.id',
        'Dependente.id',
        'Unidade.nome',
        'Unidade.id',
        'Empresa.nome',
        'Empresa.situacao_seconci',
        'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
        'ultimo_contato' => '(SELECT data_hora_registro FROM atendimentos_contatos WHERE filas_id = AtendimentosFilas.id ORDER BY id DESC LIMIT 1)',
        'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiario.id ORDER BY id ASC)'
      ],
      'join' => [
        [
          'table' => 'beneficiarios',
          'alias' => 'Beneficiario',
          'type' => 'LEFT',
          'conditions' => 'Beneficiario.id = AtendimentosFilas.beneficiarios_id',
        ],
        [
          'table' => 'beneficiarios_dependentes',
          'alias' => 'Dependente',
          'type' => 'LEFT',
          'conditions' => 'Dependente.id = AtendimentosFilas.dependentes_id',
        ],
        [
          'table' => 'apoio_especialidades',
          'alias' => 'Especialidade',
          'type' => 'LEFT',
          'conditions' => 'Especialidade.id = AtendimentosFilas.especialidades_id',
        ],
        [
          'table' => 'apoio_unidades',
          'alias' => 'Unidade',
          'type' => 'LEFT',
          'conditions' => 'Unidade.id = AtendimentosFilas.unidades_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'LEFT',
          'conditions' => 'Empresa.id = AtendimentosFilas.empresas_id',
        ]
      ],
      'sortWhitelist'=> [
        'AtendimentosFilas.id',
        'AtendimentosFilas.data_hora_registro',
        'AtendimentosFilas.nome_solicitante',
        'AtendimentosFilas.telefone_solicitante',
        'AtendimentosFilas.observacao',
        'Especialidade.descricao',
        'Dependente.id',
        'Unidade.nome',
        'Empresa.nome',
        'paciente',
        'ultimo_contato'
      ],
      'order' => ['AtendimentosFilas.data_hora_registro' => 'asc'],
    ];

    $this->loadModel('ApoioUnidades');
    $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome'])->toArray();

    $this->loadModel('ApoioEspecialidades');
    $especialidades = $this->ApoioEspecialidades->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

    $this->set('atendimentos', $this->paginate($this->AtendimentosFilas));
    $this->set(compact('unidades', 'especialidades'));
  }

  public function add($beneficiario = null, $dependente = null) {
      $this->loadModel('BeneficiariosDependentes');
      $this->loadModel('AtendimentosFilas');
      $this->loadModel('Beneficiarios');
      $this->loadModel('Consultas');

      $dependente = $this->BeneficiariosDependentes->find('all', ['conditions' => ['id' => $dependente]])->first();
      $beneficiario = $this->Beneficiarios->find('all', [
        'conditions' => [
          'Empresa.id' => $this->request->session()->read('Auth.User.empresas_id'),
          'Beneficiarios.id' => $beneficiario,
        ],
        'fields' => [
            'Beneficiarios.id',
            'Beneficiarios.nome',
            'Beneficiarios.celular',
            'Beneficiarios.telefone',
            'Beneficiarios.situacao',
            'Empresa.id',
            'Empresa.nome',
            'Empresa.situacao',
            'Empresa.situacao_seconci',
            'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiarios.id ORDER BY id ASC)'
        ],
        'join' => [
            [
                'table' => 'beneficiarios_empresas',
                'alias' => 'be',
                'type' => 'LEFT',
                'conditions' => 'Beneficiarios.id = be.beneficiarios_id AND be.situacao = "A"',
            ],
            [
                'table' => 'empresas',
                'alias' => 'Empresa',
                'type' => 'LEFT',
                'conditions' => 'Empresa.id = be.empresas_id',
            ],
        ],
      ])->first();

      $consulta = $this->Consultas->newEntity();
      if ($this->request->is('post')) {
        $this->loadModel('AtendimentosFilas');

        $conditions = [
          'beneficiarios_id' => $this->request->data['beneficiarios_id'],
          'situacao' => 'AA',
        ];

        if(isset($this->request->data['dependentes_id'])) {
          $conditions[] = $this->request->data['dependentes_id'] ? "dependentes_id = {$this->request->data['dependentes_id']}" : 'dependentes_id IS NULL';
        }

        $existente = $this->AtendimentosFilas->find('all', ['conditions' => $conditions])->first();
        if($existente) {
          $this->Flash->error('J&aacute; existe um registro na fila!');
        } else {
          $fila = $this->AtendimentosFilas->patchEntity(
            $this->AtendimentosFilas->newEntity(), [
                'make_colaboradores_id' => $this->request->session()->read('Auth.User.id'),
                'dependentes_id' => isset($this->request->data['dependentes_id']) ? $this->request->data['dependentes_id'] : null,
                'beneficiarios_id' => $this->request->data['beneficiarios_id'],
                'empresas_id' => $this->request->data['empresas_id'],
                'unidades_id' => $this->request->data['unidades_id'],
                'especialidades_id' => $this->request->data['especialidades_id'],
                'nome_solicitante' => $this->request->data['nome_solicitante'],
                'telefone_solicitante' => $this->request->data['telefone_solicitante'],
                'turno_preferencia' => $this->request->data['turno'],
                'observacao' => $this->request->data['observacao'],
                'data_hora_registro' => date('Y-m-d H:i:s'),
                'situacao' => 'AA',
            ]);

          if ($this->AtendimentosFilas->save($fila)) {
              $this->Flash->success(__($this::MSG_SUCESSO_ADD));
              return $this->redirect(['action' => 'index']);
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }
        }
      }

      $existente = $this->AtendimentosFilas->find('all', ['conditions' => [
        'beneficiarios_id' => $beneficiario['id'],
        'situacao' => 'AA',
      ]])->first();
      if($existente) {
        $this->Flash->set('J&aacute; existe um registro na fila!', [
          'element' => 'warning'
        ]);
      }

      $existente = $this->Consultas->find('all', ['conditions' => [
        'especialidades_id' => $this->request->query('especialidade'),
        'beneficiarios_id' => $beneficiario['id'],
        'st_consulta IN("AG")'
      ]])->first();
      if($existente) {
        $this->Flash->set('Paciente j&aacute; possui um agendamento para '.date('d/m/Y H:i', strtotime($existente['data_hora_agendado'])), [
          'element' => 'warning'
        ]);
      }

      if($beneficiario['situacao'] != 'A' && $beneficiario['id']) {
        $this->Flash->set('Paciente com cadastro desativado.', [
          'element' => 'warning'
        ]);
      }

      if($beneficiario['Empresa']['situacao'] != 'A' && $beneficiario['id']) {
        $this->Flash->set('Empresa com cadastro desativado.', [
          'element' => 'warning'
        ]);
      }

      if($beneficiario['Empresa']['situacao_seconci'] != 'C' && $beneficiario['id']) {
        $beneficiario['Empresa']['situacao'] = 'I';
        $this->Flash->set('Empresa com cadastro inadimplente.', [
          'element' => 'warning'
        ]);
      }

      $this->loadModel('ApoioUnidades');
      $unidades = $this->ApoioUnidades->find('list',['conditions' => ["id IN(1,2)"], 'keyField' => 'id', 'valueField' => 'nome'])->toArray();

      $this->loadModel('ApoioEspecialidades');
      $especialidades = $this->ApoioEspecialidades->find('list',['conditions' => ["id IN(1)"], 'keyField' => 'id', 'valueField' => 'descricao'])->toArray();

      $this->loadModel('Profissionais');
      $profissionais = $this->Profissionais->find('list',[
        'conditions' => [
          'pe.especialidades_id' => $this->request->query('especialidade'),
          'Profissionais.situacao' => 'A'
        ],
        'keyField' => 'id',
        'valueField' => 'nome',
        'join' => [
            [
                'table' => 'profissionais_especialidades',
                'alias' => 'pe',
                'type' => 'INNER',
                'conditions' => 'Profissionais.id = pe.profissionais_id',
            ],
        ],
        'order' => ['nome']
      ])->toArray();

      $fone = '';
      if($dependente['celular']) {
        $fone = $dependente['celular'];
      } else if($dependente['telefone']) {
        $fone = $dependente['telefone'];
      } else if($beneficiario['celular']) {
        $fone = $beneficiario['celular'];
      } else if($beneficiario['telefone']) {
        $fone = $beneficiario['telefone'];
      }

      $fone = $this->Beneficiarios->mask(preg_replace('/[^0-9]/', '', $fone),'(##) #########');

      $this->set(compact('consulta', 'beneficiario', 'dependente', 'unidades', 'especialidades', 'profissionais', 'fone'));
  }

  public function pesquisar() {
    $this->loadModel('Beneficiarios');

    $option = ['be.empresas_id' => $this->request->session()->read('Auth.User.empresas_id')];
    if ($this->request->query('cpf')) {
        $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
        $option[] = "cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
    }

    if ($this->request->query('nome')) {
        $option['nome LIKE'] = "%{$this->request->query('nome')}%";
    }

    $beneficiario = $this->Beneficiarios->find('all', [
      'conditions' => $option,
      'fields' => [
        'Beneficiarios.id',
        'Beneficiarios.nome',
        'Beneficiarios.cpf',
        'Beneficiarios.situacao'
      ],
      'join' => [
          [
              'table' => 'beneficiarios_empresas',
              'alias' => 'be',
              'type' => 'LEFT',
              'conditions' => 'Beneficiarios.id = be.beneficiarios_id AND be.situacao = "A"',
          ]
      ],
      'order'=>['Beneficiarios.nome']
    ]);

    $this->set('pacientes', $this->paginate($beneficiario));
    $this->viewBuilder()->layout('ajax');
  }

  public function remover($id = null) {
    $this->loadModel('AtendimentosFilas');
    if ($this->request->is(['patch', 'post', 'put'])) {
        if($this->AtendimentosFilas->updateAll($this->request->data, ['id' => $id])) {
            $this->Flash->success('Registro removido da fila');
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error($this::MSG_ERRO);
        }
    }

    $fila = $this->AtendimentosFilas->find('all', [
        'conditions' => [
            'AtendimentosFilas.id' => $id,
        ],
        'fields' => [
            'AtendimentosFilas.id',
            'Beneficiario.id',
            'Beneficiario.cpf',
            'Beneficiario.nome',
            'Beneficiario.data_nascimento',
            'Dependente.id',
            'Dependente.cpf',
            'Dependente.nome',
            'Dependente.data_nascimento'
        ],
        'join' => [
            [
                'table' => 'beneficiarios',
                'alias' => 'Beneficiario',
                'type' => 'LEFT',
                'conditions' => 'Beneficiario.id = AtendimentosFilas.beneficiarios_id',
            ],
            [
                'table' => 'beneficiarios_dependentes',
                'alias' => 'Dependente',
                'type' => 'LEFT',
                'conditions' => 'Dependente.id = AtendimentosFilas.dependentes_id',
            ]
        ]
    ])->first();

    $this->set(compact('fila'));
    $this->viewBuilder()->layout('ajax');
  }
}
