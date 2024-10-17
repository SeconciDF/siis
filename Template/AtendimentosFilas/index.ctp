<?php
$search = $this->request->query('nome');
$search .= $this->request->query('cpf');
$search .= $this->request->query('id');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
      <li class="marginleft15">
          <?php echo $this->Html->link('<span class="icon-time"></span> Hist&oacute;rico de Retiradas', ['action' => 'historico'], ['class' => 'btn', 'escape'=>false]) ?>
      </li>

      <!-- <li class="marginleft15" style="float: right;">
        <select id="paciente" name="paciente" style="width: 130px;">
          <option value="">Todos</option>
          <option value="1" <?php echo $this->request->query('paciente') == '1' ? 'selected' : null?>>Titular</option>
          <option value="2" <?php echo $this->request->query('paciente') == '2' ? 'selected' : null?>>Dependente</option>
        </select>
      </li> -->

      <li class=" marginleft15" style="float: right;">
        <select id="especialidade" name="especialidade" style="width: 200px;" >
          <option value="">Todas as Especialidades</option>
          <?php
              foreach ($especialidades as $key => $value) {
                $selected = $this->request->query('especialidade') == $key ? 'selected' : null;
                echo "<option value='{$key}' {$selected}>{$value}</option>";
              }
          ?>
        </select>
      </li>

      <li class=" marginleft15" style="float: right;">
        <select id="unidade" name="unidade" style="width: 150px;" >
          <option value="">Todas as Unidades</option>
          <?php
              foreach ($unidades as $key => $value) {
                $selected = $this->request->query('unidade') == $key ? 'selected' : null;
                echo "<option value='{$key}' {$selected}>{$value}</option>";
              }
          ?>
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

<h4 class="widgettitle nomargin">FILA DE ESPERA</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <td style="background: #fff; text-align: right;" colspan="10">
                <small style="width: 12px; height: 12px; background: #FFFF99; display: inline-block;"></small> Situação Normal &nbsp;
                <small style="width: 12px; height: 12px; background: #FF7755; display: inline-block;"></small> Empresa Inadimplente &nbsp;

            </td>
        </tr>
        <tr>
            <th style="min-width: 50px;"><?= $this->Paginator->sort('Dependente.id', 'Prontu&aacute;rio', ['escape' => false]) ?></th>
            <th style="min-width: 160px;"><?= $this->Paginator->sort('paciente', 'Benefici&aacute;rio / Dependente', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Especialidade.descricao', 'Especialidade', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('Empresa.nome', 'Empresa', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('AtendimentosFilas.nome_solicitante', 'Solicitante', ['escape' => false]) ?></th>
            <th style="width: 100px;"><?= $this->Paginator->sort('AtendimentosFilas.telefone_solicitante', 'Telefone', ['escape' => false]) ?></th>
            <th><?= $this->Paginator->sort('AtendimentosFilas.observacao', 'Observa&ccedil;&atilde;o', ['escape' => false]) ?></th>
            <th style="width: 50px; text-align: center;"><?= $this->Paginator->sort('ultimo_contato', '&Uacute;ltimo Contato', ['escape' => false]) ?></th>
            <th style="width: 160px;"></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($atendimentos as $atendimento): ?>
        <?php
            $style = ['background'=>''];
            if($atendimento['Empresa']['situacao_seconci'] == 'I') {
                $style['background'] = '#FF7755';
            } else {
                $style['background'] = '#FFFF99';
            }
        ?>
        <tr style="background: <?php echo $style['background']; ?>;">
            <td style="text-align: center; vertical-align:middle;">
              <?php
                $prontuario = '';
                if($atendimento['Dependente']['id']) {
                  $prontuario = array_search($atendimento['Dependente']['id'], explode(',',$atendimento['dependentes']));
                  $prontuario = '.' . ++ $prontuario;
                }
                echo $atendimento['Beneficiario']['id'] . $prontuario;
              ?>
            </td>
            <td>
              <?php
                if($atendimento['Dependente']['id']) {
                  echo $this->Html->link("<span class='iconfa-pencil'></span> (D) {$atendimento['paciente']}", ['controller' => 'beneficiarios', 'action' => 'view-dependente', $atendimento['Beneficiario']['id'], $atendimento['Dependente']['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]);
                } else {
                  echo $this->Html->link("<span class='iconfa-pencil'></span> {$atendimento['paciente']}", ['controller' => 'beneficiarios', 'action' => 'view', $atendimento['Beneficiario']['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]);
                }

                echo '<br/>Data de Inclus&atilde;o: '.date('d/m/Y', strtotime($atendimento['data_hora_registro']));
                echo "<br/>Unidade: {$atendimento['Unidade']['nome']}";
              ?>
            </td>
            <td style="text-align: center; vertical-align:middle;"><?= h($atendimento['Especialidade']['descricao']); ?></td>
            <td style="vertical-align:middle;"><?= h($atendimento['Empresa']['nome']); ?></td>
            <td style="text-align: center; vertical-align:middle;"><?= h($atendimento['nome_solicitante']); ?></td>
            <td style="text-align: center; vertical-align:middle;"><?= h($atendimento['telefone_solicitante']); ?></td>
            <td style="text-align: center; vertical-align:middle;"><?= h($atendimento['observacao']); ?></td>
            <td style="text-align: center; vertical-align:middle;"><?= $atendimento['ultimo_contato'] ? date('d/m/Y H:i', strtotime($atendimento['ultimo_contato'])) : null; ?></td>
            <td style="text-align: center; vertical-align:middle;">
              <?php echo $this->Html->link('<span class="iconfa-edit"></span>', ['controller' => 'consultas', 'action' => 'add', $atendimento['Beneficiario']['id'], $atendimento['Dependente']['id'], 'date' => date('Y-m-d'), 'unidade'=>$atendimento['Unidade']['id'], 'especialidade'=>$atendimento['Especialidade']['id'], 'fila'=>'N', 'filas_id'=>$atendimento['id']], ['class' => 'btn', 'style' => 'font-size: large;', 'title' => 'Marcar Consulta', 'escape'=>false]); ?>
              <?php echo $this->Html->link('<span class="iconfa-comments-alt"></span>', ['action' => 'observacao', $atendimento['id']], ['class' => 'btn btn-popup', 'style' => 'font-size: large;', 'title' => 'Observa&ccedil;&otilde;es', 'escape'=>false]); ?>
              <?php echo $this->Html->link('<span class="iconfa-trash"></span>', ['action' => 'remover', $atendimento['id']], ['class' => 'btn btn-popup', 'style' => 'font-size: large; color: #FF0000;', 'title' => 'Retirar da fila', 'escape'=>false]); ?>
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
    var url = '<?php echo $this->Url->build(['controller' => $this->request->controller], true); ?>/index/';
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
        if (jQuery('#paciente').val()) {
            url += 'paciente=' + jQuery('#paciente').val() + '&';
        }
        if (jQuery('#especialidade').val()) {
            url += 'especialidade=' + jQuery('#especialidade').val() + '&';
        }
        if (jQuery('#unidade').val()) {
            url += 'unidade=' + jQuery('#unidade').val() + '&';
        }
        if (jQuery('#search').val()) {
          url += jQuery('#campo').val()+'='+jQuery('#search').val();
        }
        lock();
        location.href = url;
    }
</script>
