<?php

namespace App\Controller;

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
require_once(ROOT . DS . 'vendor' . DS . 'UploadHandler.php');

use App\Controller\AppController;
use Cake\Event\Event;
use UploadHandler;

/**
* Atividades Controller
*
* @property \App\Model\Table\AtividadesTable $Atividades
*/
class AnexosController extends AppController {

  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->Auth->allow(['download', 'enviar', 'edit', 'delete', 'upload']);
  }

  public function edit($id = null, $anexo_id = null, $json = null) {
    $this->loadModel('Programas');
    $programa = $this->Programas->find('all', ['conditions' => ['id' => $id]])->first();
    if (!$programa) {
      return $this->redirect(['controller' => 'programas']);
    }

    $anexo = $this->Anexos->find('all', [
      'conditions' => ['Anexos.id' => $anexo_id, 'Anexos.programas_id' => $programa->id],
      'fields' => [
        'Anexos.id',
        'Anexos.nome',
        'Anexos.data_envio',
        'Anexos.data_alteracao',
        'Anexos.descricao',
        'Anexos.local',
        'tag.id',
        'tag.descricao',
        'usuario.id',
        'usuario.nome'
      ],
      'join' => [
        [
          'table' => 'anexos_tags',
          'alias' => 'tag',
          'type' => 'INNER',
          'conditions' => 'tag.id = Anexos.tags_id',
        ],
        [
          'table' => 'seguranca_colaboradores',
          'alias' => 'usuario',
          'type' => 'INNER',
          'conditions' => 'usuario.id = Anexos.usuario_registro',
        ]
      ]
      ])->first();

      if ($this->request->is(['patch', 'post', 'put'])) {
        $anexo = $this->Anexos->patchEntity($anexo, $this->request->data);
        if ($this->Anexos->save($anexo)) {
          $this->loadComponent('Log');
          if(!$this->Log->save('edit', $anexo)) {
            $this->Flash->error($this::MSG_ERRO_LOG);
          }

          $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        } else {
          $this->Flash->error($this::MSG_ERRO);
        }

        $from = json_decode($json);
        return $this->redirect(['controller' => $from->controlle, 'action' => $from->action, $id, $from->id, $from->local]);
      }

      $this->set(compact('programa', 'anexo', 'json'));
      $this->viewBuilder()->layout('ajax');
    }

    public function delete($id = null, $anexo_id = null, $json = null) {
      $this->loadModel('Programas');
      $programa = $this->Programas->find('all', ['conditions' => ['id' => $id]])->first();
      if (!$programa) {
        return $this->redirect(['controller' => 'programas']);
      }

      $anexo = $this->Anexos->get($anexo_id);
      if($this->Anexos->updateAll(['trash' => true, 'usuario_trash' => $this->request->session()->read('Auth.User.id')], ['id' => $anexo_id, 'programas_id' => $id])) {
        $this->loadComponent('Log');
        if(!$this->Log->save('delete', $anexo)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success(__($this::MSG_SUCESSO_DEL));
      }

      $from = json_decode($json);
      return $this->redirect(['controller' => $from->controlle, 'action' => $from->action, $id, $from->id, $from->local]);
    }

    public function upload($tags_id = null, $id = null, $local = null) {
      $colaborador = $this->request->session()->read('Auth.User.id');
      if ($this->request->is('post') && $_FILES) {
        $fullServerPath = WWW_ROOT . "anexos/{$tags_id}/{$id}";

        if(!file_exists($fullServerPath)) {
          mkdir($fullServerPath, 0777, true);
        }

        $max_file_size = 5 * 1024 * 1024;
        if($this->request->query('unlimited')) {
          $max_file_size = null;
        }

        if(file_exists($fullServerPath)) {
          $upload_handler = new UploadHandler(array(
            'max_file_size' => $max_file_size,
            'upload_dir' => "{$fullServerPath}/",
            'accept_file_types' => '/\.(jpg|JPG)$/i'
          ), true, array(
            'max_file_size' => 'Este arquivo excede o tamanho m&aacute;ximo de 5MB',
            'accept_file_types' => 'Somente arquivos de imagem do tipo (JPG) s&atilde;o permitidos.'
          ));

          if(!isset($upload_handler->response['files'][0]->error)) {
            $oldFileName = $upload_handler->response['files'][0]->name;
            $newFileName = date('dHis');

            $data = [
              'programas_id' => $id,
              'tags_id' => $tags_id,
              'data_envio' => new \DateTime('now'),
              'data_alteracao' => new \DateTime('now'),
              'nome' => substr($oldFileName, 0, -4),
              'local' => $local ? $local : "{$id}",
              'usuario_registro' => $colaborador
            ];

            $anexo = $this->Anexos->patchEntity($this->Anexos->newEntity(), $data);
            if ($this->Anexos->save($anexo)) {
              $this->loadComponent('Log');
              if(!$this->Log->save('add', $anexo)) {
                $this->Flash->error($this::MSG_ERRO_LOG);
              }

              rename("{$fullServerPath}/{$oldFileName}","{$fullServerPath}/{$anexo['id']}.jpg");
            }
          }
        }
      }
      exit;
    }

  }
