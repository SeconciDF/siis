<?php

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Log\Log;

class RelatoriosTable extends Table {

    public function getAtendimento($ano = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT MONTH(CO.DataConsulta) as mes, NA.NomeNatureza as natureza, COUNT(CO.CodConsulta) as quantidade FROM consulta 		CO
                                  INNER JOIN natureza 	NA ON (NA.CodNatureza = CO.CodNatureza)
                                  WHERE CodProfissional != 0
                                  AND YEAR(CO.DataConsulta) = '{$ano}'
                                  AND (CO.APTO = 's' OR CO.APTO = 'n')
                                  GROUP BY MONTH(CO.DataConsulta), CO.codNatureza")->fetchAll('assoc');
    }

    public function getAudiometria($ano = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT MONTH(DataAudiometria) as mes, 'Audiometria' as natureza, COUNT(CodAudiometria) as quantidade FROM audiometria
                                  WHERE YEAR(DataAudiometria) = '{$ano}'
                                  AND (PppInterpretacao= 'A' OR PppInterpretacao = 'N')
                                  GROUP BY MONTH(DataAudiometria)")->fetchAll('assoc');
    }

    public function getHomologacao($ano = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT MONTH(HomologacaoData) as mes, 'Homologacao' as natureza, COUNT(CodHomologacao) as quantidade FROM homologacao
                                  WHERE YEAR(HomologacaoData) = '{$ano}'
                                  AND (Conclusao= 's' OR Conclusao = 'n')
                                  GROUP BY MONTH(HomologacaoData)")->fetchAll('assoc');
    }

    public function getEspirometria($ano = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT MONTH(c.DataEspirometria) as mes, 'Espirometria' as natureza, COUNT(CodConsulta) as quantidade FROM consulta c
                                  WHERE YEAR(c.DataEspirometria) = '{$ano}'
                                  GROUP BY MONTH(c.DataEspirometria)
                                  ORDER BY MONTH(c.DataEspirometria)")->fetchAll('assoc');
    }

    public function getAcuidadeVisual($ano = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT MONTH(c.DataAcVisual) as mes, 'Acuidade Visual' as natureza, COUNT(CodConsulta) as quantidade FROM consulta c
                                  WHERE YEAR(c.DataAcVisual) = '{$ano}'
                                  GROUP BY MONTH(c.DataAcVisual)
                                  ORDER BY MONTH(c.DataAcVisual)")->fetchAll('assoc');
    }

    public function getExamesComplementares($ano = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT MesReferencia, TipoExame, Exame, SUM(Quantidade) as quantidade FROM exames_complementares
                                  WHERE AnoReferencia = '{$ano}'
                                  GROUP BY MesReferencia, TipoExame, Exame")->fetchAll('assoc');
    }

    public function getRealizadasConsulta($ano = null, $mes = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT WEEK(CO.DataConsulta) as semana, P.CodProfissional as codigo, P.Nome, COUNT(CO.CodConsulta) AS Realizadas FROM consulta CO
                                  INNER JOIN profissional P ON P.CodProfissional = CO.CodProfissional
                                  WHERE DATE_FORMAT(CO.DataConsulta, '%Y-%m') = '{$ano}-{$mes}'
                                  GROUP BY WEEK(CO.DataConsulta), P.Nome")->fetchAll('assoc');
    }

    public function getRealizadasAudiometria($ano = null, $mes = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT WEEK(AU.DataAudiometria) as semana, P.CodProfissional as codigo, P.Nome, COUNT(AU.CodAudiometria) AS Realizadas FROM audiometria AU
                                  INNER JOIN profissional P ON P.CodProfissional = AU.CodProfissional
                                  WHERE DATE_FORMAT(AU.DataAudiometria, '%Y-%m') = '{$ano}-{$mes}'
                                  GROUP BY WEEK(AU.DataAudiometria), P.Nome")->fetchAll('assoc');
    }

    public function getRealizadasHomologacao($ano = null, $mes = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT WEEK(H.HomologacaoData) as semana, P.CodProfissional as codigo, P.Nome, COUNT(H.CodHomologacao) AS Realizadas FROM homologacao H
                                  INNER JOIN profissional P ON P.CodProfissional = H.CodProfissional
                                  WHERE DATE_FORMAT(H.HomologacaoData, '%Y-%m') = '{$ano}-{$mes}'
                                  GROUP BY WEEK(H.HomologacaoData), P.Nome")->fetchAll('assoc');
    }

    public function getRealizadasManuais($ano = null, $mes = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT PM.SemanaManuais as semana, P.CodProfissional as codigo, P.Nome, SUM( PM.QuantidadeManuais ) AS Realizadas  FROM prodmed_manuais PM
                                  INNER JOIN profissional P ON P.CodProfissional = PM.CodProfissional
                                  WHERE PM.AnoManuais = '{$ano}'
                                  AND PM.MesManuais = '{$mes}'
                                  GROUP BY PM.SemanaManuais, P.Nome")->fetchAll('assoc');
    }

    public function getPrevistos($ano = null, $mes = null, $profissionais = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT P.CodProfissional as codigo, P.Nome, PR.Semana, PR.CapacidadeAtendimento FROM previstas PR
                                  INNER JOIN profissional P ON P.CodProfissional = PR.CodProfissional
                                  WHERE DATE_FORMAT(PR.DataPrevistas, '%Y-%m') = '{$ano}-{$mes}'
                                  AND P.CodProfissional IN({$profissionais})
                                  GROUP BY PR.Semana, P.Nome")->fetchAll('assoc');
    }

    public function getAgendamentos($ano = null, $mes = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT WEEK(AM.DataAgenda) as semana, ISNULL(DAYNAME(AM.Comparecido)) as falta, AC.CodAgendaClasse as codigo, AC.NomeAgendaClasse as Nome, TH.Turno, COUNT(AMAC.CodAgendaClasse) as quantidade FROM agenda_medica AM
                                  INNER JOIN agenda_medica_agenda_classe AMAC ON AM.CodAgenda = AMAC.CodAgenda
                                  INNER JOIN agenda_classe AC ON AC.CodAgendaClasse = AMAC.CodAgendaClasse
                                  LEFT JOIN tipohorario TH ON TH.CodTipoHorario = AM.CodTipoHorario
                                  WHERE DATE_FORMAT(AM.DataAgenda, '%Y-%m') = '{$ano}-{$mes}'
                                  GROUP BY WEEK(AM.DataAgenda), ISNULL(DAYNAME(AM.Comparecido)), AC.NomeAgendaClasse, TH.Turno")->fetchAll('assoc');
    }


}
