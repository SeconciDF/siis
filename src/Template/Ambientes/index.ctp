<?php
$locais = [
  '1' => 'Estabelecimento do empregador',
  '2' => 'Estabelecimento de terceiros'
];
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $programa['id']], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Ambientes de Trabalho</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Ambiente.descricao', 'Ambiente'); ?></th>
            <th><?= $this->Paginator->sort('Ambientes.local', 'Local'); ?></th>
            <th><?= $this->Paginator->sort('Ambientes.identificacao', 'Identifica&ccedil;&atilde;o', ['escape' => false]); ?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($ambientes as $ambiente): ?>
        <tr>
            <td><?= h($ambiente['Ambiente']['descricao']); ?></td>
            <td><?= h($locais[$ambiente['local']]); ?></td>
            <td><?= h($ambiente['identificacao']); ?></td>
            <td style="width: 190px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $programa['id'], $ambiente['id']], ['class' => 'btn', 'escape'=>false]); ?>
                <?php echo $this->Form->postLink('<span class="iconfa-trash"></span> Deletar', ['action' => 'delete', $ambiente['id'], $programa['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn btn-danger', 'escape'=>false]); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot style="background: #fff;">
        <tr>
            <td colspan="8">
                <?php echo $this->element('paginacao'); ?>
            </td>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    jQuery(document).ready(function() {

    });
</script>
