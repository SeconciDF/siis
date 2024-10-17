<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-calendar"></span> Nova vaga', ['action' => 'add'], ['class' => 'btn btn-popup', 'escape'=>false]) ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Vagas de exames</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Data</th>
      <th>Exame</th>
      <th style="width: 150px;">Manh&atilde;</th>
      <th style="width: 150px;">Tarde</th>
      <th style="width: 100px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($vagas as $vaga): ?>
      <tr>
        <td><?php echo date('d/m/Y', strtotime($vaga['VagasData'])); ?></td>
        <td><?php echo $vaga['NomeExame']; ?></td>
        <td><?php echo $vaga['VagasManha']; ?></td>
        <td><?php echo $vaga['VagasTarde']; ?></td>
        <td style="text-align: center; vertical-align:middle;">
          <?php echo $this->Html->link('<span class="iconfa-trash"></span>', ['action' => 'deletar', $vaga['CodVagas']], ['class' => 'btn', 'style' => 'font-size: large; color: #FF0000;', 'title' => 'Cancelar', 'escape'=>false]); ?>
        </td>
      </tr>

    <?php endforeach; ?>
  </tbody>
</table>

<style> tr td { color: #000; } </style>
<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("a.btn-popup").colorbox({
      escKey: false,
      overlayClose: false,
      onLoad: function() {
        jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
      }
    });
});
</script>
