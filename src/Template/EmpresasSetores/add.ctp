<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $empresa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Cadastro de setor</h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Empresa'), ['controller' => 'Empresas', 'action' => 'edit', $empresa['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Setor'), ['controller' => 'EmpresasSetores', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Jornada'), ['controller' => 'EmpresasJornadas', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Lota&ccedil;&atilde;o'), ['controller' => 'EmpresasLotacoes', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Frentes de trabalho'), ['controller' => 'EmpresasObras', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($setor, ['class' => 'stdform stdform2']);
    echo $this->Form->input('create_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
    echo $this->Form->input('empresas_id', ['type' => 'hidden', 'value' => $empresa['id']]);
?>
<p>
    <label>Empresa</label>
    <span class="field">
        <b><?php echo $empresa['nome']; ?></b>
    </span>
</p>
<p>
    <label>Ambiente</label>
    <span class="field">
        <?php echo $this->Form->input('apoio_ambientes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $ambientes]); ?>
    </span>
</p>
<p>
    <label>Local</label>
    <span class="field">
        <?php echo $this->Form->input('local', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => ['1' => 'Estabelecimento do empregador', '2' => 'Estabelecimento de terceiros']]); ?>
    </span>
</p>
<p>
    <label>Identifica&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('tipo_identificacao', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-small', 'options' => ['1' => 'CNPJ', '3' => 'CAEPF']]); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('identificacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?> &nbsp;&nbsp;&nbsp;
    </span>
</p>

<p>
    <label>Descri&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('descricao', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge' ]); ?>
    </span>
</p>
<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
      $(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: false});
      $('#identificacao').mask('99.999.999/9999-99');
    });
</script>
