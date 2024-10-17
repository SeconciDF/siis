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

<div id="tabs">
   <ul>
      <li><a href="#tabs1">Descri&ccedil;&atilde;o do programa </a></li>
      <li><a href="#tabs2">Respons&aacute;veis</a></li>
   </ul>
   <div id="tabs1" class="nopadding">
    <?php
        //echo $this->element('template');
        $this->Form->templates(['inputContainer' => '{{content}}']);
        echo $this->Form->create($programa, ['url' => ['action' => 'edit', $programa['id']], 'class' => 'stdform stdform2']);
    ?>
    <p>
        <label>Empresa</label>
        <span class="field">
            <?php echo $this->Form->input('empresas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge', 'options' => $empresas]); ?>
        </span>
    </p>
    <p>
        <label>Programa</label>
        <span class="field">
            <?php echo $this->Form->input('apoio_programas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $programas]); ?>
        </span>
    </p>
    <p>
        <label>Data Inicial</label>
        <span class="field">
            <?php $programa->data_inicial = isset($programa->data_inicial) ? $programa->data_inicial->format('d/m/Y') : null; ?>
            <?php echo $this->Form->input('data_inicial', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
        </span>
    </p>
    <p>
        <label>Data Final</label>
        <span class="field">
            <?php $programa->data_final = isset($programa->data_final) ? $programa->data_final->format('d/m/Y') : null; ?>
            <?php echo $this->Form->input('data_final', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
        </span>
    </p>

    <?php
        echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
        echo $this->Form->end();
    ?>
  </div>
  <div id="tabs2" class="nopadding">
   <?php
       //echo $this->element('template');
       $this->Form->templates(['inputContainer' => '{{content}}']);
       echo $this->Form->create($responsavel, ['url' => ['action' => 'responsaveis', $programa['id'], $responsavel['id']], 'class' => 'stdform stdform2']);

       if(!$responsavel['id']) {
         echo $this->Form->input('create_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
         echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
         echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa['id']]);
       }
   ?>
   <p>
       <label>Nome</label>
       <span class="field">
           <?php echo $this->Form->input('nome_responsaveil', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xlarge']); ?>
       </span>
   </p>
   <p>
       <label>NIS</label>
       <span class="field">
           <?php echo $this->Form->input('nis_responsaveil', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium']); ?>
       </span>
   </p>
   <p>
       <label>Identifica&ccedil;&atilde;o</label>
       <span class="field">
           <?php echo $this->Form->input('numero_orgao_classe', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?> &nbsp;&nbsp;&nbsp;
           <?php echo $this->Form->input('uf_orgao_classe', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-small', 'options' => $estados]); ?>  &nbsp;&nbsp;&nbsp;
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
     echo "<p class='stdformbutton'>
     {$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}
     {$this->Html->link('Novo', ['action' => 'responsaveis', $programa['id'], 'tab' => '2'], ['class' => 'btn btn-large', 'escape'=>false])}
     {$this->Html->link('Listar Todos', ['action' => 'edit', $programa['id'], 'tab' => '2'], ['class' => 'btn btn-large', 'escape'=>false])}
     </p>";
     echo $this->Form->end();
   ?>
 </div>
</div>

<input type="hidden" id="tab" value="<?php echo $this->request->query('tab'); ?>" />
<script type="text/javascript">
    jQuery(document).ready(function ($) {
      $('a[href="#tabs'+$('#tab').val()+'"]').parent().addClass('ui-tabs-active ui-state-active');
      // $('#tabs'+$('#tab').val()).addClass('in active');
      $('#tabs').tabs();
    });
</script>
