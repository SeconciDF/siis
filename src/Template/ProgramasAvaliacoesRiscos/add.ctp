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

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Medida de Controle Existente', ['controller' => 'ProgramasMedidasControles', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('EPI Existente', ['controller' => 'ProgramasEpi', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Perfil da Exposi&ccedil;&atilde;o Existente', ['controller' => 'ProgramasPerfilExposicoes', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Avalia&ccedil;&atilde;o de Risco', ['controller' => 'ProgramasAvaliacoesRiscos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Plano de a&ccedil;&atilde;o', ['controller' => 'ProgramasPlanosAcoes', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
       </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($entity, ['class' => 'stdform stdform2']);
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
    <label>Agente / Perigo / Poss&iacute;vel dano</label>
    <span class="field">
        <?php echo $this->Form->input('perigos_danos_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge', 'options' => $perigos ]); ?>
    </span>
</p>
<p>
    <label>Avalia&ccedil;&atilde;o do risco</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_riscos_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => $riscos]); ?>
    </span>
</p>
<p>
    <label>Probabilidade</label>
    <span class="field">
        <?php echo $this->Form->input('probabilidade', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small' ]); ?>
    </span>
</p>
<p>
    <label>Gravidade</label>
    <span class="field">
        <?php echo $this->Form->input('gravidade', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small' ]); ?>
    </span>
</p>
<p>
    <label>Grau de incerteza</label>
    <span class="field">
        <?php echo $this->Form->input('grau_incerteza', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small' ]); ?>
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
