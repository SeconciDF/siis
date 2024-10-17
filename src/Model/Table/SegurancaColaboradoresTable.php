<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class SegurancaColaboradoresTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        //$entity->modified = new \DateTime('now');
    }

}
