<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
      <li class="marginleft15">
          <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
      </li>

      <li class=" marginleft15" style="float: right; width: 200px;">
        <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
        <input type="text" id="search" class="filekeyword" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
      </li>
      <li class=" marginleft15" style="float: right; width: 100px;">
        <select id="campo" style="width: 100%;">
          <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
          <option value="cpf" <?php echo $this->request->query('cpf') ? 'selected' : null; ?>>CPF</option>
        </select>
      </li>

      <li class=" marginleft15" style="float: right;">
        <select id="especialidade" name="especialidade" style="width: 150px;" onchange="javascript: filtrar();">
          <option value="">Todas as Especialidades</option>
          <?php
              foreach ($especialidades as $key => $value) {
                $selected = $this->request->query('especialidade') == $key ? 'selected' : null;
                echo "<option value='{$key}' {$selected}>{$value}</option>";
              }
          ?>
        </select>
      </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">HIST&Oacute;RICO DE RETIRADAS</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 250px;"><?= $this->Paginator->sort('paciente', 'Benefici&aacute;rio / Dependente', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('AtendimentosFilas.motivo_retirada', 'Motivo', ['escape' => false]) ?></th>
            <th style="width: 100px;"><?= $this->Paginator->sort('data_hora', 'Data / Hora', ['escape' => false]) ?></th>
            <th style="width: 150px;"><?= $this->Paginator->sort('Colaborador.nome', 'Respons&aacute;vel', ['escape' => false]) ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($atendimentos as $atendimento): ?>

        <?php
            $style = ['background'=>''];
            // if(!$consulta['data_hora_pre_atendimento'] && !$consulta['data_hora_atendimento'] && strtotime($consulta['data_hora_agendado']) > strtotime(date('Y-m-d'))) {
            //     $style['background'] = '#FFFF99';
            // } else if(!$consulta['data_hora_pre_atendimento'] && !$consulta['data_hora_atendimento'] && strtotime($consulta['data_hora_agendado']) < strtotime(date('Y-m-d'))) {
            //     $style['background'] = '#FF7755';
            // }
        ?>

        <tr style="background: <?php echo $style['background']; ?>;">
            <td><?= $atendimento['Dependente']['id'] ? "(D) {$atendimento['paciente']}" : $atendimento['paciente']; ?></td>
            <td><?= h(($atendimento['situacao'] == 'RF' && $atendimento['data_hora_agendamento']) ? 'CONSULTA AGENDADA' : $atendimento['motivo_retirada']); ?></td>
            <td style="width: 100px; text-align: center;"><?= $atendimento['data_hora'] ? date('d/m/Y H:i', strtotime($atendimento['data_hora'])) : null; ?></td>
            <td><?= h($atendimento['Colaborador']['nome']); ?></td>
            <td style="width: 50px; text-align: center;">
              <?php echo $this->Html->link('<span class="iconfa-comments-alt"></span>', ['action' => 'observacao', $atendimento['id']], ['class' => 'btn btn-popup', 'style' => 'font-size: large;', 'title' => 'Observa&ccedil;&otilde;es', 'escape'=>false]); ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </tbody>
    <tfoot style="background: #fff;">
        <tr>
            <td colspan="8">
                <?php echo $this->element('paginacao'); ?>
            </td>
        </tr>
    </tfoot>
</table>

<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
  var url = '<?php echo $this->Url->build(['controller' => $this->request->controller, 'action' => 'historico'], true); ?>/';
  jQuery(document).ready(function() {
    jQuery('#btn-search').click(function() { search(); });
    jQuery('#search').onEnter(function() { search(); });

    jQuery("a.btn-popup").colorbox({
      escKey: false,
      overlayClose: false,
      onLoad: function() {
        jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
      }
    });
  });

  function search() {
    url += '?'+jQuery('#campo').val()+'='+jQuery('#search').val();
    if (jQuery('#especialidade').val()) {
        url += '&especialidade=' + jQuery('#especialidade').val();
    }
    var target = jQuery('#search');
    if(target.val()) {
      lock();
      location.href = url;
    }
  }

  function filtrar() {
    url += '?';
    if (jQuery('#especialidade').val()) {
        url += 'especialidade=' + jQuery('#especialidade').val() + '&';
    }
    if (jQuery('#campo').val()) {
        url += jQuery('#campo').val()+'='+jQuery('#search').val();
    }
    lock();
    location.href = url;
  }
</script>
