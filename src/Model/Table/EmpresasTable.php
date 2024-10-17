<?php

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Log\Log;

class EmpresasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->identificacao = preg_replace('/[^0-9]/', '', $entity->identificacao);

        $entity->cep_localizacao = preg_replace('/[^0-9]/', '', $entity->cep_localizacao);
        $entity->cep_cobranca = preg_replace('/[^0-9]/', '', $entity->cep_cobranca);

        if($entity->data_inicio_atividade) {
            $entity->data_inicio_atividade = $this->formatDate($entity->data_inicio_atividade);
        }

        if($entity->data_inicio_cobranca) {
            $entity->data_inicio_cobranca = $this->formatDate($entity->data_inicio_cobranca);
        }
    }

    public function afterSave(Event $event, Entity $entity) {
      $connection = ConnectionManager::get('siis');

      $empresa = [
        ':cnpj' => preg_replace('/[^0-9]/', '', $entity['identificacao']),
        ':razao_social' => $entity['nome'],
        ':nm_fantasia' => $entity['nome_fantasia'],
        ':endereco' => $entity['logradouro_localizacao'],
        ':numero' => $entity['numero_localizacao'],
        ':complemento' => $entity['complemento_localizacao'],
        ':bairro' => $entity['bairro_localizacao'],
        ':cidade' => $entity['cidade_localizacao'],
        ':cep' => preg_replace('/[^0-9]/', '', $entity['cep_localizacao']),
        ':uf' => $entity['estado_localizacao'],
        ':email' => $entity['email_contato'],
        ':contato' => substr($entity['nome_contato'],0,19),
        ':telefone' => isset($entity['contatos'][0]) ? preg_replace('/[^0-9]/', '', $entity['contatos'][0]) : '',
        ':telefone2' => isset($entity['contatos'][1]) ? preg_replace('/[^0-9]/', '', $entity['contatos'][1]) : '',
        ':telefone3' => isset($entity['contatos'][2]) ? preg_replace('/[^0-9]/', '', $entity['contatos'][2]) : '',
        ':dt_cad' => isset($entity['data_hora_registro']) ? date('Y-m-d', strtotime($entity['data_hora_registro'])) : null,
        ':dt_ultima_alteracao' => isset($entity['data_hora_atualizacao']) ? date('Y-m-d', strtotime($entity['data_hora_atualizacao'])) : null,
      ];

      switch ($entity['situacao']) {
        case 'A': $empresa[':ativo'] = 'S'; break;
        case 'I': $empresa[':ativo'] = 'N'; break;
        default: break;
      }

      switch ($entity['situacao_seconci']) {
        case 'C': $empresa[':inadimplente'] = 'N'; break;
        case 'I': $empresa[':inadimplente'] = 'S'; break;
        default: break;
      }

      if($entity['situacao'] == 'A' && $entity['situacao_seconci'] == 'C') {
        $empresa[':suspenso'] = 'N';
      } else {
        $empresa[':suspenso'] = 'S';
      }

      $cnpj = preg_replace('/[^0-9]/', '', $entity['identificacao']);
      try {
        $result = $connection->execute("SELECT cd_emp, cnpj FROM tb_empresa WHERE cnpj = {$cnpj}")->fetch('assoc');
      } catch (\Exception $e) {
        $this->error = __('Falha ao sincronizar com SIIS v1!');
        Log::write('debug',[
          'id' => $entity['id'],
          'nome' => $entity['nome'],
          'message' => $e->getMessage()
        ]);
      }

      if(isset($result['cd_emp']) && !isset($this->error)) {
        try {
          $stmt = $connection->prepare('UPDATE tb_empresa SET cnpj = :cnpj, razao_social = :razao_social, nm_fantasia = :nm_fantasia, endereco = :endereco,
                                        numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, cep = :cep, uf = :uf, email = :email,
                                        telefone = :telefone, telefone2 = :telefone2, telefone3 = :telefone3, dt_cad = :dt_cad, contato = :contato, ativo = :ativo,
                                        inadimplente = :inadimplente, suspenso = :suspenso, dt_ultima_alteracao = :dt_ultima_alteracao WHERE cd_emp = :cd_emp;');

          $stmt->execute([
            ':cd_emp' => $result['cd_emp'],
            ':cnpj' => $empresa[':cnpj'],
            ':razao_social' => $empresa[':razao_social'],
            ':nm_fantasia' => $empresa[':nm_fantasia'],
            ':endereco' => $empresa[':endereco'],
            ':numero' => $empresa[':numero'],
            ':complemento' => $empresa[':complemento'],
            ':bairro' => $empresa[':bairro'],
            ':cidade' => $empresa[':cidade'],
            ':cep' => $empresa[':cep'],
            ':uf' => $empresa[':uf'],
            ':email' => $empresa[':email'],
            ':telefone' => $empresa[':telefone'],
            ':telefone2' => $empresa[':telefone2'],
            ':telefone3' => $empresa[':telefone3'],
            ':dt_cad' => $empresa[':dt_cad'],
            ':contato' => $empresa[':contato'],
            ':dt_ultima_alteracao' => $empresa[':dt_ultima_alteracao'],
            ':ativo' => $empresa[':ativo'],
            ':inadimplente' => $empresa[':inadimplente'],
            ':suspenso' => $empresa[':suspenso']
          ]);

          $this->success = __('Registro sincronizado com SIISv1!');
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
          $stmt = $connection->prepare('INSERT INTO tb_empresa (cd_emp, cnpj, razao_social, nm_fantasia, endereco, numero, complemento, bairro, cidade, cep, uf, email, telefone, telefone2, telefone3, dt_cad, contato, ativo, inadimplente, suspenso, dt_ultima_alteracao)
                                        VALUES (NULL, :cnpj, :razao_social, :nm_fantasia, :endereco, :numero, :complemento, :bairro, :cidade, :cep, :uf, :email, :telefone, :telefone2, :telefone3, :dt_cad, :contato, :ativo, :inadimplente, :suspenso, :dt_ultima_alteracao);');

          $stmt->execute($empresa);
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
    }

    public function selecionar($cnpj = null) {
      $connection = ConnectionManager::get('siis');
      $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
      return $connection->execute("SELECT e.cd_emp, e.cnpj, p.AtivoPrograma FROM tb_empresa e
                                  LEFT JOIN contrato c ON e.cd_emp = c.CodEmpresa
                                  LEFT JOIN contrato_programa p ON c.CodContrato = p.CodContrato
                                  WHERE e.cnpj = '{$cnpj}' ORDER BY c.DataAssinado DESC")->fetch('assoc');
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

    public function formatCnpj($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return $cnpj;
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
}
