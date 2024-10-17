<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['controller' => 'relatorios', 'action' => 'medicos'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">RELAT&Oacute;RIO GER&Ecirc;NCIA M&Eacute;DICA</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($relatorio, ['class' => 'stdform stdform2']);
  ?>

  <p>
    <label >M&ecirc;s/Ano</label>
    <span class="field">
      <?php echo $this->Form->input('referencia', ['label' => false, 'type' => 'text', 'required' => true, 'maxlength' => '7', 'autocomplete' => 'off', 'placeholder' => 'mm/aaaa', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Total de a&ccedil;&otilde;es comunit&aacute;rias <small>Atividades</small></label>
    <span class="field">
      <?php echo $this->Form->input('atividades_conunitarias', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>N&uacute;mero de participantes <small>A&ccedil;&otilde;es comunit&aacute;rias</small></label>
    <span class="field">
      <?php echo $this->Form->input('atividades_conunitarias_qtd', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Palestras <small>Atividades</small></label>
    <span class="field">
      <?php echo $this->Form->input('atividades_palestras', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>N&uacute;mero de participantes <small>Palestras</small></label>
    <span class="field">
      <?php echo $this->Form->input('atividades_palestras_qtd', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>
  <p>
    <label>Curso de CIPA <small>Atividades</small></label>
    <span class="field">
      <?php echo $this->Form->input('atividades_cipa', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>N&uacute;mero de participantes <small>Curso de CIPA</small></label>
    <span class="field">
      <?php echo $this->Form->input('atividades_cipa_qtd', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>
  <p>
    <label>SECONCI Itinerante <small>Dias de atendimento</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_dias', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>N&uacute;mero de Empresas <small>SECONCI Itinerante</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_empresas', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>
  <p>
    <label>Novos <small>PCMSO</small></label>
    <span class="field">
      <?php echo $this->Form->input('pcmso_novos', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Encerrados <small>PCMSO</small></label>
    <span class="field">
      <?php echo $this->Form->input('pcmso_encerrados', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>
  <p>
    <label>Andamentos <small>PCMSO</small></label>
    <span class="field">
      <?php echo $this->Form->input('pcmso_andamentos', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Renovados no per&iacute;odo <small>PCMSO</small></label>
    <span class="field">
      <?php echo $this->Form->input('pcmso_renovados', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>
  <p>
    <label>Contratos Vigentes <small>PCMSO</small></label>
    <span class="field">
      <?php echo $this->Form->input('pcmso_vigentes', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Press&atilde;o Arterial <small>Atendimento Itinerante</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_arterial', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>
  <p>
    <label>Glicemia <small>Atendimento Itinerante</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_glicemia', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small e78']); ?>
    </span>
  </p>

  <p>
    <label>IMC <small>Atendimento Itinerante</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_imc', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Acuidade Visual <small>Atendimento Itinerante</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_acuidade', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>N&uacute;mero de atendimentos <small>Atendimento Itinerante</small></label>
    <span class="field">
      <?php echo $this->Form->input('itinerante_atendidos', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Audiometria <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_audiometria', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eAUD']); ?>
    </span>
  </p>

  <p>
    <label>Acuidade Visual <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_acuidade', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eACV']); ?>
    </span>
  </p>

  <p>
    <label>ECG <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_ecg', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eECG']); ?>
    </span>
  </p>

  <p>
    <label>EEG <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_eeg', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eEEG']); ?>
    </span>
  </p>

  <p>
    <label>Espirometria <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_espirometria', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eESP']); ?>
    </span>
  </p>

  <p>
    <label>Exames Laboratoriais <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_laboratoriais', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Raio-X Torax <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_raiox', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eRXT']); ?>
    </span>
  </p>

  <p>
    <label>Homologa&ccedil;&otilde;es <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_homologacoes', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eHOM']); ?>
    </span>
  </p>

  <p>
    <label>Cl&iacute;nicas externas <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outras_clinicas_externas', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Consultas Ocupacionais Externas <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_ocupacionais_externas', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Consultas Assistenciais Externas <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_assistenciais_externas', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small']); ?>
    </span>
  </p>

  <p>
    <label>Consultas Assistenciais Sede <small>Outros Atendimentos</small></label>
    <span class="field">
      <?php echo $this->Form->input('outros_assistenciais_sede', ['label' => false, 'type' => 'number', 'required' => false, 'autocomplete' => 'off', 'class' => 'input-small eASSsede']); ?>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
  echo $this->Form->end();
  ?>
</div>

<script type="text/javascript">
  jQuery(document).ready(function ($) {
    $('#referencia').mask('99/9999');
    $('#referencia').blur(function() {
      var ano = $('#referencia').val().substr(3,4);
      var mes = $('#referencia').val().substr(0,2);

      jQuery.get('<?php echo $this->Url->build(['controller' => 'relatorio-medico-mensal', 'action' => 'exames-por-mes-ano'], true); ?>/' + ano + '/' + mes, function (data) {
        if(data) {
          $.each( data, function( key, value ) {
            $('.'+key).val(value);
          });
        }
      }, 'json');
    });
  });
</script>
