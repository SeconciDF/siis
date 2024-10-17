<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Mpdf\Mpdf;

class AtendimentosController extends AppController {

  public function beforeFilter(Event $event) {
    $this->request->session()->write('Auth.User.MenuActive', 'seconci');
  }

  public function anamnese($id = null) {
    $this->loadModel('Consultas');
    $this->loadModel('ApoioAnamnese');
    $this->loadModel('ConsultasAnamnese');
    $anamnese = $this->ConsultasAnamnese->newEntity();
    if ($this->request->is(['patch', 'post', 'put'])) {
      /**
      * Add ConsultasAnamnese
      */
      if(is_array($this->request->data['resposta'])) {
        foreach ($this->request->data['resposta'] as $key => $value) {
          $resposta = $this->ConsultasAnamnese->patchEntity(
            $this->ConsultasAnamnese->newEntity(), [
              'observacao' => $this->request->data['observacao'][$key],
              'consultas_id' => $this->request->data['consultas_id'],
              'anamnese_id' => $key,
              'resposta' => $value
            ]);
            $this->ConsultasAnamnese->save($resposta);
            $this->loadComponent('Log');
            $resposta['id'] = $this->request->data['consultas_id'];
            if(!$this->Log->save('resposta', $resposta)) {
               $this->Flash->error($this::MSG_ERRO_LOG);
            }
          }
        }
        /**
        * End ConsultasAnamnese
        */

        $data = [
          'anamnese_qp_hda' => $this->request->data['anamnese_qp_hda'],
          'numero_escovacoes_diarias' => $this->request->data['numero_escovacoes_diarias'],
          'anamnese_odonto_obs' => $this->request->data['anamnese_odonto_obs']
        ];

        if($this->Consultas->updateAll($data, ['id' => $this->request->data['consultas_id']])) {
          $this->loadComponent('Log');
          $this->request->data['id'] = $this->request->data['consultas_id'];
          if(!$this->Log->save('anamnese', $this->request->data)) {
             $this->Flash->error($this::MSG_ERRO_LOG);
          }

          $this->Flash->success($this::MSG_SUCESSO_EDT);
          return $this->redirect(['action' => 'anamnese', $this->request->data['consultas_id']]);
        }
      }

    $consulta = $this->Consultas->find('all', [
      'conditions' => ['Consultas.id' => $id],
      'fields' => [
        'Consultas.id',
        'Consultas.anamnese_qp_hda',
        'Consultas.numero_escovacoes_diarias',
        'Consultas.anamnese_odonto_obs',
        'Consultas.data_hora_agendado',
        'Especialidade.descricao',
        'Profissional.id',
        'Profissional.nome',
        'Motivo.descricao',
        'Empresa.nome',
        'Beneficiario.id',
        'Dependente.id',
        'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
        'nascimento' => 'IFNULL(Dependente.data_nascimento,Beneficiario.data_nascimento)',
        'funcoes' => '(SELECT GROUP_CONCAT(f.descricao) FROM beneficiarios_funcoes b INNER JOIN apoio_funcoes f ON f.id = b.funcoes_id WHERE b.beneficiarios_id = Beneficiario.id)',
        'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiario.id ORDER BY id ASC)'
      ],
      'join' => [
        [
          'table' => 'beneficiarios',
          'alias' => 'Beneficiario',
          'type' => 'LEFT',
          'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
        ],
        [
          'table' => 'beneficiarios_dependentes',
          'alias' => 'Dependente',
          'type' => 'LEFT',
          'conditions' => 'Dependente.id = Consultas.dependentes_id',
        ],
        [
          'table' => 'apoio_especialidades',
          'alias' => 'Especialidade',
          'type' => 'LEFT',
          'conditions' => 'Especialidade.id = Consultas.especialidades_id',
        ],
        [
          'table' => 'apoio_motivos_consultas',
          'alias' => 'Motivo',
          'type' => 'LEFT',
          'conditions' => 'Motivo.id = Consultas.motivos_consultas_id',
        ],
        [
          'table' => 'profissionais',
          'alias' => 'Profissional',
          'type' => 'LEFT',
          'conditions' => 'Profissional.id = Consultas.profissionais_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'LEFT',
          'conditions' => 'Empresa.id = Consultas.empresas_id',
        ]
      ]])->first();

    if($this->request->session()->read('Auth.User.profissionais_id')) {
      if($this->request->session()->read('Auth.User.profissionais_id') != $consulta['Profissional']['id']) {
        $this->Flash->error('Consulta n&atilde;o atribuida ao profissional logado!');
        return $this->redirect(['controller' => 'agendas', 'date' => date('Y-m-d', strtotime($consulta['data_hora_agendado']))]);
      }
    }

    $anamneses = $this->ApoioAnamnese->find('all', [
      'fields' => [
        'ApoioAnamnese.id',
        'ApoioAnamnese.pergunta',
        'Consulta.resposta',
        'Consulta.observacao',
      ],
      'join' => [
        [
          'table' => 'consultas_anamnese',
          'alias' => 'Consulta',
          'type' => 'LEFT',
          'conditions' => "ApoioAnamnese.id = Consulta.anamnese_id AND Consulta.consultas_id = {$id}",
        ],
      ]
      ])->toArray();

    $this->set(compact('anamnese', 'anamneses', 'consulta'));
  }

  public function prontuario($id = null) {
    $this->loadModel('Consultas');
    $consulta = $this->Consultas->find('all', [
      'conditions' => ['Consultas.id' => $id],
      'fields' => [
        'Consultas.id',
        'Consultas.data_hora_agendado',
        'Consultas.especialidades_id',
        'Especialidade.descricao',
        'Profissional.id',
        'Profissional.nome',
        'Motivo.descricao',
        'Empresa.nome',
        'Beneficiario.id',
        'Dependente.id',
        'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
        'nascimento' => 'IFNULL(Dependente.data_nascimento,Beneficiario.data_nascimento)',
        'funcoes' => '(SELECT GROUP_CONCAT(f.descricao) FROM beneficiarios_funcoes b INNER JOIN apoio_funcoes f ON f.id = b.funcoes_id WHERE b.beneficiarios_id = Beneficiario.id)',
        'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiario.id ORDER BY id ASC)'
      ],
      'join' => [
        [
          'table' => 'beneficiarios',
          'alias' => 'Beneficiario',
          'type' => 'LEFT',
          'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
        ],
        [
          'table' => 'beneficiarios_dependentes',
          'alias' => 'Dependente',
          'type' => 'LEFT',
          'conditions' => 'Dependente.id = Consultas.dependentes_id',
        ],
        [
          'table' => 'apoio_especialidades',
          'alias' => 'Especialidade',
          'type' => 'LEFT',
          'conditions' => 'Especialidade.id = Consultas.especialidades_id',
        ],
        [
          'table' => 'apoio_motivos_consultas',
          'alias' => 'Motivo',
          'type' => 'LEFT',
          'conditions' => 'Motivo.id = Consultas.motivos_consultas_id',
        ],
        [
          'table' => 'profissionais',
          'alias' => 'Profissional',
          'type' => 'LEFT',
          'conditions' => 'Profissional.id = Consultas.profissionais_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'LEFT',
          'conditions' => 'Empresa.id = Consultas.empresas_id',
        ]
      ]])->first();

    if($this->request->session()->read('Auth.User.profissionais_id')) {
      if($this->request->session()->read('Auth.User.profissionais_id') != $consulta['Profissional']['id']) {
        $this->Flash->error('Consulta n&atilde;o atribuida ao profissional logado!');
        return $this->redirect(['controller' => 'agendas', 'date' => date('Y-m-d', strtotime($consulta['data_hora_agendado']))]);
      }
    }

    $opt1=[
      //'Consultas.especialidades_id' => $consulta['especialidades_id'],
      'Consultas.st_consulta' => 'AC',
    ];
    if($consulta['Beneficiario']['id']) {
      $opt1['Consultas.beneficiarios_id'] = $consulta['Beneficiario']['id'];
    }
    if($consulta['Dependente']['id']) {
      $opt1['Consultas.dependentes_id'] = $consulta['Dependente']['id'];
    } else {
      $opt1[] = 'Consultas.dependentes_id IS NULL';
    }

    $consultas = $this->Consultas->find('all', [
      'conditions' => $opt1,
      'fields' => [
        'Consultas.id',
        'Consultas.data_hora_agendado',
        'Consultas.anamnese_qp_hda',
        'Consultas.tratamento_alta_retorno',
        'Consultas.afastamento_data_inicio',
        'Consultas.afastamento_data_fim',
        'Consultas.afastamento_motivo',
        'Consultas.tratamento',
        'conclusao' => "( SELECT c.nome FROM logs l
                          INNER JOIN seguranca_colaboradores c ON c.id = l.colaboradores_id
                          WHERE l.tabela = 'atendimentos' AND l.acao = 'conclusao' AND l.registro_id = Consultas.id
                          ORDER BY l.data_hora DESC LIMIT 1)"
      ],
      'order' => [
        'Consultas.data_hora_agendado' => 'DESC'
      ]
    ])->toArray();

    $opt2=[
      //'Consulta.especialidades_id' => $consulta['especialidades_id'],
      'Consulta.st_consulta' => 'AC',
    ];
    if($consulta['Beneficiario']['id']) {
      $opt2['OdontoOdontogramas.beneficiarios_id'] = $consulta['Beneficiario']['id'];
    }
    if($consulta['Dependente']['id']) {
      $opt2['OdontoOdontogramas.dependentes_id'] = $consulta['Dependente']['id'];
    } else {
      $opt2[] = 'OdontoOdontogramas.dependentes_id IS NULL';
    }

    $this->loadModel('OdontoOdontogramas');
    $odontos = $this->OdontoOdontogramas->find('all', [
      'conditions' => $opt2,
      'fields' => [
        'OdontoOdontogramas.id',
        'OdontoOdontogramas.referencia',
        'OdontoOdontogramas.consultas_id',
        'OdontoOdontogramas.data_hora_registro',
        'Consulta.data_hora_agendado',
        'Procedimento.id',
        'Procedimento.nome',
        'Procedimento.boca_dente',
        'Dente.id',
        'Dente.numero',
        'Aplicado.id',
        'Aplicado.boca_dente',
        'Aplicado.dentes_id',
        'Aplicado.face_vestibular',
        'Aplicado.face_mesial',
        'Aplicado.face_distal',
        'Aplicado.face_oclusal',
        'Aplicado.face_lingual',
        'Aplicado.face_palatina',
        'Aplicado.total_executado',
        'Aplicado.total_previsto',
        'Aplicado.total_realizado_hoje'
      ],
      'join' => [
        [
          'table' => 'consultas',
          'alias' => 'Consulta',
          'type' => 'INNER',
          'conditions' => 'Consulta.id = OdontoOdontogramas.consultas_id',
        ],
        [
          'table' => 'odonto_procedimentos_aplicados',
          'alias' => 'Aplicado',
          'type' => 'INNER',
          'conditions' => 'OdontoOdontogramas.id = Aplicado.odontogramas_id',
        ],
        [
          'table' => 'odonto_procedimentos',
          'alias' => 'Procedimento',
          'type' => 'INNER',
          'conditions' => 'Procedimento.id = Aplicado.procedimentos_id',
        ],
        [
          'table' => 'odonto_dentes',
          'alias' => 'Dente',
          'type' => 'LEFT',
          'conditions' => 'Dente.id = Aplicado.dentes_id',
        ],
      ],
      'order' => [
        'OdontoOdontogramas.data_hora_registro' => 'DESC',
        'Dente.numero',
        'Procedimento.nome'
      ]])->toArray();

    $dentes=[];
    $odontogramas=[];
    foreach ($odontos as $key => $value) {
      $face=[];
      if($value['Aplicado']['face_mesial'])     { $face[] = 'M'; }
      if($value['Aplicado']['face_vestibular']) { $face[] = 'V'; }
      if($value['Aplicado']['face_oclusal'])    { $face[] = 'O'; }
      if($value['Aplicado']['face_palatina'])   { $face[] = 'P'; }
      if($value['Aplicado']['face_distal'])     { $face[] = 'D'; }
      if($value['Aplicado']['face_lingual'])    { $face[] = 'L'; }

      $odontogramas[($value['referencia'] ? 'referencia' : 'odontograma')][date('Y-m-d', strtotime($value['Consulta']['data_hora_agendado']))][($value['Dente']['numero'] ? $value['Dente']['numero'] : 'boca')][] = [
        'id' => $value['Aplicado']['id'],
        'codigo' => $value['Procedimento']['id'],
        'procedimento' => $value['Procedimento']['nome'],
        'previsto' => $value['Aplicado']['total_previsto'],
        'realizado' => $value['Aplicado']['total_executado'],
        'feito_hoje' => $value['Aplicado']['total_realizado_hoje'],
        'face' => implode(',',$face)
      ];
    }

    $this->set(compact('consulta', 'consultas', 'odontogramas'));
  }

  public function conclusao($id = null) {
    $this->loadModel('Consultas');
    $consulta = $this->Consultas->find('all', [
      'conditions' => ['Consultas.id' => $id],
      'fields' => [
        'Consultas.id',
        'Consultas.tratamento',
        'Consultas.tratamento_alta_retorno',
        'Consultas.afastamento_data_inicio',
        'Consultas.afastamento_data_fim',
        'Consultas.afastamento_motivo',
        'Consultas.data_hora_agendado',
        'Especialidade.descricao',
        'Profissional.id',
        'Profissional.nome',
        'Motivo.descricao',
        'Empresa.nome',
        'Beneficiario.id',
        'Dependente.id',
        'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
        'nascimento' => 'IFNULL(Dependente.data_nascimento,Beneficiario.data_nascimento)',
        'funcoes' => '(SELECT GROUP_CONCAT(f.descricao) FROM beneficiarios_funcoes b INNER JOIN apoio_funcoes f ON f.id = b.funcoes_id WHERE b.beneficiarios_id = Beneficiario.id)',
        'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiario.id ORDER BY id ASC)'
      ],
      'join' => [
        [
          'table' => 'beneficiarios',
          'alias' => 'Beneficiario',
          'type' => 'LEFT',
          'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
        ],
        [
          'table' => 'beneficiarios_dependentes',
          'alias' => 'Dependente',
          'type' => 'LEFT',
          'conditions' => 'Dependente.id = Consultas.dependentes_id',
        ],
        [
          'table' => 'apoio_especialidades',
          'alias' => 'Especialidade',
          'type' => 'LEFT',
          'conditions' => 'Especialidade.id = Consultas.especialidades_id',
        ],
        [
          'table' => 'apoio_motivos_consultas',
          'alias' => 'Motivo',
          'type' => 'LEFT',
          'conditions' => 'Motivo.id = Consultas.motivos_consultas_id',
        ],
        [
          'table' => 'profissionais',
          'alias' => 'Profissional',
          'type' => 'LEFT',
          'conditions' => 'Profissional.id = Consultas.profissionais_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'LEFT',
          'conditions' => 'Empresa.id = Consultas.empresas_id',
        ]
      ]])->first();

    if($this->request->session()->read('Auth.User.profissionais_id')) {
      if($this->request->session()->read('Auth.User.profissionais_id') != $consulta['Profissional']['id']) {
        $this->Flash->error('Consulta n&atilde;o atribuida ao profissional logado!');
        return $this->redirect(['controller' => 'agendas', 'date' => date('Y-m-d', strtotime($consulta['data_hora_agendado']))]);
      }
    }

    if ($this->request->is(['patch', 'post', 'put'])) {
      if($this->request->data['afastamento_data_inicio']) {
        $this->request->data['afastamento_data_inicio'] = $this->Consultas->formatDate($this->request->data['afastamento_data_inicio']);
      } else {
        unset($this->request->data['afastamento_data_inicio']);
      }
      if($this->request->data['afastamento_data_fim']) {
        $this->request->data['afastamento_data_fim'] = $this->Consultas->formatDate($this->request->data['afastamento_data_fim']);
      } else {
        unset($this->request->data['afastamento_data_fim']);
      }
      unset($this->request->data['afastar']);

      if($this->Consultas->updateAll($this->request->data, ['id' => $consulta['id']])) {
        $this->loadComponent('Log');
        $this->request->data['id'] = $consulta['id'];
        if(!$this->Log->save('conclusao', $this->request->data)) {
           $this->Flash->error($this::MSG_ERRO_LOG);
        }
        $this->Flash->success($this::MSG_SUCESSO_EDT);
      }
      return $this->redirect(['action' => 'conclusao', $consulta['id']]);
    }

    $this->set(compact('consulta'));
  }

  public function odontograma($id = null) {
    $this->loadModel('Consultas');
    $this->loadModel('OdontoOdontogramas');

    $consulta = $this->Consultas->find('all', [
      'conditions' => ['Consultas.id' => $id],
      'fields' => [
        'Consultas.id',
        'Consultas.anamnese_qp_hda',
        'Consultas.numero_escovacoes_diarias',
        'Consultas.anamnese_odonto_obs',
        'Consultas.profissionais_id',
        'Consultas.beneficiarios_id',
        'Consultas.dependentes_id',
        'Consultas.data_hora_agendado',
        'Consultas.data_hora_atendimento',
        'Consultas.consultas_id',
        'Especialidade.descricao',
        'Profissional.id',
        'Profissional.nome',
        'Motivo.descricao',
        'Empresa.nome',
        'Beneficiario.id',
        'Dependente.id',
        'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
        'nascimento' => 'IFNULL(Dependente.data_nascimento,Beneficiario.data_nascimento)',
        'funcoes' => '(SELECT GROUP_CONCAT(f.descricao) FROM beneficiarios_funcoes b INNER JOIN apoio_funcoes f ON f.id = b.funcoes_id WHERE b.beneficiarios_id = Beneficiario.id)',
        'dependentes' => '(SELECT GROUP_CONCAT(id) FROM beneficiarios_dependentes WHERE beneficiarios_id = Beneficiario.id ORDER BY id ASC)'
      ],
      'join' => [
        [
          'table' => 'beneficiarios',
          'alias' => 'Beneficiario',
          'type' => 'LEFT',
          'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
        ],
        [
          'table' => 'beneficiarios_dependentes',
          'alias' => 'Dependente',
          'type' => 'LEFT',
          'conditions' => 'Dependente.id = Consultas.dependentes_id',
        ],
        [
          'table' => 'apoio_especialidades',
          'alias' => 'Especialidade',
          'type' => 'LEFT',
          'conditions' => 'Especialidade.id = Consultas.especialidades_id',
        ],
        [
          'table' => 'apoio_motivos_consultas',
          'alias' => 'Motivo',
          'type' => 'LEFT',
          'conditions' => 'Motivo.id = Consultas.motivos_consultas_id',
        ],
        [
          'table' => 'profissionais',
          'alias' => 'Profissional',
          'type' => 'LEFT',
          'conditions' => 'Profissional.id = Consultas.profissionais_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'Empresa',
          'type' => 'LEFT',
          'conditions' => 'Empresa.id = Consultas.empresas_id',
        ]
      ]])->first();

      if($this->request->session()->read('Auth.User.profissionais_id')) {
        if($this->request->session()->read('Auth.User.profissionais_id') != $consulta['Profissional']['id']) {
          $this->Flash->error('Consulta n&atilde;o atribuida ao profissional logado!');
          return $this->redirect(['controller' => 'agendas', 'date' => date('Y-m-d', strtotime($consulta['data_hora_agendado']))]);
        }
      }

      $consulta_id = $consulta['id'];
      if ($this->request->session()->read('Auth.User.bloqueado')) {
        if(!$consulta['data_hora_atendimento']) {
          $consulta_id = $consulta['consultas_id'];
        }
      }

      $referencia = $this->OdontoOdontogramas->find('all', [
        'conditions' => [
          'OdontoOdontogramas.consultas_id' => $consulta_id,
          'OR' => [
            'OdontoOdontogramas.beneficiarios_id' => $consulta['Beneficiario']['id'],
            'OdontoOdontogramas.dependentes_id' => $consulta['Dependente']['id']
          ]
        ],
      ])->toArray();

      if(sizeof($referencia) == 1 && !$this->request->query('referencia')) {
        if($referencia[0]['referencia']) {
          $this->Flash->error('&Eacute; necess&aacute;rio encerrar o odontograma de refer&ecirc;ncia!');
          return $this->redirect(['controller' => 'atendimentos', 'action' => 'odontograma', $consulta['id'], 'referencia' => '1']);
        }
      }

      $odontos = $this->OdontoOdontogramas->find('all', [
        'conditions' => [
          //'OdontoOdontogramas.id = (SELECT MAX(id) FROM odonto_odontogramas WHERE consultas_id = OdontoOdontogramas.consultas_id)',
          'OdontoOdontogramas.referencia' => ($this->request->query('referencia') ? '1' : '0'),
          'OdontoOdontogramas.consultas_id' => $consulta_id,
          'OR' => [
            'OdontoOdontogramas.beneficiarios_id' => $consulta['Beneficiario']['id'],
            'OdontoOdontogramas.dependentes_id' => $consulta['Dependente']['id']
          ]
        ],
        'fields' => [
          'OdontoOdontogramas.id',
          'OdontoOdontogramas.referencia',
          'OdontoOdontogramas.data_hora_registro',
          'Procedimento.id',
          'Procedimento.nome',
          'Procedimento.boca_dente',
          'Dente.id',
          'Dente.numero',
          'Aplicado.id',
          'Aplicado.boca_dente',
          'Aplicado.dentes_id',
          'Aplicado.face_vestibular',
          'Aplicado.face_mesial',
          'Aplicado.face_distal',
          'Aplicado.face_oclusal',
          'Aplicado.face_lingual',
          'Aplicado.face_palatina',
          'Aplicado.total_executado',
          'Aplicado.total_previsto',
          'Aplicado.total_realizado_hoje'
        ],
        'join' => [
          [
            'table' => 'odonto_procedimentos_aplicados',
            'alias' => 'Aplicado',
            'type' => 'INNER',
            'conditions' => 'OdontoOdontogramas.id = Aplicado.odontogramas_id',
          ],
          [
            'table' => 'odonto_procedimentos',
            'alias' => 'Procedimento',
            'type' => 'INNER',
            'conditions' => 'Procedimento.id = Aplicado.procedimentos_id',
          ],
          [
            'table' => 'odonto_dentes',
            'alias' => 'Dente',
            'type' => 'LEFT',
            'conditions' => 'Dente.id = Aplicado.dentes_id',
          ],
        ],
        'order' => [
          'Dente.numero' => 'ASC',
          'Aplicado.id' => 'ASC'
        ]])->toArray();

        $dentes=[];
        $odontogramas=[];
        foreach ($odontos as $key => $value) {
          $face=[];
          if($value['Aplicado']['face_mesial'])     { $face[] = 'M'; }
          if($value['Aplicado']['face_vestibular']) { $face[] = 'V'; }
          if($value['Aplicado']['face_oclusal'])    { $face[] = 'O'; }
          if($value['Aplicado']['face_palatina'])   { $face[] = 'P'; }
          if($value['Aplicado']['face_distal'])     { $face[] = 'D'; }
          if($value['Aplicado']['face_lingual'])    { $face[] = 'L'; }

          $odontogramas[$value['Dente']['numero']][] = [
            'id' => $value['Aplicado']['id'],
            'codigo' => $value['Procedimento']['id'],
            'procedimento' => $value['Procedimento']['nome'],
            'previsto' => $value['Aplicado']['total_previsto'],
            'realizado' => $value['Aplicado']['total_executado'],
            'feito_hoje' => $value['Aplicado']['total_realizado_hoje'],
            'face' => implode(',',$face)
          ];

          if(!isset($dentes[$value['Dente']['numero']]['executado'])) {
            $dentes[$value['Dente']['numero']]['executado']=[];
          }

          if(!isset($dentes[$value['Dente']['numero']]['previsto'])) {
            $dentes[$value['Dente']['numero']]['previsto']=[];
          }

          if($value['Aplicado']['total_executado']) {
            $dentes[$value['Dente']['numero']]['executado'] = array_unique(array_merge($dentes[$value['Dente']['numero']]['executado'], $face));
          } else {
            $dentes[$value['Dente']['numero']]['previsto'] = array_unique(array_merge($dentes[$value['Dente']['numero']]['previsto'], $face));
          }
        }

        $this->loadModel('OdontoProcedimentos');
        $procedimentos = $this->OdontoProcedimentos->find('list', [
            'conditions' => ['situacao' => '1'],
            'keyField' => 'id',
            'valueField' => function($row) {
              return ['dente'=>($row['boca_dente']?'1':'0'), 'procedimento'=>$row['nome']];
            },
            'order' => ['nome']
        ])->toArray();

        $this->set(compact('referencia','dentes','odontogramas','consulta','procedimentos'));
    }

    public function procedimento($id = null, $procedimento = null) {
        $this->loadModel('OdontoOdontogramas');
        $this->loadModel('OdontoProcedimentosAplicados');
        $this->loadModel('OdontoProcedimentosProfissionais');

        $this->loadModel('Consultas');
        $consulta = $this->Consultas->find('all', [
            'conditions' => [
                'Consultas.id' => $id,
            ],
            'fields' => [
                'Consultas.id',
                'Consultas.dependentes_id',
                'Consultas.beneficiarios_id',
                'Consultas.profissionais_id',
            ]
        ])->first();

        if($this->request->session()->read('Auth.User.profissionais_id')) {
          if($this->request->session()->read('Auth.User.profissionais_id') != $consulta['profissionais_id']) {
            $this->Flash->error('Consulta n&atilde;o atribuida ao profissional logado!');
            return $this->redirect(['controller' => 'agendas', 'date' => date('Y-m-d', strtotime($consulta['data_hora_agendado']))]);
          }
        }

        $this->loadModel('OdontoDentes');
        $dente = $this->OdontoDentes->find('list', [
            'keyField' => 'id',
            'valueField' => 'numero',
            'order' => ['numero']
        ])->toArray();

        $this->loadModel('OdontoOdontogramas');
        $odontograma = $this->OdontoOdontogramas->find('all', ['conditions' => [
          'referencia' => ($this->request->query('referencia') ? '1' : '0'),
          'consultas_id' => $consulta['id']
        ]])->first();

        if(!$odontograma) {
          $odontograma = $this->OdontoOdontogramas->newEntity();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
          $dente = array_flip($dente);
          if(!isset($this->request->data['procedimento']['dentes_id'])){
            $this->request->data['procedimento']['dentes_id'] = null;
          }

          if(isset($dente[$this->request->data['procedimento']['dentes_id']])) {
            $this->request->session()->write('Auth.User.filtro.dente', $this->request->data['procedimento']['dentes_id']);
            $this->request->data['procedimento']['dentes_id'] = $dente[$this->request->data['procedimento']['dentes_id']];
          } else if($this->request->data['procedimento']['boca_dente'] == '0') {
            $this->Flash->error('N&uacute;mero de dente inv&aacute;lido');
            return $this->redirect(['action' => 'odontograma', $consulta['id']]);
          }

          $procedimento = $this->request->data['procedimento'];
          unset($this->request->data['procedimento']);

          if(!$odontograma['id']) {
              $odontograma = $this->OdontoOdontogramas->patchEntity($odontograma, $this->request->data);
              if ($this->OdontoOdontogramas->save($odontograma)) {
                  $this->loadComponent('Log');
                  if(!$this->Log->save('odontograma', $odontograma)) {
                     $this->Flash->error($this::MSG_ERRO_LOG);
                  }
              } else {
                  $this->Flash->error($this::MSG_ERRO);
              }
          }

          $procedimento['odontogramas_id'] = $odontograma['id'];
          $procedimento = $this->OdontoProcedimentosAplicados->patchEntity($this->OdontoProcedimentosAplicados->newEntity(), $procedimento);
          if ($this->OdontoProcedimentosAplicados->save($procedimento)) {
            $this->loadComponent('Log');
            if(!$this->Log->save('procedimento', $procedimento)) {
               $this->Flash->error($this::MSG_ERRO_LOG);
            }
            $this->Flash->success(__($this::MSG_SUCESSO_ADD));
          } else {
              $this->Flash->error($this::MSG_ERRO);
          }

          return $this->redirect(['action' => 'odontograma', $consulta['id'], 'referencia' => $this->request->query('referencia'), '#' => 'odontograma']);
        }

        $this->loadModel('OdontoProcedimentosAplicados');
        $procedimento = $this->OdontoProcedimentosAplicados->find('all', [
          'conditions' => ['id' => $procedimento],
          'fields' => [
            'id', 'odontogramas_id', 'procedimentos_id', 'boca_dente', 'dentes_id',
            'face_vestibular', 'face_mesial', 'face_distal', 'face_oclusal', 'face_lingual', 'face_palatina',
            'total_executado', 'total_previsto', 'total_realizado_hoje'
          ]
        ])->first();

        $this->loadModel('OdontoProcedimentos');
        $procedimentos = $this->OdontoProcedimentos->find('list', [
            'conditions' => ['situacao' => '1'],
            'keyField' => 'id',
            'valueField' => 'nome',
            'groupField' => 'boca_dente',
            'order' => ['nome']
        ])->toArray();

        $this->set(compact('odontograma', 'consulta', 'procedimento', 'procedimentos', 'dente'));
        $this->viewBuilder()->layout('ajax');
    }

    public function encerrar($id) {
      if ($this->request->is(['patch', 'post', 'put'])) {
        $this->loadModel('OdontoOdontogramas');
        $odontograma = $this->OdontoOdontogramas->find('all', ['conditions' => [
          'consultas_id' => $id,
          'referencia' => '1'
        ]])->first();

        $this->loadModel('OdontoProcedimentosAplicados');
        $procedimentos = $this->OdontoProcedimentosAplicados->find('all', ['conditions' => [
          'odontogramas_id' => $odontograma['id'],
        ]])->toArray();

        unset($odontograma['id']);
        $odontograma['referencia'] = '0';
        $odontograma = $this->OdontoOdontogramas->patchEntity($this->OdontoOdontogramas->newEntity(), $odontograma->toArray());
        if ($this->OdontoOdontogramas->save($odontograma)) {
            $this->loadComponent('Log');
            if(!$this->Log->save('odontograma', $odontograma)) {
               $this->Flash->error($this::MSG_ERRO_LOG);
            }

            foreach ($procedimentos as $key => $value) {
              unset($value['id']);
              $value['odontogramas_id'] = $odontograma['id'];
              $procedimento = $this->OdontoProcedimentosAplicados->patchEntity($this->OdontoProcedimentosAplicados->newEntity(), $value->toArray());
              if (!$this->OdontoProcedimentosAplicados->save($procedimento)) {
                $this->Flash->error("Falha ao gravar o procedimento {$value['procedimentos_id']}");
              } else {
                $this->loadComponent('Log');
                if(!$this->Log->save('procedimento', $procedimento)) {
                   $this->Flash->error($this::MSG_ERRO_LOG);
                }
              }
            }

            $this->Flash->success(__($this::MSG_SUCESSO_ADD));
        } else {
            $this->Flash->error(__($this::MSG_ERRO));
        }
      }
      return $this->redirect(['action' => 'odontograma', $id, 'referencia' => $this->request->query('referencia')]);
    }

    public function delete($id, $consulta) {
      $this->loadModel('OdontoProcedimentosAplicados');
      $this->request->allowMethod(['post', 'delete']);
      $procedimento = $this->OdontoProcedimentosAplicados->find('all', ['conditions' => ['id' => $id]])->first();
      if ($this->OdontoProcedimentosAplicados->delete($procedimento)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('procedimento-deletado', $procedimento)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_DEL);
      }
      return $this->redirect(['action' => 'odontograma', $consulta, 'referencia' => $this->request->query('referencia')]);
    }

    public function atualizarPrevisto() {
      $this->loadModel('OdontoProcedimentosAplicados');
      if ($this->request->is(['patch', 'post', 'put'])) {
          $procedimento = $this->OdontoProcedimentosAplicados->find('all', ['conditions' => ['id' => $this->request->data['id']]])->first();

          if($this->request->data['total_previsto'] < $procedimento['total_realizado_hoje']) {
            echo json_encode(['success' => false, 'error' => true, 'msg' => 'Qt. Prevista menor que total feito hoje!']);
            exit;
          }

          if($this->request->data['total_previsto'] < $procedimento['total_executado']) {
            echo json_encode(['success' => false, 'error' => true, 'msg' => 'Qt. Prevista menor que total realizado!']);
            exit;
          }

          $data = [
            'total_previsto' => $this->request->data['total_previsto']
          ];
          if($this->OdontoProcedimentosAplicados->updateAll($data, ['id' => $this->request->data['id']])) {
            $this->loadComponent('Log');
            $this->Log->save('previsto', $this->request->data);

            echo json_encode(['success' => true, 'error' => false]);
          } else {
            echo json_encode(['success' => false, 'error' => true]);
          }
      } else {
        echo json_encode(['success' => false, 'error' => true]);
      }
      exit;
    }

    public function atualizarFeitoHoje() {
      $this->loadModel('OdontoProcedimentosAplicados');
      if ($this->request->is(['patch', 'post', 'put'])) {
          $procedimento = $this->OdontoProcedimentosAplicados->find('all', ['conditions' => ['id' => $this->request->data['id']]])->first();
          $this->request->data['total_executado'] = ($this->request->data['total_realizado_hoje']-$procedimento['total_realizado_hoje']) + $procedimento['total_executado'];

          if($this->request->data['total_realizado_hoje'] > $procedimento['total_previsto']) {
            echo json_encode(['success' => false, 'error' => true, 'msg' => 'Total feito hoje maior que quantidade prevista!']);
            exit;
          }

          if($this->request->data['total_executado'] > $procedimento['total_previsto']) {
            echo json_encode(['success' => false, 'error' => true, 'msg' => 'Total realizado maior que quantidade prevista!']);
            exit;
          }

          $data = [
            'total_realizado_hoje' => $this->request->data['total_realizado_hoje'],
            'total_executado' => $this->request->data['total_executado']
          ];

          if($this->OdontoProcedimentosAplicados->updateAll($data, ['id' => $this->request->data['id']])) {
            $this->loadComponent('Log');
            $this->Log->save('feito_hoje', $this->request->data);

            if($this->request->data['total_realizado_hoje']) {
              $realizado = [
                'profissionais_id' => $this->request->data['profissionais_id'],
                'colaborador_nome' => $this->request->data['profissional'],
                'total_realizado' => $this->request->data['total_realizado_hoje'],
                'aplicados_id' => $this->request->data['id'],
                'data_hora_registro' => date('Y-m-d H:i:s')
              ];

              $this->loadModel('OdontoProcedimentosProfissionais');
              $this->OdontoProcedimentosProfissionais->deleteAll(array("aplicados_id = {$this->request->data['id']}", "profissionais_id = {$this->request->data['profissionais_id']}"));
              $realizado = $this->OdontoProcedimentosProfissionais->patchEntity($this->OdontoProcedimentosProfissionais->newEntity(), $realizado);
              if ($this->OdontoProcedimentosProfissionais->save($realizado)) {
                $this->Log->save('realizado', $realizado);
              } else {
                echo json_encode(['success' => false, 'error' => true, 'msg' => MSG_ERRO]);
                exit;
              }
            }

            echo json_encode(['success' => true, 'error' => false, 'total_executado' => $this->request->data['total_executado']]);
          } else {
            echo json_encode(['success' => false, 'error' => true]);
          }
      } else {
        echo json_encode(['success' => false, 'error' => true]);
      }
      exit;
    }

    public function finalizar($id = null) {
      $this->loadModel('Consultas');
      if ($this->request->is(['patch', 'post', 'put'])) {
        if($this->Consultas->updateAll(['data_hora_fecha_atendimento' => date('Y-m-d H:i:s'), 'st_consulta' => 'AC'], ['id' => $this->request->data['id']])) {
          $this->loadComponent('Log');
          if(!$this->Log->save('finalizar-atendimento', $this->request->data)) {
            $this->Flash->error($this::MSG_ERRO_LOG);
          }

          $this->Flash->success(__('Atendimento encerrado!'));
          return $this->redirect(['controller' => 'agendas', 'action' => 'index', 'date' => $this->request->data['date'], 'profissional' => $this->request->data['profissional']]);
        } else {
          $this->Flash->error(__($this::MSG_ERRO));
          return $this->redirect(['controller' => 'Atendimentos', 'action' => 'anamnese', $this->request->data['id']]);
        }
      }

      $msg = ['fatal'=>[], 'warning'=>[]];

      $consulta = $this->Consultas->find('all', [
          'conditions' => [
              'Consultas.id' => $id,
          ],
          'fields' => [
              'Consultas.id',
              'Consultas.profissionais_id',
              'Consultas.data_hora_agendado',
              'Consultas.anamnese_qp_hda',
              'Consultas.numero_escovacoes_diarias',
              'Consultas.anamnese_odonto_obs',
              'Consultas.tratamento_alta_retorno',
              'Consultas.afastamento_data_inicio',
              'Consultas.afastamento_data_fim',
              'Consultas.afastamento_motivo',
              'Consultas.tratamento',
              'Beneficiario.id',
              'Beneficiario.cpf',
              'Beneficiario.nome',
              'Beneficiario.data_nascimento',
              'Dependente.id',
              'Dependente.cpf',
              'Dependente.nome',
              'Dependente.data_nascimento',
          ],
          'join' => [
              [
                  'table' => 'beneficiarios',
                  'alias' => 'Beneficiario',
                  'type' => 'LEFT',
                  'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
              ],
              [
                  'table' => 'beneficiarios_dependentes',
                  'alias' => 'Dependente',
                  'type' => 'LEFT',
                  'conditions' => 'Dependente.id = Consultas.dependentes_id',
              ]
          ]
      ])->first();

      $this->loadModel('ApoioAnamnese');
      $anamneses = $this->ApoioAnamnese->find('all', [
        'fields' => [
          'ApoioAnamnese.id',
          'ApoioAnamnese.pergunta',
          'Consulta.resposta',
          'Consulta.observacao',
        ],
        'join' => [
          [
            'table' => 'consultas_anamnese',
            'alias' => 'Consulta',
            'type' => 'LEFT',
            'conditions' => "ApoioAnamnese.id = Consulta.anamnese_id AND Consulta.consultas_id = {$id}",
          ],
        ]
        ])->toArray();

      $this->loadModel('OdontoOdontogramas');
      $odontogramas = $this->OdontoOdontogramas->find('all', [
        'conditions' => [
          'OdontoOdontogramas.consultas_id' => $consulta['id'],
          'OR' => [
            'OdontoOdontogramas.beneficiarios_id' => $consulta['Beneficiario']['id'],
            'OdontoOdontogramas.dependentes_id' => $consulta['Dependente']['id']
          ]
        ],
      ])->toArray();

      $procedimentos = $this->OdontoOdontogramas->find('all', [
        'conditions' => [
          //'OdontoOdontogramas.id = (SELECT MAX(id) FROM odonto_odontogramas WHERE consultas_id = OdontoOdontogramas.consultas_id)',
          'OdontoOdontogramas.referencia' => '0',
          'OdontoOdontogramas.consultas_id' => $consulta['id'],
          'OR' => [
            'OdontoOdontogramas.beneficiarios_id' => $consulta['Beneficiario']['id'],
            'OdontoOdontogramas.dependentes_id' => $consulta['Dependente']['id']
          ]
        ],
        'fields' => [
          'pendente' => '(SUM(Aplicado.total_previsto) - SUM(IFNULL(Aplicado.total_executado,0)))',
        ],
        'join' => [
          [
            'table' => 'odonto_procedimentos_aplicados',
            'alias' => 'Aplicado',
            'type' => 'INNER',
            'conditions' => 'OdontoOdontogramas.id = Aplicado.odontogramas_id',
          ]
        ]])->first();

      if(!$consulta['anamnese_qp_hda']) {
        $msg['fatal'][] = "Campo obrigat&oacute;rio \"QP / HDA\" na aba anamnese.";
      }

      foreach ($anamneses as $key => $value) {
        if(!$value['Consulta']['resposta']) {
          $msg['fatal'][] = "Anamnese n&atilde;o respondida \"{$value['pergunta']}\".";
        }
      }

      if(!$consulta['tratamento']) {
        $msg['fatal'][] = "Campo obrigat&oacute;rio \"Tratamento\" na aba conclus&atilde;o.";
      }

      if(!in_array($consulta['tratamento_alta_retorno'], ['0','1'])) {
        $msg['fatal'][] = "Campo obrigat&oacute;rio \"Receber alta ou retornar\" na aba conclus&atilde;o.";
      }

      if(sizeof($odontogramas)) {
        foreach ($odontogramas as $key => $value) {
          // if(sizeof($odontogramas)==1 && !$value['referencia']) {
          //   $msg['warning'][] = "Atendimento sem odontograma de refer&ecirc;ncia.";
          // }
          if(sizeof($odontogramas)==1 && $value['referencia']) {
            $msg['fatal'][] = "Odontograma de refer&ecirc;ncia não foi encerrado.";
          }
        }
      } else {
        $msg['fatal'][] = "Atendimento sem odontograma.";
      }

      if($procedimentos['pendente'] > 0) {
        $msg['warning'][] = "{$procedimentos['pendente']} procedimento(s) não realizado(s).";
      }

      $this->set(compact('consulta', 'anamneses', 'msg'));
      $this->viewBuilder()->layout('ajax');
    }

    public function imprimir($id = null) {
      $this->loadModel('Consultas');
      $consulta = $this->Consultas->find('all', [
        'conditions' => ['Consultas.id' => $id],
        'fields' => [
          'Consultas.id',
          'Consultas.data_hora_agendado',
          'Consultas.anamnese_qp_hda',
          'Consultas.numero_escovacoes_diarias',
          'Consultas.anamnese_odonto_obs',
          'Consultas.tratamento',
          'Consultas.tratamento_alta_retorno',
          'Consultas.afastamento_data_inicio',
          'Consultas.afastamento_data_fim',
          'Consultas.afastamento_motivo',
          'Especialidade.descricao',
          'Profissional.nome',
          'Motivo.descricao',
          'Empresa.nome',
          'Beneficiario.id',
          'Dependente.id',
          'sexo' => 'IF(Dependente.id,Dependente.sexo,Beneficiario.sexo)',
          'paciente' => 'IF(Dependente.id,Dependente.nome,Beneficiario.nome)',
          'nascimento' => 'IF(Dependente.id,Dependente.data_nascimento,Beneficiario.data_nascimento)',
          'funcoes' => '(SELECT GROUP_CONCAT(f.descricao) FROM beneficiarios_funcoes b INNER JOIN apoio_funcoes f ON f.id = b.funcoes_id WHERE b.beneficiarios_id = Beneficiario.id)'
        ],
        'join' => [
          [
            'table' => 'beneficiarios',
            'alias' => 'Beneficiario',
            'type' => 'LEFT',
            'conditions' => 'Beneficiario.id = Consultas.beneficiarios_id',
          ],
          [
            'table' => 'beneficiarios_dependentes',
            'alias' => 'Dependente',
            'type' => 'LEFT',
            'conditions' => 'Dependente.id = Consultas.dependentes_id',
          ],
          [
            'table' => 'apoio_especialidades',
            'alias' => 'Especialidade',
            'type' => 'LEFT',
            'conditions' => 'Especialidade.id = Consultas.especialidades_id',
          ],
          [
            'table' => 'apoio_motivos_consultas',
            'alias' => 'Motivo',
            'type' => 'LEFT',
            'conditions' => 'Motivo.id = Consultas.motivos_consultas_id',
          ],
          [
            'table' => 'profissionais',
            'alias' => 'Profissional',
            'type' => 'LEFT',
            'conditions' => 'Profissional.id = Consultas.profissionais_id',
          ],
          [
            'table' => 'empresas',
            'alias' => 'Empresa',
            'type' => 'LEFT',
            'conditions' => 'Empresa.id = Consultas.empresas_id',
          ]
        ]])->first();

      $this->loadModel('ApoioAnamnese');
      $anamneses = $this->ApoioAnamnese->find('all', [
        'fields' => [
          'ApoioAnamnese.id',
          'ApoioAnamnese.pergunta',
          'Consulta.resposta',
          'Consulta.observacao',
        ],
        'join' => [
          [
            'table' => 'consultas_anamnese',
            'alias' => 'Consulta',
            'type' => 'LEFT',
            'conditions' => "ApoioAnamnese.id = Consulta.anamnese_id AND Consulta.consultas_id = {$consulta['id']}",
          ],
        ]
        ])->toArray();

      $this->loadModel('OdontoOdontogramas');
      $odontos = $this->OdontoOdontogramas->find('all', [
        'conditions' => [
          'OdontoOdontogramas.consultas_id' => $consulta['id'],
          'OR' => [
            'OdontoOdontogramas.beneficiarios_id' => $consulta['Beneficiario']['id'],
            'OdontoOdontogramas.dependentes_id' => $consulta['Dependente']['id']
          ]
        ],
        'fields' => [
          'OdontoOdontogramas.id',
          'OdontoOdontogramas.referencia',
          'OdontoOdontogramas.consultas_id',
          'OdontoOdontogramas.data_hora_registro',
          'Consulta.data_hora_agendado',
          'Procedimento.id',
          'Procedimento.nome',
          'Procedimento.boca_dente',
          'Dente.id',
          'Dente.numero',
          'Aplicado.id',
          'Aplicado.boca_dente',
          'Aplicado.dentes_id',
          'Aplicado.face_vestibular',
          'Aplicado.face_mesial',
          'Aplicado.face_distal',
          'Aplicado.face_oclusal',
          'Aplicado.face_lingual',
          'Aplicado.face_palatina',
          'Aplicado.total_executado',
          'Aplicado.total_previsto',
          'Aplicado.total_realizado_hoje'
        ],
        'join' => [
          [
            'table' => 'consultas',
            'alias' => 'Consulta',
            'type' => 'INNER',
            'conditions' => 'Consulta.id = OdontoOdontogramas.consultas_id',
          ],
          [
            'table' => 'odonto_procedimentos_aplicados',
            'alias' => 'Aplicado',
            'type' => 'INNER',
            'conditions' => 'OdontoOdontogramas.id = Aplicado.odontogramas_id',
          ],
          [
            'table' => 'odonto_procedimentos',
            'alias' => 'Procedimento',
            'type' => 'INNER',
            'conditions' => 'Procedimento.id = Aplicado.procedimentos_id',
          ],
          [
            'table' => 'odonto_dentes',
            'alias' => 'Dente',
            'type' => 'LEFT',
            'conditions' => 'Dente.id = Aplicado.dentes_id',
          ],
        ],
        'order' => [
          'Dente.numero',
          'Procedimento.nome'
        ]])->toArray();

      $odontogramas=[];
      foreach ($odontos as $key => $value) {
        $face=[];
        if($value['Aplicado']['face_mesial'])     { $face[] = 'M'; }
        if($value['Aplicado']['face_vestibular']) { $face[] = 'V'; }
        if($value['Aplicado']['face_oclusal'])    { $face[] = 'O'; }
        if($value['Aplicado']['face_palatina'])   { $face[] = 'P'; }
        if($value['Aplicado']['face_distal'])     { $face[] = 'D'; }
        if($value['Aplicado']['face_lingual'])    { $face[] = 'L'; }

        $odontogramas[($value['referencia'] ? 'referencia' : 'odontograma')][($value['Dente']['numero'] ? $value['Dente']['numero'] : 'Boca')][] = [
          'id' => $value['Aplicado']['id'],
          'codigo' => $value['Procedimento']['id'],
          'procedimento' => $value['Procedimento']['nome'],
          'previsto' => $value['Aplicado']['total_previsto'],
          'realizado' => $value['Aplicado']['total_executado'],
          'feito_hoje' => $value['Aplicado']['total_realizado_hoje'],
          'face' => implode(',',$face)
        ];
      }

      $idade = new \DateTime($consulta['nascimento']);
      $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                <tr>
                  <td style='width: 40px;'></td>
                  <td style='width: 120px;'></td>
                  <td consulta='5'></td>
                </tr>
                <tr>
                    <td colspan='7' style='font-size: 16px; font-weight: bold; text-align: center; border: 0;'>
                        PRONTU&Aacute;RIO ODONTOL&Oacute;GICO
                        <hr><br/><br/><br/>
                    </td>
                </tr>
                <tr>
                    <th colspan='2' style='text-align: right;'>Prontu&aacute;rio</th>
                    <td colspan='5'>{$consulta['id']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Nome</th>
                    <td colspan='5'>{$consulta['paciente']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Sexo</th>
                    <td colspan='5'>{$consulta['sexo']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Idade</th>
                    <td colspan='5'>{$idade->diff(new \DateTime('now'))->y}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Data da consulta</th>
                    <td colspan='5'>{$consulta['data_hora_agendado']->format('d/m/Y')}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Empresa</th>
                    <td colspan='5'>{$consulta['Empresa']['nome']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Dentista Examinador</th>
                    <td colspan='5'>{$consulta['Profissional']['nome']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>Motivo da consulta</th>
                    <td colspan='5'>{$consulta['Motivo']['descricao']}</td>
                </tr>
                <tr>
                    <td colspan='7' style='font-size: 16px; font-weight: bold; border: 0;'>
                        <br/><br/> ANAMNESE <hr>
                    </td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;' valign='top'>QP / HDA</th>
                    <td colspan='5'>{$consulta['anamnese_qp_hda']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;'>N&ordm; de Escova&ccedil;&otilde;es Di&aacute;rias</th>
                    <td colspan='5'>{$consulta['numero_escovacoes_diarias']}</td>
                </tr>
                <tr>
                    <th colspan='2' style=' text-align: right;' valign='top'>Obs. da Anamnese Especial</th>
                    <td colspan='5'>{$consulta['anamnese_odonto_obs']}</td>
                </tr>";

      $html .= "<tr>
                  <td colspan='7' style='font-size: 16px; font-weight: bold; border: 0;'>
                      <br/><br/>ANAMNESE ESPECIAL <hr>
                  </td>
                </tr>";

      foreach ($anamneses as $key => $value) {
          $html .= "<tr>
                        <td colspan='3'>{$value['pergunta']}</td>
                        <td>{$value['Consulta']['resposta']}</td>
                        <td colspan='3'>{$value['Consulta']['observacao']}</td>
                    </tr>";
      }

      $html .= "<tr>
                  <td colspan='7' style='font-size: 16px; font-weight: bold; border: 0;'>
                      <br/><br/>ODONTOGRAMA DE REFER&Ecirc;NCIA <hr>
                  </td>
                </tr>
                <tr>
                    <th>C&oacute;digo</th>
                    <th colspan='2'>Procedimento</th>
                    <th style='width: 60px;'>Faces</th>
                    <th style='width: 60px;'>Qt. Prevista</th>
                    <th style='width: 60px;'>Feito Hoje</th>
                    <th style='width: 60px;'>Realizado</th>
                </tr>";

      if(isset($odontogramas['referencia'])) {
        foreach ($odontogramas['referencia'] as $dente => $procedimentos) {
          $html .= "<tr>
                        <th colspan='7' style='text-align: left;'>".(is_numeric($dente) ? "Dente {$dente}" : $dente)."</th>
                    </tr>";

          foreach ($procedimentos as $procedimento) {
            $html .= "<tr>
                          <td>{$procedimento['codigo']}</td>
                          <td colspan='2'>{$procedimento['procedimento']}</td>
                          <td>{$procedimento['face']}</td>
                          <td>{$procedimento['previsto']}</td>
                          <td>{$procedimento['realizado']}</td>
                          <td>{$procedimento['feito_hoje']}</td>
                      </tr>";
          }
        }
                }

      $html .= "<tr>
                  <td colspan='7' style='font-size: 16px; font-weight: bold; border: 0;'>
                      <br/><br/>ODONTOGRAMA <hr>
                  </td>
                </tr>
                <tr>
                    <th>C&oacute;digo</th>
                    <th colspan='2'>Procedimento</th>
                    <th style='width: 60px;'>Faces</th>
                    <th style='width: 60px;'>Qt. Prevista</th>
                    <th style='width: 60px;'>Feito Hoje</th>
                    <th style='width: 60px;'>Realizado</th>
                </tr>";

      if(isset($odontogramas['odontograma'])) {
        foreach ($odontogramas['odontograma'] as $dente => $procedimentos) {
          $html .= "<tr>
                        <th colspan='7' style='text-align: left;'>".(is_numeric($dente) ? "Dente {$dente}" : $dente)."</th>
                    </tr>";

          foreach ($procedimentos as $procedimento) {
            $html .= "<tr>
                          <td>{$procedimento['codigo']}</td>
                          <td colspan='2'>{$procedimento['procedimento']}</td>
                          <td style='text-align: center;'>{$procedimento['face']}</td>
                          <td style='text-align: center;'>{$procedimento['previsto']}</td>
                          <td style='text-align: center;'>{$procedimento['feito_hoje']}</td>
                          <td style='text-align: center;'>{$procedimento['realizado']}</td>
                      </tr>";
          }
        }
      }

      $html .= "<tr>
                    <td colspan='7' style='font-size: 16px; font-weight: bold; border: 0;'>
                        <br/><br/> CONCLUS&Atilde;O <hr>
                    </td>
                </tr>
                <tr>
                    <th style=' text-align: right;' valign='top'>Tratamento</th>
                    <td colspan='6'>{$consulta['tratamento']}</td>
                </tr>
                <tr>
                    <th style=' text-align: right;' valign='top'>Resultado</th>
                    <td colspan='6'>".($consulta['tratamento_alta_retorno'] ? 'Alta' : 'Retorno')."</td>
                </tr>
                <tr>
                    <th style=' text-align: right;' valign='top'>Afastou</th>
                    <td colspan='6'>".($consulta['afastamento_data_inicio'] ||  $consulta['afastamento_data_fim'] ? 'SIM' : 'N&Atilde;O')."</td>
                </tr> ";

      if($consulta['afastamento_data_inicio'] && $consulta['afastamento_data_fim']) {
        $html .= "<tr>
                      <th style=' text-align: right;' valign='top'>Inicio</th>
                      <td colspan='6'>{$consulta['afastamento_data_inicio']->format('d/m/Y')}</td>
                  </tr>
                  <tr>
                      <th style=' text-align: right;' valign='top'>Fim</th>
                      <td colspan='6'>{$consulta['afastamento_data_fim']->format('d/m/Y')}</td>
                  </tr>
                  <tr>
                      <th style=' text-align: right;' valign='top'>Afastou</th>
                      <td colspan='6'>{$consulta['afastamento_motivo']}</td>
                  </tr> ";
      }

      $html .= "</table>";

      $html .= "<br/><br/><br/><br/><br/>";

      $html .= "<div style='border-top: 1px solid #000; width: 25%; text-align: center; float: left;'>DATA</div>";
      $html .= "<div style='border-top: 1px solid #000; width: 65%; text-align: center; float: right;'>ASSINATURA E CARIMBO DO M&Eacute;DICO EXAMINADOR</div>";

      $mpdf = new mPDF();
      $mpdf->SetTitle('PRONTUARIO ODONTOLOGICO');
      $mpdf->SetDisplayMode('fullpage');

      $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
      $mpdf->AddPage('','','','','',null,null,25,15,0,0);

      $mpdf->WriteHTML("<style> th, td { border: 0; padding: 2px; font-size: 14px;}  </style>");
      $mpdf->WriteHTML($html);
      $mpdf->Output();
      exit;
    }
}
