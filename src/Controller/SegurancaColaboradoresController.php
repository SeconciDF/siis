<?php

namespace App\Controller;

use Cake\Auth\DefaultPasswordHasher;
use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Event\Event;

class SegurancaColaboradoresController extends AppController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['logout', 'esqueci', 'captcha', 'alterarsenha']);
        $this->request->session()->write('Auth.User.MenuActive', 'configuracao');
    }

    public function index() {
        $option = null;
        if ($this->request->query('nome')) {
            $option['SegurancaColaboradores.nome LIKE'] = "%{$this->request->query('nome')}%";
        }

        $this->paginate = [
            'fields' => [
                'SegurancaColaboradores.id',
                'SegurancaColaboradores.nome',
                'SegurancaColaboradores.login',
                'SegurancaColaboradores.ativo',
                'grupos' => '(SELECT GROUP_CONCAT(g.descricao) FROM seguranca_colaboradores_grupos cg
                              INNER JOIN seguranca_grupos g ON g.id = cg.grupos_id
                              WHERE cg.colaboradores_id = SegurancaColaboradores.id)'
            ],
            'conditions' => $option,
            'sortWhitelist'=> [
                'SegurancaColaboradores.nome',
                'SegurancaColaboradores.login',
                'SegurancaColaboradores.ativo',
                'grupos'
            ],
            'order' => [
                'SegurancaColaboradores.nome' => 'asc'
            ]
        ];

        $this->set('colaboradores', $this->paginate($this->SegurancaColaboradores));
    }

    public function add() {
        $usuario = $this->SegurancaColaboradores->newEntity();
        if ($this->request->is('post')) {
            if($this->request->data['senha'] != '') {
                $this->request->data['senha'] = (new DefaultPasswordHasher)->hash($this->request->data['senha']);
            }

            $usuario = $this->SegurancaColaboradores->patchEntity($usuario, $this->request->data);
            if ($this->SegurancaColaboradores->save($usuario)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $usuario)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                return $this->redirect(['action' => 'edit', $usuario->id]);
            }
            $this->Flash->error(__($this::MSG_ERRO));
        }

        $this->loadModel('ApoioUnidades');
        $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('Profissionais');
        $profissionais = $this->Profissionais->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->set(compact('usuario', 'profissionais', 'unidades', 'empresas'));
    }

    public function edit($id = null) {
        $usuario = $this->SegurancaColaboradores->find('all', ['conditions' => ['id' => $id]])->first();

        if ($this->request->is(['post', 'put'])) {
            if ($this->request->data['senha'] != '') {
                $this->request->data['senha'] = (new DefaultPasswordHasher)->hash($this->request->data['senha']);
            } else {
                unset($this->request->data['senha']);
            }

            $this->SegurancaColaboradores->patchEntity($usuario, $this->request->data);
            if ($this->SegurancaColaboradores->save($usuario)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('edit', $usuario)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_EDT);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this::MSG_ERRO);
        }

        $this->loadModel('ApoioUnidades');
        $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('Profissionais');
        $profissionais = $this->Profissionais->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->loadModel('Empresas');
        $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

        $this->set(compact('usuario', 'profissionais', 'unidades', 'empresas'));
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

    public function login() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                if ($user['ativo'] != 'A') {
                    $this->Flash->error(__('Login desativado!'));
                    return $this->redirect($this->Auth->logout());
                }

                $this->Auth->setUser($user);
                $this->SegurancaColaboradores->updateAll(['data_ultimo_acesso' => date('Y-m-d H:i:s')], ['id' => $this->request->session()->read('Auth.User.id')]);

                $this->loadModel('SegurancaColaboradoresGrupos');
                $seguranca = $this->SegurancaColaboradoresGrupos->find('all', [
                      'fields' => [
                        'Grupo.id',
                        'Grupo.ativo',
                        'Grupo.descricao',
                        'Permissao.visualizar',
                        'Permissao.bloquear',
                        'Permissao.editar',
                        'Permissao.excluir',
                        'Permissao.criar',
                        'Funcionalidade.descricao',
                        'Funcionalidade.controller',
                        'Funcionalidade.action',
                        'Funcionalidade.view',
                        'Funcionalidade.icon',
                        'Funcionalidade.id',
                        'Modulo.slug',
                        'Modulo.icon',
                        'Modulo.descricao',
                        'Modulo.id'
                      ],
                      'conditions' => ['colaboradores_id' => $user['id']],
                      'join' => [
                          [
                              'table' => 'seguranca_grupos',
                              'alias' => 'Grupo',
                              'type' => 'INNER',
                              'conditions' => 'Grupo.id = SegurancaColaboradoresGrupos.grupos_id',
                          ],
                          [
                              'table' => 'seguranca_permissoes',
                              'alias' => 'Permissao',
                              'type' => 'INNER',
                              'conditions' => 'Grupo.id = Permissao.grupos_id',
                          ],
                          [
                              'table' => 'seguranca_funcionalidades',
                              'alias' => 'Funcionalidade',
                              'type' => 'INNER',
                              'conditions' => 'Funcionalidade.id = Permissao.funcionalidades_id',
                          ],
                          [
                              'table' => 'seguranca_modulos',
                              'alias' => 'Modulo',
                              'type' => 'INNER',
                              'conditions' => 'Modulo.id = Funcionalidade.modulos_id',
                          ],
                      ],
                      'order' => [
                        'FIELD(Modulo.id,4,5,6,11,12,10,8)',
                        'Funcionalidade.sort'
                      ],
                  ])->toArray();

                $perfil = [];
                $acessos = [];
                foreach ($seguranca as $key => $value) {
                    $perfil[$value['Grupo']['id']] = $value['Grupo'];

                    $acessos[$value['Modulo']['id']]['slug'] = $value['Modulo']['slug'];
                    $acessos[$value['Modulo']['id']]['icon'] = $value['Modulo']['icon'];
                    $acessos[$value['Modulo']['id']]['modulo'] = $value['Modulo']['descricao'];
                    $acessos[$value['Modulo']['id']]['funcionalidades'][$value['Funcionalidade']['id']]['descricao'] = $value['Funcionalidade']['descricao'];
                    $acessos[$value['Modulo']['id']]['funcionalidades'][$value['Funcionalidade']['id']]['controller'] = $value['Funcionalidade']['controller'];
                    $acessos[$value['Modulo']['id']]['funcionalidades'][$value['Funcionalidade']['id']]['action'] = $value['Funcionalidade']['action'];
                    $acessos[$value['Modulo']['id']]['funcionalidades'][$value['Funcionalidade']['id']]['view'] = $value['Funcionalidade']['view'];
                    $acessos[$value['Modulo']['id']]['funcionalidades'][$value['Funcionalidade']['id']]['icon'] = $value['Funcionalidade']['icon'];
                    $acessos[$value['Modulo']['id']]['funcionalidades'][$value['Funcionalidade']['id']]['permissoes'] = $value['Permissao'];
                }

                $this->request->session()->write('Auth.User.perfil', $perfil);
                $this->request->session()->write('Auth.User.acessos', $acessos);

                $this->loadComponent('Log');
                if(!$this->Log->save('login', $this->request->session()->read('Auth.User'))) {
                    $this->Flash->error($this::MSG_ERRO_LOG);
                }

                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('email ou senha n&atilde;o confere'));
        }
    }

    public function logout() {
        //$this->logs('logout', $this->request->session()->read('Auth.User'));
        return $this->redirect($this->Auth->logout());
    }

    public function alterarSenha($id = null) {
        $usuario = $this->SegurancaColaboradores->find('all', ['conditions' => ['id' => $this->request->session()->read('Auth.User.id')]])->first();
        $this->set(compact('usuario'));

        if ($this->request->is('post')) {
            $senha = (new DefaultPasswordHasher)->hash($this->request->data['nova_senha']);
            $nova_senha = $this->request->data['nova_senha'];
            $confirmar = $this->request->data['confirmar_senha'];

            if (!(new DefaultPasswordHasher)->check($this->request->data['senha_atual'], $usuario['senha'])) {
                $this->Flash->error(__('Senha atual nao confere!'));
                return $this->redirect(['action' => 'alterarsenha']);
            }

            if ($nova_senha != $confirmar) {
                $this->Flash->error(__('Nova senha nao foi confirmada corretamente!'));
                return $this->redirect(['action' => 'alterarsenha']);
            }

            if ($this->SegurancaColaboradores->updateAll(['senha' => $senha], ['id' => $this->request->session()->read('Auth.User.id')])) {
                $this->Flash->success(__('Senha alterada com sucesso!'));
                return $this->redirect(['controller' => 'mains', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Falha ao gravar nova senha!'));
            }
        }
    }

    public function esqueci() {
        if ($this->request->is('post')) {
            $usuario = $this->SegurancaColaboradores->find('all', ['fields' => ['id', 'login', 'nome'], 'conditions' => ['login' => $this->request->data['login']]])->first();
            if ($usuario) {
                $nova_senha = (date('ymd') . $usuario['id']);
                $nova_senha_hash = (new DefaultPasswordHasher)->hash($nova_senha);

                if ($this->SegurancaColaboradores->updateAll(['senha' => $nova_senha_hash], ['id' => $usuario['id']])) {
                    $email = new Email('default');
                    $email->to($usuario['login'], $usuario['nome']);
                    $email->subject("Solicitação de nova senha");

                    if ($email->send("Sua nova senha: {$nova_senha}")) {
                        $this->Flash->success('Email enviado. Verifique, tamb&eacute;m, em sua caixa de spam.');
                        return $this->redirect(['action' => 'login']);
                    } else {
                        $this->Flash->error(__('Falha ao enviar email!'));
                    }
                } else {
                    $this->Flash->error(__('Falha ao gravar nova senha!'));
                }
            } else {
                $this->Flash->error(__('Email nao localizado!'));
            }
        }
    }

    public function permissoes($id = null) {
        $usuario = $this->SegurancaColaboradores->find('all', ['conditions' => ['id' => $id]])->first();

        if ($this->request->is('post')) {
            $this->loadModel('SegurancaColaboradoresGrupos');
            $usuarioAcesso = $this->SegurancaColaboradoresGrupos->newEntity();
            $usuarioAcesso = $this->SegurancaColaboradoresGrupos->patchEntity($usuarioAcesso, $this->request->data);

            if ($this->SegurancaColaboradoresGrupos->save($usuarioAcesso)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('permissao', $usuarioAcesso)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                $this->Flash->success($this::MSG_SUCESSO_ADD);
                return $this->redirect(['action' => 'permissoes', $id]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('SegurancaGrupos');
        $grupos = $this->SegurancaGrupos->find('list',['keyField' => 'id', 'valueField' => 'descricao',])->toArray();

        $this->loadModel('SegurancaColaboradoresGrupos');
        $acessos = $this->SegurancaColaboradoresGrupos->find('list', ['keyField' => 'Grupo.id', 'valueField' => 'Grupo.descricao',
                    'fields' => ['Grupo.id', 'Grupo.descricao'],
                    'conditions' => ['colaboradores_id' => $id],
                    'join' => [
                        [
                            'table' => 'seguranca_grupos',
                            'alias' => 'Grupo',
                            'type' => 'INNER',
                            'conditions' => 'Grupo.id = SegurancaColaboradoresGrupos.grupos_id',
                        ]
                    ]
                ])->all()->toArray();

        $this->set(compact('usuario', 'grupos', 'acessos'));
    }

    public function deletePermissao($ids = null) {
        $id = explode(',', $ids);
        $this->loadModel('SegurancaColaboradoresGrupos');
        if ($this->SegurancaColaboradoresGrupos->deleteAll(['colaboradores_id' => $id[0], 'grupos_id' => $id[1]])) {
            $this->loadComponent('Log');
            if(!$this->Log->save('permissao', ['colaboradores_id' => $id[0], 'grupos_id' => $id[1], 'id' => 'delete'])) {
               $this->Flash->error($this::MSG_ERRO_LOG);
            }

            $this->Flash->success($this::MSG_SUCESSO_DEL);
        } else {
            $this->Flash->error($this::MSG_ERRO);
        }

        return $this->redirect(['action' => 'permissoes', $id[0]]);
    }





 
}
