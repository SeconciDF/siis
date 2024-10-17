<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ProgramasResponsaveisTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if($entity->data_inicio) {
            $entity->data_inicio = $this->formatDate($entity->data_inicio);
        }
        if($entity->data_fim) {
            $entity->data_fim = $this->formatDate($entity->data_fim);
        }
    }

    private function formatDate($date) {
        $date = implode('-', array_reverse(explode('/', $date)));
        $date = new \DateTime($date);
        return $date;
    }
}
