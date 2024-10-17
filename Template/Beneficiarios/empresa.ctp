<?php
function formatFone($fone) {
    $fone = preg_replace('/[^0-9]/', '', $fone);
    return $fone;
}
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'empresas', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'empresa', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Empresas de <?php echo $beneficiario['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Dados do Benefici&aacute;rio'), ['action' => 'edit', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Dependentes'), ['action' => 'dependentes', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Empresas'), ['action' => 'empresas', $beneficiario['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($empresa, ['class' => 'stdform stdform2']);
    echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'value' => $beneficiario['id']]);
    echo $this->Form->input('situacao', ['type' => 'hidden']);
?>

<p>
    <label>Data de associa&ccedil;&atilde;o</label>
    <span class="field">
        <?php $empresa->data_associacao ? $empresa->data_associacao = date('d/m/Y', strtotime($empresa->data_associacao)) : null; ?>
        <?php echo $this->Form->input('data_associacao', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
        <small style="float: right; text-align: right;">
        <?php
            if($empresa['id']) {
                if($empresa['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
                else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red;' title='Tornar ativo'> INATIVO </button>";
            }
        ?>
        </small>
    </span>
</p>
<p>
    <label>Empresa</label>
    <span class="field">
      <?php echo $this->Form->input('empresas_id', ['label' => false, 'type' => 'select', 'class' => 'input-xxlarge', 'empty' => true, 'required' => true, 'options' => $empresas]); ?>
    </span>
</p>

<?php if($empresa['situacao'] == 'I') { ?>
  <p>
      <label>Data da baixa</label>
      <span class="field">
        <?php $empresa->data_baixa ? $empresa->data_baixa = date('d/m/Y', strtotime($empresa->data_baixa)) : null; ?>
        <?php echo $this->Form->input('data_baixa', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
      </span>
  </p>
  <p>
      <label>Solicitante</label>
      <span class="field">
          <?php echo $this->Form->input('baixa_solicitante', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
  </p>
  <p>
      <label>Motivo da baixa</label>
      <span class="field">
          <?php echo $this->Form->input('baixa_motivo', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
  </p>
<?php } ?>
<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery('#cpf').mask('999.999.999-99');
        jQuery('#data-nascimento').mask('99/99/9999');
        jQuery('#cpf').blur();
    });
</script>
