<div class="row-fluid">
  <div class="span4">
    <div class="widgetcontent ">
      <?php echo $this->element('relatorio'); ?>
    </div><!--widgetcontent-->
  </div>

  <div class="span8">
    <h4 class="widgettitle nomargin">PACIENTES POR PROFISSIONAL</h4>
    <div class="widgetcontent bordered shadowed" >
      <form method="get" autocomplete="off">
        <p>
          <label >Profissionais</label>
          <span class="field">
            <select name="profissional[]" class="input-xxlarge chzn-select" multiple="multiple" required data-placeholder="Selecione os profissionais"  >
              <option value=""></option>
              <?php foreach ($profissionais as $key => $value) { ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
              <?php } ?>
            </select>
          </span>
        </p>
        <p>
          <label >Per&iacute;odo</label>
          <span class="field">
            <input type="text" id="inicio" name="inicio" class="input-small mask-date" autocomplete="off" required placeholder="In&iacute;cio" />
            <input type="text" id="fim" name="fim" class="input-small mask-date" autocomplete="off" required placeholder="Fim" />
          </span>
        </p>

        <p class='stdformbutton'>
          <button type="submit" class="btn btn-success btn-large">Gerar Relat&oacute;rio</button>
        </p>
      </form>
    </div>
  </div>
</div>
