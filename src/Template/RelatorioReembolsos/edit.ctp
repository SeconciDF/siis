<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['controller' => 'relatorios', 'action' => 'medicos'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Relat&oacute;rio de Reembolso</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($reembolso, ['class' => 'stdform stdform2']);
  ?>

  <p>
      <label>Dados de reembolso</label>
      <span class="field">
          <?php echo $this->Form->input('reembolsos_id', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge', 'empty' => true, 'options' => $competencias]); ?>
      </span>
  </p>

  <p>
      <label>Empresa</label>
      <span class="field">
          <?php echo $this->Form->input('empresas_id', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge', 'empty' => true, 'options' => $empresas]); ?>
      </span>
  </p>

  <p>
    <label>Discrimina&ccedil;&atilde;o</label>
    <span class="field">
      <?php echo $this->Form->input('descricao', ['label' => false, 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>

  <p>
    <label>Valor</label>
    <span class="field">
      <?php //echo $this->Form->input('quantidade', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
      <?php $reembolso['valor'] = number_format($reembolso['valor'],2,',','.'); ?>
      <?php echo $this->Form->input('valor', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small valor']); ?>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
  echo $this->Form->end();
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  jQuery('.valor').priceFormat({
    prefix: false,
    centsSeparator: ',',
    thousandsSeparator: '.'
  });
});
</script>
