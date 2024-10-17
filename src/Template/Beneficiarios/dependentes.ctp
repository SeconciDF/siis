<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'dependentes', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'dependente', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15" style="float: right;">
            <?php echo $this->Html->link('<span class="iconfa-user-md" style="font-size: medium;"></span> Consultas', ['action' => 'consultas', $beneficiario['id']], ['class' => 'btn', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Dependentes de <?php echo $beneficiario['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Dados do Benefici&aacute;rio'), ['action' => 'edit', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Dependentes'), ['action' => 'dependentes', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Empresas'), ['action' => 'empresas', $beneficiario['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 10px;">C&oacute;digo</th>
            <th>Nome</th>
            <th>CPF</th>
            <th style="width: 40px; text-align: center;">Situa&ccedil;&atilde;o</th>
            <th style="width: 100px; text-align: center;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($dependentes as $num => $dependente): ?>
        <tr>
            <td style="text-align: center;"><?= h($num+1); ?></td>
            <td><?= h($dependente['nome']); ?></td>
            <td><?= h($dependente['cpf']); ?></td>
            <td style="width: 40px; text-align: center;"><?php echo $dependente['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'dependente', $beneficiario['id'], $dependente['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
