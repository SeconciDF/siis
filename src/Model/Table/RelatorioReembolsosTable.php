<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class RelatorioReembolsosTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
      if($entity->vencimento) {
          $entity->vencimento = $this->formatDate($entity->vencimento);
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
