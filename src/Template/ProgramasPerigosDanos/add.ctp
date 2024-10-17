<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $programa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Identifica&ccedil;&atilde;o dos Perigos e Danos</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<!-- <div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do ambiente', ['controller' => 'ambientes', 'action' => 'edit', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Instala&ccedil;&otilde;es do setor', ['controller' => 'ambientes-setores', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Processos', ['controller' => 'ambientes-processos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('GHE', ['controller' => 'ambientes-grupos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Produtos qu&iacute;micos', ['controller' => 'ambientes-quimicos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
       </ul>
    </div>
</div> -->

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($risco, ['class' => 'stdform stdform2']);
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
        <?php echo $this->Form->input('ambientes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xlarge', 'options' => $ambientes]); ?>
    </span>
</p>
<p>
    <label>Nome do GHE</label>
    <span class="field">
        <?php echo $this->Form->input('ambientes_grupos_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xlarge', 'options' => $grupos]); ?>
    </span>
</p>
<p>
    <label>Agente / Tipo</label>
    <span class="field">
        <?php echo $this->Form->input('agentes_tipos_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => $agentes]); ?>
    </span>
</p>
<p>
    <label>Perigo / Fator de risco</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_fatores_riscos_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge', 'options' => $riscos]); ?>
    </span>
</p>
<p>
    <label>Poss&iacute;vel dano</label>
    <span class="field">
        <?php echo $this->Form->input('possivel_dano', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Fontes geradoras</label>
    <span class="field">
        <?php echo $this->Form->input('fonte_geradora', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Traget&oacute;ria e meio de propaga&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('meio_propagacao', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Padr&otilde;es legais / Limite de exposi&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('limite_exposicao', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Medidas de controle</label>
    <span class="field">
        <?php echo $this->Form->input('medida_controle', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => ['1'=>'Utilizado', '0'=>'N&atilde;o Utilizado'], 'escape' => false]); ?>
    </span>
</p>
<p>
    <label>EPI</label>
    <span class="field">
        <?php echo $this->Form->input('epi', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => ['1'=>'Utilizado', '0'=>'N&atilde;o Utilizado'], 'escape' => false]); ?>
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
