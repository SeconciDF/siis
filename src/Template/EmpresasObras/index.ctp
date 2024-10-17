<?php
$perfil = array_keys($this->request->session()->read('Auth.User.perfil'));
$search = $this->request->query('nome');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar Todos', ['action' => 'index', $empresa['id']], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Nova', ['action' => 'add', $empresa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <li class=" marginleft15" style="float: right; width: 200px;">
            <input type="hidden" id="campo" value="nome" />
            <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
            <input type="text" id="search" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Frentes de trabalho</h4>

<?php if(in_array('9', $perfil)) { ?>
  <div class="navbar">
      <div class="navbar-inner">
          <ul class="nav">
              <li class=""> <?php echo $this->Html->link(__('Empresa'), ['controller' => 'Empresas', 'action' => 'edit', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Setor'), ['controller' => 'EmpresasSetores', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Jornada'), ['controller' => 'EmpresasJornadas', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Lota&ccedil;&atilde;o'), ['controller' => 'EmpresasLotacoes', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class="active"> <?php echo $this->Html->link(__('Frentes de trabalho'), ['controller' => 'EmpresasObras', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
          </ul>
      </div>
  </div>
<?php } ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('EmpresasObras.nome', 'Nome') ?></th>
            <th style="text-align: center;"><?php echo $this->Paginator->sort('EmpresasObras.situacao', 'Situa&ccedil;&atilde;o', ['escape' => false]);?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($obras as $obra): ?>
        <tr>
            <td><?= h($obra['nome']) ?></td>
            <td style="width: 40px; text-align: center;"><?php echo $obra['situacao'] == 'A' ? "<span class='iconfa-ok' style='color: green; font-size: large;'></span>" : "<span class='iconfa-ban-circle' style='color: red; font-size: large;'></span>"; ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $obra['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
      var url = '<?php echo $this->Url->build(['controller' => $this->request->controller, 'action' => 'index', $empresa['id']], true); ?>/';
      url += '?'+jQuery('#campo').val()+'='+jQuery('#search').val();
      var target = jQuery('#search');
      if(target.val()) {
        lock();
        location.href = url;
      }
    }
</script>
