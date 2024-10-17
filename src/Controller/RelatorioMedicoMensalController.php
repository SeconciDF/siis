<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;
use Cake\Event\Event;
use Mpdf\Mpdf;

class RelatorioMedicoMensalController extends AppController {

  public function beforeFilter(Event $event) {
    $this->request->session()->write('Auth.User.MenuActive', 'relatorio');
  }

  public function index() {
    $this->loadModel('RelatorioMedicoMensal');
    $this->paginate = [
      'order' => ['RelatorioMedicoMensal.referencia' => 'desc']
    ];

    $relatorios = $this->paginate($this->RelatorioMedicoMensal);

    $this->set(compact('relatorios'));
  }

  public function add() {
    $this->loadModel('RelatorioMedicoMensal');
    $relatorio = $this->RelatorioMedicoMensal->newEntity();
    if ($this->request->is(['patch', 'post', 'put'])) {
      $d = explode('/',"01/{$this->request->data['referencia']}");
      $this->request->data['referencia'] = implode('-', array_reverse($d));

      if(!strtotime($this->request->data['referencia'])) {
        $this->Flash->error('M&ecirc;s/Ano inv&aacute;lidados');
        return $this->redirect(['action' => 'add']);
      }

      $relatorio = $this->RelatorioMedicoMensal->patchEntity($relatorio, $this->request->data);
      if ($this->RelatorioMedicoMensal->save($relatorio)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('add', $relatorio)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'edit', $relatorio['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->set(compact('relatorio'));
  }

  public function edit($id = null) {
    $this->loadModel('RelatorioMedicoMensal');
    $relatorio = $this->RelatorioMedicoMensal->get($id);
    if ($this->request->is(['patch', 'post', 'put'])) {
      $d = explode('/',"01/{$this->request->data['referencia']}");
      $this->request->data['referencia'] = implode('-', array_reverse($d));

      if(!strtotime($this->request->data['referencia'])) {
        $this->Flash->error('M&ecirc;s/Ano inv&aacute;lidados');
        return $this->redirect(['action' => 'edit', $relatorio['id']]);
      }

      $relatorio = $this->RelatorioMedicoMensal->patchEntity($relatorio, $this->request->data);
      if ($this->RelatorioMedicoMensal->save($relatorio)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $relatorio)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'edit', $relatorio['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->set(compact('relatorio'));
  }

  public function delete($id) {
    $this->loadModel('RelatorioMedicoMensal');
    $this->request->allowMethod(['post', 'delete']);
    $relatorio = $this->RelatorioMedicoMensal->find('all', ['conditions' => ['id' => $id]])->first();
    if ($this->RelatorioMedicoMensal->delete($relatorio)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
      return $this->redirect(['action' => 'index']);
    }
    return $this->redirect(['action' => 'index']);
  }

  public function imprimir($ano = null) {
    if(!$ano) $ano = date('Y');
    $old = $ano-1;

    $this->loadModel('RelatorioMedicoMensal');
    $results = $this->RelatorioMedicoMensal->find('all', [
      'conditions' => [
        'or' => [
          "referencia > '{$ano}-01-01'",
          "referencia > '{$old}-01-01'"
        ]
      ],
      'order' => ['referencia' => 'asc'],
      ])->toArray();

    if(!$results) {
      $this->Flash->set('nenhum resultado', ['element' => 'warning']);
      return $this->redirect(['action' => 'index']);
    }

    $legenda['atividades_conunitarias'] = [
      'ATIVIDADES', 'Total de a&ccedil;&otilde;es comunit&aacute;rias'
    ];
    $legenda['atividades_conunitarias_qtd'] = [
      'ATIVIDADES', 'N&uacute;mero de participantes'
    ];
    $legenda['atividades_palestras'] = [
      'ATIVIDADES', 'Palestras'
    ];
    $legenda['atividades_palestras_qtd'] = [
      'ATIVIDADES', 'N&uacute;mero de participantes'
    ];
    $legenda['atividades_cipa'] = [
      'ATIVIDADES', 'Curso de CIPA'
    ];
    $legenda['atividades_cipa_qtd'] = [
      'ATIVIDADES', 'N&uacute;mero de participantes'
    ];
    $legenda['itinerante_dias'] = [
      'ATIVIDADES', 'SECONCI Itinerante'
    ];
    $legenda['itinerante_empresas'] = [
      'ATIVIDADES', 'N&uacute;mero de Empresas'
    ];
    $legenda['pcmso_novos'] = [
      'PCMSO', 'Novos'
    ];
    $legenda['pcmso_encerrados'] = [
      'PCMSO', 'Encerrados'
    ];
    $legenda['pcmso_andamentos'] = [
      'PCMSO', 'Andamentos'
    ];
    $legenda['pcmso_renovados'] = [
      'PCMSO', 'Renovados no per&iacute;odo'
    ];
    $legenda['pcmso_vigentes'] = [
      'PCMSO', 'Contratos Vigentes'
    ];
    $legenda['itinerante_arterial'] = [
      'ATENDIMENTOS SECONCI ITINERANTE', 'Press&atilde;o Arterial'
    ];
    $legenda['itinerante_glicemia'] = [
      'ATENDIMENTOS SECONCI ITINERANTE', 'Glicemia'
    ];
    $legenda['itinerante_imc'] = [
      'ATENDIMENTOS SECONCI ITINERANTE', 'IMC'
    ];
    $legenda['itinerante_acuidade'] = [
      'ATENDIMENTOS SECONCI ITINERANTE', 'Acuidade Visual'
    ];
    $legenda['itinerante_atendidos'] = [
      'ATENDIMENTOS SECONCI ITINERANTE', 'N&uacute;mero de atendimentos'
    ];
    $legenda['outros_audiometria'] = [
      'OUTROS ATENDIMENTOS', 'Audiometria'
    ];
    $legenda['outros_acuidade'] = [
      'OUTROS ATENDIMENTOS', 'Acuidade Visual'
    ];
    $legenda['outros_ecg'] = [
      'OUTROS ATENDIMENTOS', 'ECG'
    ];
    $legenda['outros_eeg'] = [
      'OUTROS ATENDIMENTOS', 'EEG'
    ];
    $legenda['outros_espirometria'] = [
      'OUTROS ATENDIMENTOS', 'Espirometria'
    ];
    $legenda['outros_laboratoriais'] = [
      'OUTROS ATENDIMENTOS', 'Exames Laboratoriais'
    ];
    $legenda['outros_raiox'] = [
      'OUTROS ATENDIMENTOS', 'Raio-X Torax'
    ];
    $legenda['outros_homologacoes'] = [
      'OUTROS ATENDIMENTOS', 'Homologa&ccedil;&otilde;es'
    ];
    $legenda['outras_clinicas_externas'] = [
      'OUTROS ATENDIMENTOS', 'Cl&iacute;nicas externas'
    ];
    $legenda['outros_ocupacionais_externas'] = [
      'OUTROS ATENDIMENTOS', 'Consultas Ocupacionais Externas'
    ];
    $legenda['outros_assistenciais_externas'] = [
      'OUTROS ATENDIMENTOS', 'Consultas Assistenciais Externas'
    ];
    $legenda['outros_assistenciais_sede'] = [
      'OUTROS ATENDIMENTOS', 'Consultas Assistenciais Sede'
    ];

      $relatorios = array();
      foreach ($results as $value) {
        if($value['referencia']->format('Y') == $old) {
          if(!isset($relatorios[$value['referencia']->format('Y')])) {
            $relatorios[$value['referencia']->format('Y')] = $value->toArray();
          } else {
            $relatorios[$value['referencia']->format('Y')]['atividades_conunitarias'] += $value['atividades_conunitarias'];
            $relatorios[$value['referencia']->format('Y')]['atividades_conunitarias_qtd'] += $value['atividades_conunitarias_qtd'];
            $relatorios[$value['referencia']->format('Y')]['atividades_palestras'] += $value['atividades_palestras'];
            $relatorios[$value['referencia']->format('Y')]['atividades_palestras_qtd'] += $value['atividades_palestras_qtd'];
            $relatorios[$value['referencia']->format('Y')]['atividades_cipa'] += $value['atividades_cipa'];
            $relatorios[$value['referencia']->format('Y')]['atividades_cipa_qtd'] += $value['atividades_cipa_qtd'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_dias'] += $value['itinerante_dias'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_empresas'] += $value['itinerante_empresas'];
            $relatorios[$value['referencia']->format('Y')]['pcmso_novos'] += $value['pcmso_novos'];
            $relatorios[$value['referencia']->format('Y')]['pcmso_encerrados'] += $value['pcmso_encerrados'];
            $relatorios[$value['referencia']->format('Y')]['pcmso_andamentos'] += $value['pcmso_andamentos'];
            $relatorios[$value['referencia']->format('Y')]['pcmso_renovados'] += $value['pcmso_renovados'];
            $relatorios[$value['referencia']->format('Y')]['pcmso_vigentes'] += $value['pcmso_vigentes'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_arterial'] += $value['itinerante_arterial'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_glicemia'] += $value['itinerante_glicemia'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_imc'] += $value['itinerante_imc'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_acuidade'] += $value['itinerante_acuidade'];
            $relatorios[$value['referencia']->format('Y')]['itinerante_atendidos'] += $value['itinerante_atendidos'];
            $relatorios[$value['referencia']->format('Y')]['outros_audiometria'] += $value['outros_audiometria'];
            $relatorios[$value['referencia']->format('Y')]['outros_acuidade'] += $value['outros_acuidade'];
            $relatorios[$value['referencia']->format('Y')]['outros_ecg'] += $value['outros_ecg'];
            $relatorios[$value['referencia']->format('Y')]['outros_eeg'] += $value['outros_eeg'];
            $relatorios[$value['referencia']->format('Y')]['outros_espirometria'] += $value['outros_espirometria'];
            $relatorios[$value['referencia']->format('Y')]['outros_laboratoriais'] += $value['outros_laboratoriais'];
            $relatorios[$value['referencia']->format('Y')]['outros_raiox'] += $value['outros_raiox'];
            $relatorios[$value['referencia']->format('Y')]['outros_homologacoes'] += $value['outros_homologacoes'];
            $relatorios[$value['referencia']->format('Y')]['outras_clinicas_externas'] += $value['outras_clinicas_externas'];
            $relatorios[$value['referencia']->format('Y')]['outros_ocupacionais_externas'] += $value['outros_ocupacionais_externas'];
            $relatorios[$value['referencia']->format('Y')]['outros_assistenciais_externas'] += $value['outros_assistenciais_externas'];
            $relatorios[$value['referencia']->format('Y')]['outros_assistenciais_sede'] += $value['outros_assistenciais_sede'];
          }
        } else if($value['referencia']->format('Y') == $ano) {
          $relatorios[$value['referencia']->format('Y')][$value['referencia']->format('m')] = $value->toArray();
        }
      }

      $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
      <thead>
      <tr>
      <td colspan='15' style='font-size: 18px; font-weight: bold; text-align: center; border: 0;'>
        RELAT&Oacute;RIO MENSAL - GER&Ecirc;NCIA M&Eacute;DICA - {$ano}
      </td>
      </tr>
      </thead>";

      $header = "<tr>
                    <th style='width: 25%;'>DESCRI&Ccedil;&Atilde;O</th>
                    <th style='width: 10%;'>ANO {$old}</th>
                    <th style='width: 5%;'>JAN</th>
                    <th style='width: 5%;'>FEV</th>
                    <th style='width: 5%;'>MAR</th>
                    <th style='width: 5%;'>ABR</th>
                    <th style='width: 5%;'>MAI</th>
                    <th style='width: 5%;'>JUN</th>
                    <th style='width: 5%;'>JUL</th>
                    <th style='width: 5%;'>AGO</th>
                    <th style='width: 5%;'>SET</th>
                    <th style='width: 5%;'>OUT</th>
                    <th style='width: 5%;'>NOV</th>
                    <th style='width: 5%;'>DEZ</th>
                    <th style='width: 5%;'>TOTAL</th>
                </tr>";

      $html .= "<tbody>";
      $title = '';
      foreach ($legenda as $key => $value) {
        if($title != $value[0]) {
          $title = $value[0];

          if($title == 'OUTROS ATENDIMENTOS') {
            $html .= "<tr><td style='border: 0;'><br/><br/><br/><br/><br/></td></tr>";
          }

          $html .= "<tr><td style='border: 0;'><br/></td></tr>";
          $html .= "<tr><th colspan='15' style='background: #ddd; font-size: 16px; font-weight: bold;'>{$title}</th></tr>";
          $html .= $header;
        }

        $html .= "<tr>";
        $html .= "<td style='text-align: left;'>{$value[1]}</td>";
        $html .= "<td>{$relatorios[$old][$key]}</td>";

        $total = 0;
        for ($i=1; $i <= 12; $i++) {
          $n = str_pad($i, 2, '0', STR_PAD_LEFT);
          if(isset($relatorios[$ano][$n][$key])) {
            $html .= "<td>{$relatorios[$ano][$n][$key]}</td>";
            $total += $relatorios[$ano][$n][$key];
          } else {
            $html .= "<td></td>";
          }
        }

        $html .= "<td>{$total}</td>";
        $html .= "</tr>";
      }
      $html .= "</tbody>";
      $html .= "</table>";


      $mpdf = new mPDF();
      $mpdf->SetTitle('GERENCIA MEDICA');
      $mpdf->SetDisplayMode('fullpage');

      $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
      $mpdf->AddPage('L','','','','',null,null,25,15,0,0);

      $mpdf->WriteHTML("<style> th, td { border: 1px solid #ddd; padding: 2px; font-size: 12px; text-align: center; } td { border-top: 0;} </style>");
      $mpdf->WriteHTML($html);
      //$mpdf->Output('CONSULTAS REALIZADAS POR EMPRESA.pdf', 'D');
      $mpdf->Output();
      exit;
    }

    public function examesPorMesAno($ano, $mes) {
      $connection = ConnectionManager::get('siis');
      $results = $connection->execute("
        SELECT 'AUD' as CodTipoExame, COUNT(CodAudiometria) as total FROM audiometria
        WHERE YEAR(DataAudiometria) = $ano
        AND MONTH(DataAudiometria) = $mes
        AND (PppInterpretacao= 'A' OR PppInterpretacao = 'N')
        GROUP BY MONTH(DataAudiometria)
        UNION
        SELECT 'ESP' as CodTipoExame, COUNT(CodConsulta) as total FROM consulta c
        WHERE YEAR(c.DataEspirometria) = $ano
        AND MONTH(c.DataEspirometria) = $mes
        AND (c.Apto = 's' OR c.Apto = 'n')
        UNION
        SELECT 'ECG' as CodTipoExame, COUNT(CodConsulta) as total FROM consulta c
        WHERE YEAR(c.DataEcg) = $ano
        AND MONTH(c.DataEcg) = $mes
        AND (c.Apto = 's' OR c.Apto = 'n')
        UNION
        SELECT 'EEG' as CodTipoExame, COUNT(CodConsulta) as total FROM consulta c
        WHERE YEAR(c.DataEeg) = $ano
        AND MONTH(c.DataEeg) = $mes
        AND (c.Apto = 's' OR c.Apto = 'n')
        UNION
        SELECT 'RXT' as CodTipoExame, COUNT(CodConsulta) as total FROM consulta c
        WHERE YEAR(c.DataRxTorax) = $ano
        AND MONTH(c.DataRxTorax) = $mes
        AND (c.Apto = 's' OR c.Apto = 'n')
        UNION
        SELECT 'ACV' as CodTipoExame, COUNT(CodConsulta) as total FROM consulta c
        WHERE YEAR(c.DataAcVisual) = $ano
        AND MONTH(c.DataAcVisual) = $mes
        AND (c.Apto = 's' OR c.Apto = 'n')
        UNION
        SELECT 'HOM' as CodTipoExame, COUNT(CodHomologacao) as total FROM homologacao
        WHERE YEAR(HomologacaoData) = $ano
        AND MONTH(HomologacaoData) = $mes
        AND (Conclusao= 's' OR Conclusao = 'n')
        UNION
        SELECT 'ASSsede' as CodTipoExame, COUNT(CO.CodConsulta) as total FROM consulta CO
        INNER JOIN natureza NA ON (NA.CodNatureza = CO.CodNatureza)
        WHERE CodProfissional != 0
        AND NA.CodNatureza = '2'
        AND YEAR(CO.DataConsulta) = $ano
        AND MONTH(CO.DataConsulta) = $mes
        AND (CO.Apto = 's' OR CO.Apto = 'n')
        GROUP BY MONTH(CO.DataConsulta), CO.codNatureza
      ")->fetchAll('assoc');

      $exames = [];
      foreach ($results as $value) {
        $exames["e{$value['CodTipoExame']}"] = $value['total'];
      }

      echo json_encode($exames);
      exit;
    }

  }
