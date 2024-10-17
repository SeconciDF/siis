<h4 class="widgettitle nomargin">Pacientes <button type='button' class='btn btn-small btn-danger' style="float: right; font-size: large;" onclick='location.reload();' ><span class="iconfa-remove-sign" ></span></button></h4>
<div style="overflow-y: auto; height: 250px; width: 600px;">
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Nome</th>
      <th>CPF</th>
      <th class="actions" style="width: 100px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($pacientes as $paciente): ?>
      <tr>
        <td><?php echo $paciente['nm_trab']; ?></td>
        <td style="text-align: center;"><?php echo $paciente['cpf']; ?></td>
        <td style="width: 100px; text-align: center;">
          <?php echo $this->Html->link('<span class="icon-edit"></span> Marcar', ['controller' => 'EmpresasAgendasExames', 'action' => 'siisv1-add', $paciente['cd_trab']], ['class' => 'btn', 'escape'=>false]); ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>

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
