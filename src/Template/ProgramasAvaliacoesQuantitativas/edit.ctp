<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $programa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Avalia&ccedil;&atilde;o Quantitativa</h4>
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
    echo $this->Form->create($avaliacao, ['class' => 'stdform stdform2']);
    echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa['id']]);
    echo $this->Form->input('empresas_id', ['type' => 'hidden', 'value' => $empresa['id']]);
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
    <label>Tipo de Avalia&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_avaliacoes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => $avaliacoes]); ?>
    </span>
</p>
<p>
    <label>Setor</label>
    <span class="field">
        <?php echo $this->Form->input('ambientes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $ambientes]); ?>
    </span>
</p>

<p>
    <label>N&uacute;mero de Avalia&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('numero_avaliacao', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small' ]); ?>
    </span>
</p>
<p>
    <label>N&uacute;mero de Trabalhadores</label>
    <span class="field">
        <?php echo $this->Form->input('numero_trabalhadores', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small' ]); ?>
    </span>
</p>
<p>
    <label>Data Avalia&ccedil;&atilde;o</label>
    <span class="field">
        <?php $avaliacao->data_avaliacao = isset($avaliacao->data_avaliacao) ? $avaliacao->data_avaliacao->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_avaliacao', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa' ]); ?>
    </span>
</p>
<p>
    <label>Grupo Homog&ecirc;neo</label>
    <span class="field">
        <?php echo $this->Form->input('grupo_homogeneo', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xlarge' ]); ?>
    </span>
</p>
<p>
    <label>GHE</label>
    <span class="field">
        <?php echo $this->Form->input('ghe', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge chzn-select', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione os itens', 'default' => $selecionadas, 'options' => $grupos]); ?>
    </span>
</p>

<?php if($avaliacao['apoio_avaliacoes_id'] == '2') { ?>
<p>
    <label>Trabalhador</label>
    <span class="field">
        <?php echo $this->Form->input('beneficiarios_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge', 'options' => $beneficiarios]); ?>
    </span>
</p>
<p>
    <label>Tempo de Exposi&ccedil;&atilde;o (min)</label>
    <span class="field">
        <?php echo $this->Form->input('tempo_exposicao', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small' ]); ?>
    </span>
</p>

<p>
    <label>Tipo de exposi&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_exposicoes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-medium', 'options' => $exposicoes]); ?>
    </span>
</p>

<?php } ?>

<p>
    <label>Observa&ccedil;&atilde;o sobre Atividades</label>
    <span class="field">
        <?php echo $this->Form->input('observacao_atividades', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Equipamento de Amostragem</label>
    <span class="field">
        <?php echo $this->Form->input('equipamento_amostragem', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Metodologia de Avalia&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('metodologia_avaliacao', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>

<?php if($avaliacao['apoio_avaliacoes_id'] == '2') { ?>
<p>
    <label>Descri&ccedil;&atilde;o das Atividades</label>
    <span class="field">
        <?php echo $this->Form->input('descricao_atividades', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Dados do Ambiente</label>
    <span class="field">
        <?php echo $this->Form->input('dados_ambiente', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Regime de Revezamento</label>
    <span class="field">
        <?php echo $this->Form->input('regime_revezamento', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<p>
    <label>Poss&iacute;vel dano</label>
    <span class="field">
        <?php echo $this->Form->input('possivel_dano', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<?php } ?>

<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

    });
</script>
