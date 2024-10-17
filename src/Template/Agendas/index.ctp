<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
$search .= $this->request->query('id');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class=" marginleft15">
          <select id="unidade" name="unidade" style="width: 150px;">
            <option value="">Todas as Unidades</option>
            <?php
            foreach ($unidades as $key => $value) {
              $selected = $this->request->query('unidade') == $key ? 'selected' : null;
              echo "<option value='{$key}' {$selected}>{$value}</option>";
            }
            ?>
          </select>
        </li>

        <li class=" marginleft15" style="float: right;">
          <select id="profissional" name="profissional" style="min-width: 250px;" >
            <option value="">Todos os Profissionais</option>
            <?php
                foreach ($profissionais as $key => $value) {
                  $selected = $this->request->query('profissional') == $key ? 'selected' : null;
                  echo "<option value='{$key}' {$selected}>{$value}</option>";
                }
            ?>
          </select>
        </li>

        <li class=" marginleft15" style="float: right;">
          <select id="especialidade" name="especialidade" style="width: 150px;" >
            <option value="">Todas as especialidades</option>
            <?php
            foreach ($especialidades as $key => $value) {
              $selected = $this->request->query('especialidade') == $key ? 'selected' : null;
              echo "<option value='{$key}' {$selected}>{$value}</option>";
            }
            ?>
          </select>
        </li>

        <li class="marginleft15" style="float: right;">
          <select id="turno" name="turno" style="width: 130px;" >
            <option value="">Todos os Turnos</option>
            <option value="1" <?php echo $this->request->query('turno') == '1' ? 'selected' : null?>>Manh&atilde;</option>
            <option value="2" <?php echo $this->request->query('turno') == '2' ? 'selected' : null?>>Tarde</option>
          </select>
        </li>

        <br/><br/>

        <li class=" marginleft15" style="float: right; width: 200px;">
          <a href="#" id="btn-search" onclick="javascript: return false;" class="btn" style="position: absolute; right: 0;" ><span class="icon-search"></span></a>
          <input type="text" id="search" class="filekeyword" placeholder="pesquisar..." style="width: 100%;" value="<?php echo $search; ?>" />
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

<h4 class="widgettitle nomargin">Agenda</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <td style="background: #fff; text-align: right;" colspan="10">
                <small style="width: 12px; height: 12px; background: #F48164; display: inline-block;"></small> Faltou &nbsp;
                <small style="width: 12px; height: 12px; background: #99CCCC; display: inline-block;"></small> Chegou &nbsp;
                <small style="width: 12px; height: 12px; background: #FFCC66; display: inline-block;"></small> Não chegou &nbsp;
                <small style="width: 12px; height: 12px; background: #99FF66; display: inline-block;"></small> Atendido &nbsp;
            </td>
        </tr>
        <tr>
            <th style="min-width: 50px;"><?= $this->Paginator->sort('Dependente.id', 'Prontu&aacute;rio', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('paciente', 'Benefici&aacute;rio / Dependente', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Especialidade.descricao', 'Especialidade', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Motivo.descricao', 'Motivo da Consulta', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Consultas.data_hora_agendado', 'H. Agend.', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Consultas.data_hora_pre_atendimento', 'H. Cheg.', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Consultas.data_hora_atendimento', 'H. Atend.', ['escape' => false]) ?></th>
            <th>C. Marc.</th>
            <th class="actions" style="width: 100px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($consultas as $consulta): ?>

          <?php
          $style = ['background'=>''];
          if(!$consulta['data_hora_pre_atendimento'] && !$consulta['data_hora_atendimento'] && strtotime($consulta['data_hora_agendado']) > strtotime(date('Y-m-d'))) {
            $style['background'] = '#FFCC66';
          } else if($consulta['data_hora_fecha_atendimento']) {
            $style['background'] = '#99FF66';
          } else if($consulta['data_hora_pre_atendimento'] || $consulta['data_hora_atendimento']) {
            $style['background'] = '#99CCCC';
          } else if($consulta['st_consulta'] == 'FA') { //Falta
            $style['background'] = '#F48164';
          }
          ?>

        <tr style="background: <?php echo $style['background']; ?>;">
          <td style="text-align: center; vertical-align:middle;">
            <?php
              $prontuario = '';
              if($consulta['Dependente']['id']) {
                $prontuario = array_search($consulta['Dependente']['id'], explode(',',$consulta['dependentes']));
                $prontuario = '.' . ++ $prontuario;
              }
              echo $consulta['Beneficiario']['id'] . $prontuario;
            ?>
          </td>
            <td>
              <?php
                if($consulta['Dependente']['id']) {
                  echo $this->Html->link("<span class='iconfa-pencil'></span> (D) {$consulta['paciente']}", ['controller' => 'beneficiarios', 'action' => 'view-dependente', $consulta['Beneficiario']['id'], $consulta['Dependente']['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]);
                } else {
                  echo $this->Html->link("<span class='iconfa-pencil'></span> {$consulta['paciente']}", ['controller' => 'beneficiarios', 'action' => 'view', $consulta['Beneficiario']['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]);
                }
              ?>
            </td>
            <td><?= h($consulta['Especialidade']['descricao']); ?></td>
            <td><?= h($consulta['Motivo']['descricao']); ?></td>
            <td style="width: 60px; text-align: center;"><?= $consulta['data_hora_agendado'] ? date('H:i', strtotime($consulta['data_hora_agendado'])) : null; ?></td>
            <td style="width: 60px; text-align: center;"><?= $consulta['data_hora_pre_atendimento'] ? date('H:i', strtotime($consulta['data_hora_pre_atendimento'])) : null; ?></td>
            <td style="width: 60px; text-align: center;"><?= $consulta['data_hora_atendimento'] ? date('H:i', strtotime($consulta['data_hora_atendimento'])) : null; ?></td>
            <td style="width: 50px; text-align: center;">
              <?php
                if(isset($intervalos[$consulta['Especialidade']['id']])) {
                  $seconds = strtotime("1970-01-01 {$consulta['consultas']} UTC");
                  echo round(($seconds/60) / $intervalos[$consulta['Especialidade']['id']]);
                }
              ?>
            </td>
            <td style="width: 100px; text-align: center;">
                <?php echo $this->Html->link('<span class="iconfa-folder-open"></span>', ['action' => 'abrir-consulta', $consulta['id']], ['class' => 'btn btn-popup', 'style' => 'font-size: large;', 'title' => 'Abrir Consulta', 'escape'=>false]); ?>
                <?php echo $this->Html->link('<span class="iconfa-comments-alt"></span>', ['action' => 'observacao', $consulta['id'], 'date' => $this->request->query('date')], ['class' => 'btn btn-popup', 'style' => 'font-size: large;', 'title' => 'Observa&ccedil;&otilde;es', 'escape'=>false]); ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </tbody>
    <tfoot style="background: #fff;">
        <tr>
            <td colspan="10">
                <?php echo $this->element('paginacao'); ?>
            </td>
        </tr>
    </tfoot>
</table>

<style> tr td { color: #000; } </style>
<input type="hidden" id="date" value="<?php echo $this->request->query('date'); ?>" />
<script type="text/javascript">
    var url = '<?php echo $this->Url->build(['controller' => $this->request->controller, 'action' => 'index'], true); ?>/';
    jQuery(document).ready(function() {
        jQuery('#btn-search').click(function() { filtrar(); });
        jQuery('#search').onEnter(function() { filtrar(); });

        jQuery("a.btn-popup").colorbox({
          escKey: false,
          overlayClose: false,
          onLoad: function() {
            jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
          }
        });
    });

    function filtrar() {
        url += '?';
        if (jQuery('#date').val()) {
            url += 'date=' + jQuery('#date').val() + '&';
        }
        if (jQuery('#unidade').val()) {
          url += 'unidade=' + jQuery('#unidade').val() + '&';
        }
        if (jQuery('#turno').val()) {
            url += 'turno=' + jQuery('#turno').val() + '&';
        }
        if (jQuery('#especialidade').val()) {
          url += 'especialidade=' + jQuery('#especialidade').val() + '&';
        }
        if (jQuery('#profissional').val()) {
            url += 'profissional=' + jQuery('#profissional').val() + '&';
        }
        if (jQuery('#search').val()) {
          url += jQuery('#campo').val()+'='+jQuery('#search').val();
        }
        lock();
        location.href = url;
    }
</script>
