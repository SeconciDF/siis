<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ProfissionaisTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->cpf = $this->formatCpf($entity->cpf);
    }

    public function formatDate($date) {
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
        return $maskared;
    }

    function validarCPF($cpf = '') {
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

    function getItervalos() {
        return [
            '1' => '39',
            '2' => '59',
            '3' => '29',
            '9' => '00',
            '10' => '00',
            '12' => '00'
        ];
    }

    function getHorarios() {
        return [
            '1' => [ //Especialidade Dentistica
              '1' => ['08:00:00','08:40:00','09:20:00','10:00:00','10:40:00','11:20:00'], // Turno da manha
              '2' => ['13:00:00','13:40:00','14:20:00','15:00:00','15:40:00','16:20:00'], // Turno da tarde
            ],
            '2' => [ //Especialidade Endodontia
              '1' => ['08:00:00','09:00:00','10:00:00','11:00:00'], // Turno da manha
              '2' => ['13:00:00','14:00:00','15:00:00','16:00:00'], // Turno da tarde
            ],
            '3' => [ //Especialidade Protese
              '1' => ['08:00:00','08:30:00','09:00:00','09:30:00','10:00:00','10:30:00','11:00:00','11:30:00'], // Turno da manha
              '2' => ['13:00:00','13:30:00','14:00:00','14:30:00','15:00:00','15:30:00','16:00:00','16:30:00'], // Turno da tarde
            ],
            '9' => [ //Especialidade Medicina Ocupacional
              '1' => [],
              '2' => []
            ],
            '10' => [ //Especialidade Fonoaudiologia
              '1' => [],
              '2' => []
            ],
            '12' => [ //Especialidade Dentistica con THD
              '1' => [],
              '2' => []
            ]
        ];
    }
}
