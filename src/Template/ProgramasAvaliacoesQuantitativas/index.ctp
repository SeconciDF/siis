<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $programa['id']], ['class' => 'btn', 'escape'=>false]) ?>
        </li>

    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Avalia&ccedil;&atilde;o Quantitativa</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="widgetcontent bordered shadowed nopadding">
  <table class="table table-bordered">
      <thead>
          <tr>
              <th><?= $this->Paginator->sort('grupo_homogeneo', 'Grupo Homog&ecirc;neo', ['escape' => false]); ?></th>
              <th class="actions" style="width: 100px;"></th>
          </tr>
      </thead>
      <tbody  style="background: #fff;">
          <?php foreach ($avaliacoes as $avaliacao): ?>
          <tr>
              <td><?= h($avaliacao['grupo_homogeneo']); ?></td>
              <td style="width: 190px; text-align: center;">
                <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $avaliacao['id']], ['class' => 'btn', 'escape'=>false]); ?>
                <?php echo $this->Form->postLink('<span class="iconfa-trash"></span> Deletar', ['action' => 'delete', $avaliacao['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn btn-danger', 'escape'=>false]); ?>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
      <tfoot style="background: #fff;">
          <tr>
              <td colspan="10">
                  <?php echo $this->element('paginacao'); ?>
              </td>
          </tr>
      </tfoot>
  </table>
</div>
