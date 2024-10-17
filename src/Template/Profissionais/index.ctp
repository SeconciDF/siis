<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
$search .= $this->request->query('especialidade');
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
            <option value="especialidade" <?php echo $this->request->query('especialidade') ? 'selected' : null; ?>>Especialidade</option>
          </select>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Profissionais</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Profissionais.nome', 'Nome') ?></th>
            <th><?= $this->Paginator->sort('Profissionais.cpf', 'CPF') ?></th>
            <th><?= $this->Paginator->sort('especialidades', 'Especialidades') ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('Profissionais.situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($profissionais as $profissional): ?>
        <tr>
            <td><?= h($profissional['nome']) ?></td>
            <td><?= h($profissional['cpf']) ?></td>
            <td><?= h($profissional['especialidades']) ?></td>
            <td style="width: 40px; text-align: center;"><?php echo $profissional['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $profissional['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
