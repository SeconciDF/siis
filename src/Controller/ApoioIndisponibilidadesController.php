<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ApoioIndisponibilidadesController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'configuracao');
    }

    public function index() {
        $option = null;
        if ($this->request->query('descricao')) {
            $option['ApoioIndisponibilidades.descricao LIKE'] = "%{$this->request->query('descricao')}%";
        }

        $this->paginate = [
            'conditions' => $option,
            'order' => ['data' => 'desc']
        ];

        $this->set('indisponibilidades', $this->paginate($this->ApoioIndisponibilidades));
    }

    public function add() {
        $indisponibilidade = $this->ApoioIndisponibilidades->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $indisponibilidade = $this->ApoioIndisponibilidades->patchEntity($indisponibilidade, $this->request->data);
            if ($this->ApoioIndisponibilidades->save($indisponibilidade)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $indisponibilidade)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $indisponibilidade['id']]);
            }
            $this->Flash->error($this::MSG_ERRO);
        }

        $this->set(compact('indisponibilidade'));
    }

    public function edit($id = null) {
        $indisponibilidade = $this->ApoioIndisponibilidades->find('all', ['conditions' => ['id' => $id]])->first();
        if ($this->request->is(['post', 'put'])) {
            $indisponibilidade = $this->ApoioIndisponibilidades->patchEntity($indisponibilidade, $this->request->data);
            if ($this->ApoioIndisponibilidades->save($indisponibilidade)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $indisponibilidade)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this::MSG_ERRO);
        }

        $this->set(compact('indisponibilidade'));
    }

    public function delete($id) {
//        $this->request->allowMethod(['post', 'delete']);
//        $usuario = $this->Usuarios->find('all', ['conditions' => ['id' => $id]])->first();
//        if ($this->Usuarios->delete($usuario)) {
//            $this->Flash->success($this::MSG_SUCESSO_DEL);
//            return $this->redirect(['action' => 'index']);
//        }
        return $this->redirect(['action' => 'index']);
    }

}
