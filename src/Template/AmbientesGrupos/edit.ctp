<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $programa['id'], $ambiente['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Caracteriza&ccedil;&atilde;o do Ambiente</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
  <div class="navbar-inner nopadding">
    <ul class="nav">
      <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do ambiente', ['controller' => 'ambientes', 'action' => 'edit', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
      <li><?php echo $this->Html->link('Instala&ccedil;&otilde;es do setor', ['controller' => 'ambientes-setores', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
      <li><?php echo $this->Html->link('Processos', ['controller' => 'ambientes-processos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
      <li class="active"><?php echo $this->Html->link('GHE', ['controller' => 'ambientes-grupos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
      <li><?php echo $this->Html->link('Produtos qu&iacute;micos', ['controller' => 'ambientes-quimicos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
    </ul>
  </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($grupo, ['class' => 'stdform stdform2']);
  echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa['id']]);
  echo $this->Form->input('tipo_identificacao', ['type' => 'hidden', 'value' => '1']);
  ?>
  <p>
    <label>Empresa</label>
    <span class="field">
      <b><?php echo $empresa['nome']; ?></b>
    </span>
  </p>
  <p>
    <label>Ano de refer&ecirc;ncia</label>
    <span class="field">
      <?php echo $programa['ano_referencia']; ?>
    </span>
  </p>
  <p>
    <label>Setor</label>
    <span class="field">
      <?php echo $this->Form->input('ambientes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xlarge', 'options' => $ambientes, 'disabled' => array_diff(array_keys($ambientes), array($ambiente['id'])), 'default' => $ambiente['id']]); ?>
    </span>
  </p>
  <p>
    <label>N&uacute;mero</label>
    <span class="field">
      <?php echo $this->Form->input('numero', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small' ]); ?>
    </span>
  </p>
  <p>
    <label>Nome do GHE</label>
    <span class="field">
      <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
  </p>
  <p>
    <label>Descri&ccedil;&atilde;o do GHE</label>
    <span class="field">
      <?php echo $this->Form->input('descricao', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge' ]); ?>
    </span>
  </p>
  <p>
    <label>C&oacute;digo do GHE</label>
    <span class="field">
      <?php echo $this->Form->input('codigo', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xlarge' ]); ?>
    </span>
  </p>
  <p>
    <label>Local</label>
    <span class="field">
      <?php echo $this->Form->input('local', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => ['1' => 'Estabelecimento do empregador', '2' => 'Estabelecimento de terceiros']]); ?>
    </span>
  </p>
  <p>
    <label>CNPJ de terceiro</label>
    <span class="field">
      <?php echo $this->Form->input('identificacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?> &nbsp;&nbsp;&nbsp;
    </span>
  </p>
  <p>
    <label>Data da validade</label>
    <span class="field">
      <small>In&iacute;cio</small><small>Fim</small><br/>
      <?php $grupo['data_inicio_validade'] ? $grupo['data_inicio_validade'] = $grupo['data_inicio_validade']->format('d/m/Y') : null; ?>
      <?php $grupo['data_fim_validade'] ? $grupo['data_fim_validade'] = $grupo['data_fim_validade']->format('d/m/Y') : null; ?>
      <?php echo $this->Form->input('data_inicio_validade', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?> &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('data_fim_validade', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
  </p>
  <p>
    <label>Fase de identifica&ccedil;&atilde;o</label>
    <span class="field">
      <?php echo $this->Form->input('fase_identificacao', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => ['1' => 'Antecipa&ccedil;&atilde;o', '2' => 'Reconhecimento'], 'escape' => false]); ?>
    </span>
  </p>
  <p>
      <label>Processos</label>
      <span class="field">
          <?php echo $this->Form->input('processos', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge chzn-select', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione os itens', 'empty' => true, 'options' => $processos, 'default' => $selecionados]); ?>
      </span>
  </p>
  <p>
    <label>Observa&ccedil;&atilde;o</label>
    <span class="field">
      <?php echo $this->Form->input('observacao', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge' ]); ?>
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
  $('#identificacao').mask('99.999.999/9999-99');
  $('#data-inicio-validade').mask('99/99/9999');
  $('#data-fim-validade').mask('99/99/9999');

  $(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: false});

});
</script>
