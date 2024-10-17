<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ProgramasTextosController extends AppController {

  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->request->session()->write('Auth.User.MenuActive', 'sst');
  }

  public function index($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $option = ['programas_id' => $programa['id']];
    if ($this->request->query('titulo')) {
      $option['titulo LIKE'] = "%{$this->request->query('titulo')}%";
    }

    $this->paginate = [
      'conditions' => $option,
      'order' => ['titulo' => 'ASC']
    ];

    $textos = $this->paginate($this->ProgramasTextos);
    $this->set(compact('textos', 'programa'));
  }

  public function add($id = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->get($id);

    $texto = $this->ProgramasTextos->newEntity();
    if ($this->request->is('post')) {
      $texto = $this->ProgramasTextos->patchEntity($texto, $this->request->data);
      if ($this->ProgramasTextos->save($texto)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $texto)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        return $this->redirect(['action' => 'edit', $texto['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $tipos = [1=>'Capa',2=>'Gloss&aacute;rio',3=>'Documento Base',4=>'Desenvolvimento',5=>'Histórico das revisões'];
    $this->set(compact('texto', 'programa', 'empresa', 'tipos'));
  }

  public function edit($id = null) {
    $texto = $this->ProgramasTextos->get($id);

    $this->loadModel('Programas');
    $programa = $this->Programas->get($texto['programas_id']);

    if ($this->request->is(['patch', 'post', 'put'])) {
      $texto = $this->ProgramasTextos->patchEntity($texto, $this->request->data);
      if ($this->ProgramasTextos->save($texto)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $texto)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'edit', $texto['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('Empresas');
    $empresa = $this->Empresas->find('all',[
      'conditions' => ['id' => $programa['empresas_id']],
      'fields' => ['id', 'nome']
    ])->first();

    $tipos = [1=>'Capa',2=>'Gloss&aacute;rio',3=>'Documento Base',4=>'Desenvolvimento',5=>'Histórico das revisões'];
    $this->set(compact('texto', 'programa', 'empresa', 'tipos'));
  }


}
