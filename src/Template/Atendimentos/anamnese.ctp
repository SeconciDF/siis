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
      <li class="active"> <?php echo $this->Html->link(__('Anamnese'), ['action' => 'anamnese', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Prontu&aacute;rio'), ['action' => 'prontuario', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Odontograma de Referência'), ['action' => 'odontograma', $consulta['id'], 'referencia' => '1'], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Odontograma'), ['action' => 'odontograma', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Conclus&atilde;o'), ['action' => 'conclusao', $consulta['id']], ['escape' => false]); ?> </li>
    </ul>
  </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($anamnese, ['class' => 'stdform stdform2']);
  echo $this->Form->input('consultas_id', ['type' => 'hidden', 'value' => $consulta['id']]);
  ?>
  <table class="table table-bordered">
    <tr>
      <th>Pergunta</th>
      <th style="width: 300px;">Resposta</th>
      <th style="width: 300px;">Observa&ccedil;&atilde;o</th>
    </tr>
    <?php foreach ($anamneses as $key => $value) { ?>
      <tr>
        <td><?php echo $value['pergunta']; ?></td>
        <td>
          <input type="radio" name="resposta[<?php echo $value['id']; ?>]" value="NS" <?php echo $value['Consulta']['resposta'] == 'NS' ? 'checked' : ''; ?> <?php echo $bloqueado ? 'disabled' : null; ?> /> N&atilde;o sei &nbsp;&nbsp;
          <input type="radio" name="resposta[<?php echo $value['id']; ?>]" value="SI" <?php echo $value['Consulta']['resposta'] == 'SI' ? 'checked' : ''; ?> <?php echo $bloqueado ? 'disabled' : null; ?> /> Sim &nbsp;&nbsp;
          <input type="radio" name="resposta[<?php echo $value['id']; ?>]" value="NA" <?php echo $value['Consulta']['resposta'] == 'NA' ? 'checked' : ''; ?> <?php echo $bloqueado ? 'disabled' : (!$value['Consulta']['resposta']?'checked':''); ?> /> N&atilde;o
        </td>
        <td>
          <textarea name="observacao[<?php echo $value['id']; ?>]" rows="2" style="width: 300px;" <?php echo $bloqueado ? 'disabled' : null; ?>><?php echo $value['Consulta']['observacao']; ?></textarea>
        </td>
      </tr>
    <?php } ?>
  </table>

  <p>
    <label>QP / HDA </label>
    <span class="field">
      <textarea name="anamnese_qp_hda" rows="3" class="input-xxlarge" required <?php echo $bloqueado ? 'disabled' : null; ?>><?php echo $consulta['anamnese_qp_hda']; ?></textarea>
    </span>
  </p>

  <p>
    <label>N&ordm; de Escova&ccedil;&otilde;es Di&aacute;rias</label>
    <span class="field">
      <input type="number" name="numero_escovacoes_diarias" class="input-mini" value="<?php echo $consulta['numero_escovacoes_diarias']; ?>" <?php echo $bloqueado ? 'disabled' : null; ?> />
    </span>
  </p>

  <p>
    <label>Observa&ccedil;&otilde;es da Anamnese Especial </label>
    <span class="field">
      <textarea name="anamnese_odonto_obs" rows="3" class="input-xxlarge" <?php echo $bloqueado ? 'disabled' : null; ?>><?php echo $consulta['anamnese_odonto_obs']; ?></textarea>
    </span>
  </p>

  <?php
  if(!$bloqueado) {
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
  }
  echo $this->Form->end();
  ?>
</div>
<script type="text/javascript">
  jQuery(document).ready(function() {
      jQuery("a.btn-popup").colorbox({
        escKey: false,
        overlayClose: false,
        onLoad: function() {
          jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
        }
      });
  });
</script>
