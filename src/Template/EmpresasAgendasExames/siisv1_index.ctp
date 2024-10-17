<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
$search .= $this->request->query('id');
?>
<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-calendar"></span> Nova guia', ['action' => 'siisv1-add'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <a href="http://seconci-df.org.br:8088/empresa/html/Tabela_Clinicas.pdf" class="btn" target="_blank"><span class="icon-list"></span> Tabela de exames</a>
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
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Guia de exames</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Trabalhador</th>
      <th style="width: 100px;">CPF</th>
      <th>Fun&ccedil;&atilde;o</th>
      <th style="width: 150px;">Cl&iacute;nica</th>
      <th style="width: 150px;">Registro</th>
      <th style="width: 100px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($exames as $exame): ?>
      <?php
      $style = ['background'=>''];
      ?>
      <tr style="background: <?php echo $style['background']; ?>;">
        <td><?php echo $exame['nm_trab']; ?></td>
        <td><?php echo $exame['cpf']; ?></td>
        <td><?php echo $exame['NomeFuncao']; ?></td>
        <td><?php echo $exame['ClinicaNome']; ?></td>
        <td><?php echo date('d/m/Y H:i', strtotime($exame['DataEmissao'])); ?></td>
        <td style="text-align: center; vertical-align:middle;">
          <?php echo $this->Html->link('<span class="iconfa-print"></span>', ['action' => 'guia', $exame['CodGuias']], ['class' => 'btn', 'style' => 'font-size: large;', 'title' => 'Comprovante de agendamento', 'escape'=>false]); ?>
        </td>
      </tr>

    <?php endforeach; ?>
  </tbody>
</table>

<style> tr td { color: #000; } </style>
<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
var url = '<?php echo $this->Url->build(['controller' => $this->request->controller], true); ?>/index/';
jQuery(document).ready(function() {
  jQuery('#btn-search').click(function() { filtrar(); });
  jQuery('#search').onEnter(function() { filtrar(); });

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
  if (jQuery('#paciente').val()) {
    url += 'paciente=' + jQuery('#paciente').val() + '&';
  }
  if (jQuery('#especialidade').val()) {
    url += 'especialidade=' + jQuery('#especialidade').val() + '&';
  }
  if (jQuery('#unidade').val()) {
    url += 'unidade=' + jQuery('#unidade').val() + '&';
  }
  if (jQuery('#search').val()) {
    url += jQuery('#campo').val()+'='+jQuery('#search').val();
  }
  lock();
  location.href = url;
}
</script>
