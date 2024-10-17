<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['action' => 'edit', $usuario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar Colaboradores', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Permiss&otilde;es</h4>
<div class="widgetcontent bordered shadowed nopadding">
    <?php echo $this->element('template'); ?>
    <?php echo $this->Form->create('Usuario', ['class' => 'stdform stdform2']); ?>
        <input type="hidden" name="colaboradores_id" value="<?php echo $usuario['id']; ?>" />
        <?php echo $this->Form->input('make_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);?>
        <?php echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d')]); ?>
        <?php echo $this->Form->input('nome', ['type' => 'text', 'value' => $usuario['nome'], 'class' => 'input-xxlarge', 'disabled' => 'disabled']); ?>
        <?php echo $this->Form->input('grupos_id', ['type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge']); ?>
        <?php echo $this->Form->button(__('Gravar'), ['class' => 'btn btn-success btn-large']); ?>
    <?php echo $this->Form->end(); ?>
</div>

<div class="ui-tabs" style="margin-top: 15px;">
    <table class="table table-bordered" id="dyntable">
        <thead>
            <th></th>
            <th>Grupos</th>
        </thead>
        <tbody>
            <?php foreach ($acessos as $key => $value): ?>
            <tr>
                <td class="actions" style="width: 10px; text-align: center;">
                    <?php echo $this->Form->postLink('<span class="icon-trash"></span>', ['action' => 'deletePermissao', "{$usuario['id']},{$key}"], ['confirm' => "Deseja excluir o grupo {$value}?", 'escape'=>false]); ?>
                </td>
                <td><?php echo $value; ?>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
