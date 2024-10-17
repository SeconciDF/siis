<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AmbientesController extends AppController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->request->session()->write('Auth.User.MenuActive', 'sst');
    }

    public function index($id = null) {
      $this->loadModel('Programas');
      $programa = $this->Programas->get($id);

      $this->paginate = [
          'conditions' => [
            'pa.programas_id' => $programa['id'],
            'Ambientes.empresas_id' => $programa['empresas_id']
          ],
          'fields' => [
            'Programa.id',
            'Programa.data_inicial',
            'Programa.data_final',
            'Ambientes.id',
            'Ambientes.local',
            'Ambientes.descricao',
            'Ambientes.identificacao',
            'Ambientes.tipo_identificacao',
            'Ambiente.id',
            'Ambiente.descricao'
          ],
          'join' => [
            [
              'table' => 'programas_ambientes',
              'alias' => 'pa',
              'type' => 'INNER',
              'conditions' => 'Ambientes.id = pa.ambientes_id',
            ],
            [
              'table' => 'programas',
              'alias' => 'Programa',
              'type' => 'INNER',
              'conditions' => 'Programa.id = pa.programas_id',
            ],
            [
              'table' => 'apoio_ambientes',
              'alias' => 'Ambiente',
              'type' => 'INNER',
              'conditions' => 'Ambiente.id = Ambientes.apoio_ambientes_id',
            ]
          ],
          'sortWhitelist'=> [
            'Programa.id',
            'Programa.data_inicial',
            'Programa.data_final',
            'Ambientes.id',
            'Ambientes.local',
            'Ambientes.descricao',
            'Ambientes.identificacao',
            'Ambientes.tipo_identificacao',
            'Ambiente.id',
            'Ambiente.descricao'
          ],
          'order' => ['Ambiente.descricao' => 'ASC']
      ];

      $this->set('programa', $programa);
      $this->set('ambientes', $this->paginate($this->Ambientes));
    }

    public function add($id = null) {
        $this->loadModel('Programas');
        $programa = $this->Programas->get($id);

        $ambiente = $this->Ambientes->newEntity();
        if ($this->request->is('post')) {
            $ambiente = $this->Ambientes->patchEntity($ambiente, $this->request->data);
            if ($this->Ambientes->save($ambiente)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $ambiente)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add ProgramasAmbientes
                 */
                $this->loadModel('ProgramasAmbientes');
                $amb = $this->ProgramasAmbientes->patchEntity(
                  $this->ProgramasAmbientes->newEntity(), [
                      'programas_id' => $programa['id'],
                      'ambientes_id' => $ambiente['id']
                  ]);
                $this->ProgramasAmbientes->save($amb);
                /**
                 * End ProgramasAmbientes
                 */

                 /**
                  * Add AmbientesRiscos
                  */
                 $this->loadModel('AmbientesRiscos');
                 if(is_array($this->request->data['riscos'])) {
                     foreach ($this->request->data['riscos'] as $key => $value) {
                         $risco = $this->AmbientesRiscos->patchEntity(
                           $this->AmbientesRiscos->newEntity(), [
                               'ambientes_id' => $ambiente['id'],
                               'fatores_riscos_id' => $value
                           ]);
                         $this->AmbientesRiscos->save($risco);
                     }
                 }
                 /**
                  * End AmbientesRiscos
                  */

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                return $this->redirect(['action' => 'edit', $programa['id'], $ambiente['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('EmpresasSetores');
        $ambientes = $this->EmpresasSetores->find('list',[
          'keyField' => 'id',
          'valueField' => 'descricao',
          'fields' => [
            'id'=>'Ambiente.id',
            'descricao'=>'Ambiente.descricao',
          ],
          'conditions' => [
            'EmpresasSetores.empresas_id' => $programa['empresas_id']
          ],
          'join' => [
            [
              'table' => 'apoio_ambientes',
              'alias' => 'Ambiente',
              'type' => 'INNER',
              'conditions' => 'Ambiente.id = EmpresasSetores.apoio_ambientes_id',
            ]
          ],
        ])->toArray();


        $this->loadModel('ApoioFatoresRiscos');
        $riscos = $this->ApoioFatoresRiscos->find('list',[
          'keyField' => 'id',
          'valueField' => function($row) {
            return "{$row['codigo']} - {$row['descricao']}";
          }
        ])->toArray();


        $this->loadModel('Empresas');
        $empresa = $this->Empresas->find('all',[
          'conditions' => ['id' => $programa['empresas_id']],
          'fields' => ['id', 'nome']
        ])->first();

        $this->set(compact('programa', 'ambiente', 'empresa', 'ambientes', 'riscos'));
    }

    public function edit($id = null, $ambiente = null) {
        $this->loadModel('Programas');
        $programa = $this->Programas->get($id);

        $ambiente = $this->Ambientes->get($ambiente);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ambiente = $this->Ambientes->patchEntity($ambiente, $this->request->data);
            if ($this->Ambientes->save($ambiente)) {
                $this->loadComponent('Log');
                if(!$this->Log->save('add', $ambiente)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }

                /**
                 * Add AmbientesRiscos
                 */
                $this->loadModel('AmbientesRiscos');
                $this->AmbientesRiscos->deleteAll(array("ambientes_id = {$ambiente['id']}"));
                if(is_array($this->request->data['riscos'])) {
                    foreach ($this->request->data['riscos'] as $key => $value) {
                        $risco = $this->AmbientesRiscos->patchEntity(
                          $this->AmbientesRiscos->newEntity(), [
                              'ambientes_id' => $ambiente['id'],
                              'fatores_riscos_id' => $value
                          ]);
                        $this->AmbientesRiscos->save($risco);
                    }
                }
                /**
                 * End AmbientesRiscos
                 */

                $this->Flash->success(__($this::MSG_SUCESSO_ADD));
                return $this->redirect(['action' => 'edit', $programa['id'], $ambiente['id']]);
            } else {
                $this->Flash->error($this::MSG_ERRO);
            }
        }

        $this->loadModel('EmpresasSetores');
        $ambientes = $this->EmpresasSetores->find('list',[
          'keyField' => 'id',
          'valueField' => 'descricao',
          'fields' => [
            'id'=>'Ambiente.id',
            'descricao'=>'Ambiente.descricao',
          ],
          'conditions' => [
            'EmpresasSetores.empresas_id' => $programa['empresas_id']
          ],
          'join' => [
            [
              'table' => 'apoio_ambientes',
              'alias' => 'Ambiente',
              'type' => 'INNER',
              'conditions' => 'Ambiente.id = EmpresasSetores.apoio_ambientes_id',
            ]
          ],
        ])->toArray();

        $this->loadModel('AmbientesRiscos');
        $selecionadas = $this->AmbientesRiscos->find('list',['keyField' => 'fatores_riscos_id', 'valueField' => 'fatores_riscos_id', 'conditions' => ['ambientes_id' => $ambiente['id']]])->toArray();

        $this->loadModel('ApoioFatoresRiscos');
        $riscos = $this->ApoioFatoresRiscos->find('list',[
          'keyField' => 'id',
          'valueField' => function($row) {
            return "{$row['codigo']} - {$row['descricao']}";
          }
        ])->toArray();

        $this->loadModel('Empresas');
        $empresa = $this->Empresas->find('all',[
          'conditions' => ['id' => $programa['empresas_id']],
          'fields' => ['id', 'nome']
        ])->first();

        $this->set(compact('programa', 'ambiente', 'empresa', 'ambientes', 'riscos', 'selecionadas'));
    }

    public function delete($id, $programa) {
      $this->request->allowMethod(['post', 'delete']);
      $ambiente = $this->Ambientes->get($id);
      if ($this->Ambientes->delete($ambiente)) {
        $this->Flash->success($this::MSG_SUCESSO_DEL);
      }
      return $this->redirect(['action' => 'index', $programa]);
    }
}
