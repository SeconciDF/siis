<?php

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Log\Log;

class BeneficiariosTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->cpf = $this->formatCpf($entity->cpf);

        $entity->cep = preg_replace('/[^0-9]/', '', $entity->cep);

        if($entity->data_nascimento) {
            $entity->data_nascimento = $this->formatDate($entity->data_nascimento);
        }

        if($entity->celular) {
            $entity->celular = $this->formatFone($entity->celular);
        }
        if($entity->telefone) {
            $entity->telefone = $this->formatFone($entity->telefone);
        }
        if($entity->recado) {
            $entity->recado = $this->formatFone($entity->recado);
        }
    }

    public function afterSave(Event $event, Entity $entity) {
      $connection = ConnectionManager::get('siis');

      $beneficiario = [
        ':cpf' => preg_replace('/[^0-9]/', '', $entity['cpf']),
        ':nm_trab' => $entity['nome'],
        ':mae' => $entity['mae'],
        ':sexo' => $entity['sexo'],
        ':rg' => $entity['identidade'],
        ':o_emissor' => $entity['orgao_expedidor'],
        ':ctps' => $entity['ctps_serie'],
        ':dt_nasc' => isset($entity['data_nascimento']) ? date('Y-m-d', strtotime($entity['data_nascimento'])) : null,
        ':endereco' => "{$entity['logradouro']} {$entity['numero']}",
        ':bairro' => $entity['bairro'],
        ':cidade' => $entity['cidade'],
        ':uf' => $entity['estado'],
        ':cep' => preg_replace('/[^0-9]/', '', $entity['cep']),
        ':telefone' => preg_replace('/[^0-9]/', '', $entity['telefone']),
        ':celular' => preg_replace('/[^0-9]/', '', $entity['celular']),
        ':email' => $entity['email']
      ];

      $cpf = preg_replace('/[^0-9]/', '', $entity['cpf']);
      try {
        $result = $connection->execute("SELECT cd_trab, cpf FROM tb_trabalhador WHERE cpf = {$cpf}")->fetch('assoc');
      } catch (\Exception $e) {
        $this->error = __('Falha ao sincronizar com SIIS v1!');
        Log::write('debug',[
          'id' => $entity['id'],
          'nome' => $entity['nome'],
          'message' => $e->getMessage()
        ]);
      }

      if(isset($result['cd_trab']) && !isset($this->error)) {
        try {
          $stmt = $connection->prepare('UPDATE tb_trabalhador SET cpf = :cpf, nm_trab = :nm_trab, mae = :mae, sexo = :sexo, o_emissor = :o_emissor,
                                        rg = :rg, ctps = :ctps, dt_nasc = :dt_nasc, endereco = :endereco, bairro = :bairro, cidade = :cidade,
                                        uf = :uf, cep = :cep, telefone = :telefone, celular = :celular, email = :email WHERE cd_trab = :cd_trab;');

          $stmt->execute([
            ':cd_trab' => $result['cd_trab'],
            ':cpf' => $beneficiario[':cpf'],
            ':nm_trab' => $beneficiario[':nm_trab'],
            ':mae' => $beneficiario[':mae'],
            ':sexo' => $beneficiario[':sexo'],
            ':rg' => $beneficiario[':rg'],
            ':o_emissor' => $beneficiario[':o_emissor'],
            ':ctps' => $beneficiario[':ctps'],
            ':dt_nasc' => $beneficiario[':dt_nasc'],
            ':endereco' => $beneficiario[':endereco'],
            ':bairro' => $beneficiario[':bairro'],
            ':cidade' => $beneficiario[':cidade'],
            ':uf' => $beneficiario[':uf'],
            ':cep' => $beneficiario[':cep'],
            ':telefone' => $beneficiario[':telefone'],
            ':celular' => $beneficiario[':celular'],
            ':email' => $beneficiario[':email']
          ]);

          $this->success = __('Registro sincronizado com SIIS v1!');
        } catch (\Exception $e) {
          $this->error = __('Falha ao sincronizar com SIIS v1!');
          Log::write('debug',[
            'id' => $entity['id'],
            'nome' => $entity['nome'],
            'message' => $e->getMessage()
          ]);
        }
      } else if(!isset($this->error)) {
        try {
          $stmt = $connection->prepare('INSERT INTO tb_trabalhador (cd_trab, cpf, nm_trab, mae, sexo, rg, o_emissor, ctps, dt_nasc, endereco, bairro, cidade, uf, cep, telefone, celular, email)
                                        VALUES (NULL, :cpf, :nm_trab, :mae, :sexo, :rg, :o_emissor, :ctps, :dt_nasc, :endereco, :bairro, :cidade, :uf, :cep, :telefone, :celular, :email);');

          $stmt->execute($beneficiario);
          $this->success = __('Registro sincronizado com SIIS v1!');
        } catch (\Exception $e) {
          $this->error = __('Falha ao sincronizar com SIIS v1!');
          Log::write('debug',[
            'id' => $entity['id'],
            'nome' => $entity['nome'],
            'message' => $e->getMessage()
          ]);
        }
      }

      if(!isset($this->error)) {
        try {
          $trabalhador = $connection->execute("SELECT cd_trab, cpf FROM tb_trabalhador WHERE cpf = {$cpf}")->fetch('assoc');
        } catch (\Exception $e) {
          $this->error = __('Falha ao sincronizar com SIIS v1!');
          Log::write('debug',[
            'id' => $entity['id'],
            'nome' => $entity['nome'],
            'message' => $e->getMessage()
          ]);
        }
      }

      if(isset($trabalhador['cd_trab']) && reset($entity['empresas']) && !isset($this->error)) {
        try {
          $e = TableRegistry::get('Empresas');
          $cnpj = $e->find('all', ['fields' => ['identificacao'], 'conditions' => ['id' => reset($entity['empresas'])]])->first();
          $cnpj = preg_replace('/[^0-9]/', '', $cnpj['identificacao']);

          $empresa = $connection->execute("SELECT cd_emp, cnpj FROM tb_empresa WHERE cnpj = {$cnpj}")->fetch('assoc');
        } catch (\Exception $e) {
          $this->error = __('Falha ao sincronizar empresa com SIIS v1!');
          Log::write('debug',[
            'id' => $entity['id'],
            'nome' => $entity['nome'],
            'message' => $e->getMessage()
          ]);
        }

        try {
          $f = TableRegistry::get('ApoioFuncoes');
          $funcao = $f->find('all', ['fields' => ['id', 'descricao'], 'conditions' => ['id' => reset($entity['funcoes'])]])->first();
          $funcao = $connection->execute("SELECT CodFuncao, NomeFuncao FROM tipofuncao WHERE NomeFuncao LIKE '{$funcao['descricao']}'")->fetch('assoc');
        } catch (\Exception $e) {
          $this->error = __('Falha ao sincronizar fun&ccedil;&atilde; com SIIS v1!');
          Log::write('debug',[
            'id' => $entity['id'],
            'nome' => $entity['nome'],
            'message' => $e->getMessage()
          ]);
        }

        if(isset($empresa['cd_emp']) && !isset($this->error)) {
          try {
            $stmt = $connection->prepare('UPDATE emprego SET Ativo = "N" WHERE CodTrabalhador = :CodTrabalhador');
            $stmt->execute([':CodTrabalhador' => $trabalhador['cd_trab']]);

            $emprego = $connection->execute("SELECT CodEmprego, CodTrabalhador, CodEmpresa, CodFuncao, Ativo FROM emprego WHERE CodTrabalhador = {$trabalhador['cd_trab']} AND CodEmpresa = {$empresa['cd_emp']} ORDER BY CodEmprego DESC LIMIT 1 ")->fetch('assoc');
          } catch (\Exception $e) {
            $this->error = __('Falha ao associar trabalhador com empresa no SIIS v1!');
            Log::write('debug',[
              'id' => $entity['id'],
              'nome' => $entity['nome'],
              'message' => $e->getMessage()
            ]);
          }
        }

        if(isset($emprego['CodEmprego']) && isset($empresa['cd_emp']) && !isset($this->error)) {
          try {
            $stmt = $connection->prepare('UPDATE emprego SET CodTrabalhador = :CodTrabalhador, CodEmpresa = :CodEmpresa, CodFuncao = :CodFuncao, Ativo = :Ativo WHERE CodEmprego = :CodEmprego;');
            $stmt->execute([
              ':CodEmprego' => $emprego['CodEmprego'],
              ':CodTrabalhador' => $trabalhador['cd_trab'],
              ':CodEmpresa' => $empresa['cd_emp'],
              ':CodFuncao' => $funcao['CodFuncao'],
              ':Ativo' =>   'S'
            ]);
          } catch (\Exception $e) {
            $this->error = __('Falha ao associar trabalhador com empresa no SIIS v1!');
            Log::write('debug',[
              'id' => $entity['id'],
              'nome' => $entity['nome'],
              'message' => $e->getMessage()
            ]);
          }

        } else if(isset($empresa['cd_emp']) && !isset($this->error)) {
          try {
            $stmt = $connection->prepare('INSERT INTO emprego (CodEmprego, CodTrabalhador, CodEmpresa, CodFuncao, DataAdmitido, Ativo) VALUES (NULL, :CodTrabalhador, :CodEmpresa, :CodFuncao, :DataAdmitido, :Ativo);');
            $stmt->execute([
              ':CodTrabalhador' => $trabalhador['cd_trab'],
              ':CodEmpresa' => $empresa['cd_emp'],
              ':CodFuncao' => $funcao['CodFuncao'],
              ':DataAdmitido' => date('Y-m-d'),
              ':Ativo' =>   'S'
            ]);
          } catch (\Exception $e) {
            $this->error = __('Falha ao associar trabalhador com empresa no SIIS v1!');
            Log::write('debug',[
              'id' => $entity['id'],
              'nome' => $entity['nome'],
              'message' => $e->getMessage()
            ]);
          }

        }
      }
    }

    public function selecionar($id = null, $empresa = null) {
      $connection = ConnectionManager::get('siis');

      if(is_null($id) || is_null($empresa)) {
        return null;
      }

      return $connection->execute("SELECT DISTINCT t.cd_trab, t.cpf, t.nm_trab, f.CodFuncao, f.NomeFuncao FROM emprego e
                                  INNER JOIN tb_trabalhador t ON t.cd_trab = e.CodTrabalhador
                                  LEFT JOIN tipofuncao f ON f.CodFuncao = e.CodFuncao
                                  WHERE e.CodEmpresa = {$empresa['cd_emp']} AND e.CodTrabalhador = {$id} AND e.Ativo = 'S' ")->fetch('assoc');
    }

    public function pesquisar($options = array(), $empresa = null) {
      $connection = ConnectionManager::get('siis');

      if(!$options || is_null($empresa)) {
        return [];
      }

      $where = '';
      foreach ($options as $key => $value) {
        $where .= " AND {$key} LIKE '{$value}' ";
      }

      return $connection->execute("SELECT DISTINCT t.cd_trab, t.cpf, t.nm_trab FROM emprego e
                                  INNER JOIN tb_trabalhador t ON t.cd_trab = e.CodTrabalhador
                                  WHERE e.CodEmpresa = {$empresa['cd_emp']} AND e.Ativo = 'S' {$where} ORDER BY t.nm_trab ASC")->fetchAll('assoc');
    }

    private function formatFone($fone) {
        $fone = preg_replace('/[^0-9]/', '', $fone);
        return $fone;
    }

    private function formatDate($date) {
        if(!is_object($date)) {
          $date = implode('-', array_reverse(explode('/', $date)));
        } else {
          $date = date('Y-m-d', strtotime($date));
        }

        //$date = new \DateTime($date);
        return $date;
    }

    public function formatCpf($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return $cpf;
    }

    public function mask($val, $mask) {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return empty($val) ? $val : $maskared;
    }

    function validarCPF($cpf = '') {
        if(empty($cpf)) {
            return true;
        }

        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);
        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return FALSE;
        } else { // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
            return true;
        }
    }

    public function prontuario($prontuario) {
        $id = explode('.', $prontuario);
        $model = TableRegistry::get('BeneficiariosDependentes');
        $datas = $model->find('list', ['keyField' => 'id', 'valueField' => 'id', 'conditions' => ['beneficiarios_id' => $id[0]], 'order' => ['id' => 'ASC']])->toArray();

        $depententes = [];
        foreach ($datas as $value) {
          $depententes[sizeof($depententes)+1] = $value;
        }

        $dependente = null;
        if(isset($id[1])) {
          if(isset($depententes[$id[1]])) {
            $dependente = $depententes[$id[1]];
          }
        }

        return ['beneficiario'=>$id[0], 'dependente'=> $dependente];
    }
}
