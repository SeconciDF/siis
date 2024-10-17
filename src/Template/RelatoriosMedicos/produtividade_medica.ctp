<div class="row-fluid">
  <div class="span4">
    <div class="widgetcontent ">
      <?php echo $this->element('relatorio_medicos'); ?>
    </div><!--widgetcontent-->
  </div>

  <div class="span8">
    <h4 class="widgettitle nomargin">PRODUTIVIDADE M&Eacute;DICA</h4>
    <div class="widgetcontent bordered shadowed" >
      <form method="get" autocomplete="off">
        <p>
          <label >M&ecirc;s/Ano</label>
          <span class="field">
            <input type="text" name="referencia" id="referencia" class="input-small" autocomplete="off" required />
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
        jQuery('#referencia').mask('99/9999');

    });
</script>
