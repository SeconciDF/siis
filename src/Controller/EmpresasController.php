<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'associado');
    }

    public function index() {
        $option = null;
        if ($this->request->query('nome')) {
            $option['Empresas.nome LIKE'] = "%{$this->request->query('nome')}%";
        }
        if ($this->request->query('cnpj')) {
            $cnpj = preg_replace('/[^0-9]/', '', $this->request->query('cnpj'));
            $option[] = "Empresas.identificacao IN('{$cnpj}','{$this->Empresas->mask($cnpj,'########/####-##')}')";
        }

        $this->paginate = [
            'fields' => [
                'Empresas.id',
                'Empresas.nome',
                'Empresas.tipo_identificacao',
                'Empresas.identificacao',
                'Empresas.situacao',
                'assistencial' => 'IF(Empresas.situacao="I","I",Empresas.situacao_seconci)',
            ],
            'conditions' => $option,
            'sortWhitelist'=> [
                'Empresas.nome',
                'Empresas.tipo_identificacao',
                'Empresas.identificacao',
                'Empresas.situacao',
                'assistencial',
            ],
            'order' => ['Empresas.nome' => 'asc'],
        ];

        $this->set('empresas', $this->paginate($this->Empresas));
    }

    public function add($id = null) {
        $empresa = $this->Empresas->newEntity();
        if ($this->request->is('post')) {
            $empresa = $this->Empresas->patchEntity($empresa, $this->request->data);
            if ($this->Empresas->save($empresa)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $empresa)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add EmpresasContatos
                 */
                $this->loadModel('EmpresasContatos');
                if(isset($this->request->data['contatos'])) {
                    foreach ($this->request->data['contatos'] as $key => $value) {
                        if($value == '') {
                            continue;
                        }

                        $contato = $this->EmpresasContatos->patchEntity(
                          $this->EmpresasContatos->newEntity(), [
                              'empresas_id' => $empresa['id'],
                              'tipo_contato' => $this->request->data['tipos'][$key],
                              'contato' => $value
                          ]);
                        $this->EmpresasContatos->save($contato);
                    }
                }
                /**
                 * End EmpresasContatos
                 */

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                if(isset($this->Empresas->success)) {
                  $this->Flash->success($this->Empresas->success);
                } else if(isset($this->Empresas->error)) {
                  $this->Flash->error($this->Empresas->error);
                }

                return $this->redirect(['action' => 'edit', $empresa['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('ApoioEstados');
        $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla',])->toArray();

        $this->set(compact('empresa', 'estados'));
    }

    public function edit($id = null) {
        $empresa = $this->Empresas->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $empresa = $this->Empresas->patchEntity($empresa, $this->request->data);
            if ($this->Empresas->save($empresa)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $empresa)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add EmpresasContatos
                 */
                $this->loadModel('EmpresasContatos');
                $this->EmpresasContatos->deleteAll(array("empresas_id = {$empresa['id']}"));
                if(isset($this->request->data['contatos'])) {
                    foreach ($this->request->data['contatos'] as $key => $value) {
                        if($value == '') {
                            continue;
                        }

                        $contato = $this->EmpresasContatos->patchEntity(
                          $this->EmpresasContatos->newEntity(), [
                              'empresas_id' => $empresa['id'],
                              'tipo_contato' => $this->request->data['tipos'][$key],
                              'contato' => $value
                          ]);
                        $this->EmpresasContatos->save($contato);
                    }
                }
                /**
                 * End EmpresasContatos
                 */

               $this->Flash->success($this::MSG_SUCESSO_EDT);
               if(isset($this->Empresas->success)) {
                 $this->Flash->success($this->Empresas->success);
               } else if(isset($this->Empresas->error)) {
                 $this->Flash->error($this->Empresas->error);
               }

               return $this->redirect(['action' => 'edit', $id]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('EmpresasContatos');
        $contatos = $this->EmpresasContatos->find('all',['conditions' => ['empresas_id' => $empresa['id']]])->toArray();

        $this->loadModel('ApoioEstados');
        $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla'])->toArray();

        $this->set(compact('empresa', 'estados', 'contatos'));
    }

    public function consultaEmpresa($identificacao = null) {
        $identificacao = preg_replace('/[^0-9]/', '', $identificacao);
        $empresa = $this->Empresas->find('all', ['fields' => ['id', 'identificacao', 'nome'], 'conditions' => ["identificacao IN('{$identificacao}','{$this->Empresas->mask($identificacao,'###.###.###-##')}','{$this->Empresas->mask($identificacao,'##.###.###/####-##')}','{$this->Empresas->mask($identificacao,'########/####-##')}')"]])->first();
        echo json_encode($empresa);
        exit;
    }
}
