<h4 class="widgettitle nomargin shadowed">Abrir Consulta</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  echo $this->Form->input('id', ['type' => 'hidden', 'value' => $consulta['id']]);
  echo $this->Form->input('profissionais_id', ['type' => 'hidden', 'value' => $consulta['profissionais_id']]);
  echo $this->Form->input('date', ['type' => 'hidden', 'value' => $consulta['data_hora_agendado']->format('Y-m-d')]);
  ?>

  <?php if($consulta['Dependente']['id']) { ?>
    <p>
      <label>Dados do Dependente</label>
      <span class="field">
        <small>CPF: <b><?php echo $consulta['Dependente']['cpf']; ?></b></small>
        <small>Nascimento: <b><?php echo date('d/m/Y', strtotime($consulta['Dependente']['data_nascimento'])); ?></b></small> <br/>
        Nome: <b><?php echo $consulta['Dependente']['nome']; ?></b>
      </span>
    </p>
  <?php } ?>

  <p>
    <label>Dados do Benefici&aacute;rio</label>
    <span class="field">
      <small>CPF: <b><?php echo $consulta['Beneficiario']['cpf']; ?></b></small>
      <small>Nascimento: <b><?php echo date('d/m/Y', strtotime($consulta['Beneficiario']['data_nascimento']));  ?></b></small> <br/>
      Nome: <b><?php echo $consulta['Beneficiario']['nome']; ?></b>
    </span>
  </p>

  <p>
    <label>Empresa</label>
    <span class="field"> <?php echo $consulta['Empresa']['nome']; ?> </span>
  </p>

  <p>
    <label>Profissional</label>
    <span class="field"> <?php echo $consulta['Profissional']['nome']; ?> </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>";
  echo "{$this->Html->link('Visualizar', ['action' => 'visualizar', $consulta['id']], ['class' => 'btn  btn-warning btn-large', 'escape'=>false])} ";

  if($consulta['data_hora_pre_atendimento']) {
    echo "{$this->Form->button(__('Abrir consulta'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} ";
  }

  echo " <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button> ";
  echo "</p>";
  echo $this->Form->end();
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
