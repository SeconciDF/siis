<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */

 date_default_timezone_set('America/Sao_Paulo');
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime', 0);

class AppController extends Controller {

      const MSG_SUCESSO_ADD = 'Dados gravados!';
      const MSG_SUCESSO_EDT = 'Dados alterados!';
      const MSG_SUCESSO_DEL = 'Dados deletados!';
      const MSG_ERRO = 'Erro ao realizar a opera&ccedil;&atilde;o, tente novamente!';
      const MSG_ERRO_LOG = 'Erro ao gravar log do sistema';

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize() {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            'authenticate' => [
                'Form' => [
                    'userModel' => 'SegurancaColaboradores',
                    'fields' => [
                        'username' => 'login',
                        'password' => 'senha'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'SegurancaColaboradores',
                'action' => 'login',
            ],
            'loginRedirect' => [
                'controller' => 'Mains',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'SegurancaColaboradores',
                'action' => 'login'
            ],
            'authError' => 'Usu&aacute;rio n&atilde;o tem permiss&atilde;o para acessar este m&oacute;dulo.'
        ]);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
    }

    public function isAuthorized($user) {
        if(!$this->Auth->user('id')) {
            return $this->redirect(['controller' => 'seguranca-colaboradores', 'action' => 'login']);
        }

        $menu = [];
        $permissoes = ['Mains'];
        $menu['mains'] = ['view' => true, 'name' => 'PRINCIPAL', 'icon' => 'icon-align-justify', 'params' => ['controller' => 'Mains', 'action' => 'index']];

        foreach ($this->request->session()->read('Auth.User.acessos') as $key => $value) {
          $menu[$value['slug']] = [
            'name' => $value['modulo'],
            'icon' => $value['icon'],
            'actions' => ['all']
          ];

          foreach ($value['funcionalidades'] as $submenu) {
            $permissoes[] = $submenu['controller'];
            $menu[$value['slug']]['submenu'][] = [
              'view' => $submenu['view'],
              'name' => $submenu['descricao'],
              'icon' => $submenu['icon'],
              'params' => [
                'controller' => $submenu['controller'],
                'action' => $submenu['action']
              ]
            ];
          }
        }

        $this->request->session()->write('Auth.User.menu', $menu);
        if (in_array($this->request->controller, $permissoes)) {
          return true;
        }
        $this->redirect(['controller' => 'mains']);
        return false;
    }

    public function menuAtivo($controllers) {
        foreach ($controllers as $controller => $value) {
            if (isset($value['view'])) {
                if ($value['view']) {
                    if ($this->request->controller == $controller) {
                        $this->request->session()->write('Auth.User.MenuActive', $controller);
                        break;
                    }
                }
            } else {
                foreach ($value['submenu'] as $sub) {
                    if (isset($sub['params']['controller'])) {
                        if ($this->request->controller == $sub['params']['controller']) {
                            $this->request->session()->write('Auth.User.MenuActive', $controller);
                            break;
                        }
                    }
                }
            }
        }
    }
}
