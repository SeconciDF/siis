<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmpresasTrabalhadoresController extends AppController {

  public function beforeFilter(Event $event) {
    $this->request->session()->write('Auth.User.MenuActive', 'empresa');
  }

  public function index() {
    $this->loadModel('Beneficiarios');
    $option = [
      'BeneficiariosEmpresas.empresas_id' => $this->request->session()->read('Auth.User.empresas_id'),
      'BeneficiariosEmpresas.situacao' => 'A'
    ];

    if ($this->request->query('cpf')) {
      $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
      $option[] = "Beneficiarios.cpf IN('{$cpf}','{$this->Beneficiarios->mask($cpf,'###.###.###-##')}')";
    }
    if ($this->request->query('nome')) {
      $option['Beneficiarios.nome LIKE'] = "%{$this->request->query('nome')}%";
    }

    $this->paginate = [
      'conditions' => $option,
      'fields' => [
        'Beneficiarios.id',
        'Beneficiarios.nome',
        'Beneficiarios.cpf',
        'Beneficiarios.situacao',
      ],
      'join' => [
        [
          'table' => 'beneficiarios_empresas',
          'alias' => 'BeneficiariosEmpresas',
          'type' => 'INNER',
          'conditions' => 'Beneficiarios.id = BeneficiariosEmpresas.beneficiarios_id',
        ],
      ],
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
    $this->loadModel('Beneficiarios');
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
        * Add BeneficiariosEmpresas
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
          * End BeneficiariosEmpresas
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

      $this->set(compact('beneficiario', 'estados', 'funcoes'));
    }

    public function edit($id = null) {
      $this->loadModel('Beneficiarios');
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

      $this->loadModel('BeneficiariosFuncoes');
      $selecionados = $this->BeneficiariosFuncoes->find('list',['keyField' => 'funcoes_id', 'valueField' => 'funcoes_id', 'conditions' => ['beneficiarios_id' => $beneficiario['id']]])->toArray();

      $this->set(compact('beneficiario', 'estados', 'funcoes', 'selecionados'));
    }

}
