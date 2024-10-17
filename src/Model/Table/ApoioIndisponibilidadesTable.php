<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ApoioIndisponibilidadesTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->data = $this->formatDate($entity->data);
    }

    public function formatDate($date, $time = null) {
        $date = implode('-', array_reverse(explode('/', $date)));
        //$date = new \DateTime($date);
        return "$date $time";
    }
}
