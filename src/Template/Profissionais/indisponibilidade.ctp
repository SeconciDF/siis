<?php
    if($this->request->query('start')) {
        $profissional['data_hora_inicio'] = date('d/m/Y', strtotime($this->request->query('start')));
    }
    if($this->request->query('end')) {
        $profissional['data_hora_fim'] = date('d/m/Y', strtotime($this->request->query('end')));
    }
?>


<h4 class="widgettitle nomargin shadowed">Indisponibilidade - <?php echo $profissional['nome']; ?></h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($profissional, ['class' => 'stdform stdform2']);
    echo $this->Form->input('profissionais_id', ['type' => 'hidden', 'value' => $profissional['id']]);
    echo $this->Form->input('colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
?>

<p>
    <label>Nome do profissional</label>
    <span class="field">
        <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
</p>

<p>
    <label>Per&iacute;odo de indisponibilidade</label>
    <span class="field">
        <small>Data e hora in&iacute;cio</small><small>Data e hora fim</small><small>Per&iacute;odo</small><br/>
        <?php echo $this->Form->input('data_hora_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small', 'placeholder' => 'dd/mm/aaaa']); ?>
        <?php echo $this->Form->input('time_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-mini', 'placeholder' => 'hh:mm']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('data_hora_fim', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small', 'placeholder' => 'dd/mm/aaaa']); ?>
        <?php echo $this->Form->input('time_fim', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-mini', 'placeholder' => 'hh:mm']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('turno', ['label' => false, 'type' => 'select', 'required' => true, 'empty' => true, 'class' => 'input-small', 'options' => ['1'=>'1&ordm; Per&iacute;odo','2'=>'2&ordm; Per&iacute;odo'], 'escape' => false]); ?>
    </span>
</p>

<p>
    <label>Especialidade</label>
    <span class="field">
        <?php echo $this->Form->input('especialidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-large', 'empty' => true, 'options' => $especialidades]); ?>
    </span>
</p>

<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
    echo $this->Form->end();
?>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Data</th>
            <th>Especialidade</th>
            <th>Hor&aacute;rio</th>
            <th>Respons&aacute;vel</th>
            <th>Data Registro</th>
            <th></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($indisponibilidades as $indisponibilidade): ?>
        <tr>
            <td><?= date('d/m/Y', strtotime($indisponibilidade['data_hora_inicio'])) ?></td>
            <td><?= h($indisponibilidade['Especialidade']['descricao']) ?></td>
            <td><?= date('H:i', strtotime($indisponibilidade['data_hora_inicio'])) . ' &agrave; ' . date('H:i', strtotime($indisponibilidade['data_hora_fim'])) ?></td>
            <td><?= h($indisponibilidade['Colaborador']['nome']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($indisponibilidade['data_hora_registro'])) ?></td>
            <td>
                <?php echo $this->Form->postLink('<span class="iconfa-trash" style="font-size: large; color: red;"></span>', ['action' => 'delete', $indisponibilidade->id, 'ProfissionaisIndisponibilidades'], ['confirm' => 'Deseja excluir?', 'title' => 'Deletar', 'escape'=>false]); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot style="background: #fff;">
        <tr>
            <td colspan="8">
                <div style="float: left;">
                    <?php echo $this->Paginator->counter('Pagina {{page}} de {{pages}}, mostrando {{current}} de {{count}} ({{start}} ate {{end}})'); ?>
                </div>
                <div class="pagination" style="float: right; margin: 0;">
                    <ul>
                        <?php
                        echo $this->Paginator->prev('< ' . __('anterior'), array('tag' => 'li'), null, array('class' => 'disabled'));
                        echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li'));
                        echo $this->Paginator->next(__('próximo') . ' >', array('tag' => 'li'), null, array('class' => 'disabled'));
                        ?>
                    </ul>
                </div>
            </td>
        </tr>
    </tfoot>
</table>

<style>.field small { min-width: 175px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery('#data-hora-inicio, #data-hora-fim').mask('99/99/9999');
        jQuery('#time-inicio, #time-fim').mask('99:99');
        jQuery("div.pagination ul li a").addClass('btn-popup');
        jQuery("a.btn-popup").colorbox({
            onLoad: function() {
                jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
            }
        });

        jQuery('#turno').change(function() {
            if(jQuery('#time-inicio').val() === '' && jQuery(this).val() === '1') {
              jQuery('#time-inicio').val('08:00');
            }
            if(jQuery('#time-fim').val() === '' && jQuery(this).val() === '1') {
              jQuery('#time-fim').val('12:00');
            }

            if(jQuery('#time-inicio').val() === '' && jQuery(this).val() === '2') {
              jQuery('#time-inicio').val('13:00');
            }
            if(jQuery('#time-fim').val() === '' && jQuery(this).val() === '2') {
              jQuery('#time-fim').val('17:00');
            }
        });
    });
</script>
