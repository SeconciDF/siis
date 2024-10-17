<?php
$locais = [
  '1' => 'Estabelecimento do empregador',
  '2' => 'Estabelecimento de terceiros'
];
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $empresa['id']], ['class' => 'btn', 'escape'=>false]) ?>
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

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Ambiente.descricao', 'Ambiente'); ?></th>
            <th><?= $this->Paginator->sort('Ambientes.local', 'Local'); ?></th>
            <th><?= $this->Paginator->sort('Ambientes.identificacao', 'Identifica&ccedil;&atilde;o', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('Ambientes.descricao', 'Descri&ccedil;&atilde;o', ['escape' => false]); ?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($setores as $setor): ?>
        <tr>
            <td><?= h($setor['Ambiente']['descricao']); ?></td>
            <td><?= h($locais[$setor['local']]); ?></td>
            <td><?= h($setor['identificacao']); ?></td>
            <td><?= h($setor['descricao']); ?></td>
            <td style="width: 190px; text-align: center;">
              <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $setor['id']], ['class' => 'btn', 'escape'=>false]); ?>
              <?php echo $this->Form->postLink('<span class="iconfa-trash"></span> Deletar', ['action' => 'delete', $setor['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn btn-danger', 'escape'=>false]); ?>
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
