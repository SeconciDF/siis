<?php
     if (strpos($this->request->referer(), '/beneficiarios/consultas/') == false) {
         $this->request->session()->write('Auth.User.referer', $this->request->referer());
     }
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <?php if($this->request->session()->read('Auth.User.referer')) { ?>
            <li class=" marginleft15">
                <a href="<?php echo $this->request->session()->read('Auth.User.referer'); ?>" class="btn"><span class="icon-arrow-left"></span> Voltar</a>
            </li>
        <?php } ?>

        <li class="marginleft15" style="float: right;">
            <?php echo $this->Html->link('<span class="iconfa-user-md" style="font-size: medium;"></span> Consultas', ['action' => 'consultas', $beneficiario['id']], ['class' => 'btn btn-inverse', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Consultas do Benefici&aacute;rio - <?php echo $beneficiario['nome']; ?></h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <td style="background: #fff; text-align: right;" colspan="10">
        <!-- <small style="width: 12px; height: 12px; background: #D685AD; display: inline-block;"></small> Triado &nbsp; -->
        <!-- <small style="width: 12px; height: 12px; background: #7FFFD4; display: inline-block;"></small> Fonoaudiologia Finalizada &nbsp; -->
        <small style="width: 12px; height: 12px; background: #F48164; display: inline-block;"></small> Faltou &nbsp;
        <small style="width: 12px; height: 12px; background: #99CCCC; display: inline-block;"></small> Chegou &nbsp;
        <small style="width: 12px; height: 12px; background: #FFCC66; display: inline-block;"></small> Não chegou &nbsp;
        <small style="width: 12px; height: 12px; background: #99FF66; display: inline-block;"></small> Atendido &nbsp;
      </td>
    </tr>
    <tr>
      <th><?= $this->Paginator->sort('paciente', 'Benefici&aacute;rio / Dependente', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Unidade.nome', 'Unidade', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Especialidade.descricao', 'Especialidade', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Profissional.nome', 'Profissional', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Consultas.data_hora_agendado', 'Data Hora', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Consultas.data_hora_pre_atendimento', 'H. Cheg.', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Consultas.data_hora_atendimento', 'H. Atend.', ['escape' => false]) ?></th>
      <th class="actions" style="width: 60px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($consultas as $consulta): ?>
      <?php
      $style = ['background'=>''];
      if(!$consulta['data_hora_pre_atendimento'] && !$consulta['data_hora_atendimento'] && strtotime($consulta['data_hora_agendado']) > strtotime(date('Y-m-d'))) {
        $style['background'] = '#FFCC66';
      } else if($consulta['data_hora_fecha_atendimento']) {
        $style['background'] = '#99FF66';
      } else if($consulta['data_hora_pre_atendimento'] || $consulta['data_hora_atendimento']) {
        $style['background'] = '#99CCCC';
      } else if($consulta['st_consulta'] == 'FA') {
        $style['background'] = '#F48164';
      }
      ?>

      <tr style="background: <?php echo $style['background']; ?>;">
        <td><?= $consulta['Dependente']['id'] ? '(D) ' : ''; ?> <?= h($consulta['paciente']); ?></td>
        <td style="width: 60px; text-align: center;"><?= h($consulta['Unidade']['nome']); ?></td>
        <td><?= h($consulta['Especialidade']['descricao']); ?></td>
        <td><?= h($consulta['Profissional']['nome']); ?></td>
        <td style="width: 100px; text-align: center;"><?= $consulta['data_hora_agendado'] && $consulta['st_consulta'] != 'CA' ? date('d/m/Y H:i', strtotime($consulta['data_hora_agendado'])) : date('d/m/Y H:i', strtotime($consulta['data_hora_nao_consulta'])) . '<br/><b>CANCELADO</b>' ; ?></td>
        <td style="width: 60px; text-align: center;"><?= $consulta['data_hora_pre_atendimento'] ? date('H:i', strtotime($consulta['data_hora_pre_atendimento'])) : null; ?> </td>
        <td style="width: 60px; text-align: center;"><?= $consulta['data_hora_atendimento'] ? date('H:i', strtotime($consulta['data_hora_atendimento'])) : null; ?></td>
        <td style="width: 100px; text-align: center;">
          <?php
            if($consulta['data_hora_atendimento']) {
              echo $this->Html->link('<span class="iconfa-folder-open"></span>', ['controller' => 'agendas', 'action' => 'visualizar', $consulta['id']], ['class' => 'btn', 'style' => 'font-size: large;', 'title' => 'Abrir Consulta', 'escape'=>false]); echo '&nbsp;&nbsp;';
              echo $this->Html->link('<span class="iconfa-print"></span>', ['controller' => 'atendimentos', 'action' => 'imprimir', $consulta['id']], ['class' => 'btn', 'style' => 'font-size: large;', 'target' => 'blank', 'title' => 'Imprimir Prontu&aacute;rio', 'escape'=>false]);
            }
          ?>
        </td>
      </tr>

    <?php endforeach; ?>
  </tbody>
  <tfoot style="background: #fff;">
    <tr>
      <td colspan="10">
        <?php echo $this->element('paginacao'); ?>
      </td>
    </tr>
  </tfoot>
</table>

<style> tr td { color: #000; } </style>
