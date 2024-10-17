<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Mpdf\Mpdf;

class RelatoriosMedicosController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'relatorio');
    }

    public function estatisticoAnualMedico() {
      if($this->request->query('ano')) {
        $this->loadModel('Relatorios');
        $relatorios = [];
        $complementares = [];

        $audiometrias = $this->Relatorios->getAudiometria($this->request->query('ano'));
        foreach ($audiometrias as $key => $value) {
          $exames[$value['natureza']][$value['mes']] = $value['quantidade'];
        }

        $atendimentos = $this->Relatorios->getAtendimento($this->request->query('ano'));
        foreach ($atendimentos as $key => $value) {
          $relatorios[$value['natureza']][$value['mes']] = $value['quantidade'];
        }

        $homologacoes = $this->Relatorios->getHomologacao($this->request->query('ano'));
        foreach ($homologacoes as $key => $value) {
          $relatorios[$value['natureza']][$value['mes']] = $value['quantidade'];
        }

        $espirometrias = $this->Relatorios->getEspirometria($this->request->query('ano'));
        foreach ($espirometrias as $key => $value) {
          $relatorios[$value['natureza']][$value['mes']] = $value['quantidade'];
        }

        $acuidades = $this->Relatorios->getAcuidadeVisual($this->request->query('ano'));
        foreach ($acuidades as $key => $value) {
          $relatorios[$value['natureza']][$value['mes']] = $value['quantidade'];
        }

        $assistenciais['Consulta - Assistencial'] = isset($relatorios['Consulta - Assistencial']) ? $relatorios['Consulta - Assistencial'] : null;
        $assistenciais['Homologacao'] = isset($relatorios['Homologacao']) ? $relatorios['Homologacao'] : null;
        unset($relatorios['Consulta - Assistencial'], $relatorios['Homologacao']);

        $ocupacionais['Admissional'] = isset($relatorios['Admissional']) ? $relatorios['Admissional'] : null;
        $ocupacionais['Demissional'] = isset($relatorios['Demissional']) ? $relatorios['Demissional'] : null;
        $ocupacionais['Periodico'] = isset($relatorios['Periodico']) ? $relatorios['Periodico'] : null;
        $ocupacionais['Retorno ao Trabalho'] = isset($relatorios['Retorno ao Trabalho']) ? $relatorios['Retorno ao Trabalho'] : null;
        $ocupacionais['Mudança de Função'] = isset($relatorios['Mudança de Função']) ? $relatorios['Mudança de Função'] : null;
        unset($relatorios['Admissional'], $relatorios['Demissional'], $relatorios['Periodico'], $relatorios['Retorno ao Trabalho'], $relatorios['Mudança de Função']);

        $ecs = $this->Relatorios->getExamesComplementares($this->request->query('ano'));
        foreach ($ecs as $key => $value) {
          $complementares[$value['TipoExame']][$value['Exame']][$value['MesReferencia']] = $value['quantidade'];
        }

        $exames['Acuidade Visual'] = isset($relatorios['Acuidade Visual']) ? $relatorios['Acuidade Visual'] : null;
        $exames['Eletrocardiograma'] = isset($complementares['Exames Complementares Seconci']['Eletrocardiograma']) ? $complementares['Exames Complementares Seconci']['Eletrocardiograma'] : null;
        $exames['Espirometria'] = isset($relatorios['Espirometria']) ? $relatorios['Espirometria'] : null;
        unset($complementares['Exames Complementares Seconci']);


        //$assistenciais['Acuidade Visual'] = isset($complementares['Saude Assistenciais']['Acuidade Visual']) ? $complementares['Saude Assistenciais']['Acuidade Visual'] : null;
        //$assistenciais['Eletrocardiograma'] = isset($complementares['Saude Assistenciais']['Eletrocardiograma']) ? $complementares['Saude Assistenciais']['Eletrocardiograma'] : null;
        $assistenciais['Acoes Comunitarias'] = isset($complementares['Saude Assistenciais']['Acoes Comunitarias']) ? $complementares['Saude Assistenciais']['Acoes Comunitarias'] : null;
        unset($complementares['Saude Assistenciais']);

        unset($complementares['Exames Complementares']);
        unset($complementares['Ocupacionais Clinicas']);
        unset($complementares['Exames Laboratoriais']);

        $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
        <thead>
        <tr>
        <td colspan='14' style='font-size: 18px; font-weight: bold; text-align: center; border: 0;'>
          RELAT&Oacute;RIO ESTAT&Iacute;STICO ANUAL - GER&Ecirc;NCIA M&Eacute;DICA - {$this->request->query('ano')}
        </td>
        <td colspan='14' style='font-size: 18px; font-weight: bold; text-align: center; border: 0;'>
          &nbsp;<br/><br/><br/><br/>
        </td>
        </tr>
        </thead>";

        $header = "<tr>
                      <th style='width: 20%;'>DESCRI&Ccedil;&Atilde;O</th>
                      <th style='width: 6%;'>JAN</th>
                      <th style='width: 6%;'>FEV</th>
                      <th style='width: 6%;'>MAR</th>
                      <th style='width: 6%;'>ABR</th>
                      <th style='width: 6%;'>MAI</th>
                      <th style='width: 6%;'>JUN</th>
                      <th style='width: 6%;'>JUL</th>
                      <th style='width: 6%;'>AGO</th>
                      <th style='width: 6%;'>SET</th>
                      <th style='width: 6%;'>OUT</th>
                      <th style='width: 6%;'>NOV</th>
                      <th style='width: 6%;'>DEZ</th>
                      <th style='width: 8%;'>TOTAL</th>
                  </tr>";

        $html .= "<tbody>";

        if($assistenciais) {
          $html .= "<tr><td colspan='14' style='font-size: 14px; font-weight: bold; text-align: center; border: 0;'>
                      SA&Uacute;DE ASSIST&Eacute;NCIAL
                    </td></tr>";
          $html .= $header;
          foreach ($assistenciais as $natureza => $value) {
            $html .= "<tr><td style='text-align: left;'>{$natureza}</td>";

            $total = 0;
            for ($i=1; $i <= 12; $i++) {
              if(isset($value[$i])) {
                $html .= "<td>{$value[$i]}</td>";
                $total += $value[$i];
              } else {
                $html .= "<td>0</td>";
              }
            }

            $html .= "<td>{$total}</td></tr>";
          }
        }

        if($ocupacionais) {
          $html .= "<tr><td colspan='14' style='font-size: 14px; font-weight: bold; text-align: center; border: 0;'>
                      <br/> SA&Uacute;DE OCUPACIONAL
                    </td></tr>";
          $html .= $header;
          foreach ($ocupacionais as $natureza => $value) {
            $html .= "<tr><td style='text-align: left;'>{$natureza}</td>";

            $total = 0;
            for ($i=1; $i <= 12; $i++) {
              if(isset($value[$i])) {
                $html .= "<td>{$value[$i]}</td>";
                $total += $value[$i];
              } else {
                $html .= "<td>0</td>";
              }
            }

            $html .= "<td>{$total}</td></tr>";
          }
        }

        if($exames) {
          $html .= "<tr><td colspan='14' style='font-size: 14px; font-weight: bold; text-align: center; border: 0;'>
                      <br/> EXAMES COMPLEMENTARES SECONCI
                    </td></tr>";
          $html .= $header;
          foreach ($exames as $natureza => $value) {
            $html .= "<tr><td style='text-align: left;'>{$natureza}</td>";

            $total = 0;
            for ($i=1; $i <= 12; $i++) {
              if(isset($value[$i])) {
                $html .= "<td>{$value[$i]}</td>";
                $total += $value[$i];
              } else {
                $html .= "<td>0</td>";
              }
            }

            $html .= "<td>{$total}</td></tr>";
          }
        }

        if(isset($complementares)) {
          $outros['EXAMES COMPLEMENTARES CLINICAS'] = isset($complementares['Exames Complementares Clinicas']) ? $complementares['Exames Complementares Clinicas'] : [];
          $outros['PALESTRAS'] = isset($complementares['Palestras']) ? $complementares['Palestras'] : [];
          $outros['CONTRATOS'] = isset($complementares['Contratos']) ? $complementares['Contratos'] : [];

          foreach ($outros as $title => $complementar) {
            if(!$complementar) {
              continue;
            }

            $html .= "<tr><td colspan='14' style='font-size: 14px; font-weight: bold; text-align: center; border: 0;'>
                        <br/> {$title}
                      </td></tr>";
            $html .= $header;
            foreach ($complementar as $exame => $value) {
              $html .= "<tr><td style='text-align: left;'>{$exame}</td>";

              $total = 0;
              for ($i=1; $i <= 12; $i++) {
                if(isset($value[$i])) {
                  $html .= "<td>{$value[$i]}</td>";
                  $total += $value[$i];
                } else {
                  $html .= "<td>0</td>";
                }
              }

              $html .= "<td>{$total}</td></tr>";
            }
          }
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
    }

    public function produtividadeMedica() {
      if($this->request->query('referencia')) {
        $this->loadModel('Relatorios');
        $referencia = explode('/', $this->request->query('referencia'));
        $profissionais = [0];
        $cabecalho = [];
        $relatorio = [];
        $totalGeral = [
          'realizadas' => 0,
          'previstas' => 0,
          'desvio' => 0
        ];

        $d = explode('/',"01/{$this->request->query('referencia')}");
        $inicio = implode('-', array_reverse($d));
        $fim = date("Y-m-t", strtotime($inicio));

        $semanas = [];
        $begin = new \DateTime($inicio);
        $end = new \DateTime($fim);
        for($i = $begin; $i <= $end; $i->modify('+1 day')) {
          if(in_array($i->format("w"), ['0','6'])) {
            continue;
          }

          $semanas[$i->format("W")-1][] = $i->format("Y-m-d");
          if(sizeof($semanas) > 1 && ($i->format("W")-1) == 0) {
            $semanas[52] = $semanas[0];
          }
        }

        $week = [];
        foreach ($semanas as $k => $v) {
          $week[] = $k;
          $semanas[$k] = [
              'de' => reset($v),
              'ate' => end($v),
              'dias' => sizeof($v)
          ];
        }

        $consultas = $this->Relatorios->getRealizadasConsulta($referencia[1], $referencia[0]);
        $audiometrias = $this->Relatorios->getRealizadasAudiometria($referencia[1], $referencia[0]);
        $homologacoes = $this->Relatorios->getRealizadasHomologacao($referencia[1], $referencia[0]);
        $manuais = $this->Relatorios->getRealizadasManuais($referencia[1], $referencia[0]);

        $agendamentos = $this->Relatorios->getAgendamentos($referencia[1], $referencia[0]);
        foreach ($agendamentos as $key => $value) {
          if($value['Nome'] != 'AUDIOMETRIA') {
            $value['Nome'] = 'MEDICO';
          }

          if($value['falta']) {
            if(!isset($cabecalho[$value['semana']][$value['Nome']]['falta'][$value['Turno']]['quantidade'])) {
              $cabecalho[$value['semana']][$value['Nome']]['falta'][$value['Turno']]['quantidade'] = 0;
            }
            $cabecalho[$value['semana']][$value['Nome']]['falta'][$value['Turno']]['quantidade'] += $value['quantidade'];
          } else {
            if(!isset($cabecalho[$value['semana']][$value['Nome']]['comparecido'][$value['Turno']]['quantidade'])) {
              $cabecalho[$value['semana']][$value['Nome']]['comparecido'][$value['Turno']]['quantidade'] = 0;
            }
            $cabecalho[$value['semana']][$value['Nome']]['comparecido'][$value['Turno']]['quantidade'] += $value['quantidade'];
          }
        }

        // foreach ($audiometrias as $key => $value) {
        //   $profissionais[] = $value['codigo'];
        //   $relatorio[$value['semana']]['audiometria'][$value['codigo']] = [
        //     'nome' => $value['Nome'],
        //     'realizadas' => $value['Realizadas'],
        //     'previstas' => 0
        //   ];
        // }

        foreach ($consultas as $key => $value) {
          $profissionais[] = $value['codigo'];
          $relatorio[$value['semana']]['medico'][$value['codigo']] = [
            'nome' => $value['Nome'],
            'realizadas' => $value['Realizadas'],
            'previstas' => 0
          ];
        }

        foreach ($homologacoes as $key => $value) {
          $profissionais[] = $value['codigo'];
          if(isset($relatorio[$value['semana']]['medico'][$value['codigo']]['realizadas'])) {
            $relatorio[$value['semana']]['medico'][$value['codigo']]['realizadas'] += $value['Realizadas'];
          } else {
            $relatorio[$value['semana']]['medico'][$value['codigo']] = [
              'nome' => $value['Nome'],
              'realizadas' => $value['Realizadas']
            ];
          }
        }

        $keys = array_keys($relatorio);
        foreach ($manuais as $key => $value) {
          $profissionais[] = $value['codigo'];
          if(isset($relatorio[$keys[($value['semana']-1)]]['medico'][$value['codigo']]['realizadas'])) {
            $relatorio[$keys[($value['semana']-1)]]['medico'][$value['codigo']]['realizadas'] += $value['Realizadas'];
          } else {
            $relatorio[$keys[($value['semana']-1)]]['medico'][$value['codigo']] = [
              'nome' => $value['Nome'],
              'realizadas' => $value['Realizadas']
            ];
          }
        }

        $profissionais = array_unique($profissionais);
        $profissionais = implode(',', $profissionais);
        $previstos = $this->Relatorios->getPrevistos($referencia[1], $referencia[0], $profissionais);

        foreach ($previstos as $key => $value) {
          if(!isset($keys[($value['Semana']-1)])) {
            continue;
          }
          if(isset($relatorio[$keys[($value['Semana']-1)]]['medico'][$value['codigo']]['realizadas'])) {
            $relatorio[$keys[($value['Semana']-1)]]['medico'][$value['codigo']]['previstas'] = $value['CapacidadeAtendimento'];
          }
        }

        foreach ($previstos as $key => $value) {
          if(!isset($keys[($value['Semana']-1)])) {
            continue;
          }
          if(isset($relatorio[$keys[($value['Semana']-1)]]['audiometria'][$value['codigo']]['realizadas'])) {
            $relatorio[$keys[($value['Semana']-1)]]['audiometria'][$value['codigo']]['previstas'] = $value['CapacidadeAtendimento'];
          }
        }

        $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
        <thead>
        <tr>
        <td colspan='5' style='font-size: 18px; font-weight: bold; text-align: center; border: 0;'>
          RELAT&Oacute;RIO DE PRODUTIVIDADE M&Eacute;DICA - {$this->request->query('referencia')}
        </td>
        </tr>
        </thead>";

        $html .= "<tbody>";

        // pr($cabecalho);
        // exit;

        $count = 0;
        foreach ($relatorio as $key => $value) {
          $count++;
          $semana = '';
          if(isset($semanas[$key])) {
            $semana = "{$semanas[$key]['dias']} DIAS &Uacute;TEIS <br/>DE ".date('d', strtotime($semanas[$key]['de'])).' A '.date('d/m', strtotime($semanas[$key]['ate']));
          }

          $html .= "<tr><th colspan='5' style='text-align: right; border: 0; border-bottom: 1px solid #ddd; padding-top: 25px;'>&nbsp;</th></tr>";
          $html .= "<tr> <td style='width: 20%; text-align: left;'>";
          $html .= "{$count}&ordm; SEMANA - {$semana}";
          $html .= "</td> <td style='width: 20%; text-align: left;'>";

          if(isset($cabecalho[$key]['MEDICO']['comparecido']['M']['quantidade'])) {
            $html .= "Marcadas Medico Mat: {$cabecalho[$key]['MEDICO']['comparecido']['M']['quantidade']}<br/>";
          } else {
            $html .= "Marcadas Medico Mat: 0<br/>";
          }

          if(isset($cabecalho[$key]['MEDICO']['comparecido']['V']['quantidade'])) {
            $html .= "Marcadas Medico Vesp: {$cabecalho[$key]['MEDICO']['comparecido']['V']['quantidade']}<br/>";
          } else {
            $html .= "Marcadas Medico Vesp: 0";
          }

          $html .= "</td> <td style='width: 20%; text-align: left;'>";

          if(isset($cabecalho[$key]['MEDICO']['falta']['M']['quantidade'])) {
            $html .= "Faltas Medico Mat: {$cabecalho[$key]['MEDICO']['falta']['M']['quantidade']}<br/>";
          } else {
            $html .= "Faltas Medico Mat: 0<br/>";
          }

          if(isset($cabecalho[$key]['MEDICO']['falta']['V']['quantidade'])) {
            $html .= "Faltas Medico Vesp: {$cabecalho[$key]['MEDICO']['falta']['V']['quantidade']}<br/>";
          } else {
            $html .= "Faltas Medico Vesp: 0";
          }

          $html .= "</td> <td style='width: 20%; text-align: left;'>";

          if(isset($cabecalho[$key]['AUDIOMETRIA']['comparecido']['M']['quantidade'])) {
            $html .= "Marcadas Audio Mat: {$cabecalho[$key]['AUDIOMETRIA']['comparecido']['M']['quantidade']}<br/>";
          } else {
            $html .= "Marcadas Audio Mat: 0<br/>";
          }

          if(isset($cabecalho[$key]['AUDIOMETRIA']['comparecido']['V']['quantidade'])) {
            $html .= "Marcadas Audio Vesp: {$cabecalho[$key]['AUDIOMETRIA']['comparecido']['V']['quantidade']}<br/>";
          } else {
            $html .= "Marcadas Audio Vesp: 0";
          }

          $html .= "</td> <td style='width: 20%; text-align: left;'>";

          if(isset($cabecalho[$key]['AUDIOMETRIA']['falta']['M']['quantidade'])) {
            $html .= "Faltas Audio Mat: {$cabecalho[$key]['AUDIOMETRIA']['falta']['M']['quantidade']}<br/>";
          } else {
            $html .= "Faltas Audio Mat: 0<br/>";
          }

          if(isset($cabecalho[$key]['AUDIOMETRIA']['falta']['V']['quantidade'])) {
            $html .= "Faltas Audio Vesp: {$cabecalho[$key]['AUDIOMETRIA']['falta']['V']['quantidade']}<br/>";
          } else {
            $html .= "Faltas Audio Vesp: 0";
          }

          $html .= "</td> </tr>";

          $html .= "<tr>
                      <th colspan='2' style='width: 40%;'>PROFISSIONAL</th>
                      <th style='width: 20%;'>PREVISTAS</th>
                      <th style='width: 20%;'>REALIZADAS</th>
                      <th style='width: 20%;'>%DESVIO</th>
                    </tr>";

          if(isset($value['medico'])) {
            $total = [
              'realizadas' => 0,
              'previstas' => 0,
              'desvio' => 0
            ];

            $html .= "<tr><th colspan='5'  style='text-align: left;'>MEDICO</th></tr>";
            foreach ($value['medico'] as $medico) {
              $desvio =  $medico['realizadas'] - $medico['previstas'];
              if($desvio !=0 && $medico['previstas'] !=0) {
                $desvio = number_format(($desvio / $medico['previstas'] * 100) , 1) . ' %';
              } else {
                $desvio = '00,0 %';
              }

              $total['realizadas'] += $medico['realizadas'];
              $total['previstas'] += $medico['previstas'];
              $total['desvio'] += $desvio;

              $totalGeral['realizadas'] += $medico['realizadas'];
              $totalGeral['previstas'] += $medico['previstas'];
              $totalGeral['desvio'] += $desvio;

              $html .= "<tr>
                          <td colspan='2' style='text-align: left;'>{$medico['nome']}</td>
                          <td>{$medico['previstas']}</td>
                          <td>{$medico['realizadas']}</td>
                          <td>{$desvio}</td>
                        </tr>";
            }

            $desvio =  $total['realizadas'] - $total['previstas'];
            if($desvio !=0 && $total['previstas'] !=0) {
              $desvio = number_format(($desvio / $total['previstas'] * 100) , 1) . ' %';
            } else {
              $desvio = '00,0 %';
            }

            $html .= "<tr>
                        <th colspan='2' style='text-align: left;'>TOTAL</th>
                        <th>{$total['previstas']}</th>
                        <th>{$total['realizadas']}</th>
                        <th>{$desvio}</th>
                      </tr>";
          }

          if(isset($value['audiometria'])) {
            $total = [
              'realizadas' => 0,
              'previstas' => 0,
              'desvio' => 0
            ];

            $html .= "<tr><th colspan='5' style='text-align: left;'>AUDIOMETIRA</th></tr>";
            foreach ($value['audiometria'] as $audiometria) {
              $desvio = $audiometria['realizadas'] - $audiometria['previstas'];
              if($desvio !=0 && $audiometria['previstas'] !=0) {
        				$desvio = number_format(($desvio / $audiometria['previstas'] * 100)) . ' %';
        			} else {
        				$desvio = '00,0 %';
        			}

              $total['realizadas'] += $audiometria['realizadas'];
              $total['previstas'] += $audiometria['previstas'];
              $total['desvio'] += $desvio;

              $html .= "<tr>
                          <td colspan='2' style='text-align: left;'>{$audiometria['nome']}</td>
                          <td>{$audiometria['previstas']}</td>
                          <td>{$audiometria['realizadas']}</td>
                          <td>{$desvio}</td>
                        </tr>";
            }

            $desvio =  $total['realizadas'] - $total['previstas'];
            if($desvio !=0 && $total['previstas'] !=0) {
              $desvio = number_format(($desvio / $total['previstas'] * 100) , 1) . ' %';
            } else {
              $desvio = '00,0 %';
            }

            $html .= "<tr>
                        <th colspan='2' style='text-align: left;'>TOTAL</th>
                        <th>{$total['previstas']}</th>
                        <th>{$total['realizadas']}</th>
                        <th>{$desvio}</th>
                      </tr>";
          }
        }

        $html .= "<tr><td colspan='5'><br/><br/></td></tr>";

        $desvio =  $totalGeral['realizadas'] - $totalGeral['previstas'];
        if($desvio !=0 && $totalGeral['previstas'] !=0) {
          $desvio = number_format(($desvio / $totalGeral['previstas'] * 100) , 1) . ' %';
        } else {
          $desvio = '00,0 %';
        }

        $html .= "<tr>
                    <th colspan='2' style='text-align: left;'>TOTAL DO M&Ecirc;S</th>
                    <th>{$totalGeral['previstas']}</th>
                    <th>{$totalGeral['realizadas']}</th>
                    <th>{$desvio}</th>
                  </tr>";


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
    }
}
