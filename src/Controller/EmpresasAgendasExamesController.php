<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Event\Event;
use Mpdf\Mpdf;

class EmpresasAgendasExamesController extends AppController {

  public function beforeFilter(Event $event) {
      $this->request->session()->write('Auth.User.MenuActive', 'empresa');
  }

  public function comprovante($id) {
    $this->loadModel('Consultas');
    $guia = $this->Consultas->gerarGuia($id);
    $exames = $this->Consultas->gerarGuiaExames($id);

    $html = '';

    $html .= "<table style='width: 100%; border: 0;'>";
    $html .= "<tr>";
    $html .= "<td style='width: 25%;'> <img src='./img/seconci_logo.png' alt='' width='150' /> </td>";
    $html .= "<td style='width: 50%; font-size: 18px; text-align: center;'> Guia de Encaminhamento </td>";
    $html .= "<td style='width: 25%;'> </td>";
    $html .= "</tr>";
    $html .= "</table>";

    $html .= "<table style='width: 100%; margin-bottom: 10px;'>";
    $html .= "<tr>";
    $html .= "<td style='border-bottom: 1px solid #000;'> <b> C&oacute;digo da Guia: </b> {$guia['CodGuias']} </td>";
    $html .= "<td style='border-bottom: 1px solid #000;'> <b> Data da emiss&atilde;o: </b> " . date('d/m/Y', strtotime($guia['DataEmissao'])) . " </td>";
    $html .= "<td style='border-bottom: 1px solid #000;'>" . ($guia['DataAgendamento'] ? '<b> Data do agendamento: </b>' . date('d/m/Y', strtotime($guia['DataAgendamento'])) . ($guia['CodTipoHorario'] == '1' ? ' - Matutino' : ' - Vespertino') : '<b> Validade da Guia: </b>' . date('d/m/Y', strtotime($guia['DataEmissao'] . ' + 7 days'))) . " </td>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<td colspan='2'> <b> Cl&iacute;nica: </b> {$guia['ClinicaNome']} </td>";
    $html .= "<td> <b> Telefone: </b> {$guia['ClinicaTelefone1']} </td>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<td colspan='3'> <b> Endere&ccedil;o: </b> {$guia['ClinicaEndereco']} </td>";
    $html .= "</tr>";
    $html .= "</table>";

    $html .= "<table style='width: 100%; margin-bottom: 10px;'>";
    $html .= "<tr>";
    $html .= "<td colspan='2'> <b> Empresa: </b> {$guia['razao_social']} </td>";
    $html .= "<td> <b> CPF: </b> {$guia['cpf']} </td>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<td colspan='2'> <b> Trabalhador: </b> {$guia['nm_trab']} </td>";
    $html .= "<td> <b> RG: </b> {$guia['rg']} {$guia['o_emissor']} </td>";
    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<td colspan='2'> <b> Fun&ccedil;&atilde;o: </b> {$guia['NomeFuncao']} </td>";
    $html .= "<td> <b> Telefone: </b> {$guia['celular']} </td>";
    $html .= "</tr>";
    $html .= "</table>";

    $html .= "<table style='width: 100%;'>";
    if($guia['CodClinica'] == '17') {
      $html .= "<tr>";
      $html .= "<td colspan='4' style='text-align: center;'> Campos de preenchimento do Seconci-DF </td>";
      $html .= "</tr>";

      $html .= "<tr>";
      $html .= "<th colspan='2'> Exames </th>";
      $html .= "<th> Data Atendimento </th>";
      $html .= "<th> Assinatura </th>";
      $html .= "</tr>";
    } else {
      $html .= "<tr>";
      $html .= "<td> <b> Exames </b> </td>";
      $html .= "</tr>";
    }

    foreach ($exames as $exame) {
      if($guia['CodClinica'] == '17') {
        $html .= "<tr>";
        $html .= "<td style='border-bottom: 1px solid #000; padding: 2px;'> {$exame['NomeExame']} </td>";
        $html .= "<td style='border: 1px solid #000; width: 25px;'>  </td>";
        $html .= "<td style='border-bottom: 1px solid #000; width: 30%;'>  </td>";
        $html .= "<td style='border-bottom: 1px solid #000; width: 30%;'>  </td>";
        $html .= "</tr>";
      } else {
        $html .= "<tr>";
        $html .= "<td> {$exame['NomeExame']} </td>";
        $html .= "</tr>";
      }
    }
    $html .= "</table>";

    $html .= "<p style='border: 2px solid #000; margin: 5px 0 5px 0;'><b>Emissor(a)<br/>(nome completo):</b></p>";

    $html .= "<p style='border: 2px solid #000; font-size: 16px; color: red; margin: 5px 0 5px 0;'><b>Esta guia s&oacute; tem validade com o carimbo e assinatura do emissor.</b></p>";

    $html .= "<table style='width: 100%;' cellspacing='0' cellpadding='0'>";
    $html .= "<tr>";
    $html .= "<td style='border: 1px solid #000; width: 50%; text-align: center; vertical-align: bottom;'> <p>Carimbo e assinatura do emissor(a)</p> </td>";
    $html .= "<td style='border: 1px solid #000; width: 50%; text-align: center;'> <img src='./img/assinatura_medico.jpg' alt='' width='120' /> <p>Assinatura do Gerente da &aacute;rea medica</p> </td>";
    $html .= "</tr>";
    $html .= "</table>";

    $html .= "<p style='border: 2px solid #000; text-align: center; font-size: 10px; margin: 5px 0 5px 0;'>Placa da Mercedes - Bandeirante - Conjunto 3 lotes 11, 13 e 15 - CEP:71732-030 <br/>
                                                 Fone: (61) 3399-1888 Fax: (61) 3399-1888 Ramal: 207 E-mail: seconci@seconci-df.org.br CNPJ: 03.656.261/0001-52</p>";

    if($guia['CodClinica'] == '17') {
      $html .= "<p style='border: 2px solid #000; font-size: 10px; margin: 5px 0 5px 0;'>
            <b>ORIENTA&Ccedil;&Otilde;ES IMPORTANTES:</b>
            <br>
            &nbsp;&nbsp;&nbsp;- Hor&aacute;rio de comparecimento no SECONCI: 8h da manh&atilde;.<br>
            &nbsp;&nbsp;&nbsp;- Trazer documento de identifica&ccedil;&atilde;o (Identidade, CPF, Carteira de Trabalho, etc)
            <br>
                <b> Exame de Acuidade Visual:</b>
            <br>
            &nbsp;&nbsp;&nbsp; - Se usa &oacute;culos, traze-lo no dia do exame
            <br>
               <b> Exame de Audiometria</b>
            <br>
            &nbsp;&nbsp;&nbsp;- N&atilde;o utilizar FONE DE OUVIDO nas 14 horas antes do exame<br>
            &nbsp;&nbsp;&nbsp;- Evitar tamb&eacute;m outras fontes de ru&iacute;do
            <br>
               <b>Coleta de Sangue (Glicemia)</b>
            <br>
            &nbsp;&nbsp;&nbsp;- Estar em jejum de 8 (oito) horas.
                (Neste caso, o trabalhador receber&aacute; o caf&eacute; da manh&atilde; no Seconci)
            <br>
             	<b>Exame de  Eletroencefalograma</b>
            <br>
						&nbsp;&nbsp;&nbsp;- Na noite anterior ao exame lavar a cabe&ccedil;a com sab&atilde;o de coco (n&atilde;o ultilizar xampu e/ou creme).<br>
						&nbsp;&nbsp;&nbsp;- No dia do exame n&atilde;o lavar a cabe&ccedil;a (o cabelo n&atilde;o pode estar molhado). <br>
						&nbsp;&nbsp;&nbsp;- N&atilde;o ultilizar nenhum produto no cabelo (laqu&ecirc;, gel, cremes, &oacute;leos, tinturas, etc).<br>
						&nbsp;&nbsp;&nbsp;- Obs: Trazer toalha de rosto para realizar o exame EEG. <br>
                                                                        &nbsp;&nbsp;&nbsp;- N&atilde;o usar chapinha. <br>
						&nbsp;&nbsp;&nbsp;- N&atilde;o ingerir bebida alco&oacute;lica nas ultimas 24hs que antecedem o exame.
						<br>
               <b> Perman&ecirc;ncia no Seconci</b>
            <br>
            &nbsp;&nbsp;&nbsp;- O trabalhador que estiver agendado para o Exame Admissional com Treinamento e ainda for fazer os Exames <br> &nbsp;&nbsp;&nbsp;&nbsp; Complementares para Trabalho em Altura e/ou RX do T&oacute;rax, dever&aacute; permanecer no Seconci o dia todo.<br>
           	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Neste caso o Seconci fornecer&aacute; o almo&ccedil;o e lanches)
						<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b> Caso n&atilde;o sigam as orieta&ccedil;&otilde;es, os exames n&atilde;o poder&atilde;o ser realizados. </b>
        </p>";
    }


    $mpdf = new mPDF();
    $mpdf->SetTitle('Guia de Encaminhamento');
    $mpdf->SetDisplayMode('fullpage');

    //$mpdf->SetHTMLFooter('<div>' . date('d/m/Y H:i') . '<div style="margin-top: -20px;" align="right">P&aacute;gina {PAGENO}/{nb}</div><br/></div>');
    $mpdf->AddPage('','','','','',null,null,0,0,0,0);

    $mpdf->WriteHTML("<style>th, td, p { padding: 2px; font-size: 12px;} table { border: 1px solid #000; } </style>");
    $mpdf->WriteHTML($html);
    $mpdf->Output();
    exit;
  }

}
