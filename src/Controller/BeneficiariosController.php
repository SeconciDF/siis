<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class BeneficiariosController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'associado');
    }

    public function index() {
        $option = null;
        if ($this->request->query('id')) {
            $option['Beneficiarios.id'] = $this->request->query('id');
            $id = explode('.', $this->request->query('id'));
            if(sizeof($id) >= 2) {
              $this->loadModel('BeneficiariosDependentes');
              $dependente = $this->BeneficiariosDependentes->find('all', [ 'conditions' => ['beneficiarios_id' => $id[0], 'digito_dependente' => $id[1]]])->first();
              if($dependente['id']) {
                return $this->redirect(['action' => 'dependente', $dependente['beneficiarios_id'], $dependente['id']]);
              }
            }
        }
        if ($this->request->query('cpf')) {
            $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
            $option[] = "Beneficiarios.cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
        }
        if ($this->request->query('nome')) {
            $option['Beneficiarios.nome LIKE'] = "%{$this->request->query('nome')}%";
        }

        $this->paginate = [
            'fields' => [
                'Beneficiarios.id',
                'Beneficiarios.nome',
                'Beneficiarios.cpf',
                'Beneficiarios.situacao',
            ],
            'conditions' => $option,
            'sortWhitelist'=> [
                'Beneficiarios.id',
                'Beneficiarios.nome',
                'Beneficiarios.cpf',
                'Beneficiarios.situacao',
            ],
            'order' => ['Beneficiarios.nome' => 'asc'],
        ];

        $this->set('beneficiarios', $this->paginate($this->Beneficiarios));
    }

    public function add($id = null) {
        $beneficiario = $this->Beneficiarios->newEntity();
        if ($this->request->is('post')) {
            $beneficiario = $this->Beneficiarios->patchEntity($beneficiario, $this->request->data);
            if ($this->Beneficiarios->save($beneficiario)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $beneficiario)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add BeneficiariosFuncoes
                 */
                $this->loadModel('BeneficiariosFuncoes');
                if(is_array($this->request->data['funcoes'])) {
                    foreach ($this->request->data['funcoes'] as $key => $value) {
                        $funcoes = $this->BeneficiariosFuncoes->patchEntity(
                          $this->BeneficiariosFuncoes->newEntity(), [
                              'beneficiarios_id' => $beneficiario['id'],
                              'funcoes_id' => $value
                          ]);
                        $this->BeneficiariosFuncoes->save($funcoes);
                    }
                }
                /**
                 * End BeneficiariosFuncoes
                 */

                /**
                 * Add BeneficiariosFuncoes
                 */
                $this->loadModel('BeneficiariosEmpresas');
                if(is_array($this->request->data['empresas'])) {
                    foreach ($this->request->data['empresas'] as $key => $value) {
                      if(!$this->BeneficiariosEmpresas->updateAll(['situacao' => 'A'], ['empresas_id' => $value, 'beneficiarios_id' => $beneficiario['id']])) {
                        $emp = $this->BeneficiariosEmpresas->patchEntity(
                          $this->BeneficiariosEmpresas->newEntity(), [
                              'beneficiarios_id' => $beneficiario['id'],
                              'empresas_id' => $value,
                              'situacao' => 'A'
                          ]);
                        $this->BeneficiariosEmpresas->save($emp);
                      }
                    }
                }
                /**
                 * End BeneficiariosFuncoes
                 */

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                if(isset($this->Beneficiarios->success)) {
                  $this->Flash->success($this->Beneficiarios->success);
                }
                if(isset($this->Beneficiarios->error)) {
                  $this->Flash->error($this->Beneficiarios->error);
                }
                return $this->redirect(['action' => 'edit', $beneficiario['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioFuncoes');
        $funcoes = $this->ApoioFuncoes->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'order' => ['descricao' => 'ASC']])->toArray();

        $this->loadModel('ApoioEstados');
        $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla'])->toArray();

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->set(compact('beneficiario', 'estados', 'funcoes', 'empresas'));
    }

    public function edit($id = null) {
        $beneficiario = $this->Beneficiarios->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $beneficiario = $this->Beneficiarios->patchEntity($beneficiario, $this->request->data);
            if ($this->Beneficiarios->save($beneficiario)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $beneficiario)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add BeneficiariosFuncoes
                 */
                $this->loadModel('BeneficiariosFuncoes');
                $this->BeneficiariosFuncoes->deleteAll(array("beneficiarios_id = {$beneficiario['id']}"));
                if(is_array($this->request->data['funcoes'])) {
                    foreach ($this->request->data['funcoes'] as $key => $value) {
                        $funcoes = $this->BeneficiariosFuncoes->patchEntity(
                          $this->BeneficiariosFuncoes->newEntity(), [
                              'beneficiarios_id' => $beneficiario['id'],
                              'funcoes_id' => $value
                          ]);
                        $this->BeneficiariosFuncoes->save($funcoes);
                    }
                }
                /**
                 * End BeneficiariosFuncoes
                 */

                /**
                 * Add BeneficiariosFuncoes
                 */
                $this->loadModel('BeneficiariosEmpresas');
                $this->BeneficiariosEmpresas->updateAll(['situacao' => 'I'], ['beneficiarios_id' => $beneficiario['id']]);
                if(is_array($this->request->data['empresas'])) {
                    foreach ($this->request->data['empresas'] as $key => $value) {
                      if(!$this->BeneficiariosEmpresas->updateAll(['situacao' => 'A'], ['empresas_id' => $value, 'beneficiarios_id' => $beneficiario['id']])) {
                        $emp = $this->BeneficiariosEmpresas->patchEntity(
                          $this->BeneficiariosEmpresas->newEntity(), [
                              'beneficiarios_id' => $beneficiario['id'],
                              'empresas_id' => $value,
                              'situacao' => 'A'
                          ]);
                        $this->BeneficiariosEmpresas->save($emp);
                      }
                    }
                }
                /**
                 * End BeneficiariosFuncoes
                 */

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                if(isset($this->Beneficiarios->success)) {
                  $this->Flash->success($this->Beneficiarios->success);
                }
                if(isset($this->Beneficiarios->error)) {
                  $this->Flash->error($this->Beneficiarios->error);
                }
                return $this->redirect(['action' => 'edit', $id]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioFuncoes');
        $funcoes = $this->ApoioFuncoes->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'order' => ['descricao' => 'ASC']])->toArray();

        $this->loadModel('ApoioEstados');
        $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla',])->toArray();

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('BeneficiariosFuncoes');
        $selecionados = $this->BeneficiariosFuncoes->find('list',['keyField' => 'funcoes_id', 'valueField' => 'funcoes_id', 'conditions' => ['beneficiarios_id' => $beneficiario['id']]])->toArray();

        $this->loadModel('BeneficiariosEmpresas');
        $selecionadas = $this->BeneficiariosEmpresas->find('list',['keyField' => 'empresas_id', 'valueField' => 'empresas_id', 'conditions' => ['beneficiarios_id' => $beneficiario['id'], 'situacao' => 'A', 'data_baixa IS NULL']])->toArray();

        $this->set(compact('beneficiario', 'estados', 'funcoes', 'empresas', 'selecionados', 'selecionadas'));
    }

    public function view($id = null) {
        $beneficiario = $this->Beneficiarios->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $beneficiario = $this->Beneficiarios->patchEntity($beneficiario, $this->request->data);
            if ($this->Beneficiarios->save($beneficiario)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $beneficiario)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add BeneficiariosFuncoes
                 */
                $this->loadModel('BeneficiariosFuncoes');
                $this->BeneficiariosFuncoes->deleteAll(array("beneficiarios_id = {$beneficiario['id']}"));
                if(is_array($this->request->data['funcoes'])) {
                    foreach ($this->request->data['funcoes'] as $key => $value) {
                        $funcoes = $this->BeneficiariosFuncoes->patchEntity(
                          $this->BeneficiariosFuncoes->newEntity(), [
                              'beneficiarios_id' => $beneficiario['id'],
                              'funcoes_id' => $value
                          ]);
                        $this->BeneficiariosFuncoes->save($funcoes);
                    }
                }
                /**
                 * End BeneficiariosFuncoes
                 */

                 /**
                  * Add BeneficiariosFuncoes
                  */
                 $this->loadModel('BeneficiariosEmpresas');
                 $this->BeneficiariosEmpresas->updateAll(['situacao' => 'I'], ['beneficiarios_id' => $beneficiario['id']]);
                 if(is_array($this->request->data['empresas'])) {
                     foreach ($this->request->data['empresas'] as $key => $value) {
                       if(!$this->BeneficiariosEmpresas->updateAll(['situacao' => 'A'], ['empresas_id' => $value, 'beneficiarios_id' => $beneficiario['id']])) {
                         $emp = $this->BeneficiariosEmpresas->patchEntity(
                           $this->BeneficiariosEmpresas->newEntity(), [
                               'beneficiarios_id' => $beneficiario['id'],
                               'empresas_id' => $value,
                               'situacao' => 'A'
                           ]);
                         $this->BeneficiariosEmpresas->save($emp);
                       }
                     }
                 }
                 /**
                  * End BeneficiariosFuncoes
                  */

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                if(isset($this->Beneficiarios->success)) {
                  $this->Flash->success($this->Beneficiarios->success);
                }
                if(isset($this->Beneficiarios->error)) {
                  $this->Flash->error($this->Beneficiarios->error);
                }
                return $this->redirect($this->request->session()->read('Auth.User.referer'));
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioFuncoes');
        $funcoes = $this->ApoioFuncoes->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'order' => ['descricao' => 'ASC']])->toArray();

        $this->loadModel('ApoioEstados');
        $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla',])->toArray();

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('BeneficiariosFuncoes');
        $selecionados = $this->BeneficiariosFuncoes->find('list',['keyField' => 'funcoes_id', 'valueField' => 'funcoes_id', 'conditions' => ['beneficiarios_id' => $beneficiario['id']]])->toArray();

        $this->loadModel('BeneficiariosEmpresas');
        $selecionadas = $this->BeneficiariosEmpresas->find('list',['keyField' => 'empresas_id', 'valueField' => 'empresas_id', 'conditions' => ['beneficiarios_id' => $beneficiario['id'], 'situacao' => 'A', 'data_baixa IS NULL']])->toArray();

        $this->set(compact('beneficiario', 'estados', 'funcoes', 'empresas', 'selecionados', 'selecionadas'));
        $this->viewBuilder()->layout('ajax');
    }

    public function viewDependente($id = null, $dependente = null) {
        $beneficiario = $this->Beneficiarios->get($id);

        $this->loadModel('BeneficiariosDependentes');
        $dependente = $this->BeneficiariosDependentes->find('all', ['conditions' => ['id' => $dependente]])->first();
        if(!$dependente) {
            $dependente = $this->BeneficiariosDependentes->newEntity();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $dependente = $this->BeneficiariosDependentes->patchEntity($dependente, $this->request->data);
            if ($this->BeneficiariosDependentes->save($dependente)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $dependente)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect($this->request->session()->read('Auth.User.referer'));
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('BeneficiariosTipoDependentes');
        $dependencias = $this->BeneficiariosTipoDependentes->find('list',['keyField' => 'id', 'valueField' => 'descricao',])->toArray();

        $this->set(compact('beneficiario', 'dependente', 'dependencias'));
        $this->viewBuilder()->layout('ajax');
    }

    public function dependentes($id = null) {
        $beneficiario = $this->Beneficiarios->get($id);

        $this->loadModel('BeneficiariosDependentes');
        $this->paginate = [
            'fields' => [
                'BeneficiariosDependentes.id',
                'BeneficiariosDependentes.nome',
                'BeneficiariosDependentes.cpf',
                'BeneficiariosDependentes.situacao',
            ],
            'conditions' => [
                'BeneficiariosDependentes.beneficiarios_id' => $beneficiario['id']
            ],
            'sortWhitelist'=> [
                'BeneficiariosDependentes.id',
                'BeneficiariosDependentes.nome',
                'BeneficiariosDependentes.cpf',
                'BeneficiariosDependentes.situacao',
            ],
            'order' => ['BeneficiariosDependentes.id' => 'asc'],
        ];

        $this->set('dependentes', $this->paginate($this->BeneficiariosDependentes));
        $this->set(compact('beneficiario'));
    }

    public function dependente($id = null, $dependente = null) {
        $beneficiario = $this->Beneficiarios->get($id);

        $this->loadModel('BeneficiariosDependentes');
        $dependente = $this->BeneficiariosDependentes->find('all', ['conditions' => ['id' => $dependente]])->first();
        if(!$dependente) {
            $dependente = $this->BeneficiariosDependentes->newEntity();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $dependente = $this->BeneficiariosDependentes->patchEntity($dependente, $this->request->data);
            if ($this->BeneficiariosDependentes->save($dependente)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $dependente)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'dependente', $beneficiario['id'], $dependente['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('BeneficiariosTipoDependentes');
        $dependencias = $this->BeneficiariosTipoDependentes->find('list',['keyField' => 'id', 'valueField' => 'descricao',])->toArray();

        $this->set(compact('beneficiario', 'dependente', 'dependencias'));
    }

    public function empresas($id = null) {
        $beneficiario = $this->Beneficiarios->get($id);

        $this->loadModel('BeneficiariosEmpresas');
        $this->paginate = [
            'conditions' => [
                'BeneficiariosEmpresas.beneficiarios_id' => $beneficiario['id']
            ],
            'fields' => [
                'BeneficiariosEmpresas.id',
                'BeneficiariosEmpresas.data_associacao',
                'BeneficiariosEmpresas.situacao',
                'Empresa.id',
                'Empresa.nome'
            ],
            'join' => [
                [
                    'table' => 'empresas',
                    'alias' => 'Empresa',
                    'type' => 'INNER',
                    'conditions' => 'Empresa.id = BeneficiariosEmpresas.empresas_id',
                ],
            ],
            'sortWhitelist'=> [
              'BeneficiariosEmpresas.id',
              'BeneficiariosEmpresas.data_associacao',
              'BeneficiariosEmpresas.situacao',
              'Empresa.id',
              'Empresa.nome'
            ],
            'order' => ['Empresa.nome' => 'asc'],
        ];

        $this->set('empresas', $this->paginate($this->BeneficiariosEmpresas));
        $this->set(compact('beneficiario'));
    }

    public function empresa($id = null, $empresa = null) {
        $beneficiario = $this->Beneficiarios->get($id);

        $this->loadModel('BeneficiariosEmpresas');
        $empresa = $this->BeneficiariosEmpresas->find('all', ['conditions' => ['id' => $empresa]])->first();
        if(!$empresa) {
            $empresa = $this->BeneficiariosEmpresas->newEntity();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $empresa = $this->BeneficiariosEmpresas->patchEntity($empresa, $this->request->data);
            if ($this->BeneficiariosEmpresas->save($empresa)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $empresa)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'empresa', $beneficiario['id'], $empresa['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome']])->toArray();

        $this->set(compact('beneficiario', 'empresa', 'empresas'));
    }

    public function consultas($id = null) {
        $beneficiario = $this->Beneficiarios->get($id);

        $this->loadModel('Consultas');
        $this->paginate = [
            'conditions' => [
              'Beneficiario.id' => $beneficiario['id']
            ],
            'fields' => [
                'Consultas.id',
                'Consultas.st_consulta',
                'Consultas.profissionais_id',
                'Consultas.data_hora_agendado',
                'Consultas.data_hora_atendimento',
                'Consultas.data_hora_pre_atendimento',
                'Consultas.data_hora_fecha_atendimento',
                'Consultas.data_hora_nao_consulta',
                'Especialidade.descricao',
                'Especialidade.id',
                'Profissional.nome',
                'Profissional.id',
                'Beneficiario.id',
                'Dependente.id',
                'Unidade.nome',
                'Unidade.id',
                'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)'
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
              'Unidade.nome',
              'paciente'
            ],
            'order' => ['Consultas.data_hora_agendado' => 'desc', 'paciente' => 'asc'],
        ];

        $this->set('consultas', $this->paginate($this->Consultas));
        $this->set(compact('beneficiario'));
    }

    public function consultaCpf($cpf = null) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $beneficiario = $this->Beneficiarios->find('all', ['fields' => ['id', 'cpf', 'nome'], 'conditions' => ["cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')"]])->first();
        echo json_encode($beneficiario);
        exit;
    }

}
