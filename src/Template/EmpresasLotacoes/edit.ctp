<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $empresa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Cadastro de Lota&ccedil;&atilde;o</h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Empresa'), ['controller' => 'Empresas', 'action' => 'edit', $empresa['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Setor'), ['controller' => 'EmpresasSetores', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Jornada'), ['controller' => 'EmpresasJornadas', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Lota&ccedil;&atilde;o'), ['controller' => 'EmpresasLotacoes', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Frentes de trabalho'), ['controller' => 'EmpresasObras', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($lotacao, ['class' => 'stdform stdform2']);
?>
<p>
    <label>Empresa</label>
    <span class="field">
        <b><?php echo $empresa['nome']; ?></b>
    </span>
</p>
<p>
    <label>Setor</label>
    <span class="field">
        <?php echo $this->Form->input('empresas_setores_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $setores]); ?>
    </span>
</p>
<p>
    <label>Cargo</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_funcoes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $funcoes]); ?>
    </span>
</p>
<p>
    <label>Função</label>
    <span class="field">
        <?php echo $this->Form->input('funcao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-large']); ?>
    </span>
</p>
<p>
    <label>Turno</label>
    <span class="field">
        <?php echo $this->Form->input('empresas_jornadas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $jornadas]); ?>
    </span>
</p>
<p>
    <label>Regime de revezamento</label>
    <span class="field">
        <?php echo $this->Form->input('revezamento', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge' ]); ?>
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
