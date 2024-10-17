<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class=" marginleft15" style="width: 200px; margin-right: 15px; float: right;">
      <a href="#" id="btn-search" class="btn btn-popup" style="position: absolute; right: 0;" onclick="jQuery(this).attr('href', '<?php echo $this->Url->build(["controller" => "consultas", "action" => "pesquisar"], true); ?>?' + jQuery('#campo').val() + '=' + encodeURIComponent(jQuery('#search').val()) + '&action=add');"><span class="icon-search"></span></a>
      <input type="text" id="search" placeholder="Pesquisar..." style="width: 100%;" />
    </li>
    <li class=" marginleft15" style="float: right; width: 100px;">
      <select id="campo" style="width: 100%;">
        <option value="nome" <?php echo $this->request->query('nome') ? 'selected' : null; ?>>Nome</option>
        <option value="cpf" <?php echo $this->request->query('cpf') ? 'selected' : null; ?>>CPF</option>
        <option value="id" <?php echo $this->request->query('id') ? 'selected' : null; ?>>Prontu&aacute;rio</option>
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
  echo $this->Form->create($consulta, ['class' => 'stdform stdform2']);
  echo $this->Form->input('data_hora_marca_consulta', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
  echo $this->Form->input('consultas_id', ['type' => 'hidden', 'value' => $this->request->query('consulta')]);
  echo $this->Form->input('filas_id', ['type' => 'hidden', 'value' => $this->request->query('filas_id')]);
  echo $this->Form->input('tipo', ['type' => 'hidden', 'value' => $this->request->query('tipo')]);
  echo $this->Form->input('empresas_id', ['type' => 'hidden', 'value' => $beneficiario['Empresa']['id']]);
  echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'value' => $beneficiario['id']]);
  echo $this->Form->input('dependentes_id', ['type' => 'hidden', 'value' => $dependente['id']]);
  ?>

  <?php if($beneficiario['id']) { ?>
  <p>
    <label>Prontu&aacute;rio</label>
    <span class="field">
      <b>
        <?php
          $prontuario = '';
          if($dependente['id']) {
            $prontuario = array_search($dependente['id'], explode(',',$beneficiario['dependentes']));
            $prontuario = '.' . ++ $prontuario;
          }
          echo $beneficiario['id'] . $prontuario;
        ?>
      </b>
    </span>
  </p>
  <?php } ?>

  <?php if($dependente['id']) { ?>
    <p>
      <label>Dependente</label>
      <span class="field">
        <?php echo $this->Html->link("<span class='iconfa-pencil'></span> {$dependente['nome']}", ['controller' => 'beneficiarios', 'action' => 'view-dependente', $beneficiario['id'], $dependente['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]); ?>
      </span>
    </p>
  <?php } ?>

  <?php if($beneficiario['id']) { ?>
    <p>
      <label>Benefici&aacute;rio</label>
      <span class="field">
        <?php echo $this->Html->link("<span class='iconfa-pencil'></span> {$beneficiario['nome']}", ['controller' => 'beneficiarios', 'action' => 'view', $beneficiario['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]); ?>
      </span>
    </p>
  <?php } else { ?>
    <p style="text-align: center; padding: 15px;">
      Selecione um benefici&aacute;rio / dependente para come&ccedil;ar.
    </p>
  <?php } ?>

  <?php if($beneficiario['Empresa']['id']) { ?>
    <p>
      <label>Empresa</label>
      <span class="field">
        <?php echo $beneficiario['Empresa']['nome']; ?>
      </span>
    </p>
  <?php } else if($beneficiario['id']) { ?>
    <p>
      <label>Empresa</label>
      <span class="field" style="color: red;">
        Trabalhador sem empresa cadastrada, <?php echo $this->Html->link('clique aqui para cadastrar uma empresa.', ['controller' => 'beneficiarios', 'action' => 'empresas', $beneficiario['id']], ['escape'=>false]) ?>
      </span>
    </p>
  <?php } ?>

  <?php if($dependente['id'] || $beneficiario['id']) { ?>
    <p>
      <label>Telefone</label>
      <span class="field">
        <?php echo $fone; ?>
      </span>
    </p>
  <?php } ?>

<?php if($beneficiario['id'] && $beneficiario['situacao'] == 'A' && $beneficiario['Empresa']['situacao'] == 'A' || $this->request->query('tipo')) { ?>
    <p>
      <label>Consulta</label>
      <span class="field">
        <small>Unidade</small><small style="width: 145px;">Especialidades</small>
        <small style="<?php echo $this->request->query('fila') == 'N' ? '' : 'display: none;'; ?>">Profissional</small><br/>
        <?php echo $this->Form->input('unidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-small', 'empty' => 'Todas', 'options' => $unidades, 'onchange' => 'filtrar();', 'default' => $this->request->query('unidade')]); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('especialidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-medium', 'empty' => 'Todas', 'options' => $especialidades, 'onchange' => 'filtrar();', 'default' => $this->request->query('especialidade')]); ?> &nbsp;&nbsp;&nbsp;
        <?php
            if ($this->request->query('fila') == 'N') {
                echo $this->Form->input('profissionais_id', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-large', 'empty' => 'Todos', 'options' => $profissionais, 'onchange' => 'filtrar();', 'default' => $this->request->query('profissional')]);
            }
        ?>
      </span>
    </p>
    <p style="<?php echo $this->request->query('consulta') ? 'display: none;' : ''; ?>">
      <label>Colocar na fila de espera?</label>
      <span class="field">
        <input type="radio" class="fila" name="fila" value="S" <?php echo $this->request->query('fila') == 'S' ? 'checked' : ''; ?> /> SIM &nbsp;&nbsp;&nbsp;
        <input type="radio" class="fila" name="fila" value="N" <?php echo $this->request->query('fila') == 'N' ? 'checked' : ''; ?> /> N&Atilde;O
      </span>
    </p>
  <?php } ?>

  <?php if($beneficiario['id'] && $this->request->query('fila') == 'S' && $beneficiario['situacao'] == 'A' && $beneficiario['Empresa']['situacao'] == 'A') { ?>
    <p>
      <label>Turno</label>
      <span class="field">
        <?php echo $this->Form->input('turno', ['label' => false, 'type' => 'select', 'required' => false, 'empty' => 'Indiferente', 'class' => 'input-medium', 'options' => ['1'=>'1&ordm; Per&iacute;odo','2'=>'2&ordm; Per&iacute;odo'], 'escape' => false]); ?>
      </span>
    </p>
    <p>
      <label>Nome do solicitante</label>
      <span class="field">
        <?php echo $this->Form->input('nome_solicitante', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge', 'escape' => false]); ?>
      </span>
    </p>
    <p>
      <label>Telefone do solicitante</label>
      <span class="field">
        <?php echo $this->Form->input('telefone_solicitante', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium', 'escape' => false]); ?>
      </span>
    </p>
    <p>
      <label>Observa&ccedil;&atilde;o</label>
      <span class="field">
        <?php echo $this->Form->input('observacao', ['label' => false, 'type' => 'textarea', 'required' => false, 'class' => 'input-xxlarge', 'escape' => false]); ?>
      </span>
    </p>
  <?php echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>"; ?>
  <?php } ?>

  <?php if($beneficiario['id'] && $this->request->query('fila') == 'N' && $beneficiario['situacao'] == 'A' && $beneficiario['Empresa']['situacao'] == 'A' || $this->request->query('tipo')) { ?>
    <p>
      <table style="width: 99%;">
        <tr>
          <td style="width: 200px;" rowspan="3">
            <div id="datepicker-consulta" style=""></div> &nbsp;
            <small style="width: 12px; height: 12px; background: #9BCF9B; display: inline-block;"></small> Hor&aacute;rio Vago &nbsp;
            <small style="width: 12px; height: 12px; background: #BF7A5F; display: inline-block;"></small> Hor&aacute;rio Marcado <br/><br/>
          </td>
        </tr>
        <tr>
          <th class="SEG" style="height: 20px;">SEG</th>
          <th class="TER" style="height: 20px;">TER</th>
          <th class="QUA" style="height: 20px;">QUA</th>
          <th class="QUI" style="height: 20px;">QUI</th>
          <th class="SEX" style="height: 20px;">SEX</th>
        </tr>
        <tr>
          <td class="SEG disponiveis max-height"><?php echo $this->Html->image('loader1.gif', ['style' => 'margin: 0 auto;']); ?></td>
          <td class="TER disponiveis max-height"><?php echo $this->Html->image('loader1.gif', ['style' => 'margin: 0 auto;']); ?></td>
          <td class="QUA disponiveis max-height"><?php echo $this->Html->image('loader1.gif', ['style' => 'margin: 0 auto;']); ?></td>
          <td class="QUI disponiveis max-height"><?php echo $this->Html->image('loader1.gif', ['style' => 'margin: 0 auto;']); ?></td>
          <td class="SEX disponiveis max-height"><?php echo $this->Html->image('loader1.gif', ['style' => 'margin: 0 auto;']); ?></td>
        </tr>
      </table>
    </p>

    <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade in" id="myModal">
      <div class="modal-header">
        <!-- <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button> -->
        <h3 id="myModalLabel">INFORMA&Ccedil;&Otilde;ES ADICIONAIS</h3>
      </div>
      <div class="modal-body nopadding" style="padding: 0; margin: -15px 0 0 0;">
        <p>
          <label>Data do agendamento</label>
          <span class="field data-agendamento">&nbsp;</span>
        </p>
        <p>
          <label>Motivo da Consultas</label>
          <span class="field">
            <?php echo $this->Form->input('motivos_consultas_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-medium', 'options' => ['4'=>'Assistencial'], 'escape' => false]); ?>
            <small id="consulta-dupla" style="display:none;"><input type="checkbox" name="consulta_dupla" value="2" /> Marcar 2 consultas simult&acirc;neas</small>
          </span>
        </p>
        <p>
          <label>Nome do solicitante</label>
          <span class="field">
            <?php echo $this->Form->input('nome_solicitante', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-large', 'autofocus' => true, 'autocomplete'=>'off', 'escape' => false, 'value' => ($dependente['nome'] ? $dependente['nome'] : $beneficiario['nome'])]); ?>
          </span>
        </p>
        <p>
          <label>Telefone do solicitante</label>
          <span class="field">
            <?php echo $this->Form->input('telefone_solicitante', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium',  'autocomplete'=>'off', 'escape' => false, 'value' => preg_replace('/[^0-9]/', '', $fone)]); ?>
          </span>
        </p>

        <?php if(!$this->request->query('tipo')) { ?>
        <p>
          <label>Por que este beneficiário não entrou na fila?</label>
          <span class="field">
            <?php echo $this->Form->input('descricao_furou_fila', ['label' => false, 'type' => 'textarea', 'rows' => '3', 'required' => true, 'class' => 'input-xlarge', 'escape' => false]); ?>
          </span>
        </p>
        <?php } ?>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-large" onclick='location.reload();'>Cancelar</button>
        <button class="btn btn-success btn-large">Salvar</button>
      </div>
    </div>

  <?php //echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'button', 'class' => 'btn btn-success btn-large'])}</p>"; ?>
  <?php } ?>
  <?php echo $this->Form->end(); ?>
</div>

<style>
td.disponiveis { width: 20%;  padding: 2px; text-transform: uppercase; }
td.disponiveis div { font-size: 10px; min-width: 150px; border: 1px solid #000; cursor: pointer; padding: 0; padding-left: 5px; margin: 1px;}
td.disponiveis div.vago { background: #9BCF9B; color: #000; }
td.disponiveis div.marcado { background: #BF7A5F; color: #fff;}
td.disponiveis div:hover { background: #ddd; color: #000; }
td.disponiveis div input { display: none; }
td.max-height { height: 300px; }
.field small { min-width: 90px; display: inline-block; margin-right: 20px; }
.ui-datepicker{ font-size:10px; }
</style>

<input type="hidden" id="url" value="<?php echo $this->Url->build(['controller' => 'Consultas', 'action' => 'montar-agenda', 'unidade' => $this->request->query('unidade'), 'especialidade' => $this->request->query('especialidade'), 'profissional' => $this->request->query('profissional')], ['escape' => false]); ?>">
<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery('#telefone-solicitante').mask('(99) 999999999');

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

  jQuery("#datepicker-consulta").datepicker({
    dateFormat: 'yy-mm-dd',
    onSelect: function(date) {
      jQuery('#date').val(date);
      filtrar();
    }
  });

  if(jQuery('#tipo').val() === 'retorno' || jQuery('#tipo').val() === 'remarcar') {
    //jQuery('#unidades-id option:not(:selected)').remove();
    jQuery('#especialidades-id option:not(:selected)').remove();
  }


  if(jQuery('#date').val()) {
    var dateParts = jQuery('#date').val().match(/(\d+)/g);
    var d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
    jQuery("#datepicker-consulta").datepicker('setDate', d);
    var dias = dates(d);

    for (var i = 0; i < dias.length; i++) {
     jQuery.get(jQuery('#url').val()+'&date='+dias[i], function (data) {
       $.each(data, function(dia, horarios) {
         jQuery('td.'+dia).find('img').remove();
         var count = {};
         $.each(horarios, function(b, hora) {
           $.each(hora, function(c, registro) {
             if(!count[dia+c]) {
               count[dia+c]=0;
             }
             var referencia = registro.date.match(/(\d+)/g);
             jQuery('th.'+dia).html(dia+' '+referencia[2]+'/'+referencia[1]);
             if(registro.consulta) {
               jQuery('td.'+dia).append('<div id="'+(dia+c+'-'+count[dia+c]++)+'" class="marcado" title="'+registro.nome+' - '+registro.unidade+' - '+registro.especialidade+'"><b>'+registro.hora.substr(0,5)+'</b> - '+getResumeName(registro.nome)+'</div>');
             } else {
               let row = '<div id="'+(dia+c+'-'+count[dia+c]++)+'" class="vago" title="'+registro.nome+' - '+registro.unidade+' - '+registro.especialidade+'" onclick="javascript: selectBox(this);"><input type="checkbox" name="vagas[]" value="'+registro.profissionais_id+';'+registro.date+' '+registro.hora+'" />';
               row += '<b>'+registro.hora.substr(0,5)+'</b> - '+getResumeName(registro.nome)+'</div>';
               jQuery('td.'+dia).append(row);
             }
           });
         });
       });
     }, 'json');
    }

    setTimeout(function() {
      jQuery('td.disponiveis').find('img').remove();
    }, 5000);
  }

  jQuery('input.fila').change(function() {
    //console.log(jQuery('input.fila:checked').val());
    filtrar();
  });

  setTimeout(function() {
      if (!jQuery('#date').val() && jQuery('input.fila:checked').val() === 'N') {
          jQuery('#date').val(new Date().toJSON().substring(0,10));
          filtrar();
      }
  }, 100);
});

function selectBox(e) {
  var target = jQuery(e).attr('id');
  target = target.split('-');
  target[1] = parseInt(target[1])+1;

  if(jQuery(e).find('input').is(':checked')) {
    jQuery(e).find('input').attr('checked', false);
    jQuery(e).removeClass('marcado').addClass('vago');
  } else {
    var d = jQuery(e).find('input').val().split(';');
    jQuery('.data-agendamento').html((new Date(d[1])).toLocaleString());

    if(jQuery('#'+target.join('-')).hasClass('vago')) {
      jQuery('#consulta-dupla').show();
    }

    jQuery(e).find('input').attr('checked', true);
    jQuery(e).removeClass('vago').addClass('marcado');
    jQuery('#myModal').modal({
      backdrop: 'static',
      keyboard: false
    });
  }
}

function getResumeName(str) {
  let resume = [];
  let words = str.split(' ');
  for (var i=0;i<words.length;i++) {
    if(resume.length < 2 && words[i].length > 2) {
      resume.push(words[i]);
    }
  }
  return resume.join(' ');
}

function filtrar() {
  url = '<?php echo $this->Url->build(['controller' => 'Consultas', 'action' => 'add', $beneficiario['id'], $dependente['id']], true); ?>?';
  if (jQuery('#date').val()) {
    url += 'date=' + jQuery('#date').val() + '&';
  }
  if (jQuery('#unidades-id').val()) {
    url += 'unidade=' + jQuery('#unidades-id').val() + '&';
  }
  if (jQuery('#especialidades-id').val()) {
    url += 'especialidade=' + jQuery('#especialidades-id').val() + '&';
  }
  if (jQuery('#profissionais-id').val()) {
    url += 'profissional=' + jQuery('#profissionais-id').val() + '&';
  }
  if (jQuery('#consultas-id').val()) {
    url += 'consulta=' + jQuery('#consultas-id').val() + '&';
  }
  if(jQuery('#filas-id').val()) {
    url += 'filas_id=' + jQuery('#filas-id').val() + '&';
  }
  if(jQuery('#tipo').val()) {
    url += 'tipo=' + jQuery('#tipo').val() + '&';
  }
  if(jQuery('input.fila:checked').val()) {
    url += 'fila=' + jQuery('input.fila:checked').val() + '&';
  }
  lock();
  location.href = url;
}

function dates(current) {
    var week= new Array();
    current.setDate((current.getDate() - current.getDay() +1));
    for (var i = 0; i < 5; i++) {
        week.push((new Date(current)).toISOString().substring(0, 10));
        current.setDate(current.getDate() +1);
    }
    return week;
}

</script>
