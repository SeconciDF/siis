<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cnpj');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar Todos', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Nova', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>

        <li class=" marginleft15" style="float: right; width: 200px;">
            <!-- <input type="hidden" id="campo" value="nome" /> -->
            <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
            <input type="text" id="search" class="filekeyword" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
        </li>
        <li class=" marginleft15" style="float: right; width: 100px;">
          <select id="campo" style="width: 100%;">
            <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
            <option value="cnpj" <?php echo $this->request->query('cnpj') ? 'selected' : null; ?>>CNPJ</option>
          </select>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Empresas</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Empresas.nome', 'Nome') ?></th>
            <th><?= $this->Paginator->sort('Empresas.tipo_identificacao', 'Tipo de Registro', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Empresas.identificacao', 'N&uacute;mero', ['escape' => false]) ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('assistencial', 'Assistencial', ['escape' => false]);?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('Empresas.situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($empresas as $empresa): ?>
        <tr>
            <td><?= h($empresa['nome']) ?></td>
            <td><?= h($empresa['tipo_identificacao']) ?></td>
            <td>
              <?php
                $empresa['identificacao'] = preg_replace('/[^0-9]/', '', $empresa['identificacao']);
                echo h($empresa['identificacao']);
              ?>
            </td>
            <td style="width: 40px; text-align: center;"><?php echo $empresa['assistencial'] == 'C' ? "Adimplente" : "Inadimplente"; ?></td>
            <td style="width: 40px; text-align: center;"><?php echo $empresa['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $empresa['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
