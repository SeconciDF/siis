<h4 class="widgettitle nomargin shadowed">Adicionar Procedimento</h4>
<div class="widgetcontent bordered shadowed nopadding" style="min-width: 600px;">
  <?php
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($odontograma, ['class' => 'stdform stdform2']);

  if(!$odontograma['id']) {
      echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
      echo $this->Form->input('profissionais_id', ['type' => 'hidden', 'value' => $consulta['profissionais_id']]);
      echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'value' => $consulta['beneficiarios_id']]);
      echo $this->Form->input('dependentes_id', ['type' => 'hidden', 'value' => $consulta['dependentes_id']]);
      echo $this->Form->input('consultas_id', ['type' => 'hidden', 'value' => $consulta['id']]);
      if($this->request->query('referencia')) {
        echo $this->Form->input('referencia', ['type' => 'hidden', 'value' => $this->request->query('referencia')]);
      } else {
          echo $this->Form->input('referencia', ['type' => 'hidden', 'value' => '0']);
      }
  }
  ?>

  <p>
    <label>Dente / Faces</label>
    <span class="field">
        <small>Dente</small><small>Faces</small><br/>
        <input type="number" min="11" max="85" id="dente" name="procedimento[dentes_id]" class="input-mini" value="<?php echo isset($dente[$procedimento['dentes_id']]) ? $dente[$procedimento['dentes_id']] : null; ?>" />

        <div class="faces-popup">
          <p style="border: 0; margin-bottom: -6px; text-align: center;">
            <input type="checkbox" name="" class="top" value="1" style="margin: 0;" />
          </p>
          <p style="border: 0; padding-left: 2px; text-align: center;">
            <input type="checkbox" name="" class="left" value="1" style="margin: -1px;" />
            <input type="checkbox" name="" class="center" value="1" style="margin: -2px;" />
            <input type="checkbox" name="" class="right" value="1" style="margin: 0;" />
          </p>
          <p style="border: 0; margin-top: -6px; text-align: center;">
            <input type="checkbox" name="" class="bottom" value="1" style="margin: 0;" />
          </p>
        </div>
    </span>
  </p>
  <p>
    <label>Procedimento</label>
    <span class="field">
      <select id="procedimento" name="procedimento[procedimentos_id]" class="input-large chzn-procedimentos" data-placeholder="." style="width: 95%;" required>
        <option value=""></option>
        <?php foreach ($procedimentos as $tipo => $values) { ?>
          <optgroup label="<?php echo $tipo ? 'BOCA' : 'DENTE'; ?>">
          <?php foreach ($values as $key => $value) { ?>
              <option value="<?php echo $key; ?>" data-tipo="<?php echo $tipo; ?>" <?php echo $procedimento['procedimentos_id'] == $key ? 'selected' : ''; ?>><?php echo str_pad($key, 6, '0', STR_PAD_LEFT); ?> - <?php echo $value; ?></option>
          <?php } ?>
          </optgroup>
        <?php } ?>
      </select>
    </span>
  </p>

  <?php if($procedimento['id']) { ?>
    <input type="hidden" name="procedimento[id]" value="<?php echo $procedimento['id']; ?>" />
  <?php } ?>

  <input type="hidden" id="boca-dente" name="procedimento[boca_dente]" value="<?php echo $procedimento['boca_dente']; ?>" />
  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  echo $this->Form->end();
  ?>
</div>

<input type="hidden" id="face-mesial" value="<?php echo $procedimento['face_mesial']; ?>" />
<input type="hidden" id="face-distal" value="<?php echo $procedimento['face_distal']; ?>" />
<input type="hidden" id="face-oclusal" value="<?php echo $procedimento['face_oclusal']; ?>" />
<input type="hidden" id="face-lingual" value="<?php echo $procedimento['face_lingual']; ?>" />
<input type="hidden" id="face-palatina" value="<?php echo $procedimento['face_palatina']; ?>" />
<input type="hidden" id="face-vestibular" value="<?php echo $procedimento['face_vestibular']; ?>" />

<style media="screen">
span.field small { min-width: 75px; display: inline-block; margin-right: 20px; }
div.faces-popup { position: absolute; width: 50px; top: 30px; left: 320px; }
@media only screen and (max-width: 1280px) {
    div.faces-popup { position: absolute; width: 50px; top: 65px; left: 100px; }
}
iframe{
    overflow:hidden;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  setTimeout(function() {
    jQuery(".chzn-procedimentos").chosen('destroy').chosen({width: "90%"});
  },100);

  jQuery("#procedimento").change(function() {
    jQuery("#boca-dente").val(jQuery(this).find(':selected').data('tipo'));
  });

  jQuery("#dente").change(function() {
    let dente = jQuery("#dente").val();
    jQuery('input[type=checkbox]').attr('name', '');

    if(jQuery.inArray(dente, ['11','12','13','14','15','16','17','18','51','52','53','54','55']) >= 0) {
      jQuery('div.faces-popup').find('input[type=checkbox].top').attr('name', 'procedimento[face_vestibular]').attr('checked',jQuery('#face-vestibular').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].left').attr('name', 'procedimento[face_distal]').attr('checked',jQuery('#face-distal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]').attr('checked',jQuery('#face-oclusal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].right').attr('name', 'procedimento[face_mesial]').attr('checked',jQuery('#face-mesial').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].bottom').attr('name', 'procedimento[face_palatina]').attr('checked',jQuery('#face-palatina').val() ? true : false);
    }

    if(jQuery.inArray(dente, ['21','22','23','24','25','26','27','28','61','62','63','64','65']) >= 0) {
      jQuery('div.faces-popup').find('input[type=checkbox].top').attr('name', 'procedimento[face_vestibular]').attr('checked',jQuery('#face-vestibular').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].left').attr('name', 'procedimento[face_mesial]').attr('checked',jQuery('#face-mesial').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]').attr('checked',jQuery('#face-oclusal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].right').attr('name', 'procedimento[face_distal]').attr('checked',jQuery('#face-distal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].bottom').attr('name', 'procedimento[face_palatina]').attr('checked',jQuery('#face-palatina').val() ? true : false);
    }

    if(jQuery.inArray(dente, ['41','42','43','44','45','46','47','48','81','82','83','84','85']) >= 0) {
      jQuery('div.faces-popup').find('input[type=checkbox].top').attr('name', 'procedimento[face_lingual]').attr('checked',jQuery('#face-lingual').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].left').attr('name', 'procedimento[face_distal]').attr('checked',jQuery('#face-distal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]').attr('checked',jQuery('#face-oclusal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].right').attr('name', 'procedimento[face_mesial]').attr('checked',jQuery('#face-mesial').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].bottom').attr('name', 'procedimento[face_vestibular]').attr('checked',jQuery('#face-vestibular').val() ? true : false);
    }

    if(jQuery.inArray(dente, ['31','32','33','34','35','36','37','38','71','72','73','74','75']) >= 0) {
      jQuery('div.faces-popup').find('input[type=checkbox].top').attr('name', 'procedimento[face_lingual]').attr('checked',jQuery('#face-lingual').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].left').attr('name', 'procedimento[face_mesial]').attr('checked',jQuery('#face-mesial').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]').attr('checked',jQuery('#face-oclusal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].right').attr('name', 'procedimento[face_distal]').attr('checked',jQuery('#face-distal').val() ? true : false);
      jQuery('div.faces-popup').find('input[type=checkbox].bottom').attr('name', 'procedimento[face_vestibular]').attr('checked',jQuery('#face-vestibular').val() ? true : false);
    }
  }).change();

});
</script>
