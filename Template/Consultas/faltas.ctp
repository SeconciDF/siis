<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
        <?php echo $this->Html->link('<span class="iconfa-check"></span> Abonar todas as faltas', ['action' => 'abonar', 'all' => '1', 'inicio' => $this->request->query('inicio'), 'fim' => $this->request->query('fim'), 'h_inicio' => $this->request->query('h_inicio'), 'h_fim' => $this->request->query('h_fim')], ['class' => 'btn btn-popup', 'escape'=>false]) ?>
    </li>

    <li class=" marginleft15" style="float: right;">
      <button type="button" name="button" class="btn" onclick="javascript: filtrar()"><span class="icon-search"></span></button>
    </li>

    <li class="marginleft15 " style="float: right;">
      <input type="text" id="fim" class="input-small mask-date" placeholder="data final" value="<?php echo $this->request->query('fim'); ?>"/>
      <input type="text" id="h-fim" class="input-mini" placeholder="00:00" value="<?php echo $this->request->query('h_fim'); ?>" />
    </li>

    <li class=" marginleft15 " style="float: right;">
      <input type="text" id="inicio" class="input-small mask-date" placeholder="data inicial" value="<?php echo $this->request->query('inicio'); ?>"/>
      <input type="text" id="h-inicio" class="input-mini" placeholder="00:00" value="<?php echo $this->request->query('h_inicio'); ?>" />
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Lista de Faltas</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <th style="min-width: 50px;"><?= $this->Paginator->sort('Beneficiario.id', 'Prontu&aacute;rio', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('paciente', 'Benefici&aacute;rio / Dependente', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Empresa.nome', 'Empresa', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Especialidade.descricao', 'Especialidade', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Profissional.nome', 'Profissional', ['escape' => false]) ?></th>
      <th><?= $this->Paginator->sort('Consultas.data_hora_agendado', 'Data e Hora', ['escape' => false]) ?></th>
      <th class="actions" style="width: 10px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($consultas as $consulta): ?>
      <tr>
        <td style="text-align: center; vertical-align:middle;">
          <?php
            $prontuario = '';
            if($consulta['Dependente']['id']) {
              $prontuario = array_search($consulta['Dependente']['id'], explode(',',$consulta['dependentes']));
              $prontuario = '.' . ++ $prontuario;
            }
            echo $consulta['Beneficiario']['id'] . $prontuario;
          ?>
        </td>
        <td><?= $consulta['Dependente']['id'] ? '(D) ' : ''; ?> <?= h($consulta['paciente']); ?></td>
        <td><?= h($consulta['Empresa']['nome']); ?></td>
        <td><?= h($consulta['Especialidade']['descricao']); ?></td>
        <td><?= h($consulta['Profissional']['nome']); ?></td>
        <td style="width: 110px; text-align: center;"><?= $consulta['data_hora_agendado'] ? date('d/m/Y H:i', strtotime($consulta['data_hora_agendado'])) : null; ?></td>
        <td style=" text-align: center;">
          <?php echo $this->Html->link('<span class="iconfa-check"></span>', ['action' => 'abonar', $consulta['id'], 'inicio' => $this->request->query('inicio'), 'fim' => $this->request->query('fim'), 'h_inicio' => $this->request->query('h_inicio'), 'h_fim' => $this->request->query('h_fim')], ['class' => 'btn btn-popup', 'style' => 'font-size: large;', 'title' => 'Abonar Falta', 'escape'=>false]); ?>
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
<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
var url = '<?php echo $this->Url->build(['controller' => $this->request->controller, 'action' => 'faltas'], true); ?>/';
jQuery(document).ready(function() {
  jQuery('#h-inicio, #h-fim').mask('99:99');
  jQuery('#btnSearch').click(function() {
    url += jQuery('#idSearch').val() + '/';
    var target = jQuery('#idSearch');
    if(target.val()) location.href = url;
  });

  jQuery("a.btn-popup").colorbox({
    escKey: false,
    overlayClose: false,
    onLoad: function() {
      jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
    }
  });
});

function filtrar() {
  url += '?';
  if (jQuery('#inicio').val()) {
    url += 'inicio=' + jQuery('#inicio').val() + '&';
  }
  if (jQuery('#fim').val()) {
    url += 'fim=' + jQuery('#fim').val() + '&';
  }
  if (jQuery('#h-inicio').val()) {
    url += 'h_inicio=' + jQuery('#h-inicio').val() + '&';
  }
  if (jQuery('#h-fim').val()) {
    url += 'h_fim=' + jQuery('#h-fim').val() + '&';
  }

  location.href = url;
}
</script>
