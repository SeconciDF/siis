<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Event\Event;
use Mpdf\Mpdf;

class EmpresasAgendasMedicosController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'empresa');
    }

    public function siisv1Index() {
      $this->loadModel('Consultas');
      $this->loadModel('Empresas');

      $empresa = $this->Empresas->find('all', ['fields' => ['id', 'identificacao'], 'conditions' => ['id' => $this->request->session()->read('Auth.User.empresas_id')]])->first();
      if(!$empresa['id']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      $empresa = $this->Empresas->selecionar($empresa['identificacao']);
      if(!$empresa['cd_emp']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      $option = '';
      if ($this->request->query('cpf')) {
        $cpf = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
        $option .= " AND t.cpf = '{$cpf}' ";
      }

      if ($this->request->query('nome')) {
        $option .= " AND t.nm_trab LIKE '%{$this->request->query('nome')}%' ";
      }

      $agendados = $this->Consultas->agendados($empresa, $option);
      $this->set(compact('agendados'));
    }

    public function siisv1Add($id = null) {
      $this->loadModel('Beneficiarios');
      $this->loadModel('Empresas');
      $this->loadModel('Consultas');

      $empresa = $this->Empresas->find('all', ['fields' => ['id', 'identificacao'], 'conditions' => ['id' => $this->request->session()->read('Auth.User.empresas_id')]])->first();
      if(!$empresa['id']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      $empresa = $this->Empresas->selecionar($empresa['identificacao']);
      if(!$empresa['cd_emp']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      if($empresa['AtivoPrograma'] != 'S' && $this->request->query('exame')) {
        $this->Flash->set('Empresa n&atilde;o possui autoriza&ccedil;&atilde;o para gerar guias, procure o SECONCI!', [
          'element' => 'warning'
        ]);
        return $this->redirect(['controller' => 'mains']);
      }

      if ($this->request->is(['patch', 'post', 'put'])) {
        if(isset($this->request->data['vagaTurno'])) {
          $vagaTurno = explode('|',$this->request->data['vagaTurno']);
          $this->request->data['CodTipoHorario'] = $vagaTurno[0];
          $this->request->data['DataAgenda'] = $vagaTurno[1];
          $this->request->data['DataAgendamento'] = $vagaTurno[1];
        } else {
          $this->request->data['CodTipoHorario'] = NULL;
          $this->request->data['DataAgendamento'] = NULL;
        }

        if($this->request->query('medico')) {
          $this->Consultas->salvarConsultaMedica($this->request->data);
          if(isset($this->Consultas->success)) {
            $this->Flash->success($this->Consultas->success);
          }
          if(isset($this->Consultas->error)) {
            $this->Flash->error($this->Consultas->error);
          }
        }

        if($this->request->query('exame')) {
          $this->Consultas->salvarExames($this->request->data);
          if(isset($this->Consultas->success)) {
            $this->Flash->success($this->Consultas->success);
          }
          if(isset($this->Consultas->error)) {
            $this->Flash->error($this->Consultas->error);
          }
        }

        if(!isset($this->Consultas->error)) {
          return $this->redirect(['action' => 'siisv1-index']);
        }
      }

      $paciente = $this->Beneficiarios->selecionar($id, $empresa);
      $naturezas = $this->Consultas->getNaturezas();
      $funcoes = $this->Consultas->getFuncoes();
      $clinicas = $this->Consultas->getClinicas();

      $exames = [];
      if($this->request->query('clinica')) {
        $exames = $this->Consultas->getExames($this->request->query('clinica'));
      }

      $this->set(compact('paciente', 'empresa', 'naturezas', 'funcoes', 'clinicas', 'exames'));
    }

    public function siisv1Pesquisar() {
      $this->loadModel('Beneficiarios');
      $this->loadModel('Empresas');

      $option = [];
      if ($this->request->query('cpf')) {
          $option['t.cpf'] = preg_replace('/[^0-9]/', '', $this->request->query('cpf'));
      }

      if ($this->request->query('nome')) {
          $option['t.nm_trab'] = "%{$this->request->query('nome')}%";
      }

      $empresa = $this->Empresas->find('all', ['fields' => ['id', 'identificacao'], 'conditions' => ['id' => $this->request->session()->read('Auth.User.empresas_id')]])->first();
      if(!$empresa['id']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      $empresa = $this->Empresas->selecionar($empresa['identificacao']);
      if(!$empresa['cd_emp']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      $pacientes = $this->Beneficiarios->pesquisar($option, $empresa);
      $this->set(compact('pacientes'));
      $this->viewBuilder()->layout('ajax');
    }

    public function montarAgenda() {
      $this->loadModel('Consultas');

      $days = array('Sunday'=>'DOM','Monday'=>'SEG','Tuesday'=>'TER','Wednesday'=>'QUA','Thursday'=>'QUI','Friday'=>'SEX','Saturday'=>'SAB');
      $disponiveis = [];

      if(isset($this->request->query['medico'])) {
        $vagasConsulta = $this->Consultas->getVagasConsulta();
        $consultasAgendadas = $this->Consultas->getConsultasAgendadas();

        foreach ($vagasConsulta as $key => $value) {
          $w = date('l', strtotime($value['VagasData']));
          $disponiveis[$value['VagasData']] = [
            'day' => $days[$w],
            'date' => $value['VagasData'],
            'matutino' => [
              'ocultar' => [($value['VagasManha'] <= 0)],
              'consultas' => (int) $value['VagasManha']
            ],
            'vespertino' => [
              'ocultar' => [($value['VagasTarde'] <= 0)],
              'consultas' => (int) $value['VagasTarde']
            ],
            'classe' => $value['CodAgendaClasse'],
          ];
        }

        foreach ($consultasAgendadas as $key => $value) {
          $turno = $value['Turno'] == 'M' ? 'matutino' : 'vespertino';
          if(isset($disponiveis[$value['DataAgenda']][$turno]['consultas']) ) {
            $disponiveis[$value['DataAgenda']][$turno]['consultas'] = $disponiveis[$value['DataAgenda']][$turno]['consultas'] - $value['agendados'];
            $disponiveis[$value['DataAgenda']][$turno]['ocultar'][] = $disponiveis[$value['DataAgenda']][$turno]['consultas'] <= 0 ;
          }
        }
      }

      if(isset($this->request->query['exame'])) {
        if($this->request->query['clinica'] == '17') {
          $vagasExame = $this->Consultas->getVagasExame($this->request->query);
          $examesAgendados = $this->Consultas->getExamesAgendados($this->request->query);

          foreach ($vagasExame as $key => $value) {
            $w = date('l', strtotime($value['VagasData']));
            if(!isset($disponiveis[$value['VagasData']])) {
              $disponiveis[$value['VagasData']] = [
                'day' => $days[$w],
                'date' => $value['VagasData'],
                'matutino' => [
                  'ocultar' => [],
                ],
                'vespertino' => [
                  'ocultar' => [],
                ],
              ];

              if(isset($this->request->query['medico'])) {
                $disponiveis[$value['VagasData']]['matutino'] = [
                  'ocultar' => [true],
                  'consultas' => 0
                ];
                $disponiveis[$value['VagasData']]['vespertino'] = [
                  'ocultar' => [true],
                  'consultas' => 0
                ];
              }
            }

            $disponiveis[$value['VagasData']]['matutino']['exames'][$value['CodTipoExame']] = (int) $value['VagasManha'];
            $disponiveis[$value['VagasData']]['vespertino']['exames'][$value['CodTipoExame']] = (int) $value['VagasTarde'];
          }

          foreach ($examesAgendados as $key => $value) {
            $turno = $value['Turno'] == 'M' ? 'matutino' : 'vespertino';
            if(isset($disponiveis[$value['DataAgendamento']][$turno]['exames'][$value['CodTipoExame']]) ) {
              $disponiveis[$value['DataAgendamento']][$turno]['exames'][$value['CodTipoExame']] = $disponiveis[$value['DataAgendamento']][$turno]['exames'][$value['CodTipoExame']] - $value['agendados'];
              $disponiveis[$value['DataAgendamento']][$turno]['ocultar'][] = $disponiveis[$value['DataAgendamento']][$turno]['exames'][$value['CodTipoExame']] <= 0;
            }
          }

          foreach ($disponiveis as $key => $value) {
            foreach (explode(',', $this->request->query['exames']) as $exame) {
              if(!isset($disponiveis[$key]['matutino']['exames'][$exame])) {
                $disponiveis[$key]['matutino']['exames'][$exame] = 0;
                $disponiveis[$key]['matutino']['ocultar'][] = true;
              }
              if(!isset($disponiveis[$key]['vespertino']['exames'][$exame])) {
                $disponiveis[$key]['vespertino']['exames'][$exame] = 0;
                $disponiveis[$key]['vespertino']['ocultar'][] = true;
              }
            }
          }
        }
      }

      foreach ($disponiveis as $key => $value) {
        $disponiveis[$key]['matutino']['ocultar'] = in_array(true, $value['matutino']['ocultar']);
        $disponiveis[$key]['vespertino']['ocultar'] = in_array(true, $value['vespertino']['ocultar']);
      }

      echo json_encode($disponiveis);
      exit;
    }

    public function cancelar($id) {
      $this->loadModel('Consultas');
      if ($this->request->is(['get', 'put'])) {
        $this->Consultas->cancelarConsultaMedica($id);
        if(isset($this->Consultas->success)) {
          $this->Flash->success($this->Consultas->success);
        }
        if(isset($this->Consultas->error)) {
          $this->Flash->error($this->Consultas->error);
        }
      }
      return $this->redirect(['controller' => 'empresas-agendas-medicos', 'action' => 'siisv1-index']);
    }

    public function comprovante($id) {
      $this->loadModel('Consultas');
      $comprovante = $this->Consultas->gerarComprovante($id);
      if(!$comprovante['cd_emp']) {
        $this->Flash->error('Empresa n&atilde;o identificada!');
        return $this->redirect(['controller' => 'mains']);
      }

      $html = '';

      $html .= "<table style='width: 100%; border: 0;'>";
      $html .= "<tr>";
      $html .= "<td style='width: 25%;'> <img src='./img/seconci_logo.png' alt='' width='150' /> </td>";
      $html .= "<td style='width: 50%; font-size: 18px; text-align: center;'> Comprovante de Agendamento " . ($comprovante['CodStatusAgenda'] == '2' && !$comprovante['Comparecido'] ? "<p style='color: red;'><b>AGENDAMENTO CANCELADO</b></p>" : '') . "</td>";
      $html .= "<td style='width: 25%;'> </td>";
      $html .= "</tr>";
      $html .= "</table>";

      $html .= "<p style='width: 100%; font-size: 14px; text-align: center; color: red;'><b>Placa da Mercedes - Conjunto 3 lotes 11, 13 e 15</b></p>";
      $html .= "<p style='width: 100%; font-size: 14px; text-align: center;'>Fone:(61) 3399-1888 - Fax: (61) 3399-1888 Ramal 207</p>";

      $html .= "<table style='width: 100%;'>";
      $html .= "<tr>";
      $html .= "<td> Data do Atendimento: " . date('d/m/Y', strtotime($comprovante['DataAgenda'])) . " </td>";
      $html .= "</tr>";
      $html .= "<tr>";
      $html .= "<td style='width: 50%;'> Turno: " . ($comprovante['Turno'] == 'M' ? 'Matutino' : 'Vespertino') . " </td>";
      $html .= "<td> <b> Chegue preferencialmente 15 minutos antes. </b> </td>";
      $html .= "</tr>";
      $html .= "<tr>";
      $html .= "<td> Natureza: {$comprovante['NomeNatureza']} </td>";
      $html .= "<td> Atendimento: M&Eacute;DICO </td>";
      $html .= "</tr>";
      $html .= "<tr>";
      $html .= "<td> TRABALHA EM ALTURA: " . ($comprovante['Altura'] ? 'SIM' : 'N&Atilde;O') . " </td>";
      $html .= "<td>  </td>";
      $html .= "</tr>";
      $html .= "<tr>";
      $html .= "<td colspan='2'> Trabalhador est&aacute;(estar&aacute;) sujeito &agrave; condi&ccedil;&atilde;o estressante: SIM: ( ) N&Atilde;O: ( ) </td>";
      $html .= "</tr>";
      $html .= "</table>";

      $html .= "<br/>";

      $html .= "<table style='width: 100%;'>";
      $html .= "<tr>";
      $html .= "<td style='width: 50%;'> Paciente: {$comprovante['nm_trab']} </td>";
      $html .= "<td> CPF: {$comprovante['cpf']} </td>";
      $html .= "</tr>";
      $html .= "<tr>";
      $html .= "<td> Função: {$comprovante['NomeFuncao']} </td>";
      $html .= "<td> RG: {$comprovante['rg']} - {$comprovante['o_emissor']} </td>";
      $html .= "</tr>";
      $html .= "</table>";

      if($comprovante['CodNatureza'] == '10') {
        $html .= "<br/>";
        $html .= "<table style='width: 100%;'>";
        $html .= "<tr>";
        $html .= "<td> <b>Nova Fun&ccedil;&atilde;o:</b> {$comprovante['novaFuncao']} </td>";
        $html .= "</tr>";
        $html .= "</table>";
      }

      $html .= "<br/>";

      $html .= "<table style='width: 100%;'>";
      $html .= "<tr>";
      $html .= "<td> Empresa: {$comprovante['nm_fantasia']} </td>";
      $html .= "</tr>";
      $html .= "</table>";

      $html .= "<br/>";

      $html .= "<b>Aten&ccedil;&atilde;o</b>";

      $html .= "<p>
                  O cancelamento do agendamento só poderá ser feito até 24 horas antes do atendimento; <br/>
                  O trabalhador deve trazer um documento de identificação (Identidade, CPF, etc...); <br/>
                  A impressão deste comprovante é obrigatória, com indicação de Trabalho em Altura e condição estressante, e deverá ser apresentado no dia da consulta. <br/>
                  O atendimento médico é de 8h às 12h e é feito por ordem de chegada. O limite para comparecimento é às 11h.
                </p>";

      $mpdf = new mPDF();
      $mpdf->SetTitle('Comprovante de Agendamento');
      $mpdf->SetDisplayMode('fullpage');

      $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
      $mpdf->AddPage('','','','','',null,null,25,15,0,0);

      $mpdf->WriteHTML("<style> th, td, p { padding: 2px; font-size: 12px;} table { border: 1px solid #000; } </style>");
      $mpdf->WriteHTML($html);
      $mpdf->Output();
      exit;
    }

}
