<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Novo Grupo</h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    echo $this->element('template');
    echo $this->Form->create($grupo, ['class' => 'stdform stdform2']);
    echo $this->Form->input('make_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
    echo $this->Form->input('ativo', ['type' => 'hidden', 'value' => 'A']);

    echo $this->Form->input('descricao', ['label' => ['text' => 'Grupo'], 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']);
    echo $this->Form->input('info', ['label' => ['text' => 'Descri&ccedil;&atilde;o', 'escape' => false],'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge']);
    echo $this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large gravar']);
    echo $this->Form->end();
?>
</div>
