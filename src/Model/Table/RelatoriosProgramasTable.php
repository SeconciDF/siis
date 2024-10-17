<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class RelatoriosProgramasTable extends Table {

    public function titleIndex($string) {
      return "<tocentry content='{$string}' /> <h3>{$string}</h3>";
    }

    public function infoGestorPrograma($empresa, $programa) {
      $mes = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
      $html = "<h3 style='text-align: center;'>GESTOR DO PPRA - ANO " . date('Y', strtotime($programa['data_inicial'])) . "/" . date('Y', strtotime($programa['data_final'])) . "</h3><br/><br/>";
      
      $html .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; A Empresa {$empresa['nome']} indica o o(a) Sr(a). {$empresa['programa_no_pessoa_contato']}, conforme o item 3.1, ocupando o cargo de {$empresa['programa_no_pessoa_cargo']}, para assumir as responsabilidades de GESTOR DO PPRA - Programa de Prevenção dos Riscos Ambientais da frente de trabalho localizada no(a) {$empresa['logradouro_localizacao']}. </p><br/>";
      $html .= "<p style='text-align: right;'>Ciente e de acordo</p><br/>";
      $html .= "<p style='text-align: right;'>Brasília-DF, " . date('d') . " de " . $mes[((int) date('m'))] . " de " . date('Y') . " </p><br/><br/><br/>";
      $html .= "<p style='text-align: center;'>ASSINATURA DO GESTOR DO PPRA</p><br/><br/><br/>";

      $html .= "<p style='text-align: center;'>{$empresa['responsavel_empresa']}<br/>RESPONSÁVEL DA EMPRESA</p>";

      return $html;
    }

    public function tabelaDadosEmpresa($empresa) {
        return "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
          <tbody>
            <tr>
              <td colspan='2' style='width: 40%;'><b>Razão social</b><br/> {$empresa['nome']} &nbsp;</td>
              <td colspan='2' style='width: 40%;'><b>Unidade/obra</b><br/> &nbsp;</td>
              <td style='width: 20%;'><b>CNPJ</b><br/> {$empresa['identificacao']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='4'><b>Endereço</b><br/> {$empresa['logradouro_localizacao']} &nbsp;</td>
              <td><b>CEP</b><br/> {$empresa['cep_localizacao']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='2'><b>Bairro</b><br/> {$empresa['bairro_localizacao']} &nbsp;</td>
              <td colspan='2'><b>Cidade</b><br/> {$empresa['cidade_localizacao']} &nbsp;</td>
              <td><b>UF</b><br/> {$empresa['estado_localizacao']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='2'><b>E-mail</b><br/> {$empresa['email_contato']} &nbsp;</td>
              <td colspan='2'><b>Telefone</b><br/> &nbsp;</td>
              <td><b>Fax</b><br/> &nbsp;</td>
            </tr>
            <tr>
              <td colspan='5'><b>Ramo de atividade</b><br/> {$empresa['ramo_atividade']} &nbsp;</td>
            </tr>
            <tr>
              <td><b>CNAE</b><br/> {$empresa['cnae']} &nbsp;</td>
              <td><b>Grau de risco (NR 4)</b><br/> {$empresa['grau_risco']} &nbsp;</td>
              <td colspan='2'><b>Inscrição estadual</b><br/> {$empresa['inscricao_estadual']} &nbsp;</td>
              <td><b>Inscrição municipal</b><br/> {$empresa['inscricao_municipal']} &nbsp;</td>
            </tr>
            <tr>
              <td style='width: 20%;'><b>Total de trabalhadores</b><br/> {$empresa['total_trabalhadores']} &nbsp;</td>
              <td style='width: 20%;'><b>Porte</b><br/> {$empresa['porte']} &nbsp;</td>
              <td style='width: 20%;'><b>Homens</b><br/> {$empresa['quantidade_homens']} &nbsp;</td>
              <td style='width: 20%;'><b>Mulheres</b><br/> {$empresa['quantidade_mulheres']} &nbsp;</td>
              <td style='width: 20%;'><b>Menores 18 anos</b><br/> {$empresa['quantidade_menores']} &nbsp;</td>
            </tr>
            <tr>
              <td><b>SESMT</b><br/> {$empresa['sesmt']} &nbsp;</td>
              <td><b>CIPA</b><br/> {$empresa['cipa']} &nbsp;</td>
              <td colspan='2'><b>Número de membros</b><br/> {$empresa['nome']} &nbsp;</td>
              <td><b>Designado da CIPA</b><br/> {$empresa['designado_cipa']} &nbsp;</td>
            </tr>
            <tr>
              <td rowspan='3'><b>Responsável pela empresa</b></td>
              <td colspan='4'><b>Nome</b><br/> {$empresa['nome_contato']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='4'><b>E-mail</b><br/> {$empresa['email_contato']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='4'><b>Telefone</b><br/> &nbsp;</td>
            </tr>
            <tr>
              <td rowspan='3'><b>Contato com a empresa</b></td>
              <td colspan='4'><b>Nome</b><br/> {$empresa['nome_contato']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='4'><b>E-mail</b><br/> {$empresa['email_contato']} &nbsp;</td>
            </tr>
            <tr>
              <td colspan='4'><b>Telefone</b><br/> &nbsp;</td>
            </tr>
            <tr>
              <td colspan='5'><b>O que a empresa produz</b><br/> &nbsp;</td>
            </tr>
          <tbody>
        </table>";
    }

    public function tabelaSetoresProcessos($setores) {
        $table = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
          <tbody>
            <tr>
              <td style='width: 40%;'><b>SETOR</b></td>
              <td style='width: 60%;'><b>PROCESSOS</b></td>
            </tr>";

        $tmpSetor = '';
        foreach ($setores as $setor) {
          foreach ($setor['processos'] as $processo) {
            $table .= "<tr>";
            if($tmpSetor != $setor['setor']) {
              $table .= "<tr> <td valign='top' rowspan='".sizeof($setor['processos'])."'><b>{$setor['setor']}</b><br/>{$setor['descricao']}</td>";
              $tmpSetor = $setor['setor'];
            }

            $table .= "<td valign='top'> <b>{$processo['processo']}</b><br/>{$processo['descricao']}</td>";
            $table .= "</tr>";
          }
        }
        $table .= "<tbody> </table>";

        return $table;
    }

    public function tabelaProdutosQuimicos($produtos) {
        $table = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
          <tbody>
            <tr>
              <td style='width: 10%;'><b>Setor</b></td>
              <td style='width: 30%;'><b>Nome do produto qu&iacute;mico</b></td>
              <td style='width: 30%;'><b>Nome da subst&acirc;ncia ativa</b></td>
              <td style='width: 30%;'><b>Forma f&iacute;sica do contaminante</b></td>
            </tr>";

        $tmpSetor = '';
        foreach ($produtos as $p) {
          foreach ($p['produtos'] as $produto) {
            $table .= "<tr>";
            if($tmpSetor != $p['setor']) {
              $table .= "<tr> <td valign='top' rowspan='".sizeof($p['produtos'])."'>{$p['setor']}</td>";
              $tmpSetor = $p['setor'];
            }

            $table .= "<td valign='top'>{$produto['produto']}</td>";
            $table .= "<td valign='top'>{$produto['substancia']}</td>";
            $table .= "<td valign='top'>{$produto['forma']}</td>";
            $table .= "</tr>";
          }
        }
        $table .= "<tbody> </table>";

        return $table;
    }

    public function tabelaGHE($ghes) {
        $table = "<table cellspacing='0' cellpadding='0' style='width: 100%;'>
          <tbody>
            <tr>
              <td style='width: 15%;'><b>Setor</b></td>
              <td style='width: 10%;'><b>GHE</b></td>
              <td style='width: 20%;'><b>Fase</b></td>
              <td style='width: 20%;'><b>Cargo</b></td>
              <td style='width: 35%;'><b>Descri&ccedil;&atilde;o das Atividades</b></td>
            </tr>";

        $tmpSetor = '';
        foreach ($ghes as $g) {
          foreach ($g['ghes'] as $ghe) {
            $table .= "<tr>";
            if($tmpSetor != $g['setor']) {
              $table .= "<tr> <td valign='top' rowspan='".sizeof($g['ghes'])."'>{$g['setor']}</td>";
              $tmpSetor = $g['setor'];
            }

            $table .= "<td valign='top'>{$ghe['numero']}</td>";
            $table .= "<td valign='top'>{$ghe['fase']}</td>";
            $table .= "<td valign='top'>{$ghe['cargo']}</td>";
            $table .= "<td valign='top'>{$ghe['descricao']}</td>";
            $table .= "</tr>";
          }
        }
        $table .= "<tbody> </table>";

        return $table;
    }

    public function tabelaResponsaveis($responsaveis) {
        $table = "<table cellspacing='0' cellpadding='0' style='width: 100%;'> <tbody>";
        foreach ($responsaveis as $responsavel) {
          $table .= "<tr>
                        <td style='width: 50%;'>
                        {$responsavel['Funcao']['descricao']} <br/>
                        {$responsavel['nome_responsaveil']} <br/>
                        {$responsavel['crea_mte']} <br/>
                        NIS: {$responsavel['nis_responsaveil']}
                        </td>
                        <td valign='top'>Assinatura</td>
                      </tr>";
        }
        $table .= "<tbody> </table>";

        return $table;
    }

    public function tabelaIdePegigoAvaliacaoRiscoGHE($ghes) {
      $table = "";
      foreach ($ghes as $ghe) {
        foreach ($ghe as $g) {
          $fase = ['1'=>'','2'=>''];
          $fase[$g['fase']] = 'X';

          $table .= "<table cellspacing='0' cellpadding='0' style='width: 100%;'> <tbody>";

          $table .= "<tr>
                      <td valign='top' colspan='20'>{$g['ghe']}</td>
                    </tr>
                    <tr>
                      <td valign='top' colspan='10'>Setor: {$g['setor']}</td>
                      <td valign='top' colspan='5'>Total de Trabalhadores expostos: 0</td>
                      <td valign='top' colspan='5'>Fase: ( {$fase['1']} ) Antecipação <br/> ( {$fase['2']} ) Reconhecimento</td>
                    </tr>
                    <tr>
                      <td valign='top' colspan='5'>Processo:<br/>{$g['processo']}</td>
                      <td valign='top' colspan='15'>Descri&ccedil;&atilde;o:<br/>{$g['descricao']}</td>
                    </tr>";

          $table .= "<tr>
                      <td valign='top' rowspan='3'>Agente / Tipo</td>
                      <td valign='top' rowspan='3'>Perigo / Fator de Risco</td>
                      <td valign='top' rowspan='3'>Poss&iacute;vel dano</td>
                      <td valign='top' rowspan='3'>Padr&otilde;es Legais / Limite de Exposi&ccedil;&atilde;o</td>
                      <td valign='top' rowspan='3'>Fonte(s) Geradora(s) / Trajet&oacute;ria e meio de propaga&ccedil;&atilde;o</td>
                      <td valign='top' colspan='6'>Controle(s) Existente(s) e sua Efic&aacute;cia</td>
                      <td valign='top' colspan='3'>Perfil de exposi&ccedil;&atilde;o existente</td>
                      <td valign='top' colspan='4'>Avalia&ccedil;&atilde;o do Risco</td>
                      <td valign='top' rowspan='3'>Defini&ccedil;&atilde;o de a&ccedil;&otilde;es necess&aacute;rias</td>
                      <td valign='top' rowspan='3'>Crit&eacute;rio para Monitora&ccedil;&atilde;o da exposi&ccedil;&atilde;o</td>
                    </tr>
                    <tr>
                      <td valign='top' colspan='2'>POAD / EPC</td>
                      <td valign='top' colspan='4'>EPI</td>
                      <td valign='top' rowspan='2'>Intens./ conc.</td>
                      <td valign='top' rowspan='2'>T&eacute;cnica Utilizada</td>
                      <td valign='top' rowspan='2'>Tipo de Exposi&ccedil;&atilde;o</td>
                      <td valign='top' rowspan='2'>P</td>
                      <td valign='top' rowspan='2'>G</td>
                      <td valign='top' rowspan='2'>Risco</td>
                      <td valign='top' rowspan='2'>IN</td>
                    </tr>
                    <tr>
                      <td valign='top'>Nome</td>
                      <td valign='top'>Eficaz S/N</td>
                      <td valign='top'>Nome</td>
                      <td valign='top'>CA</td>
                      <td valign='top'>Atenua&ccedil;&atilde;o / fator de prote&ccedil;&atilde;o</td>
                      <td valign='top'>Eficaz S/N</td>
                    </tr>";

          foreach ($g['danos'] as $d) {
            $table .= "<tr>
                        <td valign='top'>{$d['agente']}</td>
                        <td valign='top'>{$d['risco']}</td>
                        <td valign='top'>{$d['possivel_dano']}</td>
                        <td valign='top'>{$d['limite_exposicao']}</td>
                        <td valign='top'>{$d['fonte_geradora']}/{$d['meio_propagacao']}</td>
                        <td valign='top'>{$d['controle']}</td>
                        <td valign='top'>{$d['medida_eficaz']}</td>
                        <td valign='top'>{$d['epi']}</td>
                        <td valign='top'>{$d['certificado_aprovacao']}</td>
                        <td valign='top'>{$d['atenuacao']}</td>
                        <td valign='top'>{$d['epi_eficaz']}</td>
                        <td valign='top'>{$d['intensidade']}</td>
                        <td valign='top'>{$d['tecnica_utilizada']}</td>
                        <td valign='top'>{$d['exposicao']}</td>
                        <td valign='top'>{$d['probabilidade']}</td>
                        <td valign='top'>{$d['gravidade']}</td>
                        <td valign='top'>{$d['avaliacao_risco']}</td>
                        <td valign='top'>{$d['grau_incerteza']}</td>
                        <td valign='top'>{$d['acoes_necessarias']}</td>
                        <td valign='top'>{$d['monitoracao']}</td>
                      </tr>";
          }

          $table .= "<tr>
                      <td valign='top' colspan='20'>
                      POAD = Procedimentos Administrativos, EPC = Equipamentos de Prote&ccedil;&atilde;o Coletiva,
                      EPI = Equipamentos de Prote&ccedil;&atilde;o Individual. S = Sim, N = N&atilde;o, N AV = N&atilde;o Avaliado
                      NA = N&atilde;o Se Aplica, I = Inexistente CA = Certificado de Aprova&ccedil;&atilde;o P = Probabilidade
                      G = Gravidade IN = Grau de Incerteza HP = Habitual e permanente HI = Habitual e intermitente EV = Eventual INT = Intermitente
                      </td>
                    </tr>";

          $table .= "<tbody> </table><br/>";
        }
      }

      return $table;
    }

    public function tabelaQuantitativa($quantitativas) {
        $table = "<table cellspacing='0' cellpadding='0' style='width: 100%;'> <tbody>";
        foreach ($quantitativas as $quantitativa) {
          //pr($quantitativa); exit;
          $table .= "<tr>
                        <th valign='top' colspan='3'>Planilha de avalia&ccedil;&atilde;o individual - {$quantitativa['TipoAvaliacao']['descricao']}</th>
                      </tr>
                      <tr>
                        <td colspan='2'><b>Nome da Empresa</b> <br/> {$quantitativa['Empresa']['nome']}</td>
                        <td><b>CNPJ</b> <br/> {$quantitativa['Empresa']['identificacao']}</td>
                      </tr>
                      <tr>
                        <td><b>N&ordm; da Planilha</b> <br/> {$quantitativa['numero_avaliacao']}</td>
                        <td><b>Data Avalia&ccedil;&atilde;o</b> <br/> {$quantitativa['data_avaliacao']->format('d/m/Y')}</td>
                        <td><b>Setor Avaliado</b> <br/> {$quantitativa['Ambiente']['descricao']}</td>
                      </tr>
                      <tr>
                        <td colspan='2'><b>Grupo Homog&ecirc;neo</b> <br/> {$quantitativa['grupo_homogeneo']}</td>
                        <td><b>N&ordm; de Trabalhadores Expostos</b> <br/> {$quantitativa['numero_trabalhadores']}</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>GHE</b> <br/> {$quantitativa['ghes']}</td>
                      </tr>
                      <tr>
                        <td colspan='2'><b>Nome do trabalhador avaliado</b> <br/> {$quantitativa['Beneficiario']['nome']}</td>
                        <td><b>NIT</b> <br/> {$quantitativa['Beneficiario']['pis']}</td>
                      </tr>
                      <tr>
                        <td><b>Cargo</b> <br/> {$quantitativa['funcao']}</td>
                        <td colspan='2'><b>Fun&ccedil;&atilde;o</b> <br/> NA - N&atilde;o Aplic&aacute;vel</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Descri&ccedil;&atilde;o das atividades</b> <br/> {$quantitativa['descricao_atividades']}</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Observa&ccedil;&atilde;o sobre atividade</b> <br/> {$quantitativa['observacao_atividades']}</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Dados do ambiente</b> <br/> {$quantitativa['dados_ambiente']}</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Regime de revezamento</b> <br/> {$quantitativa['regime_revezamento']}</td>
                      </tr>
                      <tr>
                        <td><b>Tipo de exposi&ccedil;&atilde;o</b> <br/> {$quantitativa['TipoExposicao']['descricao']}</td>
                        <td colspan='2'><b>Tempo de exposi&ccedil;&atilde;o (min)</b> <br/> {$quantitativa['tempo_exposicao']}</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Poss&iacute;veis danos a sa&uacute;de</b> <br/> {$quantitativa['possivel_dano']}</td>
                      </tr>
                      <tr>
                        <th colspan='3'>Dados da Amostragem</th>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Equipamento utilizado na amostragem</b> <br/> {$quantitativa['equipamento_amostragem']}</td>
                      </tr>
                      <tr>
                        <td colspan='3'><b>Metodologia de avalia&ccedil;&atilde;o</b> <br/> {$quantitativa['metodologia_avaliacao']}</td>
                      </tr>";
        }
        $table .= "<tbody> </table>";

        return $table;
    }

}
