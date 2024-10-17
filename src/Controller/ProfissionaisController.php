<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ProfissionaisController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'seconci');
    }

    public function index() {
        $option = null;
        if ($this->request->query('cpf')) {
            $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
            $option[] = "Profissionais.cpf IN('{$cpf}','{$this->Profissionais->mask($cpf,'###.###.###-##')}')";
        }
        if ($this->request->query('nome')) {
            $option['Profissionais.nome LIKE'] = "%{$this->request->query('nome')}%";
        }
        if ($this->request->query('especialidade')) {
          $option['Especialidade.descricao LIKE'] = "%{$this->request->query('especialidade')}%";
        }

        $this->paginate = [
            'conditions' => $option,
            'fields' => [
                'Profissionais.id',
                'Profissionais.nome',
                'Profissionais.cpf',
                'Profissionais.situacao',
                'especialidades' => 'GROUP_CONCAT(Especialidade.descricao)',
            ],
            'join' => [
                [
                    'table' => 'profissionais_especialidades',
                    'alias' => 'pe',
                    'type' => 'LEFT',
                    'conditions' => 'Profissionais.id = pe.profissionais_id',
                ],
                [
                    'table' => 'apoio_especialidades',
                    'alias' => 'Especialidade',
                    'type' => 'LEFT',
                    'conditions' => 'Especialidade.id = pe.especialidades_id',
                ]
            ],
            'sortWhitelist'=> [
                'Profissionais.nome',
                'Profissionais.cpf',
                'Profissionais.situacao',
                'especialidades'
            ],
            'group' => [
              'Profissionais.id'
            ],
            'order' => [
              'Profissionais.nome' => 'asc'
            ],
        ];

        $this->set('profissionais', $this->paginate($this->Profissionais));
    }

    public function add($id = null) {
        $profissional = $this->Profissionais->newEntity();
        if ($this->request->is('post')) {
            $profissional = $this->Profissionais->patchEntity($profissional, $this->request->data);
            if ($this->Profissionais->save($profissional)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $profissional)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add ProfissionaisEspecialidades
                 */
                $this->loadModel('ProfissionaisEspecialidades');
                if(is_array($this->request->data['especialidades'])) {
                    foreach ($this->request->data['especialidades'] as $key => $value) {
                        $especialidades = $this->ProfissionaisEspecialidades->patchEntity(
                          $this->ProfissionaisEspecialidades->newEntity(), [
                              'profissionais_id' => $profissional['id'],
                              'especialidades_id' => $value
                          ]);
                        $this->ProfissionaisEspecialidades->save($especialidades);
                    }
                }
                /**
                 * End ProfissionaisEspecialidades
                 */

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                return $this->redirect(['action' => 'edit', $profissional['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',['keyField' => 'id', 'valueField' => 'descricao',])->toArray();

        $this->set(compact('profissional', 'especialidades'));
    }


    public function edit($id = null) {
        $this->loadModel('Profissionais');
        $profissional = $this->Profissionais->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $profissional = $this->Profissionais->patchEntity($profissional, $this->request->data);
            if ($this->Profissionais->save($profissional)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $profissional)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add ProfissionaisEspecialidades
                 */
                $this->loadModel('ProfissionaisEspecialidades');
                $this->ProfissionaisEspecialidades->deleteAll(array("profissionais_id = {$profissional['id']}"));
                if(is_array($this->request->data['especialidades'])) {
                    foreach ($this->request->data['especialidades'] as $key => $value) {
                        $especialidades = $this->ProfissionaisEspecialidades->patchEntity(
                          $this->ProfissionaisEspecialidades->newEntity(), [
                              'profissionais_id' => $profissional['id'],
                              'especialidades_id' => $value
                          ]);
                        $this->ProfissionaisEspecialidades->save($especialidades);
                    }
                }
                /**
                 * End ProfissionaisEspecialidades
                 */

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $id]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

        $this->loadModel('ProfissionaisEspecialidades');
        $selecionados = $this->ProfissionaisEspecialidades->find('list',['keyField' => 'especialidades_id', 'valueField' => 'especialidades_id', 'conditions' => ['profissionais_id' => $profissional['id']]])->toArray();

        $this->set(compact('profissional', 'especialidades', 'selecionados'));
    }

    public function agenda($id = null, $agenda = null) {
        $this->loadModel('Profissionais');
        $profissional = $this->Profissionais->get($id);

        $this->set(compact('profissional'));
    }

    public function programada($id = null) {
      $this->loadModel('ProfissionaisAgendas');
      $this->loadModel('ProfissionaisDisponiveis');
      $this->loadModel('ProfissionaisHorariosDisponiveis');

      $profissional = $this->Profissionais->get($id);
      if ($this->request->is(['patch', 'post', 'put'])) {
          if(!isset($this->request->data['dias'])) {
            $this->Flash->error($this::MSG_ERRO);
            return $this->redirect(['action' => 'agenda', $id]);
          }

          $agenda = $this->ProfissionaisAgendas->newEntity();
          $agenda = $this->ProfissionaisAgendas->patchEntity($agenda, $this->request->data);
          if ($this->ProfissionaisAgendas->save($agenda)) {
              $this->loadComponent('Log');
              if(!$this->Log->save('agenda', $agenda)) {
                 $this->Flash->error($this::MSG_ERRO_LOG);
              }

              $date = new \DateTime($agenda['data_inicio']->format('Y-m-d'));
              $date->modify('-1 day');
              $this->ProfissionaisAgendas->updateAll(['data_fim' => $date->format('Y-m-d')], [
                "profissionais_id" => $profissional['id'],
                "id != {$agenda['id']}",
                "data_fim IS NULL",
                "tipo" => "N"
              ]);

              /**
               * Add ProfissionaisDisponiveis
               */
              $this->ProfissionaisDisponiveis->deleteAll(array("agendas_id = {$agenda['id']}", "profissionais_id = {$profissional['id']}"));
              if(is_array($this->request->data['dias'])) {
                  foreach ($this->request->data['dias'] as $dia) {
                      $disponivel = $this->ProfissionaisDisponiveis->patchEntity(
                        $this->ProfissionaisDisponiveis->newEntity(), [
                            'especialidades_id' => $this->request->data['especialidades_id'],
                            'profissionais_id' => $profissional['id'],
                            'agendas_id' => $agenda['id'],
                            'dia_semana' => $dia,
                            'bloqueado' => 'D'
                        ]);

                      if($this->ProfissionaisDisponiveis->save($disponivel)) {
                          $horarios = $this->Profissionais->getHorarios();
                          if(isset($horarios[$this->request->data['especialidades_id']][$this->request->data['turno']])) {
                              foreach ($horarios[$this->request->data['especialidades_id']][$this->request->data['turno']] as $hora) {
                                  $startTime = new \DateTime($this->request->data['time_inicio']);
                                  $endTime = new \DateTime($this->request->data['time_fim']);
                                  $currentTime = new \DateTime($hora);

                                  if ($currentTime >= $startTime && $currentTime <= $endTime) {
                                      $horario = $this->ProfissionaisHorariosDisponiveis->patchEntity(
                                        $this->ProfissionaisHorariosDisponiveis->newEntity(), [
                                            'unidades_id' => $this->request->data['unidades_id'],
                                            'disponiveis_id' => $disponivel['id'],
                                            'turno' => $this->request->data['turno'],
                                            'bloqueado' => 'D',
                                            'horario' => $hora
                                        ]);

                                      if(!$this->ProfissionaisHorariosDisponiveis->save($horario)) {
                                          $this->Flash->error("Falha ao registrar a hora {$hora}");
                                      }
                                  }
                              }
                          }
                      } else {
                          $this->Flash->error("Falha ao registrar o dia {$dia}");
                      }
                  }
              }
              /**
               * End ProfissionaisDisponiveis
               */

              $this->Flash->success($this::MSG_SUCESSO_ADD);
              return $this->redirect(['action' => 'agenda', $id]);
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }
      }

      $this->loadModel('ApoioEspecialidades');
      $especialidades = $this->ApoioEspecialidades->find('list',[
        'keyField' => 'id',
        'valueField' => 'descricao',
        'fields' => [
            'ApoioEspecialidades.id',
            'ApoioEspecialidades.descricao',
        ],
        'conditions' => [
            'Especialidade.profissionais_id' => $profissional['id']
        ],
        'join' => [
            [
                'table' => 'profissionais_especialidades',
                'alias' => 'Especialidade',
                'type' => 'INNER',
                'conditions' => 'ApoioEspecialidades.id = Especialidade.especialidades_id',
            ]
        ]
      ])->toArray();

      $this->paginate = [
        'fields' => [
            'ProfissionaisAgendas.id',
            'ProfissionaisAgendas.tipo',
            'ProfissionaisAgendas.data_inicio',
            'ProfissionaisAgendas.data_fim',
            'Especialidade.descricao',
            'dias' => 'GROUP_CONCAT(Disponibilidade.dia_semana)'
        ],
        'conditions' => [
            'ProfissionaisAgendas.profissionais_id' => $profissional['id'],
            'ProfissionaisAgendas.tipo' => 'N',
        ],
        'join' => [
            [
                'table' => 'profissionais_disponiveis',
                'alias' => 'Disponibilidade',
                'type' => 'INNER',
                'conditions' => 'ProfissionaisAgendas.id = Disponibilidade.agendas_id',
            ],
            [
                'table' => 'apoio_especialidades',
                'alias' => 'Especialidade',
                'type' => 'INNER',
                'conditions' => 'Especialidade.id = Disponibilidade.especialidades_id',
            ],
        ],
        'group' => [
          'ProfissionaisAgendas.id'
        ],
        'order' => [
          'ProfissionaisAgendas.id' => 'DESC',
        ],
        'limit' => '4'
      ];

      $this->loadModel('ApoioUnidades');
      $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome'])->toArray();

      $this->set('diponiveis', $this->paginate($this->ProfissionaisAgendas));
      $this->set(compact('profissional', 'especialidades', 'unidades'));
      $this->viewBuilder()->layout('ajax');
    }

    public function extra($id = null) {
      $this->loadModel('ProfissionaisAgendas');
      $this->loadModel('ProfissionaisDisponiveis');
      $this->loadModel('ProfissionaisHorariosDisponiveis');

      $profissional = $this->Profissionais->get($id);
      if ($this->request->is(['patch', 'post', 'put'])) {
          $agenda = $this->ProfissionaisAgendas->newEntity();
          $agenda = $this->ProfissionaisAgendas->patchEntity($agenda, $this->request->data);
          if ($this->ProfissionaisAgendas->save($agenda)) {
              $this->loadComponent('Log');
              if(!$this->Log->save('agenda', $agenda)) {
                 $this->Flash->error($this::MSG_ERRO_LOG);
              }

              /**
               * Add ProfissionaisDisponiveis
               */
              $this->ProfissionaisDisponiveis->deleteAll(array("agendas_id = {$agenda['id']}", "profissionais_id = {$profissional['id']}"));
              if(is_array($this->request->data['dias'])) {
                  foreach ($this->request->data['dias'] as $key => $value) {
                      $disponivel = $this->ProfissionaisDisponiveis->patchEntity(
                        $this->ProfissionaisDisponiveis->newEntity(), [
                            'especialidades_id' => $this->request->data['especialidades_id'],
                            'profissionais_id' => $profissional['id'],
                            'agendas_id' => $agenda['id'],
                            'dia_semana' => $value,
                            'bloqueado' => 'D'
                        ]);

                        if($this->ProfissionaisDisponiveis->save($disponivel)) {
                            $horarios = $this->Profissionais->getHorarios();
                            if(isset($horarios[$this->request->data['especialidades_id']][$this->request->data['turno']])) {
                                foreach ($horarios[$this->request->data['especialidades_id']][$this->request->data['turno']] as $hora) {
                                    $startTime = new \DateTime($this->request->data['time_inicio']);
                                    $endTime = new \DateTime($this->request->data['time_fim']);
                                    $currentTime = new \DateTime($hora);

                                    if ($currentTime >= $startTime && $currentTime <= $endTime) {
                                        $horario = $this->ProfissionaisHorariosDisponiveis->patchEntity(
                                          $this->ProfissionaisHorariosDisponiveis->newEntity(), [
                                              'unidades_id' => $this->request->data['unidades_id'],
                                              'disponiveis_id' => $disponivel['id'],
                                              'turno' => $this->request->data['turno'],
                                              'bloqueado' => 'D',
                                              'horario' => $hora
                                          ]);

                                        if(!$this->ProfissionaisHorariosDisponiveis->save($horario)) {
                                            $this->Flash->error("Falha ao registrar a hora {$hora}");
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->Flash->error("Falha ao registrar o dia {$dia}");
                        }
                  }
              }
              /**
               * End ProfissionaisDisponiveis
               */

              $this->Flash->success($this::MSG_SUCESSO_ADD);
              return $this->redirect(['action' => 'agenda', $id]);
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }
      }

      $this->loadModel('ApoioEspecialidades');
      $especialidades = $this->ApoioEspecialidades->find('list',[
        'keyField' => 'id',
        'valueField' => 'descricao',
        'fields' => [
            'ApoioEspecialidades.id',
            'ApoioEspecialidades.descricao',
        ],
        'conditions' => [
            'Especialidade.profissionais_id' => $profissional['id']
        ],
        'join' => [
            [
                'table' => 'profissionais_especialidades',
                'alias' => 'Especialidade',
                'type' => 'INNER',
                'conditions' => 'ApoioEspecialidades.id = Especialidade.especialidades_id',
            ]
        ]
      ])->toArray();

      $this->paginate = [
        'fields' => [
            'ProfissionaisAgendas.id',
            'ProfissionaisAgendas.tipo',
            'ProfissionaisAgendas.data_inicio',
            'ProfissionaisAgendas.data_fim',
            'Especialidade.descricao',
            'dias' => 'GROUP_CONCAT(Disponibilidade.dia_semana)'
        ],
        'conditions' => [
            'ProfissionaisAgendas.profissionais_id' => $profissional['id'],
            'ProfissionaisAgendas.tipo' => 'E',
        ],
        'join' => [
            [
                'table' => 'profissionais_disponiveis',
                'alias' => 'Disponibilidade',
                'type' => 'INNER',
                'conditions' => 'ProfissionaisAgendas.id = Disponibilidade.agendas_id',
            ],
            [
                'table' => 'apoio_especialidades',
                'alias' => 'Especialidade',
                'type' => 'INNER',
                'conditions' => 'Especialidade.id = Disponibilidade.especialidades_id',
            ],
        ],
        'group' => [
          'ProfissionaisAgendas.id'
        ],
        'order' => [
          'ProfissionaisAgendas.id' => 'DESC',
        ],
        'limit' => '4'
      ];

      $this->loadModel('ApoioUnidades');
      $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome'])->toArray();

      $this->set('diponiveis', $this->paginate($this->ProfissionaisAgendas));
      $this->set(compact('profissional', 'especialidades', 'unidades'));
      $this->viewBuilder()->layout('ajax');
    }

    public function indisponibilidade($id = null) {
        $this->loadModel('ProfissionaisIndisponibilidades');
        $profissional = $this->Profissionais->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $indisponibilidade = $this->ProfissionaisIndisponibilidades->patchEntity($this->ProfissionaisIndisponibilidades->newEntity(), $this->request->data);
            if ($this->ProfissionaisIndisponibilidades->save($indisponibilidade)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('indisponibilidade', $indisponibilidade)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_ADD);
                return $this->redirect(['action' => 'agenda', $id]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioEspecialidades');
        $especialidades = $this->ApoioEspecialidades->find('list',[
          'keyField' => 'id',
          'valueField' => 'descricao',
          'fields' => [
              'ApoioEspecialidades.id',
              'ApoioEspecialidades.descricao',
          ],
          'conditions' => [
              'Especialidade.profissionais_id' => $profissional['id']
          ],
          'join' => [
              [
                  'table' => 'profissionais_especialidades',
                  'alias' => 'Especialidade',
                  'type' => 'INNER',
                  'conditions' => 'ApoioEspecialidades.id = Especialidade.especialidades_id',
              ]
          ]
        ])->toArray();

        $this->paginate = [
            'conditions' => [
                'ProfissionaisIndisponibilidades.profissionais_id' => $profissional['id']
            ],
            'fields' => [
                'ProfissionaisIndisponibilidades.id',
                'ProfissionaisIndisponibilidades.data_hora_registro',
                'ProfissionaisIndisponibilidades.data_hora_inicio',
                'ProfissionaisIndisponibilidades.data_hora_fim',
                'Especialidade.descricao',
                'Especialidade.id',
                'Colaborador.nome',
                'Colaborador.id'
            ],
            'join' => [
                [
                    'table' => 'apoio_especialidades',
                    'alias' => 'Especialidade',
                    'type' => 'INNER',
                    'conditions' => 'Especialidade.id = ProfissionaisIndisponibilidades.especialidades_id',
                ],
                [
                    'table' => 'seguranca_colaboradores',
                    'alias' => 'Colaborador',
                    'type' => 'INNER',
                    'conditions' => 'Colaborador.id = ProfissionaisIndisponibilidades.colaboradores_id',
                ],
            ],
            'order' => [
                'ProfissionaisIndisponibilidades.data_hora_inicio' => 'DESC'
            ],
            'limit' => '4'
        ];

        $this->set('indisponibilidades', $this->paginate($this->ProfissionaisIndisponibilidades));
        $this->set(compact('profissional', 'especialidades'));
        $this->viewBuilder()->layout('ajax');
    }

    public function delete($id = null, $controller = null) {
         $this->request->allowMethod(['post', 'delete']);

         $this->loadModel($controller);
         $agenda = $this->$controller->find('all', ['conditions' => ['id' => $id]])->first();
         if ($this->$controller->delete($agenda)) {
             $this->Flash->success($this::MSG_SUCESSO_DEL);
         }
         return $this->redirect(['action' => 'agenda', $agenda['profissionais_id']]);
    }

    public function agendas($id = null) {
      $profissional = $this->Profissionais->get($id);

      $this->loadModel('ProfissionaisAgendas');
      $disponiveis = $this->ProfissionaisAgendas->find('all',[
          'fields' => [
              'ProfissionaisAgendas.id',
              'ProfissionaisAgendas.tipo',
              'ProfissionaisAgendas.data_inicio',
              'ProfissionaisAgendas.data_fim',
              'Especialidade.descricao',
              'dias' => 'GROUP_CONCAT(Disponibilidade.dia_semana)',
              'hora_inicio' => '(SELECT MIN(horario) FROM profissionais_horarios_disponiveis WHERE disponiveis_id = Disponibilidade.id)',
              'hora_fim' => '(SELECT MAX(horario) FROM profissionais_horarios_disponiveis WHERE disponiveis_id = Disponibilidade.id)',
          ],
          'conditions' => [
              'ProfissionaisAgendas.data_inicio >= "'.date('Y', strtotime('-1 year')).'%"',
              'ProfissionaisAgendas.profissionais_id' => $profissional['id'],
              'ProfissionaisAgendas.data_inicio IS NOT NULL',
              'OR' => [
                'ProfissionaisAgendas.tipo = "N"',
                'ProfissionaisAgendas.tipo = "E" AND ProfissionaisAgendas.data_fim IS NOT NULL',
              ]
          ],
          'join' => [
              [
                  'table' => 'profissionais_disponiveis',
                  'alias' => 'Disponibilidade',
                  'type' => 'INNER',
                  'conditions' => 'ProfissionaisAgendas.id = Disponibilidade.agendas_id',
              ],
              [
                  'table' => 'apoio_especialidades',
                  'alias' => 'Especialidade',
                  'type' => 'INNER',
                  'conditions' => 'Especialidade.id = Disponibilidade.especialidades_id',
              ],
          ],
          'group' => [
            'ProfissionaisAgendas.id'
          ],
          'order' => [
            'ProfissionaisAgendas.tipo' => 'DESC',
            'ProfissionaisAgendas.data_inicio' => 'DESC',
          ],
          //'limit' => '20'
      ])->toArray();

      $eventos = [];
      $month = ['Mon' => 'SEG','Tue' => 'TER','Wed' => 'QUA','Thu' => 'QUI','Fri' => 'SEX'];

      foreach ($disponiveis as $key => $value) {
        $dias = explode(',', $value['dias']);
        $data_inicio = $value['data_inicio'];
        $data_fim = $value['data_fim'] ? $value['data_fim'] : new \DateTime('now +60 day');

        if(isset($disponiveis[$key+1])) {
          if($disponiveis[$key+1]['tipo'] == 'N') {
            $data_fim = $disponiveis[$key+1]['data_inicio'];
          }
        }

        while (strtotime($data_inicio->format('Y-m-d')) <= strtotime($data_fim->format('Y-m-d'))) {
            if(in_array(date('D', strtotime($data_inicio->format('Y-m-d'))), array_keys($month))) {
                if(in_array($month[date('D', strtotime($data_inicio->format('Y-m-d')))], $dias)) {
                    if(!$data_inicio || !$value['hora_inicio']) {
                        continue;
                    }

                    $value['hora_inicio'] = substr($value['hora_inicio'], 0, 5);
                    $value['hora_fim'] = substr($value['hora_fim'], 0, 5);

                    if($value['tipo'] == 'N') {
                        $eventos['1'.strtotime($data_inicio->format('Y-m-d'))] = [
                          'color' => '#008B00',
                          'start' => $data_inicio->format('Y-m-d'),
                          'end' => $data_inicio->format('Y-m-d'),
                          'title' => "PROGRAMADA\n{$value['hora_inicio']} às {$value['hora_fim']}\n{$value['Especialidade']['descricao']}",
                        ];
                    } else if($value['tipo'] == 'E') {
                        $eventos['2'.strtotime("{$data_inicio->format('Y-m-d')} {$value['hora_inicio']}")] = [
                          'color' => '#00008B',
                          'start' => $data_inicio->format('Y-m-d'),
                          'end' => $data_inicio->format('Y-m-d'),
                          'title' => "AGENDA EXTRA\n{$value['hora_inicio']} às {$value['hora_fim']}\n{$value['Especialidade']['descricao']}",
                        ];
                    }
                }
            }
            $data_inicio = $data_inicio->modify('+1 day');
        }
      }

      $this->loadModel('ProfissionaisIndisponibilidades');
      $indisponibilidades = $this->ProfissionaisIndisponibilidades->find('all',[
        'conditions' => [
          'ProfissionaisIndisponibilidades.data_hora_inicio >= "'.date('Y', strtotime('-1 year')).'%"',
          'ProfissionaisIndisponibilidades.profissionais_id' => $profissional['id'],
          'ProfissionaisIndisponibilidades.data_hora_inicio IS NOT NULL',
          'ProfissionaisIndisponibilidades.data_hora_fim IS NOT NULL',
        ],
        'fields' => [
            'ProfissionaisIndisponibilidades.id',
            'ProfissionaisIndisponibilidades.data_hora_registro',
            'ProfissionaisIndisponibilidades.data_hora_inicio',
            'ProfissionaisIndisponibilidades.data_hora_fim',
            'Especialidade.descricao',
            'Especialidade.id',
        ],
        'join' => [
            [
                'table' => 'apoio_especialidades',
                'alias' => 'Especialidade',
                'type' => 'INNER',
                'conditions' => 'Especialidade.id = ProfissionaisIndisponibilidades.especialidades_id',
            ],
        ],
        'order' => [
          'data_hora_inicio' => 'ASC'
        ],
      ])->toArray();

      foreach ($indisponibilidades as $key => $value) {
        $data_inicio = $value['data_hora_inicio'];
        $data_fim = $value['data_hora_fim'];

        while (strtotime($data_inicio->format('Y-m-d H:i:s')) <= strtotime($data_fim->format('Y-m-d H:i:s'))) {
          if(in_array(date('D', strtotime($data_inicio->format('Y-m-d'))), array_keys($month))) {
            $eventos['3'.strtotime($data_inicio->format('Y-m-d H:i:s'))] = [
              'color' => '#8B0000',
              'start' => $data_inicio->format('Y-m-d H:i:s'),
              'end' => $data_inicio->format('Y-m-d H:i:s'),
              'title' => "INDISPONIBILIDADE\n{$value['data_hora_inicio']->format('H:i')} às {$value['data_hora_fim']->format('H:i')}\n{$value['Especialidade']['descricao']}",
            ];
          }
          $data_inicio = $data_inicio->modify('+1 day');
        }
      }

      ksort($eventos);
      echo json_encode(array_values($eventos));
      exit;
    }

}
