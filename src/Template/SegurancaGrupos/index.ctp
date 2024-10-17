<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <!-- <li class=" marginleft15" style="float: right; width: 250px;">
            <a href="#" onclick="javascript: return false;" id="btnSearch" style="position: absolute; right: 0; margin: 5px;" ><span class="icon-search"></span></a>
            <input type="text" id="idSearch" class="filekeyword" placeholder="pesquisar..." style="width: 100%;" />
        </li> -->
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Grupos</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('SegurancaGrupos.descricao', 'Grupo');?></th>
            <th><?php echo $this->Paginator->sort('SegurancaGrupos.info', 'Descri&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('SegurancaGrupos.ativo', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th></th>
        </tr>
    </thead>
    <tbody style="background: #fff;">
        <?php foreach ($grupos as $grupo): ?>
        <tr>
            <td><?php echo $grupo['descricao']; ?> </td>
            <td><?php echo $grupo['info']; ?> </td>
            <td style="width: 40px; text-align: center;"><?php echo $grupo['ativo'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $grupo['id']], ['class' => 'btn', 'escape'=>false]); ?> &nbsp;&nbsp;
                <?php //echo $this->Form->postLink('<span class="icon-trash"></span> Deletar', ['action' => 'delete', $usuario->id], ['confirm' => 'Deseja excluir?', 'title' => 'Deletar', 'escape'=>false]); ?>
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
    var url = '<?php echo $this->Url->build(['controller' => $this->request->controller], true); ?>/index/';
    jQuery(document).ready(function() {
        jQuery('#btnSearch').click(function() {
            url += jQuery('#idSearch').val() + '/';
            var target = jQuery('#idSearch');
            if(target.val()) location.href = url;
        });
    });
</script>
