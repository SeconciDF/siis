<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Mpdf\Mpdf;

class RelatoriosController extends AppController {

    public function beforeFilter(Event $event) {
        $this->request->session()->write('Auth.User.MenuActive', 'relatorio');
    }

    public function atendimento() {

    }

    public function consultaRealizadaEmpresa() {
      if($this->request->query('inicio') && $this->request->query('fim')) {
        $this->loadModel('Consultas');

        $inicio = $this->Consultas->formatDate($this->request->query('inicio'));
        $fim = $this->Consultas->formatDate($this->request->query('fim'));

        $empresas = $this->Consultas->find('all', [
          'conditions' => [
            "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'"
          ],
          'fields' => [
            'Empresa.nome',
            'assistencial' => 'count(if(Consultas.especialidades_id IN(10),1,null))',
            'ocupacional' => 'count(if(Consultas.especialidades_id IN(9),1,null))',
            'odonto' => 'count(if(Consultas.especialidades_id IN(1,2,3,12),1,null))',
            'total' => 'count(Consultas.id)',
          ],
          'join' => [
              [
                  'table' => 'empresas',
                  'alias' => 'Empresa',
                  'type' => 'INNER',
                  'conditions' => 'Empresa.id = Consultas.empresas_id',
              ]
          ],
          'group' => ['Consultas.empresas_id'],
          'order' => ['Empresa.nome' => 'ASC']
        ])->toArray();

        $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                  <thead>
                    <tr>
                        <td colspan='5' style='font-size: 16px; font-weight: bold; text-align: center; border: 0;'>
                            CONSULTAS REALIZADAS POR EMPRESA
                        </td>
                    </tr>
                    <tr>
                        <td colspan='5' style='text-align: center; border: 0;'>
                            Per&iacute;odo: {$this->request->query('inicio')} a {$this->request->query('fim')} <br/>
                        </td>
                    </tr>
                    <tr>
                        <th>Empresa</th>
                        <th style='width: 60px;'>Assist.</th>
                        <th style='width: 60px;'>Ocup.</th>
                        <th style='width: 60px;'>Odonto</th>
                        <th style='width: 60px;'>Total</th>
                    </tr>
                  </thead>";

        $html .= "<tbody>";

        $total = 0.00;
        foreach ($empresas as $key => $value) {
          $html .= "<tr>
                        <td>{$value['Empresa']['nome']}</td>
                        <td style='text-align: center;'>{$value['assistencial']}</td>
                        <td style='text-align: center;'>{$value['ocupacional']}</td>
                        <td style='text-align: center;'>{$value['odonto']}</td>
                        <td style='text-align: center;'>{$value['total']}</td>
                    </tr>";
          $total += $value['total'];
        }

        $html .= "<tr>
                      <td colspan='4'></td>
                      <td style='text-align: center;'><b>{$total}</b></td>
                  </tr>";

        $html .= "</tbody>";

        $html .= "</table>";

        $mpdf = new mPDF();
        $mpdf->SetTitle('CONSULTAS REALIZADAS POR EMPRESA');
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
        $mpdf->AddPage('','','','','',null,null,25,15,0,0);

        $mpdf->WriteHTML("<style> th, td { border: 1px solid #ddd; padding: 2px; font-size: 12px;} td { border-top: 0;} </style>");
        $mpdf->WriteHTML($html);
        //$mpdf->Output('CONSULTAS REALIZADAS POR EMPRESA.pdf', 'D');
        $mpdf->Output();
        exit;
      }
    }

    public function pacientesEmpresa() {
      $this->loadModel('Empresas');
      $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

      if($this->request->query('tipo') && $this->request->query['empresa']) {
        $this->loadModel('Consultas');

        $options = [
          "Empresas.id IN(".implode(',',$this->request->query['empresa']).")",
          'Beneficiario.situacao' => 'A'
        ];

        if($this->request->query('inicio') && $this->request->query('fim')) {
          $inicio = $this->Consultas->formatDate($this->request->query('inicio'));
          $fim = $this->Consultas->formatDate($this->request->query('fim'));

          $options[] = "Consultas.data_hora_atendimento BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'";
          $empresas = $this->Consultas->find('all', [
            'conditions' => $options,
            'fields' => [
              'id'=>'Empresas.id',
              'nome'=>'Empresas.nome',
              'Dependente.id',
              'paciente' => 'IFNULL(Dependente.nome,Beneficiario.nome)',
              'funcao' => '(SELECT GROUP_CONCAT(DISTINCT f.descricao) FROM apoio_funcoes f INNER JOIN beneficiarios_funcoes bf ON f.id = bf.funcoes_id WHERE bf.beneficiarios_id = Beneficiario.id)'
            ],
            'join' => [
                [
                    'table' => 'empresas',
                    'alias' => 'Empresas',
                    'type' => 'INNER',
                    'conditions' => 'Empresas.id = Consultas.empresas_id',
                ],
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
            ],
            'order' => [
              'Empresas.nome' => 'ASC',
              'paciente'
            ],
            'group' => [
              'paciente'
            ]
          ])->toArray();
        } else {
          $this->loadModel('Empresas');
          $empresas = $this->Empresas->find('all', [
            'conditions' => $options,
            'fields' => [
              'Empresas.id',
              'Empresas.nome',
              'paciente' => 'Beneficiario.nome',
              'funcao' => '(SELECT GROUP_CONCAT(DISTINCT f.descricao) FROM apoio_funcoes f INNER JOIN beneficiarios_funcoes bf ON f.id = bf.funcoes_id WHERE bf.beneficiarios_id = Beneficiario.id)'
            ],
            'join' => [
                [
                    'table' => 'beneficiarios_empresas',
                    'alias' => 'r',
                    'type' => 'INNER',
                    'conditions' => 'Empresas.id = r.empresas_id AND r.situacao = "A"',
                ],
                [
                    'table' => 'beneficiarios',
                    'alias' => 'Beneficiario',
                    'type' => 'INNER',
                    'conditions' => 'Beneficiario.id = r.beneficiarios_id',
                ],
            ],
            'order' => [
              'Empresas.nome' => 'ASC',
              'paciente'
            ],
            'group' => [
              'paciente'
            ]
          ])->toArray();

        }

        $pacientes = [];
        foreach ($empresas as $key => $value) {
          $pacientes[$value['id']]['empresa'] = $value['nome'];
          $pacientes[$value['id']]['pacientes'][] = [
            'paciente' => $value['paciente'],
            'funcao' => $value['Dependente']['id'] ? ' Dependente' : $value['funcao']
          ];
        }

        $html = '';
        foreach ($pacientes as $key => $value) {
            if($html) {
              $html .= "<pagebreak>";
            }

            $html .= "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                      <thead>
                        <tr>
                            <td colspan='2' style='font-size: 16px; font-weight: bold; text-align: center; border: 0;'>
                                PACIENTES POR EMPRESA
                            </td>
                        </tr>";

            if($this->request->query('inicio') && $this->request->query('fim')) {
              $html .= "<tr>
                          <td colspan='2' style='text-align: center; border: 0; font-size: 14px; padding: 10px;'>
                            Consultados de {$this->request->query('inicio')} a {$this->request->query('fim')} <br/>
                          </td>
                        </tr>";
            }

            $html .= "  <tr>
                            <td style='text-align: left; border: 0; font-size: 14px; padding: 10px;'>
                                Empresa: {$value['empresa']}
                            </td>
                            <td style='text-align: right; border: 0; font-size: 14px; padding: 10px;'>
                                Total de Trabalhadores: ".sizeof($value['pacientes'])."
                            </td>
                        </tr>
                        <tr>
                            <th style='width: 60%;'>Paciente</th>
                            <th style='width: 40%;'>Fun&ccedil;&atilde;o</th>
                        </tr>
                      </thead>";
            $html .= "<tbody>";
            foreach ($value['pacientes'] as $paciente) {
              $html .= "<tr>
                            <td>{$paciente['paciente']}</td>
                            <td>{$paciente['funcao']}</td>
                        </tr>";
            }
            $html .= "</tbody>";
            $html .= "</table>";
        }

        $mpdf = new mPDF();
        $mpdf->SetTitle('PACIENTES POR EMPRESA');
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
        $mpdf->AddPage('','','','','',null,null,25,15,0,0);

        $mpdf->WriteHTML("<style> th, td { border: 1px solid #ddd; padding: 2px; font-size: 12px;} td { border-top: 0;} </style>");
        $mpdf->WriteHTML($html);
        //$mpdf->Output('PACIENTES POR EMPRESA.pdf', 'D');
        $mpdf->Output();
        exit;
      }

      $this->set(compact('empresas'));
    }

    public function pacientesProfissional() {
      $this->loadModel('Profissionais');
      $profissionais = $this->Profissionais->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

      if($this->request->query('profissional') && $this->request->query('inicio') && $this->request->query('fim')) {
        $this->loadModel('Consultas');

        $inicio = $this->Consultas->formatDate($this->request->query('inicio'));
        $fim = $this->Consultas->formatDate($this->request->query('fim'));
        $ids = implode(',',$this->request->query['profissional']);

        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT b.nome as bn, b.cpf as bcpf, b.data_nascimento as bdn, CONCAT(b.identidade, ' ', b.orgao_expedidor) as rg,
                                e.nome as en, e.identificacao as cnpj, d.nome as dn, t.descricao as tp, d.data_nascimento as ddn, d.cpf as dcpf,
                                CONCAT(b.logradouro, ', ', b.bairro, ', ', b.cidade, ', ', b.estado) as endereco, b.celular,
                                DATE_FORMAT(c.data_hora_atendimento, \"%d/%m\") as procedimentos, p.nome as profissional
                                FROM consultas c
                                INNER JOIN profissionais p ON p.id = c.profissionais_id
                                INNER JOIN empresas e ON e.id = c.empresas_id
                                INNER JOIN beneficiarios b ON b.id = c.beneficiarios_id
                                LEFT JOIN beneficiarios_dependentes d ON d.id = c.dependentes_id
                                LEFT JOIN beneficiarios_tipo_dependentes t ON t.id = d.tipo_dependencias_id
                                WHERE c.data_hora_atendimento BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
                                AND p.id IN({$ids}) AND c.st_consulta = 'AC'
                                ORDER BY c.data_hora_atendimento, p.nome, b.nome");

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"PACIENTES POR PROFISSIONAL.csv\";");
        header('Content-Transfer-Encoding: binary');

        $output = fopen('php://output', 'w');
        fputs($output, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($output, [ 'Nome do titular', 'CPF do titular', 'Nascimento do Titular', 'RG do titular', 'Empresa', 'CNPJ', 'Nome do Dependente', 'Parentesco', 'Nascimento do Dependente', 'CPF do Dependente', 'Endereco', 'Telefone', 'Data e Procedimentos', 'Profissional'], ";");

        $rows = $stmt->fetchAll('assoc');
        foreach ($rows as $row) {
            fputcsv($output, $row, ";");
        }
        exit;
      }

      $this->set(compact('profissionais'));
    }


    public function demonstrativoOdontologico() {
      $this->loadModel('ApoioEspecialidades');
      $especialidades = $this->ApoioEspecialidades->find('list',['keyField' => 'id', 'valueField' => 'descricao', 'conditions' => ['id IN(1,2,3,12)']])->toArray();

      if($this->request->query('inicio') && $this->request->query('fim')) {
        $especialidades[0] = 'TODAS';
        $this->loadModel('Consultas');

        $inicio = $this->Consultas->formatDate($this->request->query('inicio'));
        $fim = $this->Consultas->formatDate($this->request->query('fim'));

        $options = [
          //"Realizado.data_hora_registro BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consulta.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          'OdontoOdontogramas.referencia' => '0',
          'Especialidade.area_medica' => 'OD',
          'Consulta.st_consulta' => 'AC'
        ];

        if($this->request->query('especialidade')) {
          $options['Especialidade.id'] = $this->request->query('especialidade');
        }

        //$options[] = 'OdontoOdontogramas.id = (SELECT MIN(id) FROM odonto_odontogramas WHERE consultas_id = OdontoOdontogramas.consultas_id AND referencia = 0)';

        $this->loadModel('OdontoOdontogramas');
        $procedimentos = $this->OdontoOdontogramas->find('all', [
          'conditions' => $options,
          'fields' => [
            'Procedimento.id',
            'Procedimento.nome',
            'Procedimento.ponto',
            'quantidade' => 'SUM(Realizado.total_realizado)',
          ],
          'join' => [
              [
                  'table' => 'consultas',
                  'alias' => 'Consulta',
                  'type' => 'INNER',
                  'conditions' => 'Consulta.id = OdontoOdontogramas.consultas_id',
              ],
              [
                  'table' => 'apoio_especialidades',
                  'alias' => 'Especialidade',
                  'type' => 'INNER',
                  'conditions' => 'Especialidade.id = Consulta.especialidades_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'OdontoOdontogramas.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ],
              [
                  'table' => 'odonto_procedimentos',
                  'alias' => 'Procedimento',
                  'type' => 'INNER',
                  'conditions' => 'Procedimento.id = Aplicado.procedimentos_id',
              ],
          ],
          'group' => ['Procedimento.id'],
          'order' => ['Procedimento.nome' => 'ASC']
        ])->toArray();

        $options0 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          "Aplicado.procedimentos_id" => "130000",
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options0['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $avaliacao = $this->Consultas->find('all', [
          'conditions' => $options0,
          'fields' => [
            'avaliacao' => 'SUM(Realizado.total_realizado)',
          ],
          'join' => [
              [
                  'table' => 'odonto_odontogramas',
                  'alias' => 'Odonto',
                  'type' => 'INNER',
                  'conditions' => 'Consultas.id = Odonto.consultas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'Odonto.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ]
          ],
        ])->first();

        $options1 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          "Aplicado.procedimentos_id" => "10100",
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options1['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $inicial = $this->Consultas->find('all', [
          'conditions' => $options1,
          'fields' => [
            'inicial' => 'SUM(Realizado.total_realizado)',
          ],
          'join' => [
              [
                  'table' => 'odonto_odontogramas',
                  'alias' => 'Odonto',
                  'type' => 'INNER',
                  'conditions' => 'Consultas.id = Odonto.consultas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'Odonto.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ]
          ],
        ])->first();

        $options2 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          "Aplicado.procedimentos_id" => "10200",
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options2['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $emergencial = $this->Consultas->find('all', [
          'conditions' => $options2,
          'fields' => [
            'emergencia' => 'SUM(Realizado.total_realizado)',
          ],
          'join' => [
              [
                  'table' => 'odonto_odontogramas',
                  'alias' => 'Odonto',
                  'type' => 'INNER',
                  'conditions' => 'Consultas.id = Odonto.consultas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'Odonto.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ]
          ]
        ])->first();

        $options3 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options3['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $atendimento = $this->Consultas->find('all', [
          'conditions' => $options3,
          'fields' => [
            'titular' => 'COUNT(IF(Consultas.dependentes_id IS NULL,Consultas.beneficiarios_id,NULL))',
            'dependente' => 'COUNT(IF(Consultas.dependentes_id IS NOT NULL,Consultas.dependentes_id,NULL))',
          ]
        ])->first();

        $options4 = [
          "Consultas.data_hora_fecha_atendimento BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options4['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $trabalhadores = $this->Consultas->find('all', [
          'conditions' => $options4,
          'fields' => [
            'trabalhados' => 'COUNT(DISTINCT Consultas.profissionais_id)'
          ],
          'group' => [
            'YEAR(Consultas.data_hora_fecha_atendimento)',
            'MONTH(Consultas.data_hora_fecha_atendimento)',
            'DAY(Consultas.data_hora_fecha_atendimento)'
          ]
        ])->toArray();

        $options5 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          'Consultas.st_consulta' => 'FA'
        ];
        if($this->request->query('especialidade')) {
          $options5['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $faltas = $this->Consultas->find('all', [
          'conditions' => $options5,
          'fields' => [
            'faltas' => 'COUNT(Consultas.id)',
          ],
        ])->first();

        $options6 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          'Consultas.tratamento_alta_retorno' => '1',
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options6['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $tratamentos = $this->Consultas->find('all', [
          'conditions' => $options6,
          'fields' => [
            'titular' => 'COUNT(IF(Consultas.dependentes_id IS NULL,Consultas.beneficiarios_id,NULL))',
            'dependente' => 'COUNT(IF(Consultas.dependentes_id IS NOT NULL,Consultas.dependentes_id,NULL))',
          ]
        ])->first();

        $options7 = [
          "Consultas.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          "Consultas.consultas_id IS NOT NULL",
          'Consultas.tratamento_alta_retorno' => '0',
          'Consultas.st_consulta' => 'AC'
        ];
        if($this->request->query('especialidade')) {
          $options7['Consultas.especialidades_id'] = $this->request->query('especialidade');
        }
        $retornos = $this->Consultas->find('all', [
          'conditions' => $options7,
          'fields' => [
            'retornos' => 'COUNT(DISTINCT Consultas.beneficiarios_id)',
          ]
        ])->first();

        $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                    <thead>
                      <tr>
                          <td colspan='6' style='font-size: 16px; font-weight: bold; text-align: center; border: 0;'>
                              DEMONSTRATIVO ODONTOL&Oacute;GICO
                          </td>
                      </tr>
                      <tr>
                          <td colspan='6' style='text-align: center; border: 0;'>
                              Per&iacute;odo: {$this->request->query('inicio')} a {$this->request->query('fim')} <br/>
                              Especialidade: {$especialidades[$this->request->query('especialidade')]} <br/>
                          </td>
                      </tr>
                      <tr>
                          <th colspan='2' style='width: 33%'>Consulta</th>
                          <th colspan='2' style='width: 33%'>Atendimento</th>
                          <th colspan='2' style='width: 33%'>Atendimento</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                          <td>Inicial/B&aacute;sica</td>
                          <td>{$inicial['inicial']}</td>
                          <td>Titular</td>
                          <td>{$atendimento['titular']}</td>
                          <td>Dias &Uacute;teis</td>
                          <td>".sizeof($trabalhadores)."</td>
                      </tr>
                      <tr>
                          <td>Emerg&ecirc;ncia</td>
                          <td>{$emergencial['emergencia']}</td>
                          <td>Dependente</td>
                          <td>{$atendimento['dependente']}</td>
                          <td>Faltas de Pacientes</td>
                          <td>{$faltas['faltas']}</td>
                      </tr>
                      <tr>
                          <td>Retorno</td>
                          <td>{$retornos['retornos']}</td>
                          <td></td>
                          <td></td>
                          <td>Titulares Conclu&iacute;dos</td>
                          <td>{$tratamentos['titular']}</td>
                      </tr>
                      <tr>
                          <td>Avalia&ccedil;&atilde;o</td>
                          <td>{$avaliacao['avaliacao']}</td>
                          <td></td>
                          <td></td>
                          <td>Dependentes Conclu&iacute;dos</td>
                          <td>{$tratamentos['dependente']}</td>
                      </tr>
                      <tr>
                          <td style='width: 23%'>Total</td>
                          <td style='width: 10%'>".($inicial['inicial']+$emergencial['emergencia']+$retornos['retornos']+$avaliacao['avaliacao'])."</td>
                          <td style='width: 23%'>Total</td>
                          <td style='width: 10%'>".($atendimento['titular']+$atendimento['dependente'])."</td>
                          <td style='width: 23%'>Total Conclu&iacute;dos</td>
                          <td style='width: 10%'>".($tratamentos['titular']+$tratamentos['dependente'])."</td>
                      </tr>
                    </tbody>
                  </table>";

        $html .= "<br/>";

        $html .= "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                  <thead>
                    <tr>
                        <th colspan='2'>Procedimento</th>
                        <th>Ponto</th>
                        <th>Qtd</th>
                        <th>Total</th>
                    </tr>
                  </thead>";

        $quadro = [
          'inicial' => 0,
          'emergencial' => 0,
          'avaliacao' => 0,
          'beneficiario' => 0,
          'dependente' => 0
        ];

        foreach ($procedimentos as $value) {
          if($value['Procedimento']['id'] == '10100') {
            $quadro['inicial'] = $value['quantidade'];
          }
          if($value['Procedimento']['id'] == '10200') {
            $quadro['emergencial'] = $value['quantidade'];
          }
          if($value['Procedimento']['id'] == '130000') {
            $quadro['avaliacao'] = $value['quantidade'];
          }
        }

        $html .= "<tbody>";
        foreach ($procedimentos as $key => $value) {
          $html .= "<tr>
                        <td style='width: 80px; text-align: center;'>".str_pad($value['Procedimento']['id'], 6, '0', STR_PAD_LEFT)."</td>
                        <td>{$value['Procedimento']['nome']}</td>
                        <td style='width: 80px; text-align: center;'>".number_format($value['Procedimento']['ponto'],2,'.','')."</td>
                        <td style='width: 80px; text-align: center;'>{$value['quantidade']}</td>
                        <td style='width: 80px; text-align: right;'>".number_format(($value['Procedimento']['ponto']*$value['quantidade']),2,'.','')."</td>
                    </tr>";
        }
        $html .= "</tbody>";

        $html .= "</table>";

        $mpdf = new mPDF();
        $mpdf->SetTitle('DEMONSTRATIVO ODONTOLOGICO');
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
        $mpdf->AddPage('L','','','','',null,null,25,15,0,0);

        $mpdf->WriteHTML("<style> th, td { border: 1px solid #ddd; padding: 2px; font-size: 12px;} td { border-top: 0;} </style>");
        $mpdf->WriteHTML($html);
        //$mpdf->Output('DEMONSTRATIVO ODONTOLOGICO.pdf', 'D');
        $mpdf->Output();
        exit;
      }

      $this->set(compact('especialidades'));
    }

    public function odontoProducaoIndividual() {
      $this->loadModel('ApoioUnidades');
      $unidades = $this->ApoioUnidades->find('list',['keyField' => 'id', 'valueField' => 'nome'])->toArray();

      if($this->request->query('inicio') && $this->request->query('fim')) {
        $this->loadModel('Consultas');

        $inicio = $this->Consultas->formatDate($this->request->query('inicio'));
        $fim = $this->Consultas->formatDate($this->request->query('fim'));

        $options = [
          "Consulta.data_hora_agendado BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'",
          'OdontoOdontogramas.referencia' => '0',
          'Especialidade.area_medica' => 'OD',
          'Consulta.st_consulta' => 'AC'
        ];

        if($this->request->query('unidade')) {
          $options['Consulta.unidades_id'] = $this->request->query('unidade');
        }

        $this->loadModel('OdontoOdontogramas');
        $procedimentos = $this->OdontoOdontogramas->find('all', [
          'conditions' => $options,
          'fields' => [
            'Procedimento.id',
            'Procedimento.nome',
            'Procedimento.ponto',
            'Realizado.profissionais_id',
            'Realizado.colaborador_nome',
            'Especialidade.descricao',
            'quantidade' => 'sum(Realizado.total_realizado)',
            'atendimentos' => 'COUNT(Consulta.id)',
            'beneficiarios' => 'COUNT(DISTINCT IF(Consulta.dependentes_id IS NULL,Consulta.beneficiarios_id,NULL))',
            'dependentes' => 'COUNT(DISTINCT IF(Consulta.dependentes_id IS NOT NULL,Consulta.dependentes_id,NULL))',
            'altas' => 'SUM(Consulta.tratamento_alta_retorno)'
          ],
          'join' => [
              [
                  'table' => 'consultas',
                  'alias' => 'Consulta',
                  'type' => 'INNER',
                  'conditions' => 'Consulta.id = OdontoOdontogramas.consultas_id',
              ],
              [
                  'table' => 'apoio_especialidades',
                  'alias' => 'Especialidade',
                  'type' => 'INNER',
                  'conditions' => 'Especialidade.id = Consulta.especialidades_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'OdontoOdontogramas.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ],
              [
                  'table' => 'odonto_procedimentos',
                  'alias' => 'Procedimento',
                  'type' => 'INNER',
                  'conditions' => 'Procedimento.id = Aplicado.procedimentos_id',
              ],
          ],
          'group' => ['Realizado.profissionais_id','Procedimento.id'],
          'order' => ['Realizado.colaborador_nome','Procedimento.nome' => 'ASC']
        ])->toArray();

        $individuais = [];
        foreach ($procedimentos as $key => $value) {
          $individuais[$value['Realizado']['profissionais_id']]['profissional'] = $value['Realizado']['colaborador_nome'];
          $individuais[$value['Realizado']['profissionais_id']]['especialidade'] = $value['Especialidade']['descricao'];
          $individuais[$value['Realizado']['profissionais_id']]['procedimentos'][] = $value;

          if(!isset($individuais[$value['Realizado']['profissionais_id']]['atendimentos'])) {
            $individuais[$value['Realizado']['profissionais_id']]['atendimentos'] = 0;
          }
          $individuais[$value['Realizado']['profissionais_id']]['atendimentos'] += $value['atendimentos'];

          if(!isset($individuais[$value['Realizado']['profissionais_id']]['beneficiarios'])) {
            $individuais[$value['Realizado']['profissionais_id']]['beneficiarios'] = 0;
          }
          $individuais[$value['Realizado']['profissionais_id']]['beneficiarios'] += $value['beneficiarios'];

          if(!isset($individuais[$value['Realizado']['profissionais_id']]['dependentes'])) {
            $individuais[$value['Realizado']['profissionais_id']]['dependentes'] = 0;
          }
          $individuais[$value['Realizado']['profissionais_id']]['dependentes'] += $value['dependentes'];

          if(!isset($individuais[$value['Realizado']['profissionais_id']]['altas'])) {
            $individuais[$value['Realizado']['profissionais_id']]['altas'] = 0;
          }
          $individuais[$value['Realizado']['profissionais_id']]['altas'] += $value['altas'];

          if(!isset($individuais[$value['Realizado']['profissionais_id']]['total']['procedimentos'])) {
            $individuais[$value['Realizado']['profissionais_id']]['total']['procedimentos'] = 0;
          }
          $individuais[$value['Realizado']['profissionais_id']]['total']['procedimentos'] += $value['quantidade'];

          if(!isset($individuais[$value['Realizado']['profissionais_id']]['total']['pontos'])) {
            $individuais[$value['Realizado']['profissionais_id']]['total']['pontos'] = 0;
          }
          $individuais[$value['Realizado']['profissionais_id']]['total']['pontos'] += ($value['Procedimento']['ponto']*$value['quantidade']);
        }

        $html = '';
        foreach ($individuais as $key => $value) {
          if($html) {
            $html .= "<pagebreak>";
          }

          $html .= "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                    <tr>
                        <td colspan='7' style='font-size: 16px; font-weight: bold; text-align: center; border: 0;'>
                            PRODU&Ccedil;&Atilde;O INDIVIDUAL - ODONTO
                        </td>
                    </tr>
                    <tr>
                        <td colspan='7' style='text-align: center; border: 0;'>
                            Per&iacute;odo: {$this->request->query('inicio')} a {$this->request->query('fim')} <br/>
                            <br/>
                        </td>
                    </tr>
                    <tr><td style='border: 0; border-bottom: 1px solid #ddd;' colspan='7'><br/></td></tr>
                    <tr>
                        <td style=''><b>Unidade</b></td>
                        <td style=''>{$unidades[$this->request->query('unidade')]}</td>
                        <td style='width: 190px;'><b>Atendimentos Realizados</b></td>
                        <td style='width: 100px;'>{$value['atendimentos']}</td>
                        <td style='' colspan='2'><b>Procedimentos Realizados</b></td>
                        <td style=''>{$value['total']['procedimentos']}</td>
                    </tr>
                    <tr>
                        <td style=''><b>Profissional</b></td>
                        <td style=''>{$value['profissional']}</td>
                        <td style='width: 190px;'><b>Pacientes Atendidos</b></td>
                        <td style='width: 100px;'>T={$value['beneficiarios']} &nbsp; D={$value['dependentes']}</td>
                        <td style='' colspan='2'><b>Total de pontos</b></td>
                        <td style=''>{$value['total']['pontos']}</td>
                    </tr>
                    <tr>
                        <td style=''><b>Especialidade</b></td>
                        <td style=''>{$value['especialidade']}</td>
                        <td style='width: 190px;'><b>Altas</b></td>
                        <td style='width: 100px;'>{$value['altas']}</td>
                        <td style='' colspan='2'><b>M&eacute;dia Final</b></td>
                        <td style=''>".number_format(($value['total']['pontos']/$value['atendimentos']),2,'.','')."</td>
                    </tr>
                    <tr><td style='border: 0;' colspan='6'><br/></td></tr>
                    <tr>
                        <th colspan='4'>Procedimento</th>
                        <th>Ponto</th>
                        <th>Qtd</th>
                        <th>Total</th>
                    </tr>";

          foreach ($value['procedimentos'] as $procedimento) {
            $html .= "<tr>
                          <td style='width: 80px; text-align: center;'>".str_pad($procedimento['Procedimento']['id'], 6, '0', STR_PAD_LEFT)."</td>
                          <td colspan='3'>{$procedimento['Procedimento']['nome']}</td>
                          <td style='width: 90px; text-align: center;'>".number_format($procedimento['Procedimento']['ponto'],2,'.','')."</td>
                          <td style='width: 90px; text-align: center;'>{$procedimento['quantidade']}</td>
                          <td style='width: 80px; text-align: right;'>".number_format(($procedimento['Procedimento']['ponto']*$procedimento['quantidade']),2,'.','')."</td>
                      </tr>";
          }

          $html .= "</table>";
        }

        $mpdf = new mPDF();
        $mpdf->SetTitle('DEMONSTRATIVO ODONTOLOGICO');
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
        $mpdf->AddPage('L','','','','',null,null,25,15,0,0);

        $mpdf->WriteHTML("<style> th, td { border: 1px solid #ddd; padding: 2px; font-size: 12px;} td { border-top: 0;} </style>");
        $mpdf->WriteHTML($html);
        //$mpdf->Output('DEMONSTRATIVO ODONTOLOGICO.pdf', 'D');
        $mpdf->Output();
        exit;
      }

      $this->set(compact('unidades'));
    }

    public function sinteticoAnualOdonto() {
      if($this->request->query('ano')) {
        $this->loadModel('Consultas');

        $options = [
          "Consultas.data_hora_agendado LIKE '%{$this->request->query('ano')}%'",
          "Consultas.data_hora_fecha_atendimento IS NOT NULL",
          "Consultas.st_consulta" => "AC"
        ];

        $this->loadModel('Consultas');
        $consultas1 = $this->Consultas->find('all', [
          'conditions' => $options,
          'fields' => [
            'Unidade.id',
            'Unidade.nome',
            'mes' => 'MONTH(Consultas.data_hora_agendado)',
            'quantidade' => 'COUNT(Consultas.id)',
            'empresas' => 'COUNT(DISTINCT Consultas.empresas_id)',
          ],
          'join' => [
              [
                  'table' => 'apoio_unidades',
                  'alias' => 'Unidade',
                  'type' => 'INNER',
                  'conditions' => 'Unidade.id = Consultas.unidades_id',
              ]
          ],
          'group' => ['Unidade.nome', 'MONTH(Consultas.data_hora_agendado)'],
        ])->toArray();

        $consultas2 = $this->Consultas->find('all', [
          'conditions' => $options,
          'fields' => [
            'mes' => 'MONTH(Consultas.data_hora_agendado)',
            'beneficiario_atendimentos' => 'COUNT(IF(Consultas.dependentes_id IS NULL AND Consultas.empresas_id!=102340,Consultas.beneficiarios_id,NULL))',
            'dependente_atendimentos' => 'COUNT(IF(Consultas.dependentes_id AND Consultas.empresas_id!=102340,Consultas.dependentes_id,NULL))',
            'comunidade_atendimentos' => 'COUNT(IF(Consultas.empresas_id=102340,Consultas.beneficiarios_id,NULL))',
            'beneficiario_pessoas' => 'COUNT(DISTINCT IF(Consultas.dependentes_id IS NULL,Consultas.beneficiarios_id,NULL))',
            'dependente_pessoas' => 'COUNT(DISTINCT IF(Consultas.dependentes_id IS NOT NULL,Consultas.beneficiarios_id,NULL))',
            'comunidade_pessoas' => 'COUNT(DISTINCT IF(Consultas.empresas_id=102340,Consultas.beneficiarios_id,NULL))'
          ],
          'join' => [
              [
                  'table' => 'apoio_unidades',
                  'alias' => 'Unidade',
                  'type' => 'INNER',
                  'conditions' => 'Unidade.id = Consultas.unidades_id',
              ]
          ],
          'group' => [ 'MONTH(Consultas.data_hora_agendado)'],
        ])->toArray();

        $inicial = $this->Consultas->find('all', [
          'conditions' => [
            "Consultas.data_hora_agendado LIKE '%{$this->request->query('ano')}%'",
            "Consultas.data_hora_fecha_atendimento IS NOT NULL",
            "Aplicado.procedimentos_id" => "10100",
            'Consultas.st_consulta' => 'AC'
          ],
          'fields' => [
            'mes' => 'MONTH(Consultas.data_hora_agendado)',
            'inicial' => 'SUM(Realizado.total_realizado)',
          ],
          'join' => [
              [
                  'table' => 'odonto_odontogramas',
                  'alias' => 'Odonto',
                  'type' => 'INNER',
                  'conditions' => 'Consultas.id = Odonto.consultas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'Odonto.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ]
          ],
          'group' => ['MONTH(Consultas.data_hora_agendado)']
        ])->toArray();

        $emergencial = $this->Consultas->find('all', [
          'conditions' => [
            "Consultas.data_hora_agendado LIKE '%{$this->request->query('ano')}%'",
            "Consultas.data_hora_fecha_atendimento IS NOT NULL",
            "Aplicado.procedimentos_id" => "10200",
            'Consultas.st_consulta' => 'AC'
          ],
          'fields' => [
            'mes' => 'MONTH(Consultas.data_hora_agendado)',
            'emergencia' => 'SUM(Realizado.total_realizado)',
          ],
          'join' => [
              [
                  'table' => 'odonto_odontogramas',
                  'alias' => 'Odonto',
                  'type' => 'INNER',
                  'conditions' => 'Consultas.id = Odonto.consultas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_aplicados',
                  'alias' => 'Aplicado',
                  'type' => 'INNER',
                  'conditions' => 'Odonto.id = Aplicado.odontogramas_id',
              ],
              [
                  'table' => 'odonto_procedimentos_profissionais',
                  'alias' => 'Realizado',
                  'type' => 'INNER',
                  'conditions' => 'Aplicado.id = Realizado.aplicados_id',
              ]
          ],
          'group' => ['MONTH(Consultas.data_hora_agendado)']
        ])->toArray();

        $empresas = $this->Consultas->find('all', [
          'conditions' => $options,
          'fields' => [
            'mes' => 'MONTH(Consultas.data_hora_agendado)',
            'empresas' => 'COUNT(DISTINCT Consultas.empresas_id)',
            'altas' => 'COUNT(IF(Consultas.tratamento_alta_retorno,1,NULL))',
          ],
          'group' => ['MONTH(Consultas.data_hora_agendado)']
        ])->toArray();

        $faltas = $this->Consultas->find('all', [
          'conditions' => [
            "Consultas.data_hora_agendado LIKE '%{$this->request->query('ano')}%'",
            'Consultas.st_consulta' => 'FA'
          ],
          'fields' => [
            'mes' => 'MONTH(Consultas.data_hora_agendado)',
            'faltas' => 'COUNT(Consultas.id)',
          ],
          'group' => ['MONTH(Consultas.data_hora_agendado)']
        ])->toArray();

        $dias = $this->Consultas->find('all', [
          'conditions' => [
            "Consultas.data_hora_agendado LIKE '%{$this->request->query('ano')}%'",
            'Consultas.st_consulta' => 'AC'
          ],
          'fields' => [
            'mes' => 'MONTH(Consultas.data_hora_fecha_atendimento)',
            'dias' => 'COUNT(DISTINCT DATE_FORMAT(Consultas.data_hora_fecha_atendimento,"%Y-%m-%d"))'
          ],
          'group' => [
            'YEAR(Consultas.data_hora_fecha_atendimento)',
            'MONTH(Consultas.data_hora_fecha_atendimento)'
          ]
        ])->toArray();

        $relatorio = [];
        foreach ($consultas1 as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }

          $relatorio['atendimento'][$value['Unidade']['id']]['unidade'] = $value['Unidade']['nome'];
          $relatorio['atendimento'][$value['Unidade']['id']]['meses'][$value['mes']] = $value['quantidade'];
        }

        foreach ($consultas2 as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }

          if(!isset($relatorio['beneficiario_atendimentos'][$value['mes']])) $relatorio['beneficiario_atendimentos'][$value['mes']] = 0;
          if(!isset($relatorio['dependente_atendimentos'][$value['mes']])) $relatorio['dependente_atendimentos'][$value['mes']] = 0;
          if(!isset($relatorio['comunidade_atendimentos'][$value['mes']])) $relatorio['comunidade_atendimentos'][$value['mes']] = 0;
          if(!isset($relatorio['beneficiario_pessoas'][$value['mes']])) $relatorio['beneficiario_pessoas'][$value['mes']] = 0;
          if(!isset($relatorio['dependente_pessoas'][$value['mes']])) $relatorio['dependente_pessoas'][$value['mes']] = 0;
          if(!isset($relatorio['comunidade_pessoas'][$value['mes']])) $relatorio['comunidade_pessoas'][$value['mes']] = 0;
          if(!isset($relatorio['empresas'][$value['mes']])) $relatorio['empresas'][$value['mes']] = 0;

          $relatorio['beneficiario_atendimentos'][$value['mes']] += $value['beneficiario_atendimentos'];
          $relatorio['dependente_atendimentos'][$value['mes']] += $value['dependente_atendimentos'];
          $relatorio['comunidade_atendimentos'][$value['mes']] += $value['comunidade_atendimentos'];
          $relatorio['beneficiario_pessoas'][$value['mes']] += $value['beneficiario_pessoas'];
          $relatorio['dependente_pessoas'][$value['mes']] += $value['dependente_pessoas'];
          $relatorio['comunidade_pessoas'][$value['mes']] += $value['comunidade_pessoas'];
        }

        foreach ($empresas as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }
          $relatorio['empresas'][$value['mes']] = $value['empresas'];
          $relatorio['altas'][$value['mes']] = $value['altas'];
        }

        foreach ($inicial as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }
          $relatorio['inicial'][$value['mes']] = $value['inicial'];
        }

        foreach ($emergencial as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }
          $relatorio['emergencial'][$value['mes']] = $value['emergencia'];
        }

        foreach ($faltas as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }
          $relatorio['faltas'][$value['mes']] = $value['faltas'];
        }

        foreach ($dias as $key => $value) {
          if((int) date('m') == (int) $value['mes']) {
            continue;
          }
          $relatorio['dias'][$value['mes']] = $value['dias'];
        }

        $html = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
                  <thead>
                    <tr>
                        <td colspan='14' style='font-size: 16px; font-weight: bold; text-align: center; border: 0;'>
                            SINT&Eacute;TICO ANUAL ODONTOL&Oacute;GICO
                        </td>
                    </tr>
                    <tr>
                        <td colspan='14' style='text-align: center; border: 0;'>
                            {$this->request->query('ano')}<br/>
                        </td>
                    </tr>
                  </thead>";

        $html .= "<tbody>";
        $html .= "<tr>
                      <td style='border: 0; font-size: 14px;' colspan='14'>Atendimentos</td>
                  </tr>
                  <tr>
                      <th></th>
                      <th style='width: 60px;'>JAN</th>
                      <th style='width: 60px;'>FEV</th>
                      <th style='width: 60px;'>MAR</th>
                      <th style='width: 60px;'>ABR</th>
                      <th style='width: 60px;'>MAI</th>
                      <th style='width: 60px;'>JUN</th>
                      <th style='width: 60px;'>JUL</th>
                      <th style='width: 60px;'>AGO</th>
                      <th style='width: 60px;'>SET</th>
                      <th style='width: 60px;'>OUT</th>
                      <th style='width: 60px;'>NOV</th>
                      <th style='width: 60px;'>DEZ</th>
                      <th style='width: 100px;'>Total</th>
                  </tr>";

        $meses = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0,'total'=>0];
        foreach ($relatorio['atendimento'] as $key => $value) {
          $total = 0.00;
          foreach ($value['meses'] as $mes) {
            $total += $mes;
          }

          $html .= "<tr>
                        <td>{$value['unidade']}</td>";

          for ($i=1; $i <= 12; $i++) {
            $html .= "<td style='text-align: center;'>".(isset($value['meses'][$i]) ? $value['meses'][$i] : '0')."</td>";
            if(isset($value['meses'][$i])) {
              $meses[$i] += $value['meses'][$i];
            }
          }

          $html .= "<td style='text-align: center;'>{$total}</td>
                    </tr>";
        }

        $html .= "<tr>
                      <td><b>Total<b/></td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($meses[$i]) ? $meses[$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($meses)."</td>
                  </tr>";


        $html .= "<tr><td style='border: 0;' colspan='14'><br/></td></tr>";

        $html .= "<tr>
                      <td style='border: 0; font-size: 14px;' colspan='14'>Atividades</td>
                  </tr>
                  <tr>
                      <th></th>
                      <th style='width: 60px;'>JAN</th>
                      <th style='width: 60px;'>FEV</th>
                      <th style='width: 60px;'>MAR</th>
                      <th style='width: 60px;'>ABR</th>
                      <th style='width: 60px;'>MAI</th>
                      <th style='width: 60px;'>JUN</th>
                      <th style='width: 60px;'>JUL</th>
                      <th style='width: 60px;'>AGO</th>
                      <th style='width: 60px;'>SET</th>
                      <th style='width: 60px;'>OUT</th>
                      <th style='width: 60px;'>NOV</th>
                      <th style='width: 60px;'>DEZ</th>
                      <th style='width: 100px;'>Total</th>
                  </tr>";

        $meses = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0,'total'=>0];
        $html .= "<tr>
                      <td>Trabalhadores (atendimentos)</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['beneficiario_atendimentos'][$i]) ? $relatorio['beneficiario_atendimentos'][$i] : '0')."</td>";
          if(isset($relatorio['beneficiario_atendimentos'][$i])) {
            $meses[$i] += $relatorio['beneficiario_atendimentos'][$i];
            $meses['total'] += $relatorio['beneficiario_atendimentos'][$i];
          }
        }
        $html .= "<td style='text-align: center;'>{$meses['total']}</td>
                  </tr>";

        $meses['total'] = 0;
        $html .= "<tr>
                      <td>Dependentes (atendimentos)</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['dependente_atendimentos'][$i]) ? $relatorio['dependente_atendimentos'][$i] : '0')."</td>";
          if(isset($relatorio['dependente_atendimentos'][$i])) {
            $meses[$i] += $relatorio['dependente_atendimentos'][$i];
            $meses['total'] += $relatorio['dependente_atendimentos'][$i];
          }
        }
        $html .= "<td style='text-align: center;'>{$meses['total']}</td>
                  </tr>";

        $meses['total'] = 0;
        $html .= "<tr>
                      <td>Comunidade (atendimentos)</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['comunidade_atendimentos'][$i]) ? $relatorio['comunidade_atendimentos'][$i] : '0')."</td>";
          if(isset($relatorio['comunidade_atendimentos'][$i])) {
            $meses[$i] += $relatorio['comunidade_atendimentos'][$i];
            $meses['total'] += $relatorio['comunidade_atendimentos'][$i];
          }
        }
        $html .= "<td style='text-align: center;'>{$meses['total']}</td>
                  </tr>";

        $html .= "<tr>
                      <td><b>Total<b/></td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($meses[$i]) ? $meses[$i] : '0')."</td>";
        }
        $meses['total'] = 0;
        $html .= "<td style='text-align: center;'>".array_sum($meses)."</td>
                  </tr>";

        //$html .= "<tr><td style='border: 0;' colspan='14'><br/></td></tr>";


        $meses = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0,'total'=>0];
        $html .= "<tr>
                      <td>Trabalhadores (pessoas)</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['beneficiario_pessoas'][$i]) ? $relatorio['beneficiario_pessoas'][$i] : '0')."</td>";
          if(isset($relatorio['beneficiario_pessoas'][$i])) {
            $meses[$i] += $relatorio['beneficiario_pessoas'][$i];
            $meses['total'] += $relatorio['beneficiario_pessoas'][$i];
          }
        }
        $html .= "<td style='text-align: center;'>{$meses['total']}</td>
                  </tr>";

        $meses['total'] = 0;
        $html .= "<tr>
                      <td>Dependentes (pessoas)</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['dependente_pessoas'][$i]) ? $relatorio['dependente_pessoas'][$i] : '0')."</td>";
          if(isset($relatorio['dependente_pessoas'][$i])) {
            $meses[$i] += $relatorio['dependente_pessoas'][$i];
            $meses['total'] += $relatorio['dependente_pessoas'][$i];
          }
        }
        $html .= "<td style='text-align: center;'>{$meses['total']}</td>
                  </tr>";

        $meses['total'] = 0;
        $html .= "<tr>
                      <td>Comunidade (pessoas)</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['comunidade_pessoas'][$i]) ? $relatorio['comunidade_pessoas'][$i] : '0')."</td>";
          if(isset($relatorio['comunidade_pessoas'][$i])) {
            $meses[$i] += $relatorio['comunidade_pessoas'][$i];
            $meses['total'] += $relatorio['comunidade_pessoas'][$i];
          }
        }
        $html .= "<td style='text-align: center;'>{$meses['total']}</td>
                  </tr>";

        $html .= "<tr>
                      <td><b>Total<b/></td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($meses[$i]) ? $meses[$i] : '0')."</td>";
        }
        $meses['total'] = 0;
        $html .= "<td style='text-align: center;'>".array_sum($meses)."</td>
                  </tr>";

        $html .= "<tr>
                      <td>Altas Concedidas</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['altas'][$i]) ? $relatorio['altas'][$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($relatorio['altas'])."</td>
                  </tr>";

        $html .= "<tr>
                      <td>Consulta Inicial</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['inicial'][$i]) ? $relatorio['inicial'][$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($relatorio['inicial'])."</td>
                  </tr>";

        $html .= "<tr>
                      <td>Emergencias</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['emergencial'][$i]) ? $relatorio['emergencial'][$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($relatorio['emergencial'])."</td>
                  </tr>";

        $html .= "<tr>
                      <td>Faltas &agrave;s Consultas</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['faltas'][$i]) ? $relatorio['faltas'][$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($relatorio['faltas'])."</td>
                  </tr>";

        $html .= "<tr>
                      <td>Dias &Uacute;teis</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['dias'][$i]) ? $relatorio['dias'][$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($relatorio['dias'])."</td>
                  </tr>";

        $html .= "<tr>
                      <td>Empresas Atendidas</td>";
        for ($i=1; $i <= 12; $i++) {
          $html .= "<td style='text-align: center;'>".(isset($relatorio['empresas'][$i]) ? $relatorio['empresas'][$i] : '0')."</td>";
        }
        $html .= "<td style='text-align: center;'>".array_sum($relatorio['empresas'])."</td>
                  </tr>";

        $html .= "</tbody>";
        $html .= "</table>";

        $mpdf = new mPDF();
        $mpdf->SetTitle('SINTETICO ANULA ODONTOLOGICO');
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
        $mpdf->AddPage('L','','','','',null,null,25,15,0,0);

        $mpdf->WriteHTML("<style> th, td { border: 1px solid #ddd; padding: 2px; font-size: 12px;} td { border-top: 0;} </style>");
        $mpdf->WriteHTML($html);
        //$mpdf->Output('SINTETICO ANULA ODONTOLOGICO.pdf', 'D');
        $mpdf->Output();
        exit;
      }

    }

}
