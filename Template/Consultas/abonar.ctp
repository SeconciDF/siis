<h4 class="widgettitle nomargin shadowed">ABONO DE FALTAS</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  if(isset($consulta)) {
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
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
    <label>Profissional</label>
    <span class="field"> <?php echo $consulta['Profissional']['nome']; ?> </span>
  </p>

  <p>
    <label>Hora Agendada</label>
    <span class="field"> <?php echo $consulta['data_hora_agendado']->format('d/m/Y H:i'); ?> </span>
  </p>

  <p>
    <label>Justificativa de Abono</label>
    <span class="field">
        <?php echo $this->Form->input('justificativa', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xlarge', 'escape' => false]); ?>
    </span>
  </p>

  <input type="hidden" name="consultas[]" value="<?php echo $consulta['id']; ?>" />
  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Confirmar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  echo $this->Form->end();
  }
  ?>

  <?php
  if(isset($consultas)) {
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create('Consultas', ['class' => 'stdform stdform2']);
  ?>

    <p>
      <div style="overflow-y: auto; height: 150px; width: 600px;">
        <table class="table">
          <tr>
            <th style="min-width: 50px;">Prontu&aacute;rio</th>
            <th>Paciente</th>
            <th>Especialidade</th>
            <th>Profissional</th>
            <th style="text-align: center; width: 70px;">Data/Hora</th>
          </tr>
          <?php foreach ($consultas as $key => $value) { ?>
            <tr>
              <td style="text-align: center; vertical-align:middle;">
                <?php
                  $prontuario = '';
                  if($value['Dependente']['id']) {
                    $prontuario = array_search($value['Dependente']['id'], explode(',',$value['dependentes']));
                    $prontuario = '.' . ++ $prontuario;
                  }
                  echo $value['Beneficiario']['id'] . $prontuario;
                ?>
              </td>
              <td>
                <input type="hidden" name="consultas[]" value="<?php echo $value['id']; ?>" />
                <?= $value['Dependente']['id'] ? '(D) ' : ''; ?> <?= h($value['paciente']); ?>
              </td>
              <td><?= h($value['Especialidade']['descricao']); ?></td>
              <td><?= h($value['Profissional']['nome']); ?></td>
              <td style="text-align: center;"><?= $value['data_hora_agendado'] ? date('d/m H:i', strtotime($value['data_hora_agendado'])) : null; ?></td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </p>


  <p>
    <label>Justificativa de Abono</label>
    <span class="field">
        <?php echo $this->Form->input('justificativa', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xlarge', 'escape' => false]); ?>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Confirmar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  echo $this->Form->end();
  }
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
