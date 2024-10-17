<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;

class OdontoProcedimentosController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'configuracao');
    }

    public function index() {
        $option = null;
        if ($this->request->query('id')) {
            $option['OdontoProcedimentos.id'] = $this->request->query('id');
        }
        if ($this->request->query('nome')) {
            $option['OdontoProcedimentos.nome LIKE'] = "%{$this->request->query('nome')}%";
        }

        $this->paginate = [
            'conditions' => $option,
            'order' => ['nome' => 'asc']
        ];

        $this->set('procedimentos', $this->paginate($this->OdontoProcedimentos));
    }

    public function add() {
        $procedimento = $this->OdontoProcedimentos->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $procedimento = $this->OdontoProcedimentos->patchEntity($procedimento, $this->request->data);
            if ($this->OdontoProcedimentos->save($procedimento)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $procedimento)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $procedimento['id']]);
            }
            $this->Flash->error($this::MSG_ERRO);
        }

        $this->set(compact('procedimento'));
    }

    public function edit($id = null) {
        $procedimento = $this->OdontoProcedimentos->find('all', ['conditions' => ['id' => $id]])->first();
        if ($this->request->is(['post', 'put'])) {
            $procedimento = $this->OdontoProcedimentos->patchEntity($procedimento, $this->request->data);
            if ($this->OdontoProcedimentos->save($procedimento)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $procedimento)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this::MSG_ERRO);
        }

        $this->set(compact('procedimento'));
    }

    public function csv() {
      $conn = ConnectionManager::get('default');
      $stmt = $conn->execute("SELECT id, nome, ponto FROM odonto_procedimentos ORDER BY nome ASC");

      header('Pragma: public');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Content-Description: File Transfer');
      header('Content-Type: text/csv');
      header("Content-Disposition: attachment; filename=\"PROCEDIMENTOS.csv\";");
      header('Content-Transfer-Encoding: binary');

      $output = fopen('php://output', 'w');
      fputs($output, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

      fputcsv($output, [ 'Codigo', 'Nome', 'Ponto'], ";");

      $rows = $stmt->fetchAll('assoc');
      foreach ($rows as $row) {
          fputcsv($output, $row, ";");
      }
      exit;
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
