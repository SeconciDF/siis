<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class RelatorioReembolsosEmpresasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if(isset($entity->valor)) {
            $entity->valor = $this->formatMoney($entity->valor);
        }
    }

    private function formatMoney($money) {
        $money = str_replace('.', '', $money);
        $money = str_replace(',', '.', $money);
        return $money;
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
}
