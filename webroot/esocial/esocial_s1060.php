<?php
// error_reporting(E_ALL);
error_reporting(E_ERROR);
ini_set('display_errors', 'On');

require_once './conexao.php';
require_once './vendor/mpdf60/mpdf.php';

$pdo = new Conexao(array('host' => 'localhost', 'database' => 'seconci', 'user' => 'root', 'pass' => 's&c0ns1PwDs'));

$empresa = $pdo->select("SELECT nome, identificacao as cpf FROM empresas WHERE id = {$_GET['empresa']}")[0];
$ambientes = $pdo->select("SELECT a.id as codamb, p.data_inicial as inivalid, p.data_final as fimvalid, aa.descricao as nmamb, a.descricao as dscamb, a.local as localamb, a.tipo_identificacao as tpinsc, a.identificacao as nrinsc  FROM programas p
                          INNER JOIN programas_ambientes pa ON p.id = pa.programas_id
                          INNER JOIN ambientes a ON a.id = pa.ambientes_id
                          INNER JOIN apoio_ambientes aa ON aa.id = a.apoio_ambientes_id
                          WHERE p.id = {$_GET['programa']}");

$mpdf = new mPDF('utf-8', 'A4');
$mpdf->SetTitle('S-1060');

$mpdf->SetHTMLHeader("<div style='text-align: center; padding: 10px 0 -15px 0; border-bottom: 1px solid #000;' ><img src='./seconci.jpg' style='float: left; width: 100px;'/> <h3 style='margin-left: -100px;'>S-1060 - Tabela de Ambientes de Trabalho</h3></div>
                      <div style='text-align: right; float: right; width: 20%;' >" . date('d/m/Y H:i') . "</div>
                      <div style='text-align: left; float: left; width: 75%;' >{$empresa->nome}</div>");
$mpdf->SetHTMLFooter('<img src="./esocial.jpg" style="width: 100px; float: left;"/> <div align="right">P&aacute;gina {PAGENO} de {nb} </div>');

$mpdf->AddPage('','','','','',null,null,30,25,5,5);
$mpdf->WriteHTML("<style> td { border: 1px solid #000; padding: 5px; } </style>");

$tipo = [
  '1' => '1 - CNPJ',
  '3' => '3 - CAEPF'
];

$local = [
  '1' => '1 - Estabelecimento do empregador',
  '2' => '2 - Estabelecimento de terceiros'
];

foreach ($ambientes as $key => $value) {
  $inivalid = substr($value->inivalid, 0, 7);
  $fimvalid = substr($value->fimvalid, 0, 7);

  $mpdf->WriteHTML("
    <table style='width:100%;'>
      <tr>
        <td> <b>Código do Ambiente</b> </td>
        <td colspan='3'>#{$value->codamb}</td>
      </tr>
      <tr>
        <td style='width:190px;'><b>Início da validade</b> </td>
        <td>{$inivalid}</td>
        <td style='width:180px;'><b>Final da validade</b> </td>
        <td>{$fimvalid}</td>
      </tr>
      <tr>
        <td><b>Nome do ambiente</b></td>
        <td colspan='3'>{$value->nmamb}</td>
      </tr>
      <tr>
        <td><b>Descrição do ambiente</b></td>
        <td colspan='3'>{$value->dscamb}</td>
      </tr>
      <tr>
        <td><b>Local do ambiente</b></td>
        <td colspan='3'>{$local[$value->localamb]}</td>
      </tr>
      <tr>
        <td><b>Tipo de Inscrição</b></td>
        <td>{$tipo[$value->tpinsc]}</td>
        <td><b>Número de Inscrição</b></td>
        <td>{$value->nrinsc}</td>
      </tr>
    </table><br/>");
}
$mpdf->Output();
