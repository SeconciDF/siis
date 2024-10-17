<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-check"></span> Permiss&otilde;es', ['action' => 'permissoes', $usuario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">editar Colaborador</h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    echo $this->element('template');
    echo $this->Form->create($usuario, ['class' => 'stdform stdform2']);
    echo $this->Form->input('ativo', ['type' => 'hidden']);

    echo $this->Form->input('nome', ['type' => 'text', 'required' => true, 'class' => 'input-xlarge']);
    echo $this->Form->input('login', ['type' => 'text', 'required' => true, 'class' => 'input-xlarge']);
    echo $this->Form->input('senha', ['type' => 'password', 'class' => 'input-large', 'value' => '']);
    echo $this->Form->input('unidades_id', ['type' => 'select', 'class' => 'input-medium', 'empty' => true, 'required' => false, 'options' => $unidades]);
    echo $this->Form->input('profissionais_id', ['type' => 'select', 'class' => 'input-large', 'empty' => true, 'required' => false, 'options' => $profissionais]);
    echo $this->Form->input('empresas_id', ['type' => 'select', 'class' => 'input-xxlarge', 'empty' => true, 'required' => false, 'options' => $empresas]);

    ?>

    <p>
        <label>Situa&ccedil;&atilde;o</label>
        <span class="field">
            <?php
                if($usuario['ativo'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#ativo\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
                else echo "<button type='submit' onclick='javascript: $(\"#ativo\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red;' title='Tornar ativo'> INATIVO </button>";
            ?>
        </span>
    </p>

    <?php
    echo $this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large gravar']);
    echo $this->Form->end();
?>
</div>
