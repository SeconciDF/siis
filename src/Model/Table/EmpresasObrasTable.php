<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class EmpresasObrasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->cep = preg_replace('/[^0-9]/', '', $entity->cep);

        if($entity->celular_contato) {
            $entity->celular_contato = $this->formatFone($entity->celular_contato);
        }
        if($entity->telefone_contato) {
            $entity->telefone_contato = $this->formatFone($entity->telefone_contato);
        }
    }

    private function formatFone($fone) {
        $fone = preg_replace('/[^0-9]/', '', $fone);
        return $fone;
    }

    private function formatDate($date) {
        $date = implode('-', array_reverse(explode('/', $date)));
        //$date = new \DateTime($date);
        return $date;
    }

}
