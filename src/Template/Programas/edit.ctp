<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <li class="marginleft15 right">
            <?php echo $this->Html->link('<span class="iconfa-paper-clip"></span> Anexos', ['controller' => 'programas', 'action' => 'anexos', $programa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <li class="marginleft15 right">
            <?php echo $this->Html->link('<span class="icon-print"></span> Impress&atilde;o', ['controller' => 'relatorios-programas', 'action' => 'imprimir', $programa['id']], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Programa</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li class="active"><?php echo $this->Html->link('Descri&ccedil;&atilde;o do programa', ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Respons&aacute;veis', ['controller' => 'programas', 'action' => 'responsaveis', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Textos', ['controller' => 'programas-textos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($programa, ['url' => ['action' => 'edit', $programa['id']], 'class' => 'stdform stdform2']);
?>
<p>
    <label>Empresa</label>
    <span class="field">
        <?php echo $this->Form->input('empresas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge', 'options' => $empresas]); ?>
    </span>
</p>
<p>
    <label>Programa</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_programas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $programas]); ?>
    </span>
</p>
<p>
    <label>Ano de refer&ecirc;ncia</label>
    <span class="field">
        <?php echo $this->Form->input('ano_referencia', ['label' => false, 'type' => 'number', 'min' => '1970', 'required' => true, 'class' => 'input-small']); ?>
    </span>
</p>
<p>
    <label>Data Inicial</label>
    <span class="field">
        <?php $programa->data_inicial = isset($programa->data_inicial) ? $programa->data_inicial->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_inicial', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
</p>
<p>
    <label>Data Final</label>
    <span class="field">
        <?php $programa->data_final = isset($programa->data_final) ? $programa->data_final->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_final', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
</p>

<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
      //$('a[href="#tabs'+$('#tab').val()+'"]').parent().addClass('ui-tabs-active ui-state-active');
      // $('#tabs'+$('#tab').val()).addClass('in active');
      //$('#tabs').tabs();



    });
</script>
