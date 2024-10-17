<?php

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class AmbientesGruposTable extends Table {

  public function beforeSave(Event $event, Entity $entity) {
    $entity->identificacao = preg_replace('/[^0-9]/', '', $entity->identificacao);
    $entity->data_inicio_validade = $this->formatDate($entity->data_inicio_validade);
    $entity->data_fim_validade = $this->formatDate($entity->data_fim_validade);
  }

  private function formatDate($date) {
    if(!is_object($date)) {
      $date = implode('-', array_reverse(explode('/', $date)));
    } else {
      $date = date('Y-m-d', strtotime($date));
    }

    //$date = new \DateTime($date);
    return $date;
  }
}
