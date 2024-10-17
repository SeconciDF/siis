<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'empresas', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'empresa', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Empresas de <?php echo $beneficiario['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Dados do Benefici&aacute;rio'), ['action' => 'edit', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Dependentes'), ['action' => 'dependentes', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Empresas'), ['action' => 'empresas', $beneficiario['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Empresa.nome', 'Empresa') ?></th>
            <th style="width: 80px; text-align: center;"><?= $this->Paginator->sort('BeneficiariosEmpresas.data_associacao', 'Associa&ccedil;&atilde;o', ['escape' => false]) ?></th>
            <th style="width: 40px; text-align: center;"><?php echo $this->Paginator->sort('BeneficiariosEmpresas.situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th style="width: 100px; text-align: center;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($empresas as $empresa): ?>
        <tr>
            <td><?= h($empresa['Empresa']['nome']); ?></td>
            <td style="width: 80px; text-align: center;"><?= date('d/m/Y', strtotime($empresa['data_associacao'])); ?></td>
            <td style="width: 40px; text-align: center;"><?php echo $empresa['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'empresa', $beneficiario['id'], $empresa['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
