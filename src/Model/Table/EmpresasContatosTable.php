<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class EmpresasContatosTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->contato = preg_replace('/[^0-9]/', '', $entity->contato);

    }

}
