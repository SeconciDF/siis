<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class ProgramasAvaliacoesQuantitativasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        if($entity->data_avaliacao) {
            $entity->data_avaliacao = $this->formatDate($entity->data_avaliacao);
        }
    }

    private function formatDate($date) {
        $date = implode('-', array_reverse(explode('/', $date)));
        $date = new \DateTime($date);
        return $date;
    }
}
