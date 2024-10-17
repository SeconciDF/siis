<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class BeneficiariosEmpresasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if($entity->data_associacao) {
            $entity->data_associacao = $this->formatDate($entity->data_associacao);
        }

        if($entity->data_baixa) {
            $entity->data_baixa = $this->formatDate($entity->data_baixa);
        }
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

}
