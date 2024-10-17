<?php
    if($this->request->query('start')) {
        $profissional['data_inicio'] = date('d/m/Y', strtotime($this->request->query('start')));
    }
    if($this->request->query('end')) {
        $profissional['data_fim'] = date('d/m/Y', strtotime($this->request->query('end')));
    }
?>


<h4 class="widgettitle nomargin shadowed">Agenda Extra - <?php echo $profissional['nome']; ?></h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($profissional, ['class' => 'stdform stdform2']);
    echo $this->Form->input('profissionais_id', ['type' => 'hidden', 'value' => $profissional['id']]);
    echo $this->Form->input('tipo', ['type' => 'hidden', 'value' => 'E']);
?>

<p>
    <label>Nome do profissional</label>
    <span class="field">
        <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
</p>

<p>
    <label>Per&iacute;odo</label>
    <span class="field">
        <small>Data e hora in&iacute;cio</small><small>Data e hora fim</small><small>Per&iacute;odo</small><br/>
        <?php echo $this->Form->input('data_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small', 'placeholder' => 'dd/mm/aaaa']); ?>
        <?php echo $this->Form->input('time_inicio', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-mini', 'placeholder' => 'hh:mm']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('data_fim', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-small', 'placeholder' => 'dd/mm/aaaa']); ?>
        <?php echo $this->Form->input('time_fim', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-mini', 'placeholder' => 'hh:mm']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('turno', ['label' => false, 'type' => 'select', 'required' => true, 'empty' => 'Per&iacute;odo', 'class' => 'input-small', 'options' => ['1'=>'1&ordm; Per&iacute;odo','2'=>'2&ordm; Per&iacute;odo'], 'escape' => false]); ?>
    </span>
</p>

<p>
    <label>Dias da Semana</label>
    <span class="field">
        <input type="checkbox" id='all'> Todas &nbsp;
        <input type="checkbox" name='dias[SEG]' value="SEG"> Seg &nbsp;
        <input type="checkbox" name='dias[TER]' value="TER"> Ter &nbsp;
        <input type="checkbox" name='dias[QUA]' value="QUA"> Qua &nbsp;
        <input type="checkbox" name='dias[QUI]' value="QUI"> Qui &nbsp;
        <input type="checkbox" name='dias[SEX]' value="SEX"> Sex &nbsp;
    </span>
</p>

<p>
    <label>Especialidade / Unidade</label>
    <span class="field">
        <?php echo $this->Form->input('especialidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-medium', 'empty' => 'Especialidade', 'options' => $especialidades]); ?>
        <?php echo $this->Form->input('unidades_id', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-small', 'empty' => 'Unidade', 'options' => $unidades]); ?>
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
            <th>Dias da semana</th>
            <th></th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($diponiveis as $diponivel): ?>
        <tr>
            <td><?php echo date('d/m/Y', strtotime($diponivel['data_inicio'])); ?></td>
            <td><?php echo $diponivel['Especialidade']['descricao']; ?></td>
            <td><?php echo $diponivel['dias']; ?></td>
            <td>
                <?php echo $this->Form->postLink('<span class="iconfa-trash" style="font-size: large; color: red;"></span>', ['action' => 'delete', $diponivel['id'], 'ProfissionaisAgendas'], ['confirm' => 'Deseja excluir?', 'title' => 'Deletar', 'escape'=>false]); ?>
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
        jQuery('#data-inicio, #data-fim').mask('99/99/9999');
        jQuery('#time-inicio, #time-fim').mask('99:99');
        jQuery("div.pagination ul li a").addClass('btn-popup');
        jQuery("a.btn-popup").colorbox({
            onLoad: function() {
                jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
            }
        });

        jQuery('#all').click(function() {
            if(jQuery(this).is(':checked')) {
                jQuery("input[type='checkbox']").prop('checked', true);
            } else {
                jQuery("input[type='checkbox']").prop('checked', false);
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
