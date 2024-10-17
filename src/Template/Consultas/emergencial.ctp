<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class=" marginleft15" style="width: 200px; margin-right: 15px; float: right;">
      <a href="#" id="btn-search" class="btn btn-popup" style="position: absolute; right: 0;" onclick="jQuery(this).attr('href', '<?php echo $this->Url->build(["controller" => "consultas", "action" => "pesquisar"], true); ?>?' + jQuery('#campo').val() + '=' + encodeURIComponent(jQuery('#search').val()) + '&action=emergencial');"><span class="icon-search"></span></a>
      <input type="text" id="search" placeholder="Pesquisar..." style="width: 100%;" />
    </li>
    <li class=" marginleft15" style="float: right; width: 100px;">
      <select id="campo" style="width: 100%;">
        <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
        <option value="cpf" <?php echo $this->request->query('cpf') ? 'selected' : null; ?>>CPF</option>
        <option value="id" <?php echo $this->request->query('id') ? 'selected' : null; ?>>Prontu&aacute;rio</option>
      </select>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Marcar Consulta Emergencial</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  ?>

  <?php if($beneficiario['id']) { ?>
  <p>
    <label>Prontu&aacute;rio</label>
    <span class="field">
      <b>
        <?php
          $prontuario = '';
          if($dependente['id']) {
            $prontuario = array_search($dependente['id'], explode(',',$beneficiario['dependentes']));
            $prontuario = '.' . ++ $prontuario;
          }
          echo $beneficiario['id'] . $prontuario;
        ?>
      </b>
    </span>
  </p>
  <?php } ?>

  <?php if($dependente['id']) { ?>
    <p>
      <label>Dependente</label>
      <span class="field">
        <?php echo $this->Html->link("<span class='iconfa-pencil'></span> {$dependente['nome']}", ['controller' => 'beneficiarios', 'action' => 'view-dependente', $beneficiario['id'], $dependente['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]); ?>
        <input type="hidden" name="dependentes_id" value="<?php echo $dependente['id']; ?>" />
      </span>
    </p>
  <?php } ?>

  <?php if($beneficiario['id']) { ?>
    <p>
      <label>Benefici&aacute;rio</label>
      <span class="field">
        <?php echo $this->Html->link("<span class='iconfa-pencil'></span> {$beneficiario['nome']}", ['controller' => 'beneficiarios', 'action' => 'view', $beneficiario['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]); ?>
        <input type="hidden" name="beneficiarios_id" value="<?php echo $beneficiario['id']; ?>" />
      </span>
    </p>
  <?php } else { ?>
    <p style="text-align: center; padding: 15px;">
      Selecione um benefici&aacute;rio / dependente para come&ccedil;ar.
    </p>

  <?php } ?>

  <?php if($beneficiario['Empresa']['id']) { ?>
    <p>
      <label>Empresa</label>
      <span class="field">
        <?php echo $beneficiario['Empresa']['nome']; ?>
        <input type="hidden" name="empresas_id" value="<?php echo $beneficiario['Empresa']['id']; ?>" />
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
      <label>Consulta</label>
      <span class="field">
        <small>Unidade</small><small style="width: 145px;">Especialidades</small><br/>
        <?php echo $this->Form->input('unidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-small', 'empty' => true, 'options' => $unidades, 'default' => $this->request->session()->read('Auth.User.filtro.unidade')]); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('especialidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-medium', 'empty' => true, 'options' => $especialidades, 'default' => $this->request->session()->read('Auth.User.filtro.especialidade')]); ?>
      </span>
    </p>
    <p>
      <label>Consulta</label>
      <span class="field">
        <small>Turno</small><small>Motivo da Consulta</small><br/>
        <?php echo $this->Form->input('turno', ['label' => false, 'type' => 'select', 'required' => true, 'empty' => true, 'class' => 'input-small', 'options' => ['1'=>'1&ordm; Per&iacute;odo','2'=>'2&ordm; Per&iacute;odo'], 'escape' => false, 'default' => $this->request->session()->read('Auth.User.filtro.turno')]); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('motivos_consultas_id', ['label' => false, 'type' => 'select', 'required' => true, 'empty' => true, 'class' => 'input-medium', 'options' => ['4'=>'Assistencial'], 'escape' => false, 'default' => $this->request->session()->read('Auth.User.filtro.motivo')]); ?>
      </span>
    </p>
    <p>
      <label>Profissional</label>
      <span class="field">
        <?php echo $this->Form->input('profissionais_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-large', 'empty' => true, 'options' => $profissionais, 'default' => $this->request->session()->read('Auth.User.filtro.profissional')]); ?>
      </span>
    </p>
    <p>
        <label>Data e Hora</label>
        <span class="field">
            <small style="width: 100px;">Data</small><small>Hora</small><br/>
            <?php echo $this->Form->input('data_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa', 'value' => ($this->request->session()->read('Auth.User.filtro.date') ? date('d/m/Y', strtotime($this->request->session()->read('Auth.User.filtro.date'))) : null)]); ?> &nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->input('time_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-mini', 'placeholder' => 'hh:mm']); ?>
        </span>
    </p>

    <?php echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>"; ?>
  <?php } ?>
  <?php echo $this->Form->end(); ?>
</div>

<style>
td.disponiveis { width: 25%; padding: 10px;}
td.disponiveis div { padding: 4px; font-size: 10px; width: 100%; min-height:30px; border: 1px solid #000; cursor: pointer; margin: 2px;}
td.disponiveis div.vago { background: #9BCF9B; color: #000; }
td.disponiveis div.marcado { background: #BF7A5F; color: #fff;}
td.disponiveis div:hover { background: #ddd; color: #000; }
td.disponiveis div input { float: right; }
.field small { min-width: 90px; display: inline-block; margin-right: 20px; }
.ui-datepicker{ font-size:10px; }
</style>

<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#data-inicio').mask('99/99/9999');
    jQuery('#time-inicio').mask('99:99');
    jQuery("div.pagination ul li a").addClass('btn-popup');
    jQuery("a.btn-popup").colorbox({
      escKey: false,
      overlayClose: false,
        onLoad: function() {
            jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
        }
    });

    jQuery('#search').onEnter(function() {
      jQuery('#btn-search').click();
    });
});

</script>
