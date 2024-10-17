<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class=" marginleft15" style="width: 200px; margin-right: 15px; float: right;">
      <a href="#" id="btn-search" class="btn btn-popup" style="position: absolute; right: 0;" onclick="jQuery(this).attr('href', '<?php echo $this->Url->build(["action" => "pesquisar"], true); ?>?' + jQuery('#campo').val() + '=' + encodeURIComponent(jQuery('#search').val()) + '&action=add');"><span class="icon-search"></span></a>
      <input type="text" id="search" placeholder="Pesquisar..." style="width: 100%;" />
    </li>
    <li class=" marginleft15" style="float: right; width: 100px;">
      <select id="campo" style="width: 100%;">
        <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
        <option value="cpf" <?php echo $this->request->query('cpf') ? 'selected' : null; ?>>CPF</option>
      </select>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Fila de espera</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  echo $this->Form->input('data_hora_marca_consulta', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
  echo $this->Form->input('consultas_id', ['type' => 'hidden', 'value' => $this->request->query('consulta')]);
  echo $this->Form->input('filas_id', ['type' => 'hidden', 'value' => $this->request->query('filas_id')]);
  echo $this->Form->input('tipo', ['type' => 'hidden', 'value' => $this->request->query('tipo')]);
  echo $this->Form->input('empresas_id', ['type' => 'hidden', 'value' => $beneficiario['Empresa']['id']]);
  echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'value' => $beneficiario['id']]);
  echo $this->Form->input('dependentes_id', ['type' => 'hidden', 'value' => $dependente['id']]);
  ?>

  <?php if($beneficiario['id']) { ?>
    <p>
      <label>Benefici&aacute;rio</label>
      <span class="field">
      <?php echo $beneficiario['nome']; ?>
      </span>
    </p>
  <?php } else { ?>
    <p style="text-align: center; padding: 15px;">
      Selecione um trabalhador
    </p>
  <?php } ?>

  <?php if($beneficiario['Empresa']['id']) { ?>
    <p>
      <label>Empresa</label>
      <span class="field">
        <?php echo $beneficiario['Empresa']['nome']; ?>
      </span>
    </p>
  <?php } else if($beneficiario['id']) { ?>
    <p>
      <label>Empresa</label>
      <span class="field" style="color: red;">
        Trabalhador sem empresa cadastrada, <?php echo $this->Html->link('clique aqui para cadastrar uma empresa.', ['controller' => 'beneficiarios', 'action' => 'empresas', $beneficiario['id']], ['escape'=>false]) ?>
      </span>
    </p>
  <?php } ?>

  <?php if($beneficiario['id']) { ?>
    <p>
      <label>Telefone</label>
      <span class="field">
        <?php echo $fone; ?>
      </span>
    </p>
  <?php } ?>

<?php if($beneficiario['id'] && $beneficiario['situacao'] == 'A' && $beneficiario['Empresa']['situacao'] == 'A' || $this->request->query('tipo')) { ?>
    <p>
      <label>Consulta</label>
      <span class="field">
        <small>Unidade</small><small style="width: 145px;">Especialidades</small>
        <small style="<?php echo $this->request->query('fila') == 'N' ? '' : 'display: none;'; ?>">Profissional</small><br/>
        <?php echo $this->Form->input('unidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-small', 'empty' => true, 'options' => $unidades, 'onchange' => 'filtrar();', 'default' => $this->request->query('unidade')]); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('especialidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-medium', 'empty' => true, 'options' => $especialidades, 'onchange' => 'filtrar();', 'default' => $this->request->query('especialidade')]); ?> &nbsp;&nbsp;&nbsp;
      </span>
    </p>
    <p>
      <label>Turno</label>
      <span class="field">
        <?php echo $this->Form->input('turno', ['label' => false, 'type' => 'select', 'required' => false, 'empty' => 'Indiferente', 'class' => 'input-medium', 'options' => ['1'=>'1&ordm; Per&iacute;odo','2'=>'2&ordm; Per&iacute;odo'], 'escape' => false]); ?>
      </span>
    </p>
    <p>
      <label>Nome do solicitante</label>
      <span class="field">
        <?php echo $this->Form->input('nome_solicitante', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge', 'escape' => false]); ?>
      </span>
    </p>
    <p>
      <label>Telefone do solicitante</label>
      <span class="field">
        <?php echo $this->Form->input('telefone_solicitante', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium', 'escape' => false]); ?>
      </span>
    </p>
    <p>
      <label>Observa&ccedil;&atilde;o</label>
      <span class="field">
        <?php echo $this->Form->input('observacao', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge', 'escape' => false]); ?>
      </span>
    </p>
  <?php echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>"; ?>
  <?php } ?>
  <?php echo $this->Form->end(); ?>
</div>

<style>
td.disponiveis { width: 20%;  padding: 2px; text-transform: uppercase; }
td.disponiveis div { font-size: 10px; min-width: 150px; border: 1px solid #000; cursor: pointer; padding: 0; padding-left: 5px; margin: 1px;}
td.disponiveis div.vago { background: #9BCF9B; color: #000; }
td.disponiveis div.marcado { background: #BF7A5F; color: #fff;}
td.disponiveis div:hover { background: #ddd; color: #000; }
td.disponiveis div input { display: none; }
td.max-height { height: 300px; }
.field small { min-width: 90px; display: inline-block; margin-right: 20px; }
.ui-datepicker{ font-size:10px; }
</style>

<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery('#telefone-solicitante').mask('(99) 999999999');

  jQuery('#search').onEnter(function() {
    jQuery('#btn-search').click();
  });

  jQuery("a.btn-popup").colorbox({
    escKey: false,
    overlayClose: false,
    onLoad: function() {
      jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
    }
  });

});
</script>
