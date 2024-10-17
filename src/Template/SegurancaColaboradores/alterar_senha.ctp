<!--<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-check"></span> Cancelar', ['controller' => 'mains', 'action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>-->

<h4 class="widgettitle nomargin shadowed">Alterar senha</h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    echo $this->element('template');
    echo $this->Form->create('Usuario', ['class' => 'stdform stdform2']);
    echo $this->Form->input('id', ['type' => 'hidden', 'value' => $usuario['id']]);
    echo $this->Form->input('nome', ['disabled' => true, 'class' => 'input-xxlarge', 'value' => $usuario['nome']]);
    echo $this->Form->input('login', ['disabled' => true, 'class' => 'input-xxlarge', 'value' => $usuario['login']]);
    echo $this->Form->input('senha_atual', ['type' => 'password', 'required' => true, 'class' => 'input-xxlarge']);
    echo $this->Form->input('nova_senha', ['type' => 'password', 'required' => true, 'class' => 'input-xxlarge']);
    echo $this->Form->input('confirmar_senha', ['type' => 'password', 'required' => true, 'class' => 'input-xxlarge']);
    echo $this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large']);
    echo $this->Form->end();
?>
</div>
