<h4 class="widgettitle nomargin shadowed">Finalizar e sair do atendimento
  <?php if($msg['fatal']) { ?>
    <button type='button' class='btn btn-small btn-danger' style="float: right; font-size: large;" onclick='location.reload();' ><span class="iconfa-remove-sign" ></span></button>
  <?php } ?>
</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  echo $this->Form->input('id', ['type' => 'hidden', 'value' => $consulta['id']]);
  echo $this->Form->input('date', ['type' => 'hidden', 'value' => $consulta['data_hora_agendado']->format('Y-m-d')]);
  echo $this->Form->input('profissional', ['type' => 'hidden', 'value' => $consulta['profissionais_id']]);
  ?>

  <p>
    <?php foreach ($msg['fatal'] as $key => $value) { ?>
      <div class="alert alert-error alert-danger" style="margin: 15px 10px 0 10px;">
          <button type="button" class="close" data-dismiss="alert">x</button>
          <strong><?= $value; ?></strong>
      </div>
    <?php } ?>

    <?php foreach ($msg['warning'] as $key => $value) { ?>
      <div class='alert alert-danger' style="margin: 15px 10px 0 10px;">
          <button type="button" class="close" data-dismiss="alert">x</button>
          <strong><?= $value; ?></strong>
      </div>
    <?php } ?>
  </p>

  <?php if($consulta['Dependente']['id'] && !sizeof($msg['fatal'])) { ?>
    <p>
      <label>Dados do Dependente</label>
      <span class="field">
        <small>CPF: <b><?php echo $consulta['Dependente']['cpf']; ?></b></small>
        <small>Nascimento: <b><?php echo date('d/m/Y', strtotime($consulta['Dependente']['data_nascimento'])); ?></b></small> <br/>
        Nome: <b><?php echo $consulta['Dependente']['nome']; ?></b>
      </span>
    </p>
  <?php } ?>

  <?php if(!sizeof($msg['fatal'])) { ?>
    <p>
      <label>Dados do Benefici&aacute;rio</label>
      <span class="field">
        <small>CPF: <b><?php echo $consulta['Beneficiario']['cpf']; ?></b></small>
        <small>Nascimento: <b><?php echo date('d/m/Y', strtotime($consulta['Beneficiario']['data_nascimento']));  ?></b></small> <br/>
        Nome: <b><?php echo $consulta['Beneficiario']['nome']; ?></b>
      </span>
    </p>
  <?php } ?>

  <?php
  if(!sizeof($msg['fatal'])) {
    echo "<p class='stdformbutton'>{$this->Form->button(__('Confirmar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  }
  echo $this->Form->end();
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
