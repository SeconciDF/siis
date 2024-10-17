<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class BeneficiariosDependentesTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->cpf = $this->formatCpf($entity->cpf);

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

        $model = TableRegistry::get('BeneficiariosDependentes');
        $results = $model->find('list', ['keyField' => 'NULL', 'valueField' => 'id', 'conditions' => ['beneficiarios_id' => $entity->beneficiarios_id], 'order' => ['id' => 'ASC']])->toArray();
        foreach ($results as $key => $value) {
          if($value == $entity->id) {
            $entity->digito_dependente = $key+1;
          }
        }
        if(!$entity->digito_dependente) {
          $entity->digito_dependente = sizeof($results)+1;
        }
    }

    private function formatFone($fone) {
        $fone = preg_replace('/[^0-9]/', '', $fone);
        return $fone;
    }

    private function formatDate($date) {
        $date = implode('-', array_reverse(explode('/', $date)));
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
}
