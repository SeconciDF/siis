<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
      <li class="marginleft15">
          <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $programa['id'], $ambiente['id']], ['class' => 'btn', 'escape'=>false]) ?>
      </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Caracteriza&ccedil;&atilde;o do Ambiente</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do ambiente', ['controller' => 'ambientes', 'action' => 'edit', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Instala&ccedil;&otilde;es do setor', ['controller' => 'ambientes-setores', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Processos', ['controller' => 'ambientes-processos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('GHE', ['controller' => 'ambientes-grupos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Produtos qu&iacute;micos', ['controller' => 'ambientes-quimicos', 'action' => 'index', $programa['id'], $ambiente['id']], ['escape'=>false]); ?></li>
       </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <table class="table table-bordered">
      <thead>
          <tr>
              <th><?= $this->Paginator->sort('AmbientesGrupos.numero', 'N&uacute;mero', ['escape' => false]); ?></th>
              <th><?= $this->Paginator->sort('AmbientesGrupos.codigo', 'C&oacute;digo do GHE', ['escape' => false]); ?></th>
              <th><?= $this->Paginator->sort('AmbientesGrupos.nome', 'Nome do GHE', ['escape' => false]); ?></th>
              <th class="actions" style="width: 100px;"></th>
          </tr>
      </thead>
      <tbody  style="background: #fff;">
          <?php foreach ($grupos as $grupo): ?>
          <tr>
              <td style="width: 5%;"><?= h($grupo['numero']); ?></td>
              <td style="width: 35%;"><?= h($grupo['codigo']); ?></td>
              <td><?= h($grupo['nome']); ?></td>
              <td style="width: 190px; text-align: center;">
                <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $grupo['id']], ['class' => 'btn', 'escape'=>false]); ?>
                <?php echo $this->Form->postLink('<span class="iconfa-trash"></span> Deletar', ['action' => 'delete', $grupo['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn btn-danger', 'escape'=>false]); ?>
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
</div>
