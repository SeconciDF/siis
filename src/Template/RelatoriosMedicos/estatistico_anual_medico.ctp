<div class="row-fluid">
  <div class="span4">
    <div class="widgetcontent ">
      <?php echo $this->element('relatorio_medicos'); ?>
    </div><!--widgetcontent-->
  </div>

  <div class="span8">
    <h4 class="widgettitle nomargin">ESTAT&Iacute;STICO ANUAL M&Eacute;DICO</h4>
    <div class="widgetcontent bordered shadowed" >
      <form method="get" autocomplete="off">
        <p>
          <label >Ano</label>
          <span class="field">
            <input type="number" min="0" max="9999" name="ano" class="input-small" autocomplete="off" required />
          </span>
        </p>

        <p class='stdformbutton'>
          <button type="submit" class="btn btn-success btn-large">Gerar Relat&oacute;rio</button>
        </p>
      </form>
    </div>
  </div>
</div>
