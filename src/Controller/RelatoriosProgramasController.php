<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Mpdf\Mpdf;

class RelatoriosProgramasController extends AppController {

    public function beforeFilter(Event $event) {
      parent::beforeFilter($event);
      $this->Auth->allow(['imprimir']);
    }

    public function imprimir($id = null) {
      $this->loadModel('Programas');
      $this->loadModel('Empresas');
      $this->loadModel('Anexos');
      $this->loadModel('Ambientes');
      $this->loadModel('ProgramasEpi');
      $this->loadModel('ProgramasTextos');
      $this->loadModel('ProgramasPlanosAcoes');
      $this->loadModel('ProgramasResponsaveis');
      $this->loadModel('ProgramasPerigosDanos');
      $this->loadModel('ProgramasMedidasControles');
      $this->loadModel('ProgramasPerfilExposicoes');
      $this->loadModel('ProgramasAvaliacoesRiscos');
      $this->loadModel('ProgramasAvaliacoesQuantitativas');
      $this->loadModel('AmbientesQuimicos');
      $this->loadModel('AmbientesGrupos');

      $programa = $this->Programas->get($id);
      $empresa = $this->Empresas->get($programa['empresas_id']);
      $capa = $this->ProgramasTextos->find('all',['conditions' => ['programas_id' => $programa['id'], 'tipo' => '1'], 'order' => ['id' => 'ASC']])->first();
      $revisoes = $this->ProgramasTextos->find('all',['conditions' => ['programas_id' => $programa['id'], 'tipo' => '5'], 'order' => ['id' => 'ASC']])->first();
      $glossario = $this->ProgramasTextos->find('all',['conditions' => ['programas_id' => $programa['id'], 'tipo' => '2'], 'order' => ['id' => 'ASC']])->first();
      $base = $this->ProgramasTextos->find('all',['conditions' => ['programas_id' => $programa['id'], 'tipo' => '3'], 'order' => ['id' => 'ASC']])->toArray();
      $desenvolvimento = $this->ProgramasTextos->find('all',['conditions' => ['programas_id' => $programa['id'], 'tipo' => '4'], 'order' => ['id' => 'ASC']])->toArray();
      $anexos = $this->Anexos->find('all',['conditions' => ['trash is null', 'tags_id' => '1', 'programas_id' => $programa['id']], 'order' => ['nome' => 'ASC']])->toArray();

      $responsaveis = $this->ProgramasResponsaveis->find('all',[
        'conditions' => [
          'ProgramasResponsaveis.programas_id' => $programa['id']
        ],
        'fields' => [
          'ProgramasResponsaveis.id',
          'ProgramasResponsaveis.crea_mte',
          'nis_responsavel' => 'ProgramasResponsaveis.nis_responsaveil',
          'nome_responsavel' => 'ProgramasResponsaveis.nome_responsaveil',
          'Funcao.descricao'
        ],
        'join' => [
          [
            'table' => 'apoio_funcoes',
            'alias' => 'Funcao',
            'type' => 'INNER',
            'conditions' => 'Funcao.id = ProgramasResponsaveis.funcoes_id',
          ]
        ],
        'order' => ['Funcao.descricao' => 'ASC']
      ])->toArray();

      $ambientes = $this->Ambientes->find('all',[
        'conditions' => [
          'pa.programas_id' => $programa['id'],
          'Ambientes.empresas_id' => $programa['empresas_id']
        ],
        'fields' => [
          'Programa.id',
          'Ambientes.id',
          'Ambiente.id',
          'Ambiente.descricao',
          'Setor.descricao',
          'Processo.processo',
          'Processo.descricao'
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
          ],
          [
            'table' => 'ambientes_setores',
            'alias' => 'Setor',
            'type' => 'INNER',
            'conditions' => 'Ambientes.id = Setor.ambientes_id',
          ],
          [
            'table' => 'ambientes_processos',
            'alias' => 'Processo',
            'type' => 'INNER',
            'conditions' => 'Ambientes.id = Processo.ambientes_id',
          ]
        ],
        'order' => ['Ambiente.descricao' => 'ASC', 'Processo.processo' => 'ASC']
      ])->toArray();

      $quimicos = $this->AmbientesQuimicos->find('all',[
        'conditions' => [
          'AmbientesQuimicos.programas_id' => $programa['id'],
        ],
        'fields' => [
          'Ambiente.id',
          'Ambiente.descricao',
          'AmbientesQuimicos.produto_quimico',
          'AmbientesQuimicos.substancia_ativa',
          'AmbientesQuimicos.forma_fisica_contaminante',
        ],
        'join' => [
          [
            'table' => 'ambientes',
            'alias' => 'ambientes',
            'type' => 'INNER',
            'conditions' => 'ambientes.id = AmbientesQuimicos.ambientes_id',
          ],
          [
            'table' => 'apoio_ambientes',
            'alias' => 'Ambiente',
            'type' => 'INNER',
            'conditions' => 'Ambiente.id = ambientes.apoio_ambientes_id',
          ]
        ],
        'order' => []
      ])->toArray();

      $grupos = $this->AmbientesGrupos->find('all',[
        'conditions' => [
          'AmbientesGrupos.programas_id' => $programa['id'],
        ],
        'fields' => [
          'Ambiente.id',
          'Ambiente.descricao',
          'AmbientesGrupos.id',
          'AmbientesGrupos.programas_id',
          'AmbientesGrupos.ambientes_id',
          'AmbientesGrupos.nome',
          'AmbientesGrupos.numero',
          'AmbientesGrupos.descricao',
          'AmbientesGrupos.fase_identificacao',
          'Processo.processo'
        ],
        'join' => [
          [
            'table' => 'ambientes',
            'alias' => 'ambientes',
            'type' => 'INNER',
            'conditions' => 'ambientes.id = AmbientesGrupos.ambientes_id',
          ],
          [
            'table' => 'apoio_ambientes',
            'alias' => 'Ambiente',
            'type' => 'INNER',
            'conditions' => 'Ambiente.id = ambientes.apoio_ambientes_id',
          ],
          [
            'table' => 'ambientes_grupos_processos',
            'alias' => 'Grupo',
            'type' => 'INNER',
            'conditions' => 'Grupo.ambientes_grupos_id = AmbientesGrupos.id',
          ],
          [
            'table' => 'ambientes_processos',
            'alias' => 'Processo',
            'type' => 'INNER',
            'conditions' => 'Grupo.ambientes_processos_id = Processo.id',
          ]
        ],
        'order' => []
      ])->toArray();

      $quantitativas = $this->ProgramasAvaliacoesQuantitativas->find('all',[
        'conditions' => [
          'ProgramasAvaliacoesQuantitativas.programas_id' => $programa['id'],
        ],
        'fields' => [
          'Ambiente.id',
          'Ambiente.descricao',
          'Empresa.nome',
          'Empresa.identificacao',
          'Beneficiario.nome',
          'Beneficiario.pis',
          'TipoAvaliacao.descricao',
          'TipoExposicao.descricao',
          'ProgramasAvaliacoesQuantitativas.numero_avaliacao',
          'ProgramasAvaliacoesQuantitativas.numero_trabalhadores',
          'ProgramasAvaliacoesQuantitativas.grupo_homogeneo',
          'ProgramasAvaliacoesQuantitativas.observacao_atividades',
          'ProgramasAvaliacoesQuantitativas.equipamento_amostragem',
          'ProgramasAvaliacoesQuantitativas.metodologia_avaliacao',
          'ProgramasAvaliacoesQuantitativas.descricao_atividades',
          'ProgramasAvaliacoesQuantitativas.dados_ambiente',
          'ProgramasAvaliacoesQuantitativas.regime_revezamento',
          'ProgramasAvaliacoesQuantitativas.tempo_exposicao',
          'ProgramasAvaliacoesQuantitativas.possivel_dano',
          'ProgramasAvaliacoesQuantitativas.data_avaliacao',
          'funcao' => '(SELECT f.descricao FROM beneficiarios_funcoes b INNER JOIN apoio_funcoes f ON f.id = b.funcoes_id WHERE b.beneficiarios_id = 3 ORDER BY b.situacao DESC LIMIT 1)',
          'ghes' => 'GROUP_CONCAT(Grupo.nome)'
        ],
        'join' => [
          [
            'table' => 'empresas',
            'alias' => 'Empresa',
            'type' => 'INNER',
            'conditions' => 'Empresa.id = ProgramasAvaliacoesQuantitativas.empresas_id',
          ],
          [
            'table' => 'beneficiarios',
            'alias' => 'Beneficiario',
            'type' => 'INNER',
            'conditions' => 'Beneficiario.id = ProgramasAvaliacoesQuantitativas.beneficiarios_id',
          ],
          [
            'table' => 'ambientes',
            'alias' => 'ambientes',
            'type' => 'INNER',
            'conditions' => 'ambientes.id = ProgramasAvaliacoesQuantitativas.ambientes_id',
          ],
          [
            'table' => 'apoio_ambientes',
            'alias' => 'Ambiente',
            'type' => 'INNER',
            'conditions' => 'Ambiente.id = ambientes.apoio_ambientes_id',
          ],
          [
            'table' => 'programas_gh_ghe',
            'alias' => 'ghghe',
            'type' => 'INNER',
            'conditions' => 'ghghe.avaliacoes_quantitativas_id = ProgramasAvaliacoesQuantitativas.id',
          ],
          [
            'table' => 'ambientes_grupos',
            'alias' => 'Grupo',
            'type' => 'INNER',
            'conditions' => 'ghghe.ambientes_grupos_id = Grupo.id',
          ],
          [
            'table' => 'apoio_avaliacoes',
            'alias' => 'TipoAvaliacao',
            'type' => 'INNER',
            'conditions' => 'TipoAvaliacao.id = ProgramasAvaliacoesQuantitativas.apoio_avaliacoes_id',
          ],
          [
            'table' => 'apoio_exposicoes',
            'alias' => 'TipoExposicao',
            'type' => 'INNER',
            'conditions' => 'TipoExposicao.id = ProgramasAvaliacoesQuantitativas.apoio_exposicoes_id',
          ]
        ],
        'group' => ['ProgramasAvaliacoesQuantitativas.id'],
        'order' => ['TipoAvaliacao.descricao']
      ])->toArray();



      $setores = [];
      foreach ($ambientes as $ambiente) {
        $setores[$ambiente['Ambiente']['id']]['setor'] = $ambiente['Ambiente']['descricao'];
        $setores[$ambiente['Ambiente']['id']]['descricao'] = $ambiente['Setor']['descricao'];
        $setores[$ambiente['Ambiente']['id']]['processos'][] = [
          'processo' => $ambiente['Processo']['processo'],
          'descricao' => $ambiente['Processo']['descricao']
        ];
      }

      $produtos = [];
      foreach ($quimicos as $quimico) {
        $produtos[$quimico['Ambiente']['id']]['setor'] = $quimico['Ambiente']['descricao'];
        $produtos[$quimico['Ambiente']['id']]['produtos'][] = [
          'produto' => $quimico['produto_quimico'],
          'substancia' => $quimico['substancia_ativa'],
          'forma' => $quimico['forma_fisica_contaminante']
        ];
      }

      $ghes = [];
      foreach ($grupos as $grupo) {
        $fase = '';
        switch ($grupo['fase_identificacao']) {
          case '1': $fase = 'Antecipa&ccedil;&atilde;o'; break;
          case '2': $fase = 'Reconhecimento'; break;
          default: break;
        }

        $ghes[$grupo['Ambiente']['id']]['setor'] = $grupo['Ambiente']['descricao'];
        $ghes[$grupo['Ambiente']['id']]['ghes'][] = [
          'cargo' => $grupo['Processo']['processo'],
          'descricao' => $grupo['descricao'],
          'numero' => $grupo['numero'],
          'fase' => $fase
        ];
      }

      $tabelaGHEs = [];
      foreach ($grupos as $g) {
        $danos = $this->ProgramasPerigosDanos->find('all',[
          'conditions' => [
            'ProgramasPerigosDanos.ambientes_grupos_id' => $g['id'],
            'ProgramasPerigosDanos.programas_id' => $g['programas_id'],
            'ProgramasPerigosDanos.ambientes_id' => $g['ambientes_id'],
          ],
          'fields' => [
            'ProgramasPerigosDanos.id',
            'ProgramasPerigosDanos.possivel_dano',
            'ProgramasPerigosDanos.fonte_geradora',
            'ProgramasPerigosDanos.meio_propagacao',
            'ProgramasPerigosDanos.limite_exposicao',
            'Agente.descricao',
            'Risco.descricao',
            'Risco.codigo',
          ],
          'join' => [
            [
              'table' => 'apoio_agentes_tipos',
              'alias' => 'Agente',
              'type' => 'INNER',
              'conditions' => 'Agente.id = ProgramasPerigosDanos.agentes_tipos_id',
            ],
            [
              'table' => 'apoio_fatores_riscos',
              'alias' => 'Risco',
              'type' => 'INNER',
              'conditions' => 'Risco.id = ProgramasPerigosDanos.apoio_fatores_riscos_id',
            ]
          ]
        ])->toArray();

        $dano = [];
        foreach ($danos as $d) {
          $medidas = $this->ProgramasMedidasControles->find('all',[
            'conditions' => [
              'ProgramasMedidasControles.ambientes_grupos_id' => $g['id'],
              'ProgramasMedidasControles.programas_id' => $g['programas_id'],
              'ProgramasMedidasControles.ambientes_id' => $g['ambientes_id'],
              'ProgramasMedidasControles.perigos_danos_id' => $d['id'],
            ],
            'fields' => [
              'ProgramasMedidasControles.id',
              'ProgramasMedidasControles.descricao',
              'ProgramasMedidasControles.eficaz',
              'ProgramasMedidasControles.epc'
            ]
          ])->first();

          $epi = $this->ProgramasEpi->find('all', [
            'conditions' => [
              'ProgramasEpi.ambientes_grupos_id' => $g['id'],
              'ProgramasEpi.programas_id' => $g['programas_id'],
              'ProgramasEpi.ambientes_id' => $g['ambientes_id'],
              'ProgramasEpi.perigos_danos_id' => $d['id'],
            ],
            'fields' => [
              'ProgramasEpi.id',
              'ProgramasEpi.certificado_aprovacao',
              'ProgramasEpi.descricao',
              'ProgramasEpi.atenuacao',
              'ProgramasEpi.eficaz'
            ]
          ])->first();

          $exposicao = $this->ProgramasPerfilExposicoes->find('all', [
            'conditions' => [
              'ProgramasPerfilExposicoes.ambientes_grupos_id' => $g['id'],
              'ProgramasPerfilExposicoes.programas_id' => $g['programas_id'],
              'ProgramasPerfilExposicoes.ambientes_id' => $g['ambientes_id'],
              'ProgramasPerfilExposicoes.perigos_danos_id' => $d['id'],
            ],
            'fields' => [
              'ProgramasPerfilExposicoes.id',
              'ProgramasPerfilExposicoes.tecnica_utilizada',
              'ProgramasPerfilExposicoes.intensidade',
              'Exposicao.sigla'
            ],
            'join' => [
              [
                'table' => 'apoio_exposicoes',
                'alias' => 'Exposicao',
                'type' => 'INNER',
                'conditions' => 'Exposicao.id = ProgramasPerfilExposicoes.apoio_exposicoes_id',
              ]
            ]
          ])->first();

          $avalicao = $this->ProgramasAvaliacoesRiscos->find('all', [
            'conditions' => [
              'ProgramasAvaliacoesRiscos.ambientes_grupos_id' => $g['id'],
              'ProgramasAvaliacoesRiscos.programas_id' => $g['programas_id'],
              'ProgramasAvaliacoesRiscos.ambientes_id' => $g['ambientes_id'],
              'ProgramasAvaliacoesRiscos.perigos_danos_id' => $d['id'],
            ],
            'fields' => [
              'ProgramasAvaliacoesRiscos.id',
              'ProgramasAvaliacoesRiscos.grau_incerteza',
              'ProgramasAvaliacoesRiscos.probabilidade',
              'ProgramasAvaliacoesRiscos.gravidade',
              'Risco.descricao'
            ],
            'join' => [
              [
                'table' => 'apoio_riscos',
                'alias' => 'Risco',
                'type' => 'INNER',
                'conditions' => 'Risco.id = ProgramasAvaliacoesRiscos.apoio_riscos_id',
              ]
            ]
          ])->first();

          $acoes = $this->ProgramasPlanosAcoes->find('all', [
            'conditions' => [
              'ProgramasPlanosAcoes.ambientes_grupos_id' => $g['id'],
              'ProgramasPlanosAcoes.programas_id' => $g['programas_id'],
              'ProgramasPlanosAcoes.ambientes_id' => $g['ambientes_id'],
              'ProgramasPlanosAcoes.perigos_danos_id' => $d['id'],
            ],
            'fields' => [
              'ProgramasPlanosAcoes.id',
              'ProgramasPlanosAcoes.acoes_necessarias',
              'ProgramasPlanosAcoes.monitoracao'
            ]
          ])->first();

          // pr($acoes);
          // exit;

          $dano[] = [
            'id' => $d['id'],
            'possivel_dano' => $d['possivel_dano'],
            'fonte_geradora' => $d['fonte_geradora'],
            'meio_propagacao' => $d['meio_propagacao'],
            'limite_exposicao' => $d['limite_exposicao'],
            'agente' => $d['Agente']['descricao'],
            'risco' => "{$d['Risco']['descricao']} {$d['Risco']['codigo']}",
            'controle' => $medidas['descricao'],
            'medida_eficaz' => $medidas['eficaz'],
            'epi' => $epi['descricao'],
            'certificado_aprovacao' => $epi['certificado_aprovacao'],
            'atenuacao' => $epi['atenuacao'],
            'epi_eficaz' => $epi['eficaz'],
            'intensidade' => $exposicao['intensidade'],
            'tecnica_utilizada' => $exposicao['tecnica_utilizada'],
            'exposicao' => $exposicao['Exposicao']['sigla'],
            'grau_incerteza' => $avalicao['grau_incerteza'],
            'probabilidade' => $avalicao['probabilidade'],
            'gravidade' => $avalicao['gravidade'],
            'avaliacao_risco' => $avalicao['Risco']['descricao'],
            'acoes_necessarias' => $acoes['acoes_necessarias'],
            'monitoracao' => $acoes['monitoracao']
          ];
        }

        $tabelaGHEs[$g['Ambiente']['id']][] = [
          'setor' => $g['Ambiente']['descricao'],
          'ghe' => "GHE {$g['numero']} ({$g['nome']})",
          'descricao' => $g['descricao'],
          'processo' => $g['Processo']['processo'],
          'fase' => $g['fase_identificacao'],
          'danos' => $dano
        ];
      }

      // pr($tabelaGHEs);
      // exit;

      $mpdf = new mPDF();
      $mpdf->SetTitle('RELATORIO PPRA');
      $mpdf->SetDisplayMode('fullpage');

      $mpdf->AddPage('','','','','',null,null,25,15,0,0);

      $mpdf->WriteHTML("<style> tr, th, td { border: 1px solid #ddd; padding: 5px; font-size: 12px;} table { border: 1px solid #ddd; width: 100% !important; } </style>");
      $mpdf->WriteHTML("
        <img src='./img/seconci_logo.png' style='float: left; width: 200px;'/>
        <img src='./img/seconci_logo.png' style='float: right; width: 150px;'/>
      ");

      $capa['texto'] .= "<p style='text-align: center; font-size: 18px;'><b>{$empresa['nome']}\n<small>{$empresa['logradouro_localizacao']}</small></b></p>";
      $mpdf->WriteHTML(nl2br($capa['texto']));

      $mpdf->SetHTMLFooter("<p style='text-align: center; font-size: 12px; padding-bottom: 150px;'> Vigência " . date('m/Y', strtotime($programa['data_inicial'])) . " a " . date('m/Y', strtotime($programa['data_final'])) . "<br/>Versão xx</p>");
      $mpdf->WriteHTML('<pagebreak>');  
      $mpdf->SetHTMLFooter("<div style='text-align: right;'>{PAGENO}</div> <div style='text-align: center; font-size: 10px; padding-bottom: 15px;' > <small>{$empresa['nome']}<br/>{$empresa['logradouro_localizacao']}</small></div>");

      $mpdf->WriteHTML("<div style='height: 50%;'></div>"); 
      $mpdf->WriteHTML("<p style='text-align: right; font-size: 14px;'>ELABORAÇÃO</p>"); 
      
      foreach ($responsaveis as $key => $value) {
        $mpdf->WriteHTML("
          <p style='text-align: right; font-size: 12px;'>
            {$value['nome_responsavel']}<br/>
            {$value['crea_mte']}<br/>
            {$value['Funcao']['descricao']}
          </p>
        "); 
      }
      
      $mpdf->WriteHTML("
        <p style='text-align: right; font-size: 12px;'>
          Seconci-DF Serviço Social da Indústria da Construção Civil do Distrito Federal<br/>
          SPLM Conjunto 3 – Lotes 11, 13 e 15 – Setor Placa da Mercedes<br/>
          Núcleo Bandeirante/DF<br/>
          Telefone 3399-1888<br/>
          www.seconci-df.org.br
        </p>
      ");  

      $mpdf->WriteHTML('<pagebreak>');

      $html = '';
      if(isset($revisoes['id'])) {
        if($revisoes['show_titulo']) {
          $html .= "<h3 style='text-align: center;'>{$revisoes['titulo']}</h3>";
        }
  
        $html .= "<p>".$revisoes['texto']."</p>";
  
        if($revisoes['pagebreak']) {
          $html .= '<pagebreak>';
        }
      }
      
      $html .= $this->RelatoriosProgramas->infoGestorPrograma($empresa, $programa);

      $mpdf->WriteHTML($html);      
      $mpdf->WriteHTML('<tocpagebreak resetpagenum="6" pagenumstyle="6" suppress="off" toc-resetpagenum="0" />');
      
      $html = $this->RelatoriosProgramas->titleIndex("1. CADASTRO DA EMPRESA");
      $html .= $this->RelatoriosProgramas->tabelaDadosEmpresa($empresa);

      $html .= $this->RelatoriosProgramas->titleIndex("2. DADOS DA FRENTE DE TRABALHO");
      $html .= $this->RelatoriosProgramas->titleIndex("2.1. QUADRO DE CARGOS");
      $html .= $this->RelatoriosProgramas->titleIndex("2.2. DESCRIÇÃO DOS CARGOS");
      $html .= $this->RelatoriosProgramas->titleIndex("2.3. QUADRO DE CARGOS PREVISTAS DE FUNCIONÁRIOS DA EMPRESA");
      $html .= $this->RelatoriosProgramas->titleIndex("2.4. EMPREITEIROS");
      $html .= '<pagebreak>';

      $html .= $this->RelatoriosProgramas->titleIndex('DOCUMENTO BASE');
      foreach ($base as $b) {
        if($b['show_titulo']) {
          $html .= $this->RelatoriosProgramas->titleIndex($b['titulo']);
        }
        $html .= "<p>".$b['texto']."</p>";
        if($b['pagebreak']) {
          $html .= '<pagebreak>';
        }
      }

      $html .= $this->RelatoriosProgramas->titleIndex('DESENVOLVIMENTO DO PPRA');
      foreach ($desenvolvimento as $d) {
        if($d['show_titulo']) {
          $html .= $this->RelatoriosProgramas->titleIndex($d['titulo']);
        }
        $html .= "<p>".$d['texto']."</p>";
        if($d['pagebreak']) {
          $html .= '<pagebreak>';
        }
      }

      // $mpdf->WriteHTML($html);
      // $html = $this->RelatoriosProgramas->titleIndex("Tabelas de Identifica&ccedil;&atilde;o de Perigos e Avalia&ccedil;&atilde;o de Riscos por GHE");
      // $html .= $this->RelatoriosProgramas->tabelaIdePegigoAvaliacaoRiscoGHE($tabelaGHEs);

      // $html .= '<pagebreak>';
      // $html .= "<div style='padding-top: 40%;'><h1 style='text-align: center;'>Desenvolvimento do Programa</h1></div>";
      // $html .= '<pagebreak>';

      // $html .= "<tocentry content='Atividade e Vis&atilde;o Geral do Processo Produtivo' />  <h3>Atividade e Vis&atilde;o Geral do Processo Produtivo</h3>";
      // foreach ($anexos as $anexo) {
      //   $html .= "<p style='text-align: center;'><img style='width: 100%;' src='{$this->request->webroot}anexos/{$anexo['tags_id']}/{$programa['id']}/{$anexo['id']}.jpg' alt='{$anexo['nome']}'/> <br> <small>{$anexo['descricao']} </small></p>";
      // }

      // $html .= '<pagebreak>';
      // $html .= $this->RelatoriosProgramas->titleIndex("Defini&ccedil;&atilde;o dos setores e processos");
      // $html .= $this->RelatoriosProgramas->tabelaSetoresProcessos($setores);

      // $i=0;
      // $html .= '<pagebreak>';
      // $html .= $this->RelatoriosProgramas->titleIndex("Setores");
      // $html .= '<p>A empresa &eacute; composta por ' . sizeof($setores) . ' setores:</p>';
      // foreach ($setores as $setor) { $i++;
      //   $html .= "<p>{$i} - {$setor['setor']}</p>";
      // }

      // $html .= $this->RelatoriosProgramas->titleIndex("Invent&aacute;rio de produtos qu&iacute;micos");
      // $html .= $this->RelatoriosProgramas->tabelaProdutosQuimicos($produtos);

      // $html .= $this->RelatoriosProgramas->titleIndex("Defini&ccedil;&atilde;o dos Grupos Homog&ecirc;neos de Exposi&ccedil;&atilde;o - GHE");
      // $html .= $this->RelatoriosProgramas->tabelaGHE($ghes);

      

      // $html .= $this->RelatoriosProgramas->tabelaResponsaveis($responsaveis);

      // $html .= '<pagebreak>';
      // $html .= "<div style='padding-top: 40%;'><h1 style='text-align: center;'>Tabelas de Identifica&ccedil;&atilde;o de Perigos e Avalia&ccedil;&atilde;o de Riscos por GHE</h1></div>";

      // $mpdf->WriteHTML($html);

      // $html = $this->RelatoriosProgramas->titleIndex("Tabelas de Identifica&ccedil;&atilde;o de Perigos e Avalia&ccedil;&atilde;o de Riscos por GHE");
      // $html .= $this->RelatoriosProgramas->tabelaIdePegigoAvaliacaoRiscoGHE($tabelaGHEs);

      // $mpdf->AddPage('L');
      // $mpdf->WriteHTML($html);

      // $html = "<div style='padding-top: 40%;'><h1 style='text-align: center;'>Planilhas de Apresenta&ccedil;&atilde;o dos Resultados das avalia&ccedil;&otilde;es quantitativas</h1></div>";
      // $html .= '<pagebreak>';
      // $html .= $this->RelatoriosProgramas->titleIndex("Planilhas de Apresenta&ccedil;&atilde;o dos Resultados das avalia&ccedil;&otilde;es quantitativas");
      // $html .= $this->RelatoriosProgramas->tabelaQuantitativa($quantitativas);

      // if($glossario['pagebreak']) $html .= '<pagebreak>';
      // if($glossario['show_titulo']) $html .= $this->RelatoriosProgramas->titleIndex($glossario['titulo']);
      // $html .= "<p>".nl2br($glossario['texto'])."</p>";

      // $mpdf->AddPage('P');

      $mpdf->WriteHTML($html);

      $mpdf->Output();
      exit;
    }

}
