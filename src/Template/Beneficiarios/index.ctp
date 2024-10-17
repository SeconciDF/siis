<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
$search .= $this->request->query('id');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar Todos', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>

        <li class=" marginleft15" style="float: right; width: 200px;">
          <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
          <input type="text" id="search" class="filekeyword" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
        </li>
        <li class=" marginleft15" style="float: right; width: 100px;">
          <select id="campo" style="width: 100%;">
            <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
            <option value="cpf" <?php echo $this->request->query('cpf') ? 'selected' : null; ?>>CPF</option>
            <option value="id" <?php echo $this->request->query('id') ? 'selected' : null; ?>>Prontu&aacute;rio</option>
          </select>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Benefici&aacute;rios</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 60px;"><?= $this->Paginator->sort('Beneficiarios.id', 'Prontu&aacute;rio', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Beneficiarios.nome', 'Nome') ?></th>
            <th><?= $this->Paginator->sort('Beneficiarios.cpf', 'CPF') ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('Beneficiarios.situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($beneficiarios as $beneficiario): ?>
        <tr>
            <td style="text-align: center;"><?= h($beneficiario['id']) ?></td>
            <td><?= h($beneficiario['nome']) ?></td>
            <td><?= h($beneficiario['cpf']) ?></td>
            <td style="width: 40px; text-align: center;"><?php echo $beneficiario['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $beneficiario['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
