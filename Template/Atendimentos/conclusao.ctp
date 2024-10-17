<?php
  $bloqueado = $this->request->session()->read('Auth.User.bloqueado');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <?php if(!$bloqueado) { ?>
          <li class="marginleft15 right">
            <?php echo $this->Html->link('<span class="iconfa-ok" style="font-size: medium;"></span> Finalizar Atendimento', ['action' => 'finalizar', $consulta['id']], ['class' => 'btn btn-popup', 'escape'=>false]); ?>
          </li>
        <?php } ?>

        <li class="marginleft15 right">
          <?php echo $this->Html->link('<span class="iconfa-print" style="font-size: medium;"></span> Imprimir', ['action' => 'imprimir', $consulta['id']], ['class' => 'btn', 'target' => 'blank', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Consulta</h4>
<div class="navbar">
  <div class="navbar-inner">
    <ul class="nav">
      <li class=""> <?php echo $this->Html->link(__('Anamnese'), ['action' => 'anamnese', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Prontu&aacute;rio'), ['action' => 'prontuario', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Odontograma de Referência'), ['action' => 'odontograma', $consulta['id'], 'referencia' => '1'], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Odontograma'), ['action' => 'odontograma', $consulta['id']], ['escape' => false]); ?> </li>
      <li class="active"> <?php echo $this->Html->link(__('Conclusão'), ['action' => 'conclusao', $consulta['id']], ['escape' => false]); ?> </li>
    </ul>
  </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  ?>

  <p>
    <label>Tratamento</label>
    <span class="field">
      <textarea name="tratamento" rows="3" class="input-xxlarge" required <?php echo $bloqueado ? 'disabled' : null; ?>><?php echo $consulta['tratamento']; ?></textarea>
    </span>
  </p>

  <p>
    <label>O paciente deve:</label>
    <span class="field">
      <input type="radio" name="tratamento_alta_retorno" value="1" <?php  echo $consulta['tratamento_alta_retorno'] == '1' ? 'checked' : null; ?> <?php echo $bloqueado ? 'disabled' : null; ?> /> Receber alta &nbsp;&nbsp;
      <input type="radio" name="tratamento_alta_retorno" value="0" <?php  echo $consulta['tratamento_alta_retorno'] == '0' ? 'checked' : null; ?> <?php echo $bloqueado ? 'disabled' : null; ?> /> Retornar
    </span>
  </p>

  <p>
    <label>Deseja afastar o paciente?</label>
    <span class="field">
      <input type="radio" name="afastar" class="afastar sim" value="S" <?php echo $bloqueado ? 'disabled' : null; ?> /> SIM &nbsp;&nbsp;
      <input type="radio" name="afastar" class="afastar" value="N" checked <?php echo $bloqueado ? 'disabled' : null; ?> /> N&Atilde;O
    </span>
  </p>

  <p style="display: none;" class="afastar">
    <label>Per&iacute;odo de afastamento</label>
    <span class="field">
      <small>In&iacute;cio</small><small>Fim</small><br/>
      <input type="text" id="data-inicio" name="afastamento_data_inicio" class='input-small mask-date inicio' placeholder='dd/mm/aaaa' value="<?php echo $consulta['afastamento_data_inicio'] ? date('d/m/Y', strtotime($consulta['afastamento_data_inicio'])) : null; ?>" <?php echo $bloqueado ? 'disabled' : null; ?> /> &nbsp;&nbsp;&nbsp;
      <input type="text" id="data-fim" name="afastamento_data_fim" class='input-small mask-date fim' placeholder='dd/mm/aaaa' value="<?php echo $consulta['afastamento_data_fim'] ? date('d/m/Y', strtotime($consulta['afastamento_data_fim'])) : null; ?>" <?php echo $bloqueado ? 'disabled' : null; ?> />
    </span>
  </p>

  <p style="display: none;" class="afastar">
    <label>Motivo do afastamento</label>
    <span class="field">
      <textarea id="motivo" name="afastamento_motivo" rows="3" class="input-xxlarge" <?php echo $bloqueado ? 'disabled' : null; ?>><?php echo $consulta['afastamento_motivo']; ?></textarea>
    </span>
  </p>

  <?php
  if(!$bloqueado) {
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
  }
  echo $this->Form->end();
  ?>
</div>

<style media="screen">
span.field small { min-width: 100px; display: inline-block; margin-right: 20px; }
</style>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  jQuery("a.btn-popup").colorbox({
    escKey: false,
    overlayClose: false,
    onLoad: function() {
      jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
    }
  });

  jQuery("input.afastar").change(function() {
    if(jQuery(this).val() === 'S') {
      jQuery("#data-inicio, #data-fim, #motivo").attr('required', true);
      jQuery("p.afastar").show();
    } else {
      jQuery("#data-inicio, #data-fim, #motivo").attr('required', false);
      jQuery("p.afastar").hide();
    }
  });

  if(jQuery("input.inicio").val() || jQuery("input.fim").val()) {
    jQuery("input.sim").attr("checked", true).change();
  }
});
</script>
