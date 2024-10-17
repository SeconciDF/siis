<h4 class="widgettitle nomargin shadowed">Hora de chegada</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  echo $this->Form->input('id', ['type' => 'hidden', 'value' => $consulta['id']]);
  echo $this->Form->input('date', ['type' => 'hidden', 'value' => $consulta['data_hora_agendado']->format('Y-m-d')]);
  echo $this->Form->input('turno', ['type' => 'hidden', 'value' => $consulta['data_hora_agendado']->format('H:i:s') <= '12:00:00' ? '1' : '2']);
  echo $this->Form->input('unidade', ['type' => 'hidden', 'value' => $consulta['unidades_id']]);
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
    <label>Apresentou documento</label>
    <span class="field">
      <input type="radio" name="apresentou_documento" value="1" checked /> SIM
      <input type="radio" name="apresentou_documento" value="0" /> N&Atilde;O
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
  echo "<p class='stdformbutton'>{$this->Form->button(__('Confirmar Chegada'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  echo $this->Form->end();
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
