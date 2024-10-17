<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AgendasController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'seconci');
    }

    public function index() {
        $this->loadModel('Consultas');
        $this->loadModel('Beneficiarios');

        $option = ["Consultas.st_consulta NOT IN('CA')"];
        if($this->request->query('date')) {
            $this->request->session()->write('Auth.User.filtro.date', $this->request->query('date'));
            $option['Consultas.data_hora_agendado LIKE'] = "{$this->request->query('date')}%";
        } else {
            return $this->redirect([
              'action' => 'index',
              'date' => $this->request->session()->read('Auth.User.filtro.date') ? $this->request->session()->read('Auth.User.filtro.date') : date('Y-m-d'),
              'unidade' => $this->request->session()->read('Auth.User.filtro.unidade') ? $this->request->session()->read('Auth.User.filtro.unidade') : $this->request->session()->read('Auth.User.unidades_id'),
              'turno' => $this->request->session()->read('Auth.User.filtro.turno') ? $this->request->session()->read('Auth.User.filtro.turno') : (date('H:i:s') <= '12:00:00' ? '1' : '2'),
              'especialidade'=>$this->request->session()->read('Auth.User.filtro.especialidade'),
              'profissional'=>$this->request->session()->read('Auth.User.filtro.profissional'),
            ]);
        }

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

        if($this->request->query('unidade')) {
            $this->request->session()->write('Auth.User.filtro.unidade', $this->request->query('unidade'));
            $option['Consultas.unidades_id'] = $this->request->query('unidade');
        } else {
            $this->request->session()->delete('Auth.User.filtro.unidade');
        }

        if($this->request->query('especialidade')) {
            $this->request->session()->write('Auth.User.filtro.especialidade', $this->request->query('especialidade'));
            $option['Especialidade.id'] = $this->request->query('especialidade');
        } else {
            $this->request->session()->delete('Auth.User.filtro.especialidade');
        }

        if($this->request->query('profissional')) {
            $this->request->session()->write('Auth.User.filtro.profissional', $this->request->query('profissional'));
            $option['Consultas.profissionais_id'] = $this->request->query('profissional');
        } else {
            $this->request->session()->delete('Auth.User.filtro.profissional');
        }

        if($this->request->query('turno')) {
            $this->request->session()->write('Auth.User.filtro.turno', $this->request->query('turno'));
            if($this->request->query('turno') == '1') {
                $option['Consultas.data_hora_agendado <='] = "{$this->request->query('date')} 12:00:00";
            }
            if($this->request->query('turno') == '2') {
                $option['Consultas.data_hora_agendado >='] = "{$this->request->query('date')} 13:00:00";
            }
        } else {
            $this->request->session()->delete('Auth.User.filtro.turno');
        }

        if($this->request->session()->read('Auth.User.profissionais_id')) {
          $option["Consultas.profissionais_id"] = $this->request->session()->read('Auth.User.profissionais_id');
        }

        $this->paginate = [
            'conditions' => $option,
            'fields' => [
                'Consultas.id',
                'Consultas.st_consulta',
                'Consultas.profissionais_id',
                'Consultas.data_hora_agendado',
                'Consultas.data_hora_atendimento',
                'Consultas.data_hora_pre_atendimento',
                'Consultas.data_hora_fecha_atendimento',
                'Especialidade.descricao',
                'Especialidade.id',
                'Motivo.descricao',
                'Beneficiario.id',
                'Dependente.id',
                'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
                'consultas' => "TIMEDIFF(Consultas.data_hora_termino_previsto, Consultas.data_hora_agendado)",
                'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiario.id ORDER BY id ASC)'
            ],
            'join' => [
                [
                    'table' => 'beneficiarios',
                    'alias' => 'Beneficiario',
                    'type' => 'LEFT',
                    'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
                ],
                [
                    'table' => 'beneficiarios_dependentes',
                    'alias' => 'Dependente',
                    'type' => 'LEFT',
                    'conditions' => 'Dependente.id = Consultas.dependentes_id',
                ],
                [
                    'table' => 'apoio_especialidades',
                    'alias' => 'Especialidade',
                    'type' => 'LEFT',
                    'conditions' => 'Especialidade.id = Consultas.especialidades_id',
                ],
                [
                    'table' => 'apoio_motivos_consultas',
                    'alias' => 'Motivo',
                    'type' => 'LEFT',
                    'conditions' => 'Motivo.id = Consultas.motivos_consultas_id',
                ]
            ],
            'sortWhitelist'=> [
              'Consultas.id',
              'Consultas.data_hora_agendado',
              'Consultas.data_hora_atendimento',
              'Consultas.data_hora_pre_atendimento',
              'Especialidade.descricao',
              'Motivo.descricao',
              'Dependente.id',
              'paciente'
            ],
            'order' => ['Consultas.data_hora_agendado' => 'asc'],
            'limit' => '100'
        ];

        $optionProfissionais=['Consultas.data_hora_agendado LIKE' => "{$this->request->query('date')}%"];
        if($this->request->session()->read('Auth.User.profissionais_id')) {
          $optionProfissionais["Profissional.id"] = $this->request->session()->read('Auth.User.profissionais_id');
        }

        $profissionais = $this->Consultas->find('list',[
          'conditions' => $optionProfissionais,
          'keyField' => 'Profissional.id',
          'valueField' => function($row) {
              return "{$row['Profissional']['nome']} {$row['atendidos']}/{$row['agendados']}";
          },
          'fields' => [
              'Profissional.id',
              'Profissional.nome',
              'agendados' => "(SELECT count(id) FROM consultas WHERE profissionais_id = Profissional.id AND data_hora_agendado LIKE '{$this->request->query('date')}%' AND st_consulta NOT IN('CA'))",
              'atendidos' => "(SELECT count(id) FROM consultas WHERE profissionais_id = Profissional.id AND data_hora_agendado LIKE '{$this->request->query('date')}%' AND st_consulta NOT IN('CA') AND data_hora_fecha_atendimento IS NOT NULL)"
          ],
          'join' => [
              [
                  'table' => 'profissionais',
                  'alias' => 'Profissional',
                  'type' => 'INNER',
                  'conditions' => 'Profissional.id = Consultas.profissionais_id',
              ],
          ],
          'group' => ['Profissional.id'],
          'order' => ['Profissional.nome']
        ])->toArray();

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',['conditions' => ["id IN(1,2,3)"], 'keyField' => 'id', 'valueField' => 'descricao'])->toArray();

        $this->loadModel('ApoioUnidades');
        $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome']])->toArray();

        $this->loadModel('Profissionais');
        $intervalos = $this->Profissionais->getItervalos();

        $this->set(compact('profissionais', 'especialidades', 'unidades', 'intervalos'));
        $this->set('consultas', $this->paginate($this->Consultas));
    }

    public function observacao($id = null) {
      $this->loadModel('ConsultasObservacoes');
      $observacao = $this->ConsultasObservacoes->newEntity();
      if ($this->request->is(['patch', 'post', 'put'])) {
          $this->request->data['consultas_id'] = $id;
          $this->request->data['tipo_observacao'] = '1';
          $this->ConsultasObservacoes->patchEntity($observacao, $this->request->data);
          if ($this->ConsultasObservacoes->save($observacao)) {
              $this->Flash->success(__($this::MSG_SUCESSO_ADD));
              return $this->redirect(['action' => 'index', 'date' => $this->request->query('date')]);
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }
      }

      $this->paginate = [
        'conditions' => [
          'consultas_id' => $id
        ],
        'order' => [
          'data_hora_registro' => 'DESC'
        ],
        'limit' => '3'
      ];

      $this->set('observacoes', $this->paginate($this->ConsultasObservacoes));
      $this->set(compact('observacao'));
      $this->viewBuilder()->layout('ajax');
    }

    public function abrirConsulta($id = null) {
      $this->loadModel('Consultas');
      if ($this->request->is(['patch', 'post', 'put'])) {
        $consulta = $this->Consultas->find('all', ['fields' => ['id', 'beneficiarios_id', 'dependentes_id', 'especialidades_id', 'consultas_id', 'tratamento_alta_retorno', 'data_hora_atendimento', 'data_hora_agendado'], 'conditions' => ['id' => $this->request->data['id']]])->first();
        $referencia = $this->Consultas->find('all', ['conditions' => ['id' => $consulta['consultas_id'], 'st_consulta' => 'AC']])->first();

        if(!$referencia && !$consulta['tratamento_alta_retorno'] && !$consulta['data_hora_atendimento']) {
          $opt=[
            "data_hora_agendado <= '{$consulta['data_hora_agendado']->format('Y-m-d H:i:s')}'",
            'especialidades_id' => $consulta['especialidades_id'],
            "id NOT IN({$consulta['id']})",
            'st_consulta' => 'AC'
          ];
          if(isset($consulta['beneficiarios_id'])) {
            $opt['beneficiarios_id'] = $consulta['beneficiarios_id'];
          }
          if(isset($consulta['dependentes_id'])) {
            $opt['dependentes_id'] = $consulta['dependentes_id'];
          } else {
            $opt[] = 'dependentes_id IS NULL';
          }

          $referencia = $this->Consultas->find('all', [
            'conditions' => $opt,
            'fields' => [
              'id',
              'anamnese_qp_hda',
              'anamnese_odonto_obs',
              'tratamento_alta_retorno',
              'numero_escovacoes_diarias',
            ],
            'order' => [
              'data_hora_agendado' => 'DESC'
            ]])->first();
        }

        if($referencia['tratamento_alta_retorno'] || $consulta['data_hora_atendimento']) {
          $referencia = $this->Consultas->newEntity();
        }

        $this->request->session()->write('Auth.User.bloqueado', false);
        $r = $this->Consultas->updateAll([
          'data_hora_atendimento' => date('Y-m-d H:i:s'),
          'numero_escovacoes_diarias' => $referencia['numero_escovacoes_diarias'],
          'anamnese_odonto_obs' => $referencia['anamnese_odonto_obs'],
          'anamnese_qp_hda' => $referencia['anamnese_qp_hda'],
        ], ['id' => $this->request->data['id'], 'data_hora_atendimento IS NULL']);

        $this->loadModel('ConsultasProfissionais');
        $data = $this->ConsultasProfissionais->patchEntity(
          $this->ConsultasProfissionais->newEntity(), [
              'consultas_id' => $this->request->data['id'],
              'profissionais_id' => $this->request->data['profissionais_id'],
              'data_hora_inicio' => date('Y-m-d H:i:s'),
              'tipo_atendimento' => $r ? 'AP' : 'AC',
          ]);

        if($this->ConsultasProfissionais->save($data)) {
          if($referencia['id']) {
            foreach (['1','0'] as $r) {
              $this->loadModel('OdontoOdontogramas');
              $odontograma = $this->OdontoOdontogramas->find('all', ['conditions' => [
                'consultas_id' => $referencia['id'],
                'referencia' => $r
              ], 'order' => ['id' => 'DESC']])->first();

              if(!$odontograma['id']) {
                continue;
              }

              $this->loadModel('OdontoProcedimentosAplicados');
              $procedimentos = $this->OdontoProcedimentosAplicados->find('all', ['conditions' => [
                'odontogramas_id' => $odontograma['id'],
              ]])->toArray();

              unset($odontograma['id']);
              $odontograma['consultas_id'] = $consulta['id'];
              $odontograma['data_hora_registro'] = date('Y-m-d H:i:s');
              $odontograma = $this->OdontoOdontogramas->patchEntity($this->OdontoOdontogramas->newEntity(), $odontograma->toArray());
              if ($this->OdontoOdontogramas->save($odontograma)) {
                  $this->loadComponent('Log');
                  if(!$this->Log->save('odontograma', $odontograma)) {
                     $this->Flash->error($this::MSG_ERRO_LOG);
                  }

                  foreach ($procedimentos as $key => $value) {
                    unset($value['id']);
                    $value['total_realizado_hoje'] = null;
                    $value['odontogramas_id'] = $odontograma['id'];
                    $procedimento = $this->OdontoProcedimentosAplicados->patchEntity($this->OdontoProcedimentosAplicados->newEntity(), $value->toArray());
                    if (!$this->OdontoProcedimentosAplicados->save($procedimento)) {
                      $this->Flash->error("Falha ao gravar o procedimento {$value['procedimentos_id']}");
                    } else {
                      $this->loadComponent('Log');
                      if(!$this->Log->save('procedimento', $procedimento)) {
                         $this->Flash->error($this::MSG_ERRO_LOG);
                      }
                    }
                  }
              } else {
                  $this->Flash->error(__('Falha ao gerar odontograma!'));
              }

              unset($odontograma);
              unset($procedimentos);
              unset($procedimento);
            }

            $this->loadModel('ApoioAnamnese');
            $anamneses = $this->ApoioAnamnese->find('all', [
              'fields' => [
                'ApoioAnamnese.id',
                'Anamnese.resposta',
                'Anamnese.observacao',
              ],
              'join' => [
                [
                  'table' => 'consultas_anamnese',
                  'alias' => 'Anamnese',
                  'type' => 'LEFT',
                  'conditions' => "ApoioAnamnese.id = Anamnese.anamnese_id AND Anamnese.consultas_id = {$referencia['id']}",
                ],
              ]])->toArray();

            $this->loadModel('ConsultasAnamnese');
            foreach ($anamneses as $a) {
              $anamnese = $this->ConsultasAnamnese->patchEntity(
                $this->ConsultasAnamnese->newEntity(), [
                    'anamnese_id' => $a['id'],
                    'consultas_id' => $consulta['id'],
                    'resposta' => $a['Anamnese']['resposta'],
                    'observacao' => $a['Anamnese']['observacao']
                ]);

              if (!$this->ConsultasAnamnese->save($anamnese)) {
                $this->Flash->error(__('Falha ao gerar anamnese!'));
              }
            }

          }
        }

        return $this->redirect(['controller' => 'Atendimentos', 'action' => 'anamnese', $this->request->data['id']]);
      }

      $consulta = $this->Consultas->find('all', [
          'conditions' => [
              'Consultas.id' => $id,
          ],
          'fields' => [
              'Consultas.id',
              'Consultas.profissionais_id',
              'Consultas.data_hora_agendado',
              'Consultas.data_hora_atendimento',
              'Consultas.data_hora_pre_atendimento',
              'Especialidade.descricao',
              'Profissional.nome',
              'Beneficiario.id',
              'Beneficiario.cpf',
              'Beneficiario.nome',
              'Beneficiario.data_nascimento',
              'Dependente.id',
              'Dependente.cpf',
              'Dependente.nome',
              'Dependente.data_nascimento',
              'Empresa.id',
              'Empresa.nome',
              'funcoes' => '(SELECT GROUP_CONCAT(af.descricao) FROM apoio_funcoes af
                             INNER JOIN beneficiarios_funcoes bf ON af.id = bf.funcoes_id
                             WHERE bf.beneficiarios_id = Beneficiario.id)'
          ],
          'join' => [
              [
                  'table' => 'beneficiarios',
                  'alias' => 'Beneficiario',
                  'type' => 'LEFT',
                  'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
              ],
              [
                  'table' => 'beneficiarios_dependentes',
                  'alias' => 'Dependente',
                  'type' => 'LEFT',
                  'conditions' => 'Dependente.id = Consultas.dependentes_id',
              ],
              [
                  'table' => 'beneficiarios_empresas',
                  'alias' => 'be',
                  'type' => 'LEFT',
                  'conditions' => 'Beneficiario.id = be.beneficiarios_id AND be.situacao = "A"',
              ],
              [
                  'table' => 'empresas',
                  'alias' => 'Empresa',
                  'type' => 'LEFT',
                  'conditions' => 'Empresa.id = be.empresas_id',
              ],
              [
                  'table' => 'profissionais',
                  'alias' => 'Profissional',
                  'type' => 'LEFT',
                  'conditions' => 'Profissional.id = Consultas.profissionais_id',
              ],
              [
                  'table' => 'apoio_especialidades',
                  'alias' => 'Especialidade',
                  'type' => 'LEFT',
                  'conditions' => 'Especialidade.id = Consultas.especialidades_id',
              ]
          ]
      ])->first();

      $this->set(compact('consulta'));
      $this->viewBuilder()->layout('ajax');
    }

    public function visualizar($id = null) {
      $this->request->session()->write('Auth.User.bloqueado', true);
      return $this->redirect(['controller' => 'Atendimentos', 'action' => 'anamnese', $id]);
    }

}
