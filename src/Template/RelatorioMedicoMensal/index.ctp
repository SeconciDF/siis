<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['controller' => 'relatorios', 'action' => 'medicos'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>

    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>

    <li class="marginleft15">
      <?php $d = date('Y'); ?>
      <?php echo $this->Html->link('Imprimir', ['action' => 'imprimir'], ['onclick' => "var ano = prompt('Digite o ano', '{$d}'); $(this).attr('href', $(this).attr('href')+'/'+ano);" , 'class' => 'btn']); ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">RELAT&Oacute;RIO GER&Ecirc;NCIA M&Eacute;DICA</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <th><?= $this->Paginator->sort('empresa.nome', 'Refer&ecirc;ncia', ['escape' => false]); ?></th>
      <th></th>
      <th class="actions" style="width: 200px;"></th>
    </tr>
  </thead>
  <tbody  style="background: #fff;">
    <?php foreach ($relatorios as $relatorio): ?>
      <tr>
        <td><?= h(date('m/Y', strtotime($relatorio['referencia']))); ?></td>
        <td> </td>
        <td style="text-align: center;">
          <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $relatorio['id']], ['class' => 'btn', 'escape'=>false]); ?>
          <?php //echo $this->Form->postLink('<span class="icon-trash"></span> Deletar', ['action' => 'delete', $reembolso['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn', 'escape'=>false]); ?>
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
