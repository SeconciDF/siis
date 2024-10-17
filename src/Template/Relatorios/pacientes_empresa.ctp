<div class="row-fluid">
  <div class="span4">
    <div class="widgetcontent ">
      <?php echo $this->element('relatorio'); ?>
    </div><!--widgetcontent-->
  </div>

  <div class="span8">
    <h4 class="widgettitle nomargin">PACIENTES POR EMPRESA</h4>
    <div class="widgetcontent bordered shadowed" >
      <form method="get" autocomplete="off">
        <p>
          <label >Empresas</label>
          <span class="field">
            <select name="empresa[]" class="input-xxlarge chzn-select" multiple="multiple" required data-placeholder="Selecione as empresas"  >
              <option value=""></option>
              <?php foreach ($empresas as $key => $value) { ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
              <?php } ?>
            </select> <br/>
            <input type="radio" name="tipo" value="T" checked /> Todos os pacientes &nbsp;&nbsp;&nbsp;
            <input type="radio" name="tipo" value="P" /> Pacientes consultados por per&iacute;odo
          </span>
        </p>
        <p id='periodo' style="display: none;">
          <label >Per&iacute;odo</label>
          <span class="field">
            <input type="text" id="inicio" name="inicio" class="input-small mask-date" autocomplete="off" placeholder="In&iacute;cio" />
            <input type="text" id="fim" name="fim" class="input-small mask-date" autocomplete="off" placeholder="Fim" />
          </span>
        </p>

        <p class='stdformbutton'>
          <button type="submit" class="btn btn-success btn-large">Gerar Relat&oacute;rio</button>
        </p>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
      jQuery("input[name='tipo']").click(function() {
        jQuery("#inicio, #fim").attr('required', false).val('');
        jQuery("#periodo").hide();

        if(jQuery("input[name='tipo']:checked").val() === 'P') {
          jQuery("#inicio, #fim").attr('required', true);
          jQuery("#periodo").show();
        }
      });
    });
</script>
