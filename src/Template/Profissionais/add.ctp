<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Novo Profissional</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
      //echo $this->element('template');
      $this->Form->templates(['inputContainer' => '{{content}}']);
      echo $this->Form->create($profissional, ['class' => 'stdform stdform2']);
      echo $this->Form->input('situacao', ['type' => 'hidden', 'value' => 'A']);
  ?>

  <p>
      <label>Documentos</label>
      <span class="field">
          <small>Registro Profissional</small><small>CPF</small><br/>
          <?php echo $this->Form->input('registro_profissional', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;&nbsp;
          <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium', 'onblur' => 'campo_cpf(this)']); ?>
      </span>
  </p>
  <p>
      <label>Nome</label>
      <span class="field">
          <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
      </span>
  </p>
  <p>
      <label>Email</label>
      <span class="field">
          <?php echo $this->Form->input('email', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
  </p>
  <p>
      <label>Apelido</label>
      <span class="field">
          <?php echo $this->Form->input('apelido', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xlarge']); ?>
      </span>
  </p>
  <p>
      <label>Especialidades</label>
      <span class="field">
          <?php echo $this->Form->input('especialidades', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge chzn-select', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione as especialidades', 'empty' => true, 'options' => $especialidades]); ?>
      </span>
  </p>
  <p>
      <label>Atendimento</label>
      <span class="field">
          <small>Idade M&aacute;xima</small><small>Idade M&iacute;nima</small><small>N&uacute;mero de Pacientes por Hor&aacute;rio</small><br/>
          <?php echo $this->Form->input('idade_minima', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
          <?php echo $this->Form->input('idade_maxima', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
          <?php echo $this->Form->input('limite_paciente_consulta', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?>
      </span>
  </p>

  <?php
      echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large'])}</p>";
      echo $this->Form->end();
  ?>
  </div>

  <style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
  <script type="text/javascript">
      jQuery(document).ready(function ($) {
          jQuery(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: true});
      });
  </script>
