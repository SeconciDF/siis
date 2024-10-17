<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class SegurancaGruposController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'configuracao');
    }

    public function index($search = null) {
        $option = null;
        if ($search) {
            $option['OR']['SegurancaGrupos.descricao LIKE'] = "%{$search}%";
        }

        $this->paginate = [
            'fields' => [
                'SegurancaGrupos.id',
                'SegurancaGrupos.descricao',
                'SegurancaGrupos.ativo',
                'SegurancaGrupos.info',
            ],
            'conditions' => $option,
            'sortWhitelist'=> [
                'SegurancaGrupos.id',
                'SegurancaGrupos.descricao',
                'SegurancaGrupos.ativo',
                'SegurancaGrupos.info',
            ],
            'order' => [
                'SegurancaGrupos.descricao' => 'asc'
            ]
        ];

        $this->set('grupos', $this->paginate($this->SegurancaGrupos));
    }

    public function add() {
        $grupo = $this->SegurancaGrupos->newEntity();
        if ($this->request->is('post')) {
            $grupo = $this->SegurancaGrupos->patchEntity($grupo, $this->request->data);
            if ($this->SegurancaGrupos->save($grupo)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $grupo)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                return $this->redirect(['action' => 'edit', $grupo->id]);
            }
            $this->Flash->error(__($this::MSG_ERRO));
        }
        $this->set('grupo', $grupo);
    }

    public function edit($id = null) {
        $grupo = $this->SegurancaGrupos->find('all', ['conditions' => ['id' => $id]])->first();
        if ($this->request->is(['post', 'put'])) {
            $grupo = $this->SegurancaGrupos->patchEntity($grupo, $this->request->data);

            if ($this->SegurancaGrupos->save($grupo)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $grupo)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add SegurancaPermissoes
                 */
                $this->loadModel('SegurancaPermissoes');
                if(isset($this->request->data['permissoes'])) {
                    $this->SegurancaPermissoes->deleteAll(array("grupos_id = {$grupo->id}"));
                    foreach ($this->request->data['permissoes'] as $key => $value) {
                        $value['make_colaboradores_id'] = $this->request->session()->read('Auth.User.id');
                        $value['data_hora_update'] = date('Y-m-d H:i:s');
                        $value['grupos_id'] = $grupo->id;

                        $permissoes = $this->SegurancaPermissoes->patchEntity($this->SegurancaPermissoes->newEntity(), $value);
                        $this->SegurancaPermissoes->save($permissoes);
                    }
                }
                /**
                 * End SegurancaPermissoes
                 */

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this::MSG_ERRO);
        }

        $this->loadModel('SegurancaModulos');
        $modulos = $this->SegurancaModulos->find('all', [
              'fields' => [
                'SegurancaModulos.id',
                'SegurancaModulos.descricao',
                'Funcionalidade.controller',
                'Funcionalidade.descricao',
                'Funcionalidade.info',
                'Funcionalidade.id',
                'Permissao.visualizar',
                'Permissao.bloquear',
                'Permissao.editar',
                'Permissao.excluir',
                'Permissao.criar',
                'Permissao.id',
              ],
              'join' => [
                  [
                      'table' => 'seguranca_funcionalidades',
                      'alias' => 'Funcionalidade',
                      'type' => 'INNER',
                      'conditions' => 'SegurancaModulos.id = Funcionalidade.modulos_id',
                  ],
                  [
                      'table' => 'seguranca_permissoes',
                      'alias' => 'Permissao',
                      'type' => 'LEFT',
                      'conditions' => "Funcionalidade.id = Permissao.funcionalidades_id AND Permissao.grupos_id = {$grupo['id']}",
                  ],
              ],
              'group' => [
                  'SegurancaModulos.descricao',
                  'Funcionalidade.descricao',
              ]
          ])->toArray();

        $acessos = [];
        foreach ($modulos as $key => $value) {
            $acessos[$value['id']]['modulo'] = $value['descricao'];
            $acessos[$value['id']]['funcionalidades'][$value['Funcionalidade']['id']]['funcionalidade'] = $value['Funcionalidade']['descricao'];
            $acessos[$value['id']]['funcionalidades'][$value['Funcionalidade']['id']]['controller'] = $value['Funcionalidade']['controller'];
            $acessos[$value['id']]['funcionalidades'][$value['Funcionalidade']['id']]['info'] = $value['Funcionalidade']['info'];
            $acessos[$value['id']]['funcionalidades'][$value['Funcionalidade']['id']]['id'] = $value['Funcionalidade']['id'];
            $acessos[$value['id']]['funcionalidades'][$value['Funcionalidade']['id']]['permissoes'] = $value['Permissao'];
        }

        $this->set(compact('grupo', 'acessos'));
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
