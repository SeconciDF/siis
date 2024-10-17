<h4 class="widgettitle nomargin">Pacientes <button type='button' class='btn btn-small btn-danger' style="float: right; font-size: large;" onclick='location.reload();' ><span class="iconfa-remove-sign" ></span></button></h4>
<div style="overflow-y: auto; height: 250px; width: 600px;">
<table class="table table-bordered">
  <thead>
    <tr>
      <th><?= $this->Paginator->sort('paciente', 'Nome') ?></th>
      <th><?= $this->Paginator->sort('cpf', 'CPF') ?></th>
      <th style="text-align: center;"><?php echo $this->Paginator->sort('situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
      <th class="actions" style="width: 100px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($pacientes as $paciente): ?>
      <tr>
        <td><?php echo $paciente['nome']; ?></td>
        <td style="text-align: center;"><?php echo $paciente['cpf']; ?></td>
        <td style="width: 40px; text-align: center;"><?php echo $paciente['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
        <td style="width: 100px; text-align: center;">
          <?php echo $this->Html->link('<span class="icon-edit"></span> Marcar', ['controller' => 'EmpresasAgendasOdontos', 'action' => ($this->request->query('action') ? $this->request->query('action') : 'add'), $paciente['id']], ['class' => 'btn', 'escape'=>false]); ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot style="background: #fff;">
    <tr>
      <td colspan="8">
        <div style="float: left;">
          <?php echo $this->Paginator->counter('Pagina {{page}} de {{pages}}, mostrando {{current}} de {{count}} ({{start}} ate {{end}})'); ?>
        </div>
        <div class="pagination" style="float: right; margin: 0;">
          <ul>
            <?php
            echo $this->Paginator->prev('< ' . __('anterior'), array('tag' => 'li'), null, array('class' => 'disabled'));
            echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li'));
            echo $this->Paginator->next(__('próximo') . ' >', array('tag' => 'li'), null, array('class' => 'disabled'));
            ?>
          </ul>
        </div>
      </td>
    </tr>
  </tfoot>
</table>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery("thead tr th a").addClass('btn-popup');
  jQuery("div.pagination ul li a").addClass('btn-popup');
  jQuery("a.btn-popup").colorbox({
    onLoad: function() {
      jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
    }
  });


});
</script>
