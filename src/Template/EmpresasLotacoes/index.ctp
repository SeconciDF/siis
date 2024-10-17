<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $empresa['id']], ['class' => 'btn', 'escape'=>false]) ?>
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

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Ambiente.descricao', 'Setor'); ?></th>
            <th><?= $this->Paginator->sort('Funcao.descricao', 'Cargo'); ?></th>
            <th><?= $this->Paginator->sort('funcao', 'Fun&ccedil;&atilde;o', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('Jornada.turno', 'Turno'); ?></th>
            <th><?= $this->Paginator->sort('revezamento', 'Regime de revezamento'); ?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($lotacoes as $lotacao): ?>
        <tr>
            <td><?= h($lotacao['Ambiente']['descricao']); ?></td>
            <td><?= h($lotacao['Funcao']['descricao']); ?></td>
            <td><?= h($lotacao['funcao']); ?></td>
            <td><?= h($lotacao['Jornada']['turno']); ?></td>
            <td><?= h($lotacao['revezamento']); ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $lotacao['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
