<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ProgramasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if($entity->data_inicial) {
            $entity->data_inicial = $this->formatDate($entity->data_inicial);
        }
        if($entity->data_final) {
            $entity->data_final = $this->formatDate($entity->data_final);
        }
    }

    private function formatDate($date) {
        $date = implode('-', array_reverse(explode('/', $date)));
        $date = new \DateTime($date);
        return $date;
    }
}
