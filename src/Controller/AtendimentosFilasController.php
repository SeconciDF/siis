<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AtendimentosFilasController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'consulta');
    }

    public function index() {
        $this->loadModel('Beneficiarios');

        $option = [
            'AtendimentosFilas.situacao' => 'AA',
            'AtendimentosFilas.data_hora_registro > "2015-12-31"'
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

    public function historico() {
        $this->loadModel('Beneficiarios');

        $option = ['AtendimentosFilas.situacao IN("RF","RO")'];
        if($this->request->query('especialidade')) {
            $option['Especialidade.id'] = $this->request->query('especialidade');
        }
        if ($this->request->query('cpf')) {
            $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
            $option[] = "IFNULL(Dependente.cpf,Beneficiario.cpf) IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
        }
        if ($this->request->query('nome')) {
            $option['IFNULL(Dependente.nome,Beneficiario.nome) LIKE'] = "%{$this->request->query('nome')}%";
        }

        $this->paginate = [
            'conditions' => $option,
            'fields' => [
                'Colaborador.nome',
                'AtendimentosFilas.id',
                'AtendimentosFilas.situacao',
                'AtendimentosFilas.motivo_retirada',
                'AtendimentosFilas.data_hora_agendamento',
                'Dependente.id',
                'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
                'data_hora' => 'IF(AtendimentosFilas.situacao="RF",AtendimentosFilas.data_hora_agendamento,AtendimentosFilas.data_hora_retira)',
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
                    'table' => 'seguranca_colaboradores',
                    'alias' => 'Colaborador',
                    'type' => 'LEFT',
                    'conditions' => 'Colaborador.id = AtendimentosFilas.retirada_colaboradores_id',
                ]
            ],
            'sortWhitelist'=> [
              'Colaborador.nome',
              'AtendimentosFilas.id',
              'AtendimentosFilas.situacao',
              'AtendimentosFilas.motivo_retirada',
              'paciente',
              'data_hora'
            ],
            'order' => ['data_hora' => 'DESC'],
        ];

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

        $this->set('atendimentos', $this->paginate($this->AtendimentosFilas));
        $this->set(compact('especialidades'));
    }

    public function observacao($id = null) {
      $this->loadModel('AtendimentosContatos');
      $contato = $this->AtendimentosContatos->newEntity();
      if ($this->request->is(['patch', 'post', 'put'])) {
          $this->AtendimentosContatos->patchEntity($contato, $this->request->data);
          if ($this->AtendimentosContatos->save($contato)) {
              $this->Flash->success(__($this::MSG_SUCESSO_ADD));
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
              'AtendimentosFilas.situacao',
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

      $this->paginate = [
        'conditions' => [
          'filas_id' => $fila['id']
        ],
        'order' => [
          'data_hora_registro' => 'DESC'
        ],
        'limit' => '3'
      ];

      $this->set('contatos', $this->paginate($this->AtendimentosContatos));
      $this->set(compact('fila', 'contato'));
      $this->viewBuilder()->layout('ajax');
    }

    public function remover($id = null) {
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
