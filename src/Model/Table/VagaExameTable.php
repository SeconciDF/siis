<?php

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Log\Log;

class VagaExameTable extends Table {

  public function vagasExames($empresa = null) {
    $connection = ConnectionManager::get('siis');
    return $connection->execute("SELECT v.*, e.NomeExame FROM vagas_exames v
                                INNER JOIN tipoexame e ON e.CodTipoExame = v.CodTipoExame
                                ORDER BY v.VagasData DESC, e.NomeExame ASC")->fetchAll('assoc');
  }

  public function salvarVaga($data = null) {
    $connection = ConnectionManager::get('siis');

    $vaga = [
      ':CodTipoExame' => $data['CodTipoExame'],
      ':VagasData' => $this->formatDate($data['VagasData']),
      ':VagasManha' => $data['VagasManha'],
      ':VagasTarde' => $data['VagasTarde']
    ];

    try {
      $stmt = $connection->prepare("INSERT INTO vagas_exames (CodVagas, CodTipoExame, VagasData, VagasManha, VagasTarde)
                                    VALUES (NULL, :CodTipoExame, :VagasData, :VagasManha, :VagasTarde);");

      $stmt->execute($vaga);
      $this->success = __('gravado com sucesso!');
    } catch (\Exception $e) {
      $this->error = __('Falha ao gravar vaga');
      Log::write('debug',[
        'CodTipoExame' => $data['CodTipoExame'],
        'VagasData' => $data['VagasData'],
        'message' => $e->getMessage()
      ]);
    }
  }

  public function deletarVaga($id = null) {
    $connection = ConnectionManager::get('siis');

    try {
      $stmt = $connection->prepare('DELETE FROM vagas_exames WHERE CodVagas = :CodVagas');
      $stmt->execute([':CodVagas' => $id]);

      $this->success = __('Vaga excluida!');
    } catch (\Exception $e) {
      $this->error = __('Falha ao excluir vaga');
      Log::write('debug',[
        'CodVagas' => $id,
        'message' => $e->getMessage()
      ]);
    }
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
