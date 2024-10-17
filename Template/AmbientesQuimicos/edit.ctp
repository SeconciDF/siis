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
          <li><?php echo $this->Html->link('GHE', ['controller' => 'ambientes-grupos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Produtos qu&iacute;micos', ['controller' => 'ambientes-quimicos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
       </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($quimico, ['class' => 'stdform stdform2']);
    echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa['id']]);
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
    <label>Nome do produto qu&iacute;mico</label>
    <span class="field">
        <?php echo $this->Form->input('produto_quimico', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Nome da subst&acirc;ncia ativa</label>
    <span class="field">
        <?php echo $this->Form->input('substancia_ativa', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Forma f&iacute;sica do contaminante</label>
    <span class="field">
        <?php echo $this->Form->input('forma_fisica_contaminante', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

    });
</script>
