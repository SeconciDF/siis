<?php
function formatFone($fone) {
    $fone = preg_replace('/[^0-9]/', '', $fone);
    return $fone;
}
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>

        <li class="marginleft15" style="float: right;">
            <?php echo $this->Html->link('<span class="iconfa-user-md" style="font-size: medium;"></span> Consultas', ['action' => 'consultas', $beneficiario['id']], ['class' => 'btn', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Benefici&aacute;rio - <?php echo $beneficiario['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class="active"> <?php echo $this->Html->link(__('Dados do Benefici&aacute;rio'), ['action' => 'edit', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Dependentes'), ['action' => 'dependentes', $beneficiario['id']], ['escape' => false]); ?> </li>
            <li class=""> <?php echo $this->Html->link(__('Empresas'), ['action' => 'empresas', $beneficiario['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($beneficiario, ['class' => 'stdform stdform2']);
    echo $this->Form->input('situacao', ['type' => 'hidden']);
?>
<p>
  <label>Prontu&aacute;rio</label>
  <span class="field">
    <b><?php echo $beneficiario['id']; ?></b>
  </span>
</p>
<p>
    <label>CPF</label>
    <span class="field">
        <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium', 'onblur' => 'campo_cpf(this)']); ?>

        <small style="float: right; text-align: right;">
        <?php
            if($beneficiario['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
            else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red;' title='Tornar ativo'> INATIVO </button>";
        ?>
      </small>
    </span>
</p>
<p>
    <label>Nome do benefici&aacute;rio</label>
    <span class="field">
        <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>M&atilde;e do benefici&aacute;rio</label>
    <span class="field">
        <?php echo $this->Form->input('mae', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Documentos pessoais</label>
    <span class="field">
        <small>Identidade</small><small>Expedidor</small><br/>
        <?php echo $this->Form->input('identidade', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('orgao_expedidor', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>
</p>
<p>
    <label>Documentos profissionais</label>
    <span class="field">
        <small>PIS</small><small>CTPS</small><br/>
        <?php echo $this->Form->input('pis', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('ctps_serie', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?>
    </span>
</p>
<p>
    <label>Informa&ccedil;&otilde;es pessoais</label>
    <span class="field">
        <small>G&ecirc;nero</small><small>Data de nascimento</small><br/>
        <?php echo $this->Form->input('sexo', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'style' => 'min-width: 100px;', 'options' => ['F'=>'Feminino', 'M'=>'Masculino']]); ?> &nbsp;&nbsp;&nbsp;
        <?php $beneficiario['data_nascimento'] ? $beneficiario['data_nascimento'] = $beneficiario['data_nascimento']->format('d/m/Y') : null; ?>
        <?php echo $this->Form->input('data_nascimento', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
</p>
<p>
    <label>Fun&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('funcoes', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge chzn-select', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione os itens', 'empty' => true, 'options' => $funcoes, 'default' => $selecionados]); ?>
    </span>
</p>
<p>
    <label>Empresa</label>
    <span class="field">
        <?php echo $this->Form->input('empresas', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xxlarge chzn-select', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione os itens', 'empty' => true, 'options' => $empresas, 'default' => $selecionadas]); ?>
    </span>
</p>

<p class='stdformbutton'  style="padding: 0;">
    <h4 style="padding: 15px;">CONTATOS</h4>
</p>

<p>
    <label>Contatos</label>
    <span class="field">
        <small>Celular</small><small>Celular / Telefone</small><small>Celular / Recado</small><br/>
        <?php $beneficiario->celular ? $beneficiario->celular = formatFone($beneficiario->celular) : null; ?>
        <?php echo $this->Form->input('celular', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?>  &nbsp;&nbsp;&nbsp;
        <?php $beneficiario->telefone ? $beneficiario->telefone = formatFone($beneficiario->telefone) : null; ?>
        <?php echo $this->Form->input('telefone', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?> &nbsp;&nbsp;&nbsp;
        <?php $beneficiario->recado ? $beneficiario->recado = formatFone($beneficiario->recado) : null; ?>
        <?php echo $this->Form->input('recado', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?>
    </span>
</p>
<p>
    <label>Email</label>
    <span class="field">
        <?php echo $this->Form->input('email', ['label' => false, 'type' => 'email', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>

<p class='stdformbutton'  style="padding: 0;">
    <h4 style="padding: 15px;">ENDERE&Ccedil;O</h4>
</p>

<p>
    <label>CEP / Logradouro</label>
    <span class="field">
        <small>CEP</small><small>Logradouro</small><br/>
        <?php echo $this->Form->input('cep', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('logradouro', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>N&uacute;mero / Complemento</label>
    <span class="field">
        <small>N&uacute;mero</small><small>Complemento</small><br/>
        <?php echo $this->Form->input('numero', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('complemento', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Bairro</label>
    <span class="field">
        <?php echo $this->Form->input('bairro', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Cidade / UF</label>
    <span class="field">
        <?php echo $this->Form->input('cidade', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xlarge']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('estado', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'options' => $estados]); ?>
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
        jQuery('#cep').mask('99.999-999');
        jQuery('#cpf').mask('999.999.999-99');
        jQuery('#data-nascimento').mask('99/99/9999');
        jQuery('#cpf').blur();

        jQuery(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: false});
    });
</script>
