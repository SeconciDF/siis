<?php

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;
use Cake\Event\Event;

class MigrarController extends AppController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['trabalhadores']);
    }

    public function trabalhadores($cnpj = null) {
      $this->loadModel('BeneficiariosEmpresas');
      $this->loadModel('BeneficiariosFuncoes');
      $this->loadModel('Beneficiarios');
      $this->loadModel('ApoioFuncoes');
      $this->loadModel('Empresas');

      $connection = ConnectionManager::get('siis');
      $empresa = $connection->execute("SELECT cd_emp, cnpj FROM tb_empresa WHERE cnpj = {$cnpj}")->fetch('assoc');
      $empresa2 = $this->Empresas->find('all', ['fields' => ['id', 'identificacao'], 'conditions' => ["identificacao IN('{$empresa['cnpj']}','{$this->Empresas->mask($empresa['cnpj'],'########/####-##')}')"]])->first();

      if(!$empresa2['id']) {
        echo 'empresa nao identificada';
        exit;
      }

      $trabalhadores = $connection->execute("SELECT f.CodFuncao, f.NomeFuncao, t.* FROM emprego e
                                            INNER JOIN tb_trabalhador t ON t.cd_trab = e.CodTrabalhador
                                            INNER JOIN tipofuncao f ON f.CodFuncao = e.CodFuncao
                                            WHERE e.CodEmpresa = '{$empresa['cd_emp']}'")->fetchAll('assoc');

      foreach ($trabalhadores as $trabalhador) {
        $cpf = preg_replace('/[^0-9]/', '', $trabalhador['cpf']);
        $beneficiario = $this->Beneficiarios->find('all', ['conditions' => ['cpf' => $cpf]])->first();
        $funcao = $this->ApoioFuncoes->find('all', ['conditions' => ['descricao' => $trabalhador['NomeFuncao']]])->first();

        $data = [
          'identidade' => $trabalhador['rg'],
          'orgao_expedidor' => $trabalhador['o_emissor'],
          'cpf' => $trabalhador['cpf'],
          'ctps_serie' => $trabalhador['ctps'],
          'nome' => $trabalhador['nm_trab'],
          'data_nascimento' => $trabalhador['dt_nasc'],
          'sexo' => $trabalhador['sexo'],
          'mae' => $trabalhador['mae'],
          'cep' => $trabalhador['cep'],
          'logradouro' => $trabalhador['endereco'],
          'bairro' => $trabalhador['bairro'],
          'cidade' => $trabalhador['cidade'],
          'estado' => $trabalhador['uf'],
          'telefone' => $trabalhador['telefone'],
          'celular' => $trabalhador['celular'],
          'colaboradores_id' => $this->request->session()->read('Auth.User.id'),
          'data_hora_registo' => date('Y-m-d'),
          'situacao' => 'A',
          'empresas' => [],
          'funcoes' => [],
        ];

        if($beneficiario['id']) {
          unset($data['colaboradores_id'], $data['data_hora_registo'], $data['situacao']);
          $data['id'] = $beneficiario['id'];
        }

        $record = $this->Beneficiarios->patchEntity($this->Beneficiarios->newEntity(), $data);
        if($this->Beneficiarios->save($record)) {
          $this->loadComponent('Log');
          $this->Log->save('migrar', $empresa);
        }

        $this->BeneficiariosFuncoes->deleteAll(array("beneficiarios_id = {$record['id']}", "funcoes_id = {$funcao['id']}"));
        $funcao= $this->BeneficiariosFuncoes->patchEntity(
          $this->BeneficiariosFuncoes->newEntity(), [
          'beneficiarios_id' => $record['id'],
          'funcoes_id' => $funcao['id'],
          'situacao' => '1'
        ]);
        $this->BeneficiariosFuncoes->save($funcao);


        $this->BeneficiariosEmpresas->deleteAll(array("beneficiarios_id = {$record['id']}", "empresas_id = {$empresa2['id']}"));
        $funcao= $this->BeneficiariosEmpresas->patchEntity(
          $this->BeneficiariosEmpresas->newEntity(), [
          'beneficiarios_id' => $record['id'],
          'empresas_id' => $empresa2['id'],
          'situacao' => 'A'
        ]);
        $this->BeneficiariosEmpresas->save($funcao);
      }

      exit;
    }

}
