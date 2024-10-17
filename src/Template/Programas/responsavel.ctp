<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Programa</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do programa', ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Respons&aacute;veis', ['controller' => 'programas', 'action' => 'responsaveis', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Textos', ['controller' => 'programas-textos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($responsavel, ['url' => ['action' => 'responsavel', $programa['id'], $responsavel['id']], 'class' => 'stdform stdform2']);

    if(!$responsavel['id']) {
      echo $this->Form->input('create_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
      echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
      echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa['id']]);
    }
?>
<p>
    <label>Fun&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('funcoes_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $funcoes]); ?>  &nbsp;&nbsp;&nbsp;
    </span>
</p>
<p>
    <label>Nome</label>
    <span class="field">
        <?php echo $this->Form->input('nome_responsaveil', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xlarge']); ?>
    </span>
</p>
<p>
    <label>CREA/MTE</label>
    <span class="field">
        <?php echo $this->Form->input('crea_mte', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium']); ?>
    </span>
</p>
<p>
    <label>NIS</label>
    <span class="field">
        <?php echo $this->Form->input('nis_responsaveil', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium']); ?>
    </span>
</p>
<p>
    <label>Data Inicial</label>
    <span class="field">
        <?php $responsavel->data_inicio = isset($responsavel->data_inicio) ? $responsavel->data_inicio->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
</p>
<p>
    <label>Data Final</label>
    <span class="field">
        <?php $responsavel->data_fim = isset($responsavel->data_fim) ? $responsavel->data_fim->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_fim', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
</p>

<?php
  echo "<p class='stdformbutton'> {$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} </p>";
  echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
      //$('a[href="#tabs'+$('#tab').val()+'"]').parent().addClass('ui-tabs-active ui-state-active');
      // $('#tabs'+$('#tab').val()).addClass('in active');
      //$('#tabs').tabs();
    });
</script>
