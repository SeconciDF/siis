<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ProfissionaisIndisponibilidadesTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if(isset($entity->data_hora_inicio)) {
          $entity->data_hora_inicio = $this->formatDate("{$entity->data_hora_inicio} {$entity->time_inicio}");
        }
        if(isset($entity->data_hora_fim)) {
          $entity->data_hora_fim = $this->formatDate("{$entity->data_hora_fim} {$entity->time_fim}");
        }
    }

    public function formatDate($date = null) {
        $date = \DateTime::createFromFormat('d/m/Y H:i', $date);
        //$date = new \DateTime($date);
        return $date;
    }

}
