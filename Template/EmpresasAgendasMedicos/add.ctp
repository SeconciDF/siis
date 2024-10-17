<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['action' => 'index'], ['class' => 'btn', 'escape' => false]) ?>
    </li>
    <li class="marginleft15">
      <a href="//seconci-df.org.br:8088/empresa/html/Tabela_Clinicas.pdf" class="btn" target="_blank"><span class="icon-list"></span> Tabela de exames</a>
    </li>
    <li class=" marginleft15" style="width: 200px; margin-right: 15px; float: right;">
      <a href="#" id="btn-search" class="btn btn-popup" style="position: absolute; right: 0;" onclick="jQuery(this).attr('href', '<?php echo $this->Url->build(['action' => 'pesquisar'], true); ?>?' + jQuery('#campo').val() + '=' + encodeURIComponent(jQuery('#search').val()) + '&action=add');"><span class="icon-search"></span></a>
      <input type="text" id="search" placeholder="Pesquisar..." style="width: 100%;" />
    </li>
    <li class=" marginleft15" style="float: right; width: 100px;">
      <select id="campo" style="width: 100%;">
        <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
        <option value="cpf" <?php echo $this->request->query('cpf') ? 'selected' : null; ?>>CPF</option>
      </select>
    </li>
  </ul>
  <span class="clearall"></span>
</div>


<h4 class="widgettitle nomargin shadowed">Marcar Consulta</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create('Consulta', ['class' => 'stdform stdform2']);
  echo $this->Form->input('LoginAgendado', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
  echo $this->Form->input('DataRegistro', ['type' => 'hidden', 'value' => date('Y-m-d')]);
  echo $this->Form->input('CodEmpresa', ['type' => 'hidden', 'value' => $empresa['cd_emp']]);
  echo $this->Form->input('CodTrabalhador', ['type' => 'hidden', 'value' => $paciente['cd_trab']]);
  echo $this->Form->input('EfetuadoPor', ['type' => 'hidden', 'value' => 'Empresa']);
  echo $this->Form->input('CodStatusAgenda', ['type' => 'hidden', 'value' => '1']);
  echo $this->Form->input('CodTipoHorario', ['type' => 'hidden']);
  echo $this->Form->input('DataAgenda', ['type' => 'hidden']);
  echo $this->Form->input('DataAgendamento', ['type' => 'hidden']);

  echo $this->Form->input('CodLogin', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
  echo $this->Form->input('DataEmissao', ['type' => 'hidden', 'value' => date('Y-m-d')]);
  echo $this->Form->input('CodFuncao', ['type' => 'hidden', 'value' => $paciente['CodFuncao']]);
  ?>

  <?php if ($paciente['cd_trab']) { ?>
    <p>
      <label>CPF</label>
      <span class="field">
        <?php echo $paciente['cpf']; ?>
      </span>
    </p>
    <p>
      <label>Trabalhador</label>
      <span class="field">
        <?php echo $paciente['nm_trab']; ?>
      </span>
    </p>
    <p>
      <label>Fun&ccedil;&atilde;o</label>
      <span class="field">
        <?php echo $paciente['NomeFuncao']; ?>
      </span>
    </p>
  <?php } else { ?>
    <p style="text-align: center; padding: 15px;">
      Selecione um trabalhador
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab']) { ?>
    <p>
      <label>Servi&ccedil;os</label>
      <span class="field">
        <input type="checkbox" id="servicoMedico" name="ServicoMedico" class="servico" value="1" <?php echo $this->request->query('medico') == '1' ? 'checked' : ''; ?> /> Agendamento M&eacute;dico
        <input type="checkbox" id="servicoExame" name="ServicoExame" class="servico" value="1" <?php echo $this->request->query('exame') == '1' ? 'checked' : ''; ?> /> Guia de Exame
      </span>
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab'] && $this->request->query('medico')) { ?>
    <p>
      <label>Atendimento</label>
      <span class="field">
        <select class="" name="CodAgendaClasse" required="required">
          <option value="2">M&Eacute;DICO</option>
        </select>
      </span>
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab'] && $this->request->query('medico')) { ?>
    <p>
      <label>Cl&iacute;nica</label>
      <span class="field">
        <select class="input-xlarge" id="clinica" name="CodClinica" required="required">
          <option value="" selected disabled></option>
          <?php foreach ($clinicas as $clinica) { ?>
            <?php if ($clinica['CodClinica'] == '17') { ?>
              <?php $selected = $this->request->query('clinica') == $clinica['CodClinica'] ? 'selected' : ''; ?>
              <option value="<?php echo $clinica['CodClinica']; ?>" <?php echo $selected; ?>><?php echo $clinica['ClinicaNome']; ?></option>
          <?php }
          } ?>
        </select>
      </span>
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab'] && $this->request->query('clinica') == 17) { ?>
    <p>
      <label>Natureza</label>
      <span class="field">
        <select class="" id="natureza" name="CodNatureza" required="required">
          <option value=""></option>
          <?php foreach ($naturezas as $natureza) { ?>
            <?php
            if ($empresa['AtivoPrograma'] != 'S' && $natureza['CodNatureza'] != '2') {
              continue;
            }
            if ($natureza['CodNatureza'] == 2) {
            ?>
              <?php $selected = $this->request->query('natureza') == $natureza['CodNatureza'] ? 'selected' : ''; ?>
              <option value="<?php echo $natureza['CodNatureza']; ?>" <?php echo $selected; ?>><?php echo $natureza['NomeNatureza']; ?></option>
          <?php }
          } ?>
        </select>
        <?php if ($paciente['cd_trab'] && $this->request->query('natureza') == 2) { ?>
        <select class="" id="examiss" name="CodTipoExame" required="required">
          <option value=""></option>
          <?php foreach ($exames as $exame) { ?>
            <?php if ($exame['CodTipoExame'] == '24' || $exame['CodTipoExame'] == '147' || $exame['CodTipoExame'] == '621') { ?>
              <?php $selected = $this->request->query('exammme') == $exame['CodTipoExame'] ? 'selected' : ''; ?>
              <option value="<?php echo $exame['CodTipoExame']; ?>" <?php echo $selected; ?>><?php echo $exame['NomeExame']; ?></option>
          <?php }
          } ?>
        </select>
  <?php } ?>
      </span>
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab'] && $this->request->query('medico')) { ?>
    <p style="display: none;" class="funcao">
      <label>Nova fun&ccedil;&atilde;o</label>
      <span class="field">
        <select class="" id="funcao" name="CodFuncaoNova" required="required">
          <?php foreach ($funcoes as $funcao) { ?>
            <?php
            if ($this->request->query('funcao')) {
              $selected = $this->request->query('funcao') == $funcao['CodFuncao'] ? 'selected' : '';
            } else {
              $selected = $paciente['CodFuncao'] == $funcao['CodFuncao'] ? 'selected' : '';
            }

            ?>
            <option value="<?php echo $funcao['CodFuncao']; ?>" <?php echo $selected; ?>><?php echo $funcao['NomeFuncao']; ?></option>
          <?php } ?>
        </select>
      </span>
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab'] && $this->request->query('medico')) { ?>
    <p style="display: none;" class="altura">
      <label>Trabalha em altura?</label>
      <span class="field">
        <input type="radio" name="Altura" value="S" <?php echo $this->request->query('altura') == 'S' ? 'checked' : ''; ?>> Sim
        <input type="radio" name="Altura" value="N" <?php echo $this->request->query('altura') == 'N' ? 'checked' : ''; ?>> N&atilde;o
      </span>
    </p>
  <?php } ?>

  <?php if ($paciente['cd_trab'] && $this->request->query('exame')) { ?>
    <p>
      <label>Cl&iacute;nica</label>
      <span class="field">
        <select class="input-xlarge" id="clinica" name="CodClinica" required="required">
          <option value=""></option>
          <?php foreach ($clinicas as $clinica) { ?>
            <?php $selected = $this->request->query('clinica') == $clinica['CodClinica'] ? 'selected' : ''; ?>
            <option value="<?php echo $clinica['CodClinica']; ?>" <?php echo $selected; ?>><?php echo $clinica['ClinicaNome']; ?></option>
          <?php } ?>
        </select>
      </span>
    </p>
  <?php } ?>

  <?php if ($exames && $this->request->query('exame')) { ?>
    <p>
      <label>Exames</label>
      <span class="field">
        <?php foreach ($exames as $exame) { ?>
          <?php if ($exame['CodTipoExame'] != '24' && $exame['CodTipoExame'] != '147' && $exame['CodTipoExame'] != '621') { ?>
            <small>
              <input type="checkbox" name="exames[]" value="<?php echo $exame['CodTipoExame']; ?>" />
              <?php echo $exame['NomeExame']; ?>
            </small>
        <?php }
        } ?>
      </span>
    </p>
  <?php }  ?>

  <?php if(($paciente['cd_trab'] && ($this->request->query('exammme')) || ($this->request->query('clinica') == '17' && $this->request->query('exame')))) { ?>
    <p>
      <!-- <label style="height: 50px; margin-right: 5px;">Selecione dia e turno </label> -->
      <label style="height: 50px; margin-right: 5px;" for="MarcData">Agendar</label>
      <span class="field" style=" padding: 15px;">
      <input id='MarcData' type="date" min="<?php echo date('Y-m-d') ?>" name="DataAgenda" onchange="ChamarHora()">
      <input style="display: none" type="time" name="vagaTurno" id="MarcHora" min="8:00" step="600" max="16:40" onchange="vaga(this)"> 
        <!-- <button type="button" class="btn buscar" onclick="javascript: agenda();" style="float: right;">Buscar datas dispon&iacute;veis</button>
        <table class="table table-bordered" style="width: 40%; display: none;">
          <thead>
            <tr>
              <th style="width: 30%;">Data</th>
              <th style="width: 20%;">Dia</th>
              <th style="width: 25%;">Manh&atilde;</th>
              <th style="width: 25%;">Tarde</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table> -->
      </span>
    </p>
  <?php } ?>

  <?php echo "<p class='stdformbutton' style='display: none'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>"; ?>
  <?php echo $this->Form->end(); ?>
</div>

<style>
  .field small {
    width: 250px;
    display: inline-block;
    margin-right: 20px;
  }
</style>
<script type="text/javascript">

  function ChamarHora(){
    jQuery('#MarcHora').show();
  }

  jQuery(document).ready(function() {
    jQuery('#telefone-solicitante').mask('(99) 999999999');

    jQuery('#natureza').change(function() {
      if (jQuery(this).val() == '1' || jQuery(this).val() == '7' || jQuery(this).val() == '9' || jQuery(this).val() == '10') {
        jQuery('p.altura').attr('required', true).show();
      } else if (jQuery(this).val() == '2' || jQuery(this).val() == '4') {
        jQuery('p.altura').attr('required', false).hide();
      }

      if (jQuery(this).val() == '10') {
        jQuery('p.funcao').show();
      } else {
        jQuery('p.funcao').hide();
      }

    }).change();

    jQuery('#search').onEnter(function() {
      jQuery('#btn-search').click();
    });

    jQuery("a.btn-popup").colorbox({
      escKey: false,
      overlayClose: false,
      onLoad: function() {
        jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
      }
    });

    jQuery("input[name='exames[]']").click(function() {
      jQuery("button[type='submit']").parent().hide();
      if (jQuery('#clinica').val() === '17' || jQuery('#servicoMedico').is(':checked')) {
        jQuery('table.table').hide();
        jQuery('tbody').html('');
      } else if (jQuery("input[name='exames[]']:checked").length) {
        jQuery("button[type='submit']").parent().show();
      }
    });

    jQuery('#clinica, #natureza, #funcao, #examiss,.servico').change(function() {
      filtrar();
      lock();
    });

    jQuery("input[name='Altura']").change(function() {
      filtrar();
      lock();
    });
  });

  function vaga(e) {
    jQuery("button[type='submit']").parent().show();
  }

  function filtrar() {
    url = '<?php echo $this->Url->build(['controller' => 'EmpresasAgendasMedicos', 'action' => 'add', $paciente['cd_trab']], true); ?>?1=1';
    if (jQuery('#servicoMedico').is(':checked')) {
      url += '&medico=' + jQuery('#servicoMedico').val();
    }
    if (jQuery('#servicoExame').is(':checked')) {
      url += '&exame=' + jQuery('#servicoExame').val();
    }
    if (jQuery('#natureza').val()) {
      url += '&natureza=' + jQuery('#natureza').val();
    }
    if (jQuery('#examiss').val()) {
      url += '&exammme=' + jQuery('#examiss').val();
    }
    if (jQuery('#funcao').val()) {
      url += '&funcao=' + jQuery('#funcao').val();
    }
    if (jQuery('#clinica').val()) {
      url += '&clinica=' + jQuery('#clinica').val();
    }
    if (jQuery("input[name='Altura']:checked").val()) {
      url += '&altura=' + jQuery("input[name='Altura']:checked").val();
    }
    location.href = url;
  }

  function agenda() {
  var exames = [];
  var url = '<?php echo $this->Url->build([
    'controller' => 'EmpresasAgendasMedicos',
    'action' => 'montar-agenda',
    'medico' => $this->request->query('medico'),
    'exame' => $this->request->query('exame'),
    'clinica' => $this->request->query('clinica')
  ], ['escape' => false]); ?>';

  jQuery("input[name='exames[]']:checked").each(function () {
    exames.push($(this).val());
  });

  if(exames.length) {
    url += '&exames=' + exames.toString();
  }
/*
  jQuery('tbody').html('')
  jQuery("button[type='submit']").parent().hide();
  jQuery('.buscar').attr('disabled', true).text('Buscando datas disponíveis...');
  jQuery.get(url, function (data) {
    jQuery('.buscar').attr('disabled', false).text('Buscar datas disponíveis');
    jQuery('table.table').show();
    jQuery.each(data, function(key, value) {
      let dataString = value.date.split("-");
      let d = new Date(dataString[0], dataString[1]-1, dataString[2]);

      $('tbody').append('<tr>'+
        '<td>'+d.toLocaleString("pt-BR").substring(0, 10)+'</td>'+
        '<td>'+value.day+'</td>'+
        '<td style="background: '+(value.matutino.ocultar ? '#E47261' : '#CCFFCC')+';"><input type="radio" name="vagaTurno" value="1|'+value.date+'" '+(value.matutino.ocultar ? 'disabled' : '')+'  required="required" onclick="vaga(this);" /></td>'+
        '<td style="background: '+(value.vespertino.ocultar ? '#E47261' : '#CCFFCC')+';"><input type="radio" name="vagaTurno" value="25|'+value.date+'" '+(value.vespertino.ocultar ? 'disabled' : '')+'  required="required" onclick="vaga(this);" /></td>'+
        '</tr>');
    });
  }, 'json');*/
}
</script>
