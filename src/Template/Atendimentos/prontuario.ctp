<?php
  $bloqueado = $this->request->session()->read('Auth.User.bloqueado');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
          <?php echo $this->Html->link('<span class="iconfa-list" style="font-size: medium;"></span> Ver prontu&aacute;rio ' . ($this->request->query('completo') ? 'resumido' : 'completo'), ['action' => 'prontuario', $consulta['id'], 'completo' => $this->request->query('completo') ? null : '1'], ['class' => 'btn', 'escape'=>false]); ?>
        </li>

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
      <li class=""> <?php echo $this->Html->link(__('Anamnese'), ['action' => 'anamnese', $consulta['id']], ['escape' => false]); ?> </li>
      <li class="active"> <?php echo $this->Html->link(__('Prontu&aacute;rio'), ['action' => 'prontuario', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Odontograma de Referência'), ['action' => 'odontograma', $consulta['id'], 'referencia' => '1'], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Odontograma'), ['action' => 'odontograma', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Conclusão'), ['action' => 'conclusao', $consulta['id']], ['escape' => false]); ?> </li>
    </ul>
  </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <table class="table table-bordered">
    <tr>
      <th colspan="6"><h3>ANAMNESE</h3></th>
    </tr>
    <tr>
      <th style="width: 100px;">Data Consulta</th>
      <th colspan="5">QP / HDA</th>
    </tr>
    <?php $last_anamnese = ''; ?>
    <?php foreach ($consultas as $key => $value) { ?>
      <?php
        if($last_anamnese != $value['anamnese_qp_hda']) {
          $last_anamnese = $value['anamnese_qp_hda'];
        } else {
          continue;
        }
      ?>

      <tr>
        <td><?php echo $value['data_hora_agendado']->format('d/m/Y'); ?></td>
        <td colspan="5"><?php echo $value['anamnese_qp_hda']; ?></td>
      </tr>
    <?php } ?>

    <tr>
      <th colspan="6"><h3>CONCLUS&Atilde;O</h3></th>
    </tr>
    <tr>
      <th style="width: 100px;">Data Consulta</th>
      <th colspan="4">Tratamento</th>
      <th>Usu&aacute;rio</th>
    </tr>
    <?php foreach ($consultas as $key => $value) { ?>
      <tr>
        <td><?php echo $value['data_hora_agendado']->format('d/m/Y'); ?></td>
        <td colspan="4"><?php echo $value['tratamento']; ?></td>
        <td><?php echo $value['conclusao']; ?></td>
      </tr>
    <?php } ?>

    <tr>
      <th style="width: 100px;">Data Consulta</th>
      <th colspan="4">Resultado</th>
      <th>Usu&aacute;rio</th>
    </tr>
    <?php foreach ($consultas as $key => $value) { ?>
      <tr>
        <td><?php echo $value['data_hora_agendado']->format('d/m/Y'); ?></td>
        <td colspan="4"><?php echo $value['tratamento_alta_retorno'] ? 'Alta' : 'Retorno'; ?></td>
        <td><?php echo $value['conclusao']; ?></td>
      </tr>
    <?php } ?>

    <tr>
      <th style="width: 100px;">Data Consulta</th>
      <th style="width: 150px;">Afastamento</th>
      <th style="width: 150px;">Data In&iacute;cio</th>
      <th style="width: 150px;">Data Fim</th>
      <th>Motivo</th>
      <th>Usu&aacute;rio</th>
    </tr>
    <?php foreach ($consultas as $key => $value) { ?>
      <tr>
        <td><?php echo $value['data_hora_agendado']->format('d/m/Y'); ?></td>
        <td><?php echo $value['afastamento_data_inicio'] ||  $value['afastamento_data_fim'] ? 'SIM' : 'N&Atilde;O'; ?></td>
        <td><?php echo $value['afastamento_data_inicio'] ? date('d/m/Y', strtotime($value['afastamento_data_inicio'])) : null; ?></td>
        <td><?php echo $value['afastamento_data_fim'] ? date('d/m/Y', strtotime($value['afastamento_data_fim'])) : null; ?></td>
        <td><?php echo $value['afastamento_motivo']; ?></td>
        <td><?php echo $value['conclusao']; ?></td>
      </tr>
    <?php } ?>

    <tr>
      <th colspan="6"><h3>ODONTOGRAMA DE REFER&Ecirc;NCIA</h3></th>
    </tr>
    <tr>
      <th style="width: 100px;">Data Consulta</th>
      <th colspan="5">Odontograma</th>
    </tr>
    <?php if(isset($odontogramas['referencia'])) { ?>
      <?php foreach ($odontogramas['referencia'] as $key => $odontograma) { ?>
        <tr>
          <td><?php echo date('d/m/Y', strtotime($key)); ?></td>
          <td colspan="5">
              <table class="table table-bordered">
                <tr>
                  <td>Dente</td>
                  <td>Procedimento</td>
                  <td>Faces</td>
                  <td>Qt. Prevista</td>
                </tr>
                <?php foreach ($odontograma as $dente => $procedimentos) { ?>
                  <?php foreach ($procedimentos as $procedimento) { ?>
                    <tr>
                      <td><?php echo $dente; ?></td>
                      <td><?php echo $procedimento['procedimento']; ?></td>
                      <td><?php echo $procedimento['face']; ?></td>
                      <td><?php echo $procedimento['previsto']; ?></td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </table>
          </td>
        </tr>
      <?php } ?>
    <?php } ?>

    <tr>
      <th colspan="6"><h3>ODONTOGRAMA</h3></th>
    </tr>
    <tr>
      <th style="width: 100px;">Data Consulta</th>
      <th colspan="5">Odontograma</th>
    </tr>
    <?php if(isset($odontogramas['odontograma'])) { ?>
      <?php foreach ($odontogramas['odontograma'] as $key => $odontograma) { ?>
        <tr>
          <td><?php echo date('d/m/Y', strtotime($key)); ?></td>
          <td colspan="5">
              <table class="table table-bordered">
                <tr>
                  <td>Dente</td>
                  <td>Procedimento</td>
                  <td>Faces</td>
                  <td>Qt. Prevista</td>
                  <td>Feito Hoje</td>
                  <td>Realizado</td>
                </tr>
                <?php foreach ($odontograma as $dente => $procedimentos) { ?>
                  <?php foreach ($procedimentos as $procedimento) { ?>
                    <tr>
                      <td><?php echo $dente; ?></td>
                      <td><?php echo $procedimento['procedimento']; ?></td>
                      <td><?php echo $procedimento['face']; ?></td>
                      <td><?php echo $procedimento['previsto']; ?></td>
                      <td><?php echo $procedimento['feito_hoje']; ?></td>
                      <td><?php echo $procedimento['realizado']; ?></td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </table>
          </td>
        </tr>
      <?php } ?>
    <?php } ?>
  </table>
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
