<?php
$search = $this->request->query('empresa');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $programa['id']], ['class' => 'btn', 'escape'=>false]) ?>
        </li>

    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Cadastro de Documento Base</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do programa', ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Respons&aacute;veis', ['controller' => 'programas', 'action' => 'responsaveis', $programa['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Textos', ['controller' => 'programas-textos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
        </ul>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('titulo', 'T&iacute;tulo', ['escape' => false]); ?></th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($textos as $texto): ?>
        <tr>
            <td><?= h($texto['titulo']); ?></td>
            <td style="width: 100px; text-align: center;">
                <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $texto['id']], ['class' => 'btn', 'escape'=>false]); ?>
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
      var url = '<?php echo $this->Url->build(['controller' => $this->request->controller, 'action' => 'index', $programa['id']], true); ?>/';
      url += '?'+jQuery('#campo').val()+'='+jQuery('#search').val();
      var target = jQuery('#search');
      if(target.val()) {
        lock();
        location.href = url;
      }
    }
</script>
