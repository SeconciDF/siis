<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

class ConsultasController extends AppController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['autofaltas']);
        $this->request->session()->write('Auth.User.MenuActive', 'consulta');
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

        $this->paginate = [
            'conditions' => $option,
            'fields' => [
                'Consultas.id',
                'Consultas.st_consulta',
                'Consultas.profissionais_id',
                'Consultas.data_hora_agendado',
                'Consultas.data_hora_atendimento',
                'Consultas.data_hora_pre_atendimento',
                'Consultas.data_hora_termino_previsto',
                'Consultas.data_hora_fecha_atendimento',
                'Especialidade.descricao',
                'Especialidade.id',
                'Profissional.nome',
                'Profissional.id',
                'Beneficiario.id',
                'Dependente.id',
                'Unidade.nome',
                'Unidade.id',
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
                ],
                [
                    'table' => 'apoio_unidades',
                    'alias' => 'Unidade',
                    'type' => 'LEFT',
                    'conditions' => 'Unidade.id = Consultas.unidades_id',
                ]
            ],
            'sortWhitelist'=> [
              'Consultas.id',
              'Consultas.data_hora_agendado',
              'Consultas.data_hora_atendimento',
              'Consultas.data_hora_pre_atendimento',
              'Especialidade.descricao',
              'Profissional.nome',
              'Beneficiario.id',
              'Dependente.id',
              'Unidade.nome',
              'paciente'
            ],
            'order' => ['Profissional.nome' => 'asc', 'Consultas.data_hora_agendado' => 'asc'],
            'limit' => '100'
        ];

        $profissionais = $this->Consultas->find('list',[
          'keyField' => 'Profissional.id',
          'valueField' => function($row) {
              return "{$row['Profissional']['nome']} {$row['atendidos']}/{$row['agendados']}";
          },
          'conditions' => [
              'Consultas.data_hora_agendado LIKE' => "{$this->request->query('date')}%"
          ],
          'fields' => [
              'Profissional.id',
              'Profissional.nome',
              'agendados' => "(SELECT count(id) FROM consultas WHERE profissionais_id = Profissional.id AND data_hora_agendado LIKE '{$this->request->query('date')}%' AND st_consulta NOT IN('CA'))",
              'atendidos' => "(SELECT count(id) FROM consultas WHERE profissionais_id = Profissional.id AND data_hora_agendado LIKE '{$this->request->query('date')}%' AND st_consulta NOT IN('CA') AND data_hora_atendimento IS NOT NULL)"
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

    public function add($beneficiario = null, $dependente = null) {
        $this->loadModel('Beneficiarios');
        $this->loadModel('BeneficiariosDependentes');

        $dependente = $this->BeneficiariosDependentes->find('all', ['conditions' => ['id' => $dependente]])->first();
        $beneficiario = $this->Beneficiarios->find('all', [
          'conditions' => ['Beneficiarios.id' => $beneficiario],
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
            if($this->request->data['fila'] == 'S') {
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
                } else {
                    $this->Flash->error($this::MSG_ERRO);
                }
              }
            }

            /**
             * Add Consultas
             */
            if(isset($this->request->data['vagas']) && $this->request->data['fila'] == 'N') {
                $referencia = $this->Consultas->find('all', ['conditions' => ['id' => $this->request->data['consultas_id']]])->first();

                $this->loadModel('Profissionais');
                foreach ($this->request->data['vagas'] as $key => $value) {
                    $intervalos = $this->Profissionais->getItervalos();
                    $data = explode(';', $value);

                    $conditions = [
                      'especialidades_id' => $this->request->data['especialidades_id'],
                      'beneficiarios_id' => $this->request->data['beneficiarios_id'],
                      'data_hora_agendado LIKE "' . date('Y-m-d', strtotime($data[1])) . '%"',
                      'st_consulta IN("AG")'
                    ];

                    if(isset($this->request->data['dependentes_id'])) {
                      $conditions[] = $this->request->data['dependentes_id'] ? "dependentes_id = {$this->request->data['dependentes_id']}" : 'dependentes_id IS NULL';
                    }

                    $existente = $this->Consultas->find('all', ['conditions' => $conditions])->first();
                    if($existente && !in_array($this->request->query('tipo'), ['remarcar','retorno'])) {
                      $this->Flash->error('J&aacute; existe um agendamento para este dia!');
                      continue;
                    }

                    $conditions = [
                      'profissionais_id' => $this->request->data['profissionais_id'],
                      'especialidades_id' => $this->request->data['especialidades_id'],
                      'unidades_id' => $this->request->data['unidades_id'],
                      'data_hora_agendado' => $data[1],
                      'st_consulta IN("AG")'
                    ];
                    $existente = $this->Consultas->find('all', ['conditions' => $conditions])->first();
                    if($existente) {
                      $this->Flash->error('J&aacute; existe um agendamento para este dia!');
                      continue;
                    }

                    $termino = new \DateTime($data[1]);
                    if(isset($this->request->data['consulta_dupla']) && isset($intervalos[$this->request->data['especialidades_id']])) {
                      $interval = ($intervalos[$this->request->data['especialidades_id']]*$this->request->data['consulta_dupla'])+1;
                      $termino->modify("+{$interval} minutes");
                    } else if(isset($intervalos[$this->request->data['especialidades_id']])) {
                      $termino->modify("+{$intervalos[$this->request->data['especialidades_id']]} minutes");
                    }

                    $consulta = $this->Consultas->patchEntity(
                      $this->Consultas->newEntity(), [
                          'marca_colaboradores_id' => $this->request->session()->read('Auth.User.id'),
                          'dependentes_id' => isset($this->request->data['dependentes_id']) ? $this->request->data['dependentes_id'] : null,
                          'beneficiarios_id' => $this->request->data['beneficiarios_id'],
                          'empresas_id' => $this->request->data['empresas_id'],
                          'unidades_id' => $this->request->data['unidades_id'],
                          'especialidades_id' => $this->request->data['especialidades_id'],
                          'motivos_consultas_id' => $this->request->data['motivos_consultas_id'],
                          'nome_solicitante' => $this->request->data['nome_solicitante'],
                          'telefone_solicitante' => $this->request->data['telefone_solicitante'],
                          'filas_id' => $this->request->data['filas_id'],
                          'data_hora_agendado' => $data[1],
                          'data_hora_termino_previsto' => $termino->format('Y-m-d H:i:s'),
                          'data_hora_pre_atendimento' => (new \DateTime(date('Y-m-d H:i:s')) > new \DateTime($data[1])) ? $data[1] : null,
                          'consultas_id' => $referencia['id'],
                          'profissionais_id' => $data[0],
                          'st_consulta' => 'AG',
                          'emergencial' => '0',
                      ]);


                    if ($this->Consultas->save($consulta)) {
                        $this->loadComponent('Log');
                        if(!$this->Log->save('add', $consulta)) {
                           $this->Flash->error($this::MSG_ERRO_LOG);
                        }

                        if($consulta['filas_id']) {
                          $this->loadModel('AtendimentosFilas');
                          $this->AtendimentosFilas->updateAll(['situacao' => 'RF', 'data_hora_agendamento' => date('Y-m-d H:i:s')], ['id' => $consulta['filas_id']]);
                        }

                        if($this->request->query('tipo') == 'remarcar') {
                          if(!$this->Consultas->updateAll(['st_consulta' => 'CA', 'data_hora_nao_consulta' => date('Y-m-d H:i:s'), 'nao_consulta_colaboradores_id' => $this->request->session()->read('Auth.User.id')], ['id' => $referencia['id']])) {
                              $this->Flash->error(__('Falha ao cancelar consulta desmarcada!'));
                          }
                        }

                        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                    } else {
                        $this->Flash->error($this::MSG_ERRO);
                    }
                }

                return $this->redirect([
                  'action' => 'add',
                  $beneficiario['id'],
                  $dependente['id'],
                  'date'=>$this->request->query('date'),
                  'unidade'=>$this->request->query('unidade'),
                  'especialidade'=>$this->request->query('especialidade'),
                  'profissional'=>$this->request->query('profissional'),
                  'consulta'=>$this->request->query('consulta'),
                  'filas_id'=>$this->request->query('filas_id'),
                  'fila'=>$this->request->query('fila'),
                  'tipo'=>$this->request->query('tipo'),
                ]);
            }
            /**
             * End Consultas
             */
        }

        $conditions = [
          'especialidades_id' => $this->request->query('especialidade'),
          'beneficiarios_id' => $beneficiario['id'],
          'st_consulta IN("AG")'
        ];
        $conditions[] = $dependente['id'] ? "dependentes_id = {$dependente['id']}" : 'dependentes_id IS NULL';
        $existente = $this->Consultas->find('all', ['conditions' => $conditions])->first();
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
        $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome'])->toArray();

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',['conditions' => ["id IN(1,2,3)"], 'keyField' => 'id', 'valueField' => 'descricao'])->toArray();

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

    public function emergencial($beneficiario = null, $dependente = null) {
        $this->loadModel('Beneficiarios');
        $this->loadModel('BeneficiariosDependentes');

        $dependente = $this->BeneficiariosDependentes->find('all', ['conditions' => ['id' => $dependente]])->first();
        $beneficiario = $this->Beneficiarios->find('all', [
          'conditions' => ['Beneficiarios.id' => $beneficiario],
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
          if($this->request->data['data_inicio']) {
              $this->request->session()->write('Auth.User.filtro.date', $this->Consultas->formatDate($this->request->data['data_inicio']));
          }
          if($this->request->data['especialidades_id']) {
              $this->request->session()->write('Auth.User.filtro.especialidade', $this->request->data['especialidades_id']);
          }
          if($this->request->data['profissionais_id']) {
              $this->request->session()->write('Auth.User.filtro.profissional', $this->request->data['profissionais_id']);
          }
          if($this->request->data['motivos_consultas_id']) {
              $this->request->session()->write('Auth.User.filtro.motivo', $this->request->data['motivos_consultas_id']);
          }
          if($this->request->data['unidades_id']) {
              $this->request->session()->write('Auth.User.filtro.unidade', $this->request->data['unidades_id']);
          }
          if($this->request->data['turno']) {
              $this->request->session()->write('Auth.User.filtro.turno', $this->request->data['turno']);
          }

          $conditions = [
            'especialidades_id' => $this->request->data['especialidades_id'],
            'beneficiarios_id' => $this->request->data['beneficiarios_id'],
            'data_hora_agendado LIKE "' . $this->Consultas->formatDate($this->request->data['data_inicio']) . '%"',
            'st_consulta IN("AG")'
          ];

          if(isset($this->request->data['dependentes_id'])) {
            $conditions[] = $this->request->data['dependentes_id'] ? "dependentes_id = {$this->request->data['dependentes_id']}" : 'dependentes_id IS NULL';
          }

          $existente = $this->Consultas->find('all', ['conditions' => $conditions])->first();
          if($existente) {
            $this->Flash->error('J&aacute; existe um agendamento para este dia!');
            return $this->redirect(['action' => 'emergencial', $beneficiario['id'], $dependente['id']]);
          }

          $opt=['data_hora_agendado <= "'.date('Y-m-d H:i:s').'"'];
          if(isset($this->request->data['beneficiarios_id'])) {
            $opt['beneficiarios_id'] = $this->request->data['beneficiarios_id'];
          }
          if(isset($this->request->data['dependentes_id'])) {
            $opt['dependentes_id'] = $this->request->data['dependentes_id'];
          }

          if(isset($this->request->data['beneficiarios_id']) || isset($this->request->data['dependentes_id'])) {
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
          } else {
            $referencia = $this->Consultas->newEntity();
          }

          $agendamento = $this->Consultas->formatDate($this->request->data['data_inicio'], $this->request->data['time_inicio']);
          $agora = date('Y-m-d H:i:s');

          $this->loadModel('Profissionais');
          $intervalos = $this->Profissionais->getItervalos();

          $termino = new \DateTime($agendamento);
          $termino->modify("+{$intervalos[$this->request->data['especialidades_id']]} minutes");

          $consulta = $this->Consultas->patchEntity(
            $this->Consultas->newEntity(), [
                'dependentes_id' => isset($this->request->data['dependentes_id']) ? $this->request->data['dependentes_id'] : null,
                'beneficiarios_id' => $this->request->data['beneficiarios_id'],
                'empresas_id' => $this->request->data['empresas_id'],
                'unidades_id' => $this->request->data['unidades_id'],
                'especialidades_id' => $this->request->data['especialidades_id'],
                'motivos_consultas_id' => $this->request->data['motivos_consultas_id'],
                'profissionais_id' => $this->request->data['profissionais_id'],
                'marca_colaboradores_id' => $this->request->session()->read('Auth.User.id'),
                'data_hora_pre_atendimento' => (new \DateTime($agora) > new \DateTime($agendamento)) ? $agora : null,
                'data_hora_agendado' => $agendamento,
                'data_hora_termino_previsto' => $termino->format('Y-m-d H:i:s'),
                'consultas_id' => $referencia['id'],
                'st_consulta' => 'AG',
                'emergencial' => '1',
            ]);

          if ($this->Consultas->save($consulta)) {
              $this->loadComponent('Log');
              if(!$this->Log->save('emergencial', $consulta)) {
                 $this->Flash->error($this::MSG_ERRO_LOG);
              }

              $this->Flash->success(__($this::MSG_SUCESSO_ADD));
              return $this->redirect(['action' => 'emergencial', $beneficiario['id'], $dependente['id']]);
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }
        }

        $conditions = [
          'beneficiarios_id' => $beneficiario['id'],
          'st_consulta IN("AG")'
        ];
        $conditions[] = $dependente['id'] ? "dependentes_id = {$dependente['id']}" : 'dependentes_id IS NULL';
        $existente = $this->Consultas->find('all', ['conditions' => $conditions])->first();
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
        $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome'])->toArray();

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',['conditions' => ["id IN(1,2,3)"], 'keyField' => 'id', 'valueField' => 'descricao'])->toArray();

        $this->loadModel('Profissionais');
        $profissionais = $this->Profissionais->find('list',[
          'conditions' => ["pe.especialidades_id IN(1,2,3)"],
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

        $this->set(compact('consulta', 'beneficiario', 'dependente', 'unidades', 'especialidades', 'profissionais'));
    }

    public function pesquisar() {
      $this->loadModel('Beneficiarios');
      $this->loadModel('BeneficiariosDependentes');

      $option = [
        'beneficiario'=>null,
        'dependente'=>null
      ];

      if ($this->request->query('id')) {
          $prontuario = $this->Beneficiarios->prontuario($this->request->query('id'));
          $option['beneficiario']['id'] = $prontuario['beneficiario'];
          $option['dependente']['id'] = $prontuario['dependente'];
      }

      $prontuario = $this->Beneficiarios->prontuario($this->request->query('id'));
      if ($this->request->query('id') && $prontuario['beneficiario']) {
          $option['beneficiario']['id'] = $prontuario['beneficiario'];
      }
      if ($this->request->query('id') && $prontuario['dependente']) {
          $option['dependente']['id'] = $prontuario['dependente'];
      } else {
          $option[] = 'Dependente.id IS NULL';
      }


      if ($this->request->query('cpf')) {
          $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
          $option['beneficiario'][] = "cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
          $option['dependente'][] = "cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
      }
      if ($this->request->query('nome')) {
          $option['beneficiario']['nome LIKE'] = "%{$this->request->query('nome')}%";
          $option['dependente']['nome LIKE'] = "%{$this->request->query('nome')}%";
      }

      $beneficiario = $this->Beneficiarios->find('all', ['conditions' => $option['beneficiario'], 'fields' => ['id','nome','cpf','situacao','beneficiarios_id'=>'id','dependentes_id'=>'0'], 'order'=>['nome']]);
      $dependente = $this->BeneficiariosDependentes->find('all', ['conditions' => $option['dependente'],'fields' => ['id','nome','cpf','situacao','beneficiarios_id','dependentes_id'=>'id'], 'order'=>['nome']]);
      $pacientes = $beneficiario->union($dependente);

      $this->set('pacientes', $this->paginate($pacientes));
      $this->viewBuilder()->layout('ajax');
    }

    public function removerConsulta($id = null) {
      if ($this->request->is(['patch', 'post', 'put'])) {
          if($this->Consultas->updateAll(['st_consulta' => 'CA', 'data_hora_nao_consulta' => date('Y-m-d H:i:s'), 'nao_consulta_colaboradores_id' => $this->request->session()->read('Auth.User.id')], ['id' => $id])) {
              $this->Flash->success('Cancelamento registrado');
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }

          return $this->redirect([
            'action' => 'index',
            'date'=>$this->request->data['date'],
            'unidade'=>$this->request->session()->read('Auth.User.filtro.unidade'),
            'especialidade'=>$this->request->session()->read('Auth.User.filtro.especialidade'),
            'profissional'=>$this->request->session()->read('Auth.User.filtro.profissional'),
            'turno'=>$this->request->session()->read('Auth.User.filtro.turno'),
          ]);
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

    public function confirmarChegada($id = null) {
      if ($this->request->is(['patch', 'post', 'put'])) {
          if($this->Consultas->updateAll(['data_hora_pre_atendimento' => date('Y-m-d H:i:s'), 'apresentou_documento' => $this->request->data['apresentou_documento']], ['id' => $this->request->data['id']])) {
              $this->Flash->success($this::MSG_SUCESSO_EDT);
              return $this->redirect(['controller' => 'consultas', 'date' => $this->request->data['date'], 'unidade' => $this->request->data['unidade'], 'turno' => $this->request->data['turno']]);
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }
      }


      $consulta = $this->Consultas->find('all', [
          'conditions' => [
              'Consultas.id' => $id,
          ],
          'fields' => [
              'Consultas.id',
              'Consultas.unidades_id',
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

    public function montarAgenda() {
        $this->loadModel('Profissionais');
        $this->loadModel('ProfissionaisAgendas');
        $this->loadModel('ProfissionaisIndisponibilidades');

        $options = '';
        if($this->request->query('date')) {
            $options .= " WHERE '{$this->request->query('date')}' BETWEEN pa.data_inicio AND IFNULL(pa.data_fim,'{$this->request->query('date')}') ";
            $options .= " AND (SELECT id FROM apoio_indisponibilidades WHERE data = '{$this->request->query('date')}' LIMIT 1) IS NULL ";
            $options .= " AND (SELECT id FROM profissionais_indisponibilidades WHERE profissionais_id = p.id AND CONCAT('{$this->request->query('date')}',' ',phd.horario) BETWEEN data_hora_inicio AND data_hora_fim LIMIT 1) IS NULL ";
        }

        if($this->request->query('especialidade')) {
            $options .= " AND ae.id = '{$this->request->query('especialidade')}' ";
        }

        if($this->request->query('unidade')) {
            $options .= " AND au.id = '{$this->request->query('unidade')}'";
        }

        if($this->request->query('profissional')) {
            $options .= " AND p.id = '{$this->request->query('profissional')}'";
        }

        $days = array('Sunday'=>'DOM','Monday'=>'SEG','Tuesday'=>'TER','Wednesday'=>'QUA','Thursday'=>'QUI','Friday'=>'SEX','Saturday'=>'SAB');
        $w = date('l', strtotime($this->request->query('date')));
        if(isset($days[$w])) {
            $options .= " AND pd.dia_semana = '{$days[$w]}' ";
        }

        $sql = "SELECT ae.id as especialidades_id, pd.id as disponiveis_id, p.id as profissionais_id, p.nome as profissional, ae.descricao as especialidade, au.nome as unidade, pa.data_inicio, IFNULL(pa.data_fim,'{$this->request->query('date')}') as data_fim, phd.horario, pd.dia_semana ,pa.tipo,
                (SELECT TIMEDIFF(data_hora_termino_previsto, data_hora_agendado) FROM consultas WHERE profissionais_id = p.id AND st_consulta NOT IN('CA') AND data_hora_agendado = CONCAT('{$this->request->query('date')}',' ',phd.horario) AND especialidades_id = ae.id LIMIT 1) as intervalo,
                (SELECT id FROM consultas WHERE profissionais_id = p.id AND st_consulta NOT IN('CA') AND data_hora_agendado = CONCAT('{$this->request->query('date')}',' ',phd.horario) AND especialidades_id = ae.id LIMIT 1) as consulta
                FROM profissionais_agendas pa
                INNER JOIN profissionais p ON p.id = pa.profissionais_id
                INNER JOIN profissionais_disponiveis pd ON pa.id = pd.agendas_id
                INNER JOIN profissionais_horarios_disponiveis phd ON pd.id = phd.disponiveis_id
                INNER JOIN apoio_especialidades ae ON ae.id = pd.especialidades_id
                INNER JOIN apoio_unidades au ON au.id = phd.unidades_id
                {$options} ORDER BY phd.horario ASC, p.nome ASC";

        $conn = ConnectionManager::get('default');
        $agenda = $conn->execute($sql)->fetchAll('assoc');

        $json = [];
        $intervalos = $this->Profissionais->getItervalos();
        foreach ($agenda as $key => $value) {
            $seconds = strtotime("1970-01-01 {$value['intervalo']} UTC");
            $intervalo[$value['profissionais_id']][] = round(($seconds/60) / $intervalos[$value['especialidades_id']]);

            $push = $intervalo[$value['profissionais_id']];
            array_pop($push);
            if(in_array(end($push), ['0','1'])) {
              $json[$value['dia_semana']][$value['horario']][$value['profissionais_id']] = [
                'date' => $this->request->query('date'),
                'nome' => $value['profissional'],
                'unidade' => $value['unidade'],
                'especialidade' => $value['especialidade'],
                'data_inicio' => date('d/m/Y', strtotime($value['data_inicio'])),
                'data_fim' => date('d/m/Y', strtotime($value['data_fim'])),
                'hora' => $value['horario'],
                'disponiveis_id' => $value['disponiveis_id'],
                'profissionais_id' => $value['profissionais_id'],
                'dia_semana' => $value['dia_semana'],
                'consulta' => $value['consulta'],
              ];
            }
        }

        echo json_encode($json);
        // pr($json);
        exit;
    }

    public function faltas($search = null) {
        $options = [
            'Consultas.st_consulta' => 'FA'
        ];

        if($this->request->query('inicio') && !$this->request->query('fim')) {
          $options['Consultas.data_hora_agendado LIKE'] = "{$this->Consultas->formatDate($this->request->query('inicio'))}%";
        } else if($this->request->query('inicio') && $this->request->query('fim')) {
          $dInicio = trim($this->Consultas->formatDate($this->request->query('inicio')));
          $dFim = trim($this->Consultas->formatDate($this->request->query('fim')));
          $hInicio = $this->request->query('h_inicio');
          $hFim = $this->request->query('h_fim');

          if($dInicio && $dFim) {
            $options[] = "Consultas.data_hora_agendado BETWEEN '{$dInicio}' AND '{$dFim}'";
          }
          if($hInicio && $hFim) {
            $options[] = "TIME(Consultas.data_hora_agendado) BETWEEN '{$hInicio}' AND '{$hFim}'";
          }
        } else {
            return $this->redirect(['action' => 'faltas', 'inicio' => date('d/m/Y', strtotime(date('Y-m-d') . ' -1 day'))]);
        }

        $this->loadModel('Consultas');
        $this->paginate = [
          'conditions' => $options,
          'fields' => [
              'Consultas.id',
              'Consultas.data_hora_agendado',
              'Profissional.id',
              'Profissional.nome',
              'Especialidade.id',
              'Especialidade.descricao',
              'Empresa.id',
              'Empresa.nome',
              'Dependente.id',
              'Beneficiario.id',
              'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
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
                  'table' => 'profissionais',
                  'alias' => 'Profissional',
                  'type' => 'INNER',
                  'conditions' => 'Profissional.id = Consultas.profissionais_id',
              ],
              [
                  'table' => 'apoio_especialidades',
                  'alias' => 'Especialidade',
                  'type' => 'INNER',
                  'conditions' => 'Especialidade.id = Consultas.especialidades_id',
              ],
              [
                  'table' => 'empresas',
                  'alias' => 'Empresa',
                  'type' => 'INNER',
                  'conditions' => 'Empresa.id = Consultas.empresas_id',
              ],
          ],
          'sortWhitelist'=> [
              'Consultas.id',
              'Consultas.data_hora_agendado',
              'Consultas.data_hora_atendimento',
              'Consultas.data_hora_pre_atendimento',
              'Especialidade.descricao',
              'Profissional.nome',
              'Beneficiario.id',
              'Dependente.id',
              'Unidade.nome',
              'paciente'
          ],
          'order' => ['paciente']
        ];

        $this->set('consultas', $this->paginate($this->Consultas));
        // $this->set(compact(''));
    }

    public function abonar($id = null) {
      if ($this->request->is(['patch', 'post', 'put'])) {
          if(isset($this->request->data['consultas'])) {
            foreach ($this->request->data['consultas'] as $key => $value) {
              if(!$this->Consultas->updateAll(['st_consulta' => 'FB', 'inf_abono_falta' => $this->request->data['justificativa']], ['id' => $value])) {
                  $this->Flash->error($this::MSG_ERRO);
              }
            }
          }

          $this->Flash->success('Dados gravados');
          return $this->redirect(['controller' => 'consultas', 'action' => 'faltas', 'inicio' => $this->request->query('inicio'), 'fim' => $this->request->query('fim'), 'h_inicio' => $this->request->query('h_inicio'), 'h_fim' => $this->request->query('h_fim')]);
      }

      $options = [
          'Consultas.st_consulta' => 'FA'
      ];

      if($id) {
        $options['Consultas.id'] = $id;
      }

      if(!$id && $this->request->query('inicio') && !$this->request->query('fim')) {
        $options['Consultas.data_hora_agendado LIKE'] = "{$this->Consultas->formatDate($this->request->query('inicio'))}%";
      } else if(!$id && $this->request->query('all') && $this->request->query('inicio') && $this->request->query('fim')) {
        $dInicio = trim($this->Consultas->formatDate($this->request->query('inicio')));
        $dFim = trim($this->Consultas->formatDate($this->request->query('fim')));
        $hInicio = $this->request->query('h_inicio');
        $hFim = $this->request->query('h_fim');

        if($dInicio && $dFim) {
          $options[] = "Consultas.data_hora_agendado BETWEEN '{$dInicio}' AND '{$dFim}'";
        }

        if($hInicio && $hFim) {
          $options[] = "TIME(Consultas.data_hora_agendado) BETWEEN '{$hInicio}' AND '{$hFim}'";
        }
      }

      $consultas = $this->Consultas->find('all', [
        'conditions' => $options,
        'fields' => [
            'Consultas.id',
            'Consultas.data_hora_agendado',
            'Profissional.id',
            'Profissional.nome',
            'Especialidade.id',
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
            'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
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
                'table' => 'profissionais',
                'alias' => 'Profissional',
                'type' => 'INNER',
                'conditions' => 'Profissional.id = Consultas.profissionais_id',
            ],
            [
                'table' => 'apoio_especialidades',
                'alias' => 'Especialidade',
                'type' => 'INNER',
                'conditions' => 'Especialidade.id = Consultas.especialidades_id',
            ],
            [
                'table' => 'empresas',
                'alias' => 'Empresa',
                'type' => 'INNER',
                'conditions' => 'Empresa.id = Consultas.empresas_id',
            ],
        ],
        'sortWhitelist'=> [
            'Consultas.id',
            'Consultas.data_hora_agendado',
            'Consultas.data_hora_atendimento',
            'Consultas.data_hora_pre_atendimento',
            'Especialidade.descricao',
            'Profissional.nome',
            'Beneficiario.id',
            'Dependente.id',
            'Unidade.nome',
            'paciente'
        ],
        'order' => ['paciente']
      ]);

      if($id) {
        $this->set('consulta', $consultas->first());
      } else {
        $this->set('consultas', $consultas->toArray());
      }
      $this->viewBuilder()->layout('ajax');
    }

    public function autofaltas() {
      if($this->Consultas->updateAll(['st_consulta' => 'FA'], ['st_consulta' => 'AG', "data_hora_pre_atendimento IS NULL", "data_hora_agendado < '".date('Y-m-d')."'"])) {
        echo 'Registro de faltas atualizado';
      }
      exit;
    }

}
