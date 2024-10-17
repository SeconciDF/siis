<h4 class="widgettitle nomargin shadowed">Vagas de exame</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create('VagaExame', ['class' => 'stdform stdform2']);
  ?>

  <p>
    <label>Cl&iacute;nica</label>
    <span class="field">
      <select class="input-xlarge" id="codTipoExame" name="CodTipoExame" required="required">
        <option value=""></option>
        <?php  foreach ($exames as $exame) { ?>
          <option value="<?php echo $exame['CodTipoExame']; ?>"><?php echo $exame['NomeExame']; ?></option>
        <?php } ?>
      </select>
    </span>
  </p>

  <p>
    <label>Data</label>
    <span class="field">
      <input type="text" id="vagasData" name="VagasData" required="required" class="input-medium" value="" />
    </span>
  </p>

  <p>
    <label>Manha</label>
    <span class="field">
      <input type="number" id="vagasManha" name="VagasManha" required="required" class="input-small" value="" />
    </span>
  </p>

  <p>
    <label>Tarde</label>
    <span class="field">
      <input type="number" id="vagasTarde" name="VagasTarde" required="required" class="input-small" value="" />
    </span>
  </p>

  <?php echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>"; ?>
  <?php echo $this->Form->end(); ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
  jQuery('#vagasData').mask('99/99/9999');
});
</script>
