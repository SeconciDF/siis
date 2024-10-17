<?php
$search = $this->request->query('empresa');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>

        <li class=" marginleft15" style="float: right; width: 200px;">
            <input type="hidden" id="campo" value="empresa" />
            <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
            <input type="text" id="search" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Programas</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Programa.descricao', 'Programa'); ?></th>
            <th><?= $this->Paginator->sort('Empresa.nome', 'Empresa'); ?></th>
            <th style="width: 110px;"><?= $this->Paginator->sort('Programas.data_inicial', 'Data Inicial'); ?></th>
            <th style="width: 110px;"><?= $this->Paginator->sort('Programas.data_final', 'Data Final'); ?></th>
            <th style="width: 110px;"><?= $this->Paginator->sort('Programas.data_hora_registro', 'Data de Registro'); ?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($programas as $programa): ?>
        <tr>
            <td><?= h($programa['Programa']['descricao']); ?></td>
            <td><?= h($programa['Empresa']['nome']); ?></td>
            <td><?= h($programa['data_inicial']->format('d/m/Y')); ?></td>
            <td><?= h($programa['data_final']->format('d/m/Y')); ?></td>
            <td><?= h($programa['data_hora_registro']->format('d/m/Y')); ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $programa['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
      jQuery('#btn-search').click(function() { search(); });
      jQuery('#search').onEnter(function() { search(); });
    });

    function search() {
      var url = '<?php echo $this->Url->build(['controller' => $this->request->controller], true); ?>/index/';
      url += '?'+jQuery('#campo').val()+'='+jQuery('#search').val();
      var target = jQuery('#search');
      if(target.val()) {
        lock();
        location.href = url;
      }
    }
</script>
