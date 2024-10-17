<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
$search .= $this->request->query('id');
?>
<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-calendar"></span> Agendar', ['action' => 'add'], ['class' => 'btn', 'escape' => false]) ?>
    </li>
    <li class="marginleft15">
      <a href="http://seconci-df.org.br:8088/empresa/html/Tabela_Clinicas.pdf" class="btn" target="_blank"><span class="icon-list"></span> Tabela de exames</a>
    </li>

    <li class=" marginleft15" style="float: right; width: 200px;">
      <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;"><span class="icon-search"></span></a>
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

<h4 class="widgettitle nomargin">Consulta M&eacute;dica</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <td style="background: #fff; text-align: right;" colspan="10">
        <small style="width: 12px; height: 12px; background: #99FF66; display: inline-block;"></small> Atendido &nbsp;
        <small style="width: 12px; height: 12px; background: #99CCCC; display: inline-block;"></small> Marcados &nbsp;
        <small style="width: 12px; height: 12px; background: #FFCC66; display: inline-block;"></small> Não chegou &nbsp;
      <small style="width: 12px; height: 12px; background: #F48164; display: inline-block;"></small> Faltou &nbsp;
      </td>
    </tr>
    <tr>
      <th>Trabalhador</th>
      <th>Fun&ccedil;&atilde;o</th>
      <th>Descri&ccedil;&atilde;o</th>
      <th style="width: 60px;">Turno</th>
      <th style="width: 80px;">Registro</th>
      <th style="width: 80px;">Agendamento</th>
      <th style="width: 100px;">Ações</th>
    </tr>
  </thead>
  <tbody style="background: #fff;">
    <?php foreach ($agendados as $agendado) : ?>
      <?php
      $style = ['background' => ''];
      if ($agendado['CodStatusAgenda'] == '2' || $agendado['DataAgenda'] < date('Y-m-d')) {
        $style['background'] = '#E45151';
      }else if($agendado['Comparecido']) {
        $style['background'] = '#64D666';
      }else if($agendado['DataAgenda'] == date('Y-m-d')){
        $style['background'] = '#DB7E10';
      }else{
        $style['background'] = '#337DFF';
      }
      ?>
      <tr style="background: <?php echo $style['background']; ?>;">
        <td><?php echo $agendado['nm_trab']; ?></td>
        <td><?php echo $agendado['NomeFuncao']; ?></td>
        <td>
          <b><?php echo $agendado['tipo'] == 'exames' ? 'Guia de exames' : 'Agendamento M&eacute;dico'; ?></b> <br />
          <b>
          <?php
            if($agendado['descricao'] == 'Consulta - Assistencial'){
              echo $agendado['tipo'] == 'exames' ? 'Cl&iacute;nica: ' : 'Natureza: '; ?></b> <?php echo $agendado['descricao']; 
              echo "<br><strong>Exame: </strong>".$agendado['NomeExame'];
            }else{
              echo $agendado['tipo'] == 'exames' ? 'Cl&iacute;nica: ' : 'Natureza: '; ?></b> <?php echo $agendado['descricao']; 
            } 
          ?>
        </td>
        <td style="text-align: center; vertical-align: middle;"><?php echo $agendado['CodTipoHorario'] < 24 ? '<strong>Matutino</strong><br>'.$agendado['TipoHorario'].'' : '<strong>Vespertino</strong><br>'.$agendado['TipoHorario'].''; ?>
      </td>
        <td style="text-align: center; vertical-align: middle;"><?php echo date('d/m/Y', strtotime($agendado['DataRegistro'])); ?></td>
        <td style="text-align: center; vertical-align: middle;">
          <?php
          $agendado['DataAgenda'] = $agendado['DataAgenda'] ? $agendado['DataAgenda'] : date('d/m/Y', strtotime($agendado['DataRegistro'] . ' + 7 days'));
          echo date('d/m/Y', strtotime($agendado['DataAgenda']));
          ?>
        </td>
        <td style="width: 130px; text-align: left; vertical-align:middle;">
          <?php
          if (!$agendado['Comparecido'] && $agendado['CodStatusAgenda'] != '2' && $agendado['tipo'] == 'medicos' && $agendado['DataAgenda'] == date('Y-m-d')) {
            echo $this->Html->link('<span class="iconfa-user"></span>', ['controller' => "empresas-agendas-{$agendado['tipo']}", 'action' => 'presenca', $agendado['id']], ['class' => 'btn', 'style' => 'font-size: large; color: #177517;', 'title' => 'Confirmar Presença', 'escape' => false]);
          }
          ?>
          <?php echo $this->Html->link('<span class="iconfa-print"></span>', ['controller' => "empresas-agendas-{$agendado['tipo']}", 'action' => 'comprovante', $agendado['id']], ['class' => 'btn', 'style' => 'font-size: large;', 'title' => 'Comprovante', 'target' => '_blank', 'escape' => false]); ?>
          <?php
          if ($agendado['CodStatusAgenda'] != '2' && $agendado['tipo'] == 'medicos' && $agendado['DataAgenda'] > date('Y-m-d')) {
            echo $this->Html->link('<span class="iconfa-trash"></span>', ['controller' => "empresas-agendas-{$agendado['tipo']}", 'action' => 'cancelar', $agendado['id']], ['class' => 'btn', 'style' => 'font-size: large; color: #FF0000;', 'title' => 'Cancelar agendamento', 'escape' => false]);
          }
          ?>
        </td>
      </tr>

    <?php endforeach; ?>
  </tbody>
</table>

<style>
  tr td {
    color: #000;
  }
</style>
<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
  var url = '<?php echo $this->Url->build(['controller' => $this->request->controller], true); ?>/index/';
  jQuery(document).ready(function() {
    jQuery('#btn-search').click(function() {
      filtrar();
    });
    jQuery('#search').onEnter(function() {
      filtrar();
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
      url += jQuery('#campo').val() + '=' + jQuery('#search').val();
    }
    lock();
    location.href = url;
  }
</script>