<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['controller' => 'relatorios', 'action' => 'medicos'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>

    <li class="marginleft15">
      <?php echo $this->Html->link('Imprimir', ['action' => 'imprimir'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>

    <?php echo $this->Form->create($reembolso, ['url' => array('action' => 'reembolso'), 'class' => 'stdform stdform2', 'autocomplete' => 'off']); ?>
      <li class="marginleft15 right">
        <?php echo $this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success', 'style' => 'margin-top: 20px;']); ?>
      </li>
      <li class="marginleft15 right">
        <?php $reembolso['vencimento'] = date('d/m/Y', strtotime($reembolso['vencimento'])); ?>
        Vencimento: <?php echo $this->Form->input('vencimento', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa', 'autocomplete' => 'off']); ?>
      </li>
      <li class="marginleft15 right">
        Compet&ecirc;ncia: <?php echo $this->Form->input('competencia', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small', 'autocomplete' => 'off']); ?>
      </li>
    <?php echo $this->Form->end(); ?>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Relat&oacute;rio de Reembolso</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <th><?= $this->Paginator->sort('empresa.nome', 'Nome'); ?></th>
      <th><?= $this->Paginator->sort('RelatorioReembolsosEmpresas.descricao', 'Discrimina&ccedil;&atilde;o', ['escape' => false]); ?></th>
      <th class="actions" style="width: 200px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($reembolsos as $reembolso): ?>
      <tr>
        <td><?= h($reembolso['empresa']['nome']); ?></td>
        <td><?= h($reembolso['descricao']); ?></td>
        <td style="text-align: center;">
          <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $reembolso['id']], ['class' => 'btn', 'escape'=>false]); ?>
          <?php echo $this->Form->postLink('<span class="icon-trash"></span> Deletar', ['action' => 'delete', $reembolso['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn', 'escape'=>false]); ?>
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
  jQuery('#competencia').mask('99/9999');
  jQuery('#vencimento').mask('99/99/9999');
});
</script>
