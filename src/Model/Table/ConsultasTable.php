<?php

namespace App\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Log\Log;

class ConsultasTable extends Table {

    public function beforeSave(Event $event, Entity $entity) {
        $entity->telefone_solicitante = $this->formatFone($entity->telefone_solicitante);
    }

    public function formatFone($fone) {
        $fone = preg_replace('/[^0-9]/', '', $fone);
        return $fone;
    }

    public function formatDate($date, $time = null) {
        $date = implode('-', array_reverse(explode('/', $date)));
        //$date = new \DateTime($date);
        return "$date $time";
    }

    public function salvarConsultaMedica($data = null) {
      $connection = ConnectionManager::get('siis');
      $agendados = $connection->execute("SELECT a.DataAgenda, h.Turno, COUNT(a.DataAgenda) as agendados, v.VagasManha, v.VagasTarde FROM agenda_medica a
                                        INNER JOIN tipohorario h ON h.CodTipoHorario = a.CodTipoHorario
                                        INNER JOIN vagas v ON v.VagasData = a.DataAgenda
                                        WHERE a.DataAgenda = '{$data['DataAgenda']}'  AND (a.CodStatusAgenda != '2' AND a.Comparecido IS NULL )
                                        GROUP BY h.Turno, a.DataAgenda ASC")->fetchAll('assoc');

      foreach ($agendados as $key => $value) {
        if($data['CodTipoHorario'] == '1' && $value['Turno'] == 'M') {
          if($value['agendados'] >= $value['VagasManha']) {
            $this->error = __('Sem vaga dispon&iacute;vel para este dia!');
            return false;
          }
        }
        if($data['CodTipoHorario'] == '25' && $value['Turno'] == 'V') {
          if($value['agendados'] >= $value['VagasTarde']) {
            $this->error = __('Sem vaga dispon&iacute;vel para este dia!');
            return false;
          }
        }
      }

      $agenda = [
        ':CodStatusAgenda' => $data['CodStatusAgenda'],
        ':CodTrabalhador' => $data['CodTrabalhador'],
        ':CodEmpresa' => $data['CodEmpresa'],
        ':CodNatureza' => $data['CodNatureza'],
        ':CodFuncao' => $data['CodFuncaoNova'],
        ':CodTipoHorario' => $data['CodTipoHorario'],
        ':DataAgenda' => $data['DataAgenda'],
        ':DataRegistro' => $data['DataRegistro'],
        ':LoginAgendado' => $data['LoginAgendado'],
        ':EfetuadoPor' => $data['EfetuadoPor'],
        ':Altura' => ($data['Altura'] == 'S' ? 'S' : null),
        ':CodValidador' => mt_rand(1, 99),
        ':CodEntidade' => '1'
      ];

      try {
        $stmt = $connection->prepare("INSERT INTO agenda_medica (CodAgenda, CodStatusAgenda, CodTrabalhador, CodEmpresa, CodNatureza, CodFuncao, CodTipoHorario, DataAgenda, DataRegistro, LoginAgendado, EfetuadoPor, Altura, CodValidador, CodEntidade)
                                      VALUES (NULL, :CodStatusAgenda, :CodTrabalhador, :CodEmpresa, :CodNatureza, :CodFuncao, :CodTipoHorario, :DataAgenda, :DataRegistro, :LoginAgendado, :EfetuadoPor, :Altura, :CodValidador, :CodEntidade);");
        $stmt->execute($agenda);

        $stmt2 = $connection->prepare("INSERT INTO agenda_medica_agenda_classe (CodAgenda, CodAgendaClasse) VALUES (:CodAgenda, :CodAgendaClasse);");
        $stmt2->execute([':CodAgenda'=>$stmt->lastInsertId(),':CodAgendaClasse'=>$data['CodAgendaClasse']]);

        $this->success = __('Agendamento realizado com sucesso!');
      } catch (\Exception $e) {
        $this->error = __('Falha ao realizar agendamento');
        Log::write('debug',[
          'CodTrabalhador' => $agenda['CodTrabalhador'],
          'CodEmpresa' => $agenda['CodEmpresa'],
          'DataAgenda' => $agenda['DataAgenda'],
          'message' => $e->getMessage()
        ]);
      }
    }

    public function salvarExames($data = null) {
      $connection = ConnectionManager::get('siis');

      $guia = [
        ':CodProfissional' => '10',
        ':CodTrabalhador' => $data['CodTrabalhador'],
        ':CodEmpresa' => $data['CodEmpresa'],
        ':CodFuncao' => $data['CodFuncao'],
        ':CodClinica' => $data['CodClinica'],
        ':DataEmissao' => $data['DataEmissao'],
        ':DataAgendamento' => $data['DataAgendamento'],
        ':CodTipoHorario' => $data['CodTipoHorario'],
        ':CodLogin' => $data['CodLogin']
      ];

      try {
        $stmt = $connection->prepare("INSERT INTO guias (CodGuias, CodClinica, CodTrabalhador, CodEmpresa, CodProfissional, CodFuncao, DataEmissao, DataAgendamento, CodTipoHorario, CodLogin)
                                      VALUES (NULL, :CodClinica, :CodTrabalhador, :CodEmpresa, :CodProfissional, :CodFuncao, :DataEmissao, :DataAgendamento, :CodTipoHorario, :CodLogin);");

        $stmt->execute($guia);
        $this->success = __('Guia gerada com sucesso!');
      } catch (\Exception $e) {
        $this->error = __('Falha ao gerar guia');
        Log::write('debug',[
          'CodTrabalhador' => $data['CodTrabalhador'],
          'CodEmpresa' => $data['CodEmpresa'],
          'CodClinica' => $data['CodClinica'],
          'message' => $e->getMessage()
        ]);
      }

      $guia = $connection->execute("SELECT CodGuias, CodClinica, CodTrabalhador, CodEmpresa FROM guias
                            WHERE CodClinica='{$data['CodClinica']}' AND CodTrabalhador='{$data['CodTrabalhador']}' AND CodEmpresa='{$data['CodEmpresa']}' AND DataEmissao='{$data['DataEmissao']}'
                            ORDER BY CodGuias DESC")->fetch('assoc');

      if(!empty($data['exames']) && !isset($this->error) && $guia['CodGuias']) {
        foreach ($data['exames'] as $exame) {
          try {
            $stmt = $connection->prepare("INSERT INTO guias_tipoexame (CodGuias, CodTipoExame) VALUES (:CodGuias, :CodTipoExame);");
            $stmt->execute([
              ':CodGuias' => $guia['CodGuias'],
              ':CodTipoExame' => $exame
            ]);
          } catch (\Exception $e) {
            $this->error = __("Falha ao gravar exame: id #{$exame}");
            Log::write('debug',[
              'CodTrabalhador' => $guia['CodTrabalhador'],
              'CodEmpresa' => $guia['CodEmpresa'],
              'CodClinica' => $guia['CodClinica'],
              'CodExame' => $exame,
              'message' => $e->getMessage()
            ]);
          }
        }
      }
    }

    public function cancelarConsultaMedica($id = null) {
      $connection = ConnectionManager::get('siis');

      try {
        $stmt = $connection->prepare('UPDATE agenda_medica SET CodStatusAgenda = :CodStatusAgenda WHERE CodAgenda = :CodAgenda;');
        $stmt->execute([
          ':CodStatusAgenda' => '2',
          ':CodAgenda' => $id,
        ]);

        $this->success = __('Agendamento cancelado com sucesso!');
      } catch (\Exception $e) {
        $this->error = __('Falha ao cancelar agendamento');
        Log::write('debug',[
          'CodTrabalhador' => $agenda['CodTrabalhador'],
          'CodEmpresa' => $agenda['CodEmpresa'],
          'DataAgenda' => $agenda['DataAgenda'],
          'message' => $e->getMessage()
        ]);
      }
    }

    public function exameMedico($empresa = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT g.CodGuias as 'id', 'exames' as 'tipo', t.nm_trab, f.NomeFuncao, g.DataEmissao as 'DataRegistro', g.DataAgendamento as 'DataAgenda', c.ClinicaNome as 'descricao', g.CodTipoHorario FROM guias g
                                  INNER JOIN tb_trabalhador t ON g.CodTrabalhador = t.cd_trab
                                  INNER JOIN clinica c ON g.CodClinica = c.CodClinica
                                  LEFT JOIN tipofuncao f ON g.CodFuncao = f.CodFuncao
                                  WHERE g.CodEmpresa = {$empresa['cd_emp']}
                                  ORDER BY g.CodGuias DESC LIMIT 50")->fetchAll('assoc');
    }

    public function consultaMedica($empresa = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT a.CodAgenda as 'id', 'medicos' as 'tipo', t.nm_trab, f.NomeFuncao, a.DataRegistro, a.DataAgenda, n.NomeNatureza as 'descricao', a.CodTipoHorario, a.CodStatusAgenda, a.Comparecido FROM agenda_medica a
                                  INNER JOIN tb_trabalhador t ON a.CodTrabalhador = t.cd_trab
                                  INNER JOIN natureza n ON a.CodNatureza = n.CodNatureza
                                  LEFT JOIN tipofuncao f ON a.CodFuncao = f.CodFuncao
                                  WHERE a.CodEmpresa = {$empresa['cd_emp']}
                                  ORDER BY a.CodAgenda DESC LIMIT 50")->fetchAll('assoc');
    }

    public function agendados($empresa = null, $option = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT g.CodGuias as 'id', 'exames' as 'tipo', t.nm_trab, f.NomeFuncao, g.DataEmissao as 'DataRegistro', g.DataAgendamento as 'DataAgenda', c.ClinicaNome as 'descricao',
                                  g.CodTipoHorario, NULL as 'Comparecido', NULL as 'CodStatusAgenda' FROM guias g
                                  INNER JOIN tb_trabalhador t ON g.CodTrabalhador = t.cd_trab
                                  INNER JOIN clinica c ON g.CodClinica = c.CodClinica
                                  LEFT JOIN tipofuncao f ON g.CodFuncao = f.CodFuncao
                                  WHERE g.CodEmpresa = {$empresa['cd_emp']} {$option}
                                  UNION
                                  SELECT a.CodAgenda as 'id', 'medicos' as 'tipo', t.nm_trab, f.NomeFuncao, a.DataRegistro, a.DataAgenda, n.NomeNatureza as 'descricao',
                                  a.CodTipoHorario, a.Comparecido, a.CodStatusAgenda FROM agenda_medica a
                                  INNER JOIN tb_trabalhador t ON a.CodTrabalhador = t.cd_trab
                                  INNER JOIN natureza n ON a.CodNatureza = n.CodNatureza
                                  LEFT JOIN tipofuncao f ON a.CodFuncao = f.CodFuncao
                                  WHERE a.CodEmpresa = {$empresa['cd_emp']} {$option}
                                  ORDER BY DataRegistro DESC, id DESC LIMIT 50")->fetchAll('assoc');
    }

    public function gerarComprovante($id) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT a.CodAgenda, fe.NomeFuncao, fa.NomeFuncao as novaFuncao, h.Turno, t.nm_trab, t.cpf, t.rg, t.o_emissor, n.CodNatureza, n.NomeNatureza,
                                          a.Altura, a.DataAgenda, a.DataRegistro, a.Comparecido, a.CodTipoHorario, a.CodStatusAgenda, e.cd_emp, e.nm_fantasia, e.razao_social FROM agenda_medica a
                                  INNER JOIN tb_empresa e ON a.CodEmpresa = e.cd_emp
                                  INNER JOIN tb_trabalhador t ON a.CodTrabalhador = t.cd_trab
                                  INNER JOIN natureza n ON a.CodNatureza = n.CodNatureza
                                  INNER JOIN tipohorario h ON h.CodTipoHorario = a.CodTipoHorario
                                  LEFT JOIN emprego em ON em.CodTrabalhador = t.cd_trab AND em.Ativo = 'S'
                                  LEFT JOIN tipofuncao fa ON a.CodFuncao = fa.CodFuncao
                                  LEFT JOIN tipofuncao fe ON em.CodFuncao = fe.CodFuncao
                                  WHERE a.CodAgenda = {$id} ")->fetch('assoc');
    }

    public function gerarGuia($id) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT g.CodGuias, t.nm_trab, t.cpf, t.rg, t.o_emissor, t.celular,  c.CodClinica, c.ClinicaNome, c.ClinicaEndereco, c.ClinicaTelefone1, f.NomeFuncao, g.DataEmissao, g.DataAgendamento, g.CodTipoHorario, e.cd_emp, e.razao_social FROM guias g
                                  INNER JOIN tb_empresa e ON g.CodEmpresa = e.cd_emp
                                  INNER JOIN tb_trabalhador t ON g.CodTrabalhador = t.cd_trab
                                  INNER JOIN clinica c ON g.CodClinica = c.CodClinica
                                  LEFT JOIN tipofuncao f ON g.CodFuncao = f.CodFuncao
                                  WHERE g.CodGuias = {$id} ")->fetch('assoc');
    }

    public function gerarGuiaExames($id) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT e.CodTipoExame, e.NomeExame FROM guias_tipoexame g
                                  INNER JOIN tipoexame e ON e.CodTipoExame = g.CodTipoExame
                                  WHERE g.CodGuias = {$id} ORDER BY e.NomeExame ASC ")->fetchAll('assoc');
    }

    public function getExames($clinica = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT e.*, ce.LimiteExame FROM clinica_tipoexame ce
                                  INNER JOIN tipoexame e ON e.CodTipoExame = ce.CodTipoExame
                                  WHERE ce.CodClinica = {$clinica} ORDER BY e.NomeExame ")->fetchAll('assoc');
    }

    public function getClinicas() {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT c.CodClinica, c.ClinicaNome  FROM clinica c WHERE c.ClinicaStatus = 's' ORDER BY c.ClinicaNome ASC")->fetchAll('assoc');
    }

    public function getNaturezas() {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT * FROM natureza ORDER BY NomeNatureza ASC")->fetchAll('assoc');
    }

    public function getFuncoes() {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT CodFuncao, NomeFuncao FROM tipofuncao ORDER BY NomeFuncao ASC")->fetchAll('assoc');
    }

    public function getVagasConsulta() {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT * FROM vagas v WHERE v.VagasData > NOW()
                                  ORDER BY v.VagasData ASC LIMIT 15")->fetchAll('assoc');
    }

    public function getConsultasAgendadas() {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT a.DataAgenda, h.Turno, COUNT(a.DataAgenda) as agendados  FROM agenda_medica a
                                  INNER JOIN tipohorario h ON h.CodTipoHorario = a.CodTipoHorario
                                  WHERE a.DataAgenda > NOW() AND (a.CodStatusAgenda != '2' AND a.Comparecido IS NULL )
                                  GROUP BY h.Turno, a.DataAgenda ASC")->fetchAll('assoc');
    }

    public function getVagasExame($options = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT * FROM vagas_exames v WHERE v.VagasData > NOW()
                                  AND v.CodTipoExame IN({$options["exames"]})
                                  ORDER BY v.VagasData ASC LIMIT 15")->fetchAll('assoc');
    }

    public function getExamesAgendados($options = null) {
      $connection = ConnectionManager::get('siis');
      return $connection->execute("SELECT g.DataAgendamento, h.Turno, e.CodTipoExame, count(e.CodTipoExame) as agendados FROM guias g
                                  INNER JOIN guias_tipoexame e ON g.CodGuias = e.CodGuias
                                  INNER JOIN tipohorario h ON h.CodTipoHorario = g.CodTipoHorario
                                  WHERE g.DataAgendamento > NOW()
                                  AND g.CodClinica = '{$options["clinica"]}'
                                  AND e.CodTipoExame IN({$options["exames"]})
                                  GROUP BY g.DataAgendamento, h.Turno, e.CodTipoExame ")->fetchAll('assoc');
    }
}
