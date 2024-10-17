<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Profissional - <?php echo $profissional['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class="active"> <?php echo $this->Html->link(__('Dados do Profissional'), ['action' => 'edit', $profissional['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Agenda'), ['action' => 'agenda', $profissional['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($profissional, ['class' => 'stdform stdform2']);
    echo $this->Form->input('situacao', ['type' => 'hidden']);
?>

<p>
    <label>Documentos</label>
    <span class="field">
        <small>Registro Profissional</small><small>CPF</small><br/>
        <?php echo $this->Form->input('registro_profissional', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium', 'onblur' => 'campo_cpf(this)']); ?>

        <small style="float: right; text-align: right;">
        <?php
            if($profissional['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
            else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red;' title='Tornar ativo'> INATIVO </button>";
        ?>
        </small>
    </span>
</p>
<p>
    <label>Nome</label>
    <span class="field">
        <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Email</label>
    <span class="field">
        <?php echo $this->Form->input('email', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Apelido</label>
    <span class="field">
        <?php echo $this->Form->input('apelido', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xlarge']); ?> &nbsp;&nbsp;&nbsp;
    </span>
</p>
<p>
    <label>Especialidades</label>
    <span class="field">
        <?php echo $this->Form->input('especialidades', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge chzn-select', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione as especialidades', 'empty' => true, 'options' => $especialidades, 'default' => $selecionados]); ?>
    </span>
</p>
<p>
    <label>Atendimento</label>
    <span class="field">
        <small>Idade M&aacute;xima</small><small>Idade M&iacute;nima</small><small>N&uacute;mero de Pacientes por Hor&aacute;rio</small><br/>
        <?php echo $this->Form->input('idade_minima', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('idade_maxima', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('limite_paciente_consulta', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?>
    </span>
</p>

<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: true});
        jQuery('#cpf').blur();
    });
</script>
