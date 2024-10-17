<?php
function formatFone($fone) {
    $fone = preg_replace('/[^0-9]/', '', $fone);
    return $fone;
}
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'dependentes', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'dependente', $beneficiario['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15" style="float: right;">
            <?php echo $this->Html->link('<span class="iconfa-user-md" style="font-size: medium;"></span> Consultas', ['action' => 'consultas', $beneficiario['id']], ['class' => 'btn', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Dependentes de <?php echo $beneficiario['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Dados do Benefici&aacute;rio'), ['action' => 'edit', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Dependentes'), ['action' => 'dependentes', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Empresas'), ['action' => 'empresas', $beneficiario['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($dependente, ['class' => 'stdform stdform2']);
    echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'value' => $beneficiario['id']]);
    echo $this->Form->input('situacao', ['type' => 'hidden']);
?>

<p>
    <label>Tipo de Depend&ecirc;ncia</label>
    <span class="field">
        <small style="width: 150px;">Tipo de Depend&ecirc;ncia</small><small>CPF</small><br/>
        <?php echo $this->Form->input('tipo_dependencias_id', ['label' => false, 'type' => 'select', 'class' => 'input-medium', 'options' => $dependencias]); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium', 'onblur' => 'campo_cpf(this)']); ?>

        <small style="float: right; text-align: right;">
        <?php
            if($dependente['id']) {
                if($dependente['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
                else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red;' title='Tornar ativo'> INATIVO </button>";
            }
        ?>
        </small>
    </span>
</p>
<p>
    <label>Nome do dependente</label>
    <span class="field">
        <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>M&atilde;e do dependente</label>
    <span class="field">
        <?php echo $this->Form->input('mae', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Informa&ccedil;&otilde;es pessoais</label>
    <span class="field">
        <small>G&ecirc;nero</small><small style="width: 160px;">Data de nascimento</small><br/>
        <?php echo $this->Form->input('sexo', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'style' => 'min-width: 100px;', 'options' => ['F'=>'Feminino', 'M'=>'Masculino']]); ?> &nbsp;&nbsp;&nbsp;
        <?php $dependente['data_nascimento'] ? $dependente['data_nascimento'] = $dependente['data_nascimento']->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_nascimento', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?> &nbsp;&nbsp;&nbsp;
    </span>
</p>
<p>
    <label>Contatos</label>
    <span class="field">
        <small>Celular</small><small>Celular / Telefone</small><small>Celular / Recado</small><br/>
        <?php $dependente->celular ? $dependente->celular = formatFone($dependente->celular) : null; ?>
        <?php echo $this->Form->input('celular', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?>  &nbsp;&nbsp;&nbsp;
        <?php $dependente->telefone ? $dependente->telefone = formatFone($dependente->telefone) : null; ?>
        <?php echo $this->Form->input('telefone', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?> &nbsp;&nbsp;&nbsp;
        <?php $dependente->recado ? $dependente->recado = formatFone($dependente->recado) : null; ?>
        <?php echo $this->Form->input('recado', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?>
    </span>
</p>
<p>
    <label>Email</label>
    <span class="field">
        <?php echo $this->Form->input('email', ['label' => false, 'type' => 'email', 'required' => false, 'class' => 'input-xxlarge']); ?>
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
        jQuery('#cpf').mask('999.999.999-99');
        jQuery('#data-nascimento').mask('99/99/9999');
        jQuery('#cpf').blur();
    });
</script>
