<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class OdontoProcedimentosAplicadosTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->face_mesial = $entity->face_mesial ? true : false;
        $entity->face_distal = $entity->face_distal ? true : false;
        $entity->face_oclusal = $entity->face_oclusal ? true : false;
        $entity->face_lingual = $entity->face_lingual ? true : false;
        $entity->face_palatina = $entity->face_palatina ? true : false;
        $entity->face_vestibular = $entity->face_vestibular ? true : false;
    }

}
