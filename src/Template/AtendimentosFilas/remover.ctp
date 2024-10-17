<h4 class="widgettitle nomargin shadowed">Motivo de retirada da fila</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($fila, ['class' => 'stdform stdform2']);
  echo $this->Form->input('data_hora_retira', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
  echo $this->Form->input('retirada_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
  echo $this->Form->input('situacao', ['type' => 'hidden', 'value' => 'RO']);
  ?>

  <?php if($fila['Dependente']['id']) { ?>
    <p>
      <label>Dados do Dependente</label>
      <span class="field">
        <small>CPF: <b><?php echo $fila['Dependente']['cpf']; ?></b></small>
        <small>Nascimento: <b><?php echo date('d/m/Y', strtotime($fila['Dependente']['data_nascimento'])); ?></b></small> <br/>
        Nome: <b><?php echo $fila['Dependente']['nome']; ?></b>
      </span>
    </p>
  <?php } ?>

  <p>
    <label>Dados do Benefici&aacute;rio</label>
    <span class="field">
      <small>CPF: <b><?php echo $fila['Beneficiario']['cpf']; ?></b></small>
      <small>Nascimento: <b><?php echo date('d/m/Y', strtotime($fila['Beneficiario']['data_nascimento']));  ?></b></small> <br/>
      Nome: <b><?php echo $fila['Beneficiario']['nome']; ?></b>
    </span>
  </p>

  <p>
    <label>Motivo de retirada da fila</label>
    <span class="field">
        <?php echo $this->Form->input('motivo_retirada', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xlarge', 'escape' => false]); ?>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Confirmar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  echo $this->Form->end();
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
