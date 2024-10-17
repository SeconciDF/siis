<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ProfissionaisAgendasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if(isset($entity->data_inicio)) {
          $entity->data_inicio = $this->formatDate("{$entity->data_inicio} {$entity->time_inicio}");
        }
        if(isset($entity->data_fim)) {
          $entity->data_fim = $this->formatDate("{$entity->data_fim} {$entity->time_fim}");
        }
    }

    public function formatDate($date = null) {
        $date = \DateTime::createFromFormat('d/m/Y H:i', $date);
        //$date = new \DateTime($date);
        return $date;
    }

}
