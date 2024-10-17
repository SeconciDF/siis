<?php

namespace App\Controller;
require_once(ROOT . DS . 'vendor' . DS . 'autoload.php');

use App\Controller\AppController;
use Cake\Event\Event;
use Mpdf\Mpdf;

class RelatorioReembolsosController extends AppController {

  public function beforeFilter(Event $event) {
    $this->request->session()->write('Auth.User.MenuActive', 'relatorio');
  }

  public function index() {
    $this->loadModel('RelatorioReembolsosEmpresas');
    $option = [];

    $this->paginate = [
      'conditions' => $option,
      'fields' => [
        'RelatorioReembolsosEmpresas.id',
        'RelatorioReembolsosEmpresas.descricao',
        'RelatorioReembolsosEmpresas.quantidade',
        'RelatorioReembolsosEmpresas.valor',
        'reembolso.competencia',
        'reembolso.vencimento',
        'empresa.nome'
      ],
      'join' => [
        [
          'table' => 'relatorio_reembolsos',
          'alias' => 'reembolso',
          'type' => 'INNER',
          'conditions' => 'reembolso.id = RelatorioReembolsosEmpresas.reembolsos_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'empresa',
          'type' => 'INNER',
          'conditions' => 'empresa.id = RelatorioReembolsosEmpresas.empresas_id',
        ],
      ],
      'sortWhitelist'=> [
        'RelatorioReembolsosEmpresas.id',
        'RelatorioReembolsosEmpresas.descricao',
        'RelatorioReembolsosEmpresas.quantidade',
        'RelatorioReembolsosEmpresas.valor',
        'reembolso.competencia',
        'reembolso.vencimento',
        'empresa.nome'
      ],
      'order' => ['empresa.nome' => 'asc'],
    ];

    $this->loadModel('RelatorioReembolsos');
    $reembolso = $this->RelatorioReembolsos->get('1');
    $reembolsos = $this->paginate($this->RelatorioReembolsosEmpresas);

    $this->set(compact('reembolso', 'reembolsos'));
  }

  public function add() {
    $this->loadModel('RelatorioReembolsosEmpresas');
    $reembolso = $this->RelatorioReembolsosEmpresas->newEntity();
    if ($this->request->is(['patch', 'post', 'put'])) {
      $beneficiario = $this->RelatorioReembolsosEmpresas->patchEntity($reembolso, $this->request->data);
      if ($this->RelatorioReembolsosEmpresas->save($reembolso)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $reembolso)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'edit', $reembolso['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('RelatorioReembolsos');
    $competencias = $this->RelatorioReembolsos->find('list',[
      'keyField' => 'id',
      'valueField' => function($row) {
        $d = date('d/m/Y', strtotime($row['vencimento']));
        return "Competência: {$row['competencia']}, Vencimento: {$d}";
      }
    ])->toArray();

    $this->loadModel('Empresas');
    $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

    $this->set(compact('reembolso', 'empresas', 'competencias'));
  }

  public function edit($id = null) {
    $this->loadModel('RelatorioReembolsosEmpresas');
    $reembolso = $this->RelatorioReembolsosEmpresas->get($id);
    if ($this->request->is(['patch', 'post', 'put'])) {
      $beneficiario = $this->RelatorioReembolsosEmpresas->patchEntity($reembolso, $this->request->data);
      if ($this->RelatorioReembolsosEmpresas->save($reembolso)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $reembolso)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'edit', $reembolso['id']]);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }

    $this->loadModel('RelatorioReembolsos');
    $competencias = $this->RelatorioReembolsos->find('list',[
      'keyField' => 'id',
      'valueField' => function($row) {
        $d = date('d/m/Y', strtotime($row['vencimento']));
        return "Competência: {$row['competencia']}, Vencimento: {$d}";
      }
    ])->toArray();

    $this->loadModel('Empresas');
    $empresas = $this->Empresas->find('list',['keyField' => 'id', 'valueField' => 'nome', 'order' => ['nome' => 'ASC']])->toArray();

    $this->set(compact('reembolso', 'empresas', 'competencias'));
  }

  public function delete($id) {
    $this->loadModel('RelatorioReembolsosEmpresas');
    $this->request->allowMethod(['post', 'delete']);
    $reembolso = $this->RelatorioReembolsosEmpresas->find('all', ['conditions' => ['id' => $id]])->first();
    if ($this->RelatorioReembolsosEmpresas->delete($reembolso)) {
      $this->Flash->success($this::MSG_SUCESSO_DEL);
      return $this->redirect(['action' => 'index']);
    }
    return $this->redirect(['action' => 'index']);
  }

  public function reembolso($id = null) {
    $this->loadModel('RelatorioReembolsos');
    $reembolso = $this->RelatorioReembolsos->get($id);
    if ($this->request->is(['patch', 'post', 'put'])) {
      $beneficiario = $this->RelatorioReembolsos->patchEntity($reembolso, $this->request->data);
      if ($this->RelatorioReembolsos->save($reembolso)) {
        $this->loadComponent('Log');
        if(!$this->Log->save('edit', $reembolso)) {
          $this->Flash->error($this::MSG_ERRO_LOG);
        }

        $this->Flash->success($this::MSG_SUCESSO_EDT);
        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error($this::MSG_ERRO);
      }
    }
  }

  public function imprimir() {
    $this->loadModel('RelatorioReembolsosEmpresas');
    $reembolsos = $this->RelatorioReembolsosEmpresas->find('all', [
      'fields' => [
        'RelatorioReembolsosEmpresas.id',
        'RelatorioReembolsosEmpresas.descricao',
        'RelatorioReembolsosEmpresas.quantidade',
        'RelatorioReembolsosEmpresas.valor',
        'reembolso.competencia',
        'reembolso.vencimento',
        'empresa.identificacao',
        'empresa.nome',
        'empresa.id'
      ],
      'join' => [
        [
          'table' => 'relatorio_reembolsos',
          'alias' => 'reembolso',
          'type' => 'INNER',
          'conditions' => 'reembolso.id = RelatorioReembolsosEmpresas.reembolsos_id',
        ],
        [
          'table' => 'empresas',
          'alias' => 'empresa',
          'type' => 'INNER',
          'conditions' => 'empresa.id = RelatorioReembolsosEmpresas.empresas_id',
        ],
      ],
      'order' => ['empresa.nome' => 'asc'],
    ])->toArray();

    $reembolso = null;
    $relatorios = array();
    foreach ($reembolsos as $key => $value) {
      if(!isset($relatorios[$value['empresa']['id']]['valor'])) {
        $relatorios[$value['empresa']['id']]['valor'] = 0;
      }

      $reembolso = $value['reembolso'];
      $relatorios[$value['empresa']['id']]['empresa'] = $value['empresa'];
      $relatorios[$value['empresa']['id']]['valor'] += $value['valor'];
      $relatorios[$value['empresa']['id']]['descricao'][] = $value['descricao'];
    }

    $html = '';
    foreach ($relatorios as $relatorio) {
      $html .= "<p style='text-align: center;'> <b>SECONCI - Servi&ccedil;o Social da Ind&uacute;stria da Constru&ccedil;&atilde;o Civil do DF</b></p><br/>";
      $html .= "<p style='text-align: center;'>RELATORIO DE REEMBOLSO DE CUSTO MENSAL</p>";
      $html .= "<p style='text-align: center;'>PROGRAMA DE CONTROLE M&Eacute;DICO DE SA&Uacute;DE OCUPACIONAL</p>";

      $vencimento = date('d/m/Y', strtotime($reembolso['vencimento']));
      $html .= "<p><div style='width: 50%; float: left;'>M&Ecirc;S DE COMPETENCIA: {$reembolso['competencia']}</div> <div style='width: 50%; float: right; text-align: right;'>VENCIMENTO: {$vencimento}</div></p>";

      $html .= "<br/>";

      $cnpj = preg_replace('/[^0-9]/', '', $relatorio['empresa']['identificacao']);
      $cnpj = $this->RelatorioReembolsosEmpresas->mask($cnpj, '##.###.###/####-##');
      $html .= "<p>EMPRESA: {$relatorio['empresa']['nome']}<br/>CNPJ: {$cnpj}</p>";

      $html .= "<br/>";

      $html .= "<p>DISCRIMINA&Ccedil;&Atilde;O</p>";

      $descricao = implode('<br/>', $relatorio['descricao']);
      $descricao = nl2br($descricao);
      $html .= "<p>{$descricao}</p>";

      $total = number_format($relatorio['valor'],2,',','.');
      $html .= "<p style='text-align: right;'>VALOR TOTAL: {$total}</p>";

      $html .= "<pagebreak>";
    }



    $mpdf = new mPDF();
    $mpdf->SetTitle('RELATORIO DE REEMBOLSO');
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
