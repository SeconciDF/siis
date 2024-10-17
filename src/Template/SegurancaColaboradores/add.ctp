<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Novo Colaborador</h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    echo $this->element('template');
    echo $this->Form->create($usuario, ['class' => 'stdform stdform2']);
    echo $this->Form->input('ativo', ['type' => 'hidden', 'value' => 'A']);
    echo $this->Form->input('make_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('data_ultimo_acesso', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);

    echo $this->Form->input('nome', ['type' => 'text', 'required' => true, 'class' => 'input-xlarge']);
    echo $this->Form->input('login', ['type' => 'text', 'required' => true, 'class' => 'input-xlarge']);
    echo $this->Form->input('senha', ['type' => 'password', 'class' => 'input-large', 'value' => '']);
    echo $this->Form->input('unidades_id', ['type' => 'select', 'class' => 'input-medium', 'empty' => true, 'required' => false, 'options' => $unidades]);
    echo $this->Form->input('profissionais_id', ['type' => 'select', 'class' => 'input-large', 'empty' => true, 'required' => false, 'options' => $profissionais]);
    echo $this->Form->input('empresas_id', ['type' => 'select', 'class' => 'input-xxlarge', 'empty' => true, 'required' => false, 'options' => $empresas]);


    echo $this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large gravar']);
    echo $this->Form->end();
?>
</div>
