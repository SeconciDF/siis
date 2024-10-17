<?php
$search = $this->request->query('nome');
$search .= $this->request->query('id');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar Todos', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <li class=" marginleft15" style="float: right; width: 200px;">
          <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
          <input type="text" id="search" class="filekeyword" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
        </li>
        <li class=" marginleft15" style="float: right; width: 100px;">
          <select id="campo" style="width: 100%;">
            <option value="descricao" <?php echo $this->request->query('descricao') ? 'selected' : null; ?>>Descri&ccedil;&atilde;o</option>
          </select>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Feriados e Folgas</h4>
<table class="table table-bordered">
    <thead>
        <tr>
          <th style="width: 100px; text-align: center;"><?php echo $this->Paginator->sort('data', 'Data');?></th>
          <th><?php echo $this->Paginator->sort('descricao', 'Descri&ccedil;&atilde;o', ['escape' => false]);?></th>
          <th style="width: 50px; text-align: center;"><?php echo $this->Paginator->sort('situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
          <th></th>
        </tr>
    </thead>
    <tbody style="background: #fff;">
        <?php foreach ($indisponibilidades as $indisponibilidade): ?>
        <tr>
            <td style="width: 100px; text-align: center;"><?php echo date('d/m/Y', strtotime($indisponibilidade['data'])); ?> </td>
            <td><?php echo $indisponibilidade['descricao']; ?> </td>
            <td style="text-align: center;"><?php echo $indisponibilidade['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
              <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $indisponibilidade['id']], ['class' => 'btn', 'escape'=>false]); ?> &nbsp;&nbsp;
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
