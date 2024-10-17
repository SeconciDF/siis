<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['action' => 'siisv1-index'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <a href="http://seconci-df.org.br:8088/empresa/html/Tabela_Clinicas.pdf" class="btn" target="_blank"><span class="icon-list"></span> Tabela de exames</a>
    </li>
    <li class=" marginleft15" style="width: 200px; margin-right: 15px; float: right;">
      <a href="#" id="btn-search" class="btn btn-popup" style="position: absolute; right: 0;" onclick="jQuery(this).attr('href', '<?php echo $this->Url->build(["action" => "siisv1-pesquisar"], true); ?>?' + jQuery('#campo').val() + '=' + encodeURIComponent(jQuery('#search').val()) + '&action=add');"><span class="icon-search"></span></a>
      <input type="text" id="search" placeholder="Pesquisar..." style="width: 100%;" />
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

<h4 class="widgettitle nomargin shadowed">Guia de exames</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create('Exame', ['class' => 'stdform stdform2']);
  echo $this->Form->input('CodLogin', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
  echo $this->Form->input('DataEmissao', ['type' => 'hidden', 'value' => date('Y-m-d')]);
  echo $this->Form->input('CodFuncao', ['type' => 'hidden', 'value' => $paciente['CodFuncao']]);
  echo $this->Form->input('CodEmpresa', ['type' => 'hidden', 'value' => $empresa['cd_emp']]);
  echo $this->Form->input('CodTrabalhador', ['type' => 'hidden', 'value' => $paciente['cd_trab']]);
  ?>

  <?php if($paciente['cd_trab']) { ?>
    <p>
      <label>CPF</label>
      <span class="field">
        <?php echo $paciente['cpf']; ?>
      </span>
    </p>
    <p>
      <label>Trabalhador</label>
      <span class="field">
      <?php echo $paciente['nm_trab']; ?>
      </span>
    </p>
    <p>
      <label>Fun&ccedil;&atilde;o</label>
      <span class="field">
        <?php echo $paciente['NomeFuncao']; ?>
      </span>
    </p>
  <?php } else { ?>
    <p style="text-align: center; padding: 15px;">
      Selecione um trabalhador
    </p>
  <?php } ?>

  <?php if($paciente['cd_trab']) { ?>
    <p>
      <label>Cl&iacute;nica</label>
      <span class="field">
        <select class="input-xlarge" id="clinica" name="CodClinica" required="required">
          <option value=""></option>
          <?php  foreach ($clinicas as $clinica) { ?>
            <?php $selected = $this->request->query('clinica') == $clinica['CodClinica'] ? 'selected' : ''; ?>
            <option value="<?php echo $clinica['CodClinica']; ?>" <?php echo $selected; ?>><?php echo $clinica['ClinicaNome']; ?></option>
          <?php } ?>
        </select>
      </span>
    </p>
  <?php } ?>

  <?php if($exames) { ?>
    <p>
      <label>Exames</label>
      <span class="field">
        <?php foreach ($exames as $exame) { ?>
          <small>
            <input type="checkbox" name="exames[]" value="<?php echo $exame['CodTipoExame']; ?>" />
            <?php echo $exame['NomeExame']; ?>
          </small>
        <?php } ?>
      </span>
    </p>
  <?php } ?>

  <?php echo "<p class='stdformbutton' style='display: none;'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>"; ?>
  <?php echo $this->Form->end(); ?>
</div>

<style>.field small { width: 250px; display: inline-block; margin-right: 20px; }</style>
<input type="hidden" id="url" value="<?php echo $this->Url->build(['controller' => 'EmpresasAgendasMedicos', 'action' => 'montar-agenda', 'unidade' => $this->request->query('unidade'), 'especialidade' => $this->request->query('especialidade'), 'profissional' => $this->request->query('profissional')], ['escape' => false]); ?>">
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery('#search').onEnter(function() {
    jQuery('#btn-search').click();
  });

  jQuery("a.btn-popup").colorbox({
    escKey: false,
    overlayClose: false,
    onLoad: function() {
      jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
    }
  });

  jQuery('#clinica').change(function() {
    filtrar();
    lock();
  });

  jQuery("input[type='checkbox']").click(function() {
    if(jQuery("input[type='checkbox']:checked").length) {
      jQuery("button[type='submit']").parent().show();
    } else {
      jQuery("button[type='submit']").parent().hide();
    }
  });
});

function filtrar() {
  url = '<?php echo $this->Url->build(['controller' => 'EmpresasAgendasExames', 'action' => 'siisv1-add', $paciente['cd_trab']], true); ?>?';
  if(jQuery('#clinica').val()) {
    url += 'clinica=' + jQuery('#clinica').val();
  }
  location.href = url;
}

</script>
