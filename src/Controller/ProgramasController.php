<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ProgramasController extends AppController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->request->session()->write('Auth.User.MenuActive', 'sst');
    }

    public function index() {
      $option = null;
      if ($this->request->query('empresa')) {
          $option['Empresa.nome LIKE'] = "%{$this->request->query('empresa')}%";
      }

      $this->paginate = [
          'conditions' => $option,
          'fields' => [
              'Programas.id',
              'Programas.data_inicial',
              'Programas.data_final',
              'Programas.data_hora_registro',
              'Empresa.nome',
              'Programa.descricao',
          ],
          'join' => [
              [
                  'table' => 'apoio_programas',
                  'alias' => 'Programa',
                  'type' => 'INNER',
                  'conditions' => 'Programa.id = Programas.apoio_programas_id',
              ],
              [
                  'table' => 'empresas',
                  'alias' => 'Empresa',
                  'type' => 'INNER',
                  'conditions' => 'Empresa.id = Programas.empresas_id',
              ]
          ],
          'sortWhitelist'=> [
              'Empresa.nome',
              'Programa.descricao',
              'Programas.data_inicial',
              'Programas.data_final',
              'Programas.data_hora_registro',
          ],
          'order' => ['Programas.data_hora_registro' => 'DESC']
      ];

      $this->set('programas', $this->paginate($this->Programas));
    }

    public function add() {
        $programa = $this->Programas->newEntity();
        if ($this->request->is('post')) {
            $programa = $this->Programas->patchEntity($programa, $this->request->data);
            if ($this->Programas->save($programa)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $programa)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                return $this->redirect(['action' => 'edit', $programa['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'conditions' => ['situacao' => 'A'],  'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('ApoioProgramas');
        $programas = $this->ApoioProgramas->find('list',['keyField' => 'id', 'valueField' => 'descricao'])->toArray();

        $this->set(compact('programa', 'empresas', 'programas'));
    }

    public function edit($id = null) {
        $programa = $this->Programas->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programa = $this->Programas->patchEntity($programa, $this->request->data);
            if ($this->Programas->save($programa)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $programa)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $id]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('ApoioProgramas');
        $programas = $this->ApoioProgramas->find('list',['keyField' => 'id', 'valueField' => 'descricao',])->toArray();

        $this->loadModel('ProgramasResponsaveis');
        $responsaveis = $this->ProgramasResponsaveis->find('all',[
          'conditions' => [
            'programas_id' => $programa['id']
          ]
        ])->toArray();

        $this->set(compact('programa', 'empresas', 'programas', 'responsaveis'));
    }

    public function responsaveis($id = null) {
        $programa = $this->Programas->get($id);

        $this->loadModel('ProgramasResponsaveis');
        $responsaveis = $this->ProgramasResponsaveis->find('all',[
          'conditions' => [
            'programas_id' => $programa['id']
          ]
        ])->toArray();

        $this->set(compact('programa', 'responsaveis'));
    }

    public function responsavel($id = null, $responsavel = null) {
        $programa = $this->Programas->get($id);

        $this->loadModel('ProgramasResponsaveis');
        if($responsavel) {
          $responsavel = $this->ProgramasResponsaveis->get($responsavel);
        } else {
          $responsavel = $this->ProgramasResponsaveis->newEntity();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $responsavel = $this->ProgramasResponsaveis->patchEntity($responsavel, $this->request->data);
            if ($this->ProgramasResponsaveis->save($responsavel)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('responsavel', $responsavel)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'responsavel', $programa['id'], $responsavel['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('ApoioProgramas');
        $programas = $this->ApoioProgramas->find('list',['keyField' => 'id', 'valueField' => 'descricao',])->toArray();

        $this->loadModel('ApoioEstados');
        $estados = $this->ApoioEstados->find('list',['keyField' => 'sigla', 'valueField' => 'sigla'])->toArray();

        $this->loadModel('ApoioFuncoes');
        $funcoes = $this->ApoioFuncoes->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'conditions' => ['id IN(37,93,131)']])->toArray();

        $this->set(compact('programa', 'responsavel', 'empresas', 'programas', 'estados', 'funcoes'));
    }

    public function anexos($id = null) {
        $this->loadModel('Programas');
        $programa = $this->Programas->find('all', ['conditions' => ['id' => $id]])->first();
        if (!$programa) {
            return $this->redirect(['controller' => 'programas']);
        }

        $anexos = [];
        $files = glob(WWW_ROOT . "anexos/1/{$programa->id}/*");
        foreach ($files as $k => $f) {
            $f_array = explode('/', $f);
            $f_name = end($f_array);
            $f_name_lower = strtolower($f_name);
            $f_id = str_replace('.pdf', '', $f_name_lower);

            $this->loadModel('Anexos');
            $anexo = $this->Anexos->find('all', ['conditions' => ['id' => $f_id, 'programas_id' => $programa->id, 'trash IS NULL']])->first();
            if ($anexo) {
                $anexos[] = $anexo;
            }
        }

        $this->set(compact('programa', 'profissional', 'anexos'));
    }

}
