<?php
if (strpos($this->request->referer(), '/beneficiarios/') == false) {
   $this->request->session()->write('Auth.User.referer', $this->request->referer());
}

function formatFone($fone) {
    $fone = preg_replace('/[^0-9]/', '', $fone);
    return $fone;
}
?>
<h4 class="widgettitle nomargin shadowed">Edi&ccedil;&atilde;o R&aacute;pida</h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($beneficiario, ['class' => 'stdform stdform2']);
    echo $this->Form->input('situacao', ['type' => 'hidden']);
?>


<div id="tabs"  class="nopadding" style="border: 0; min-width:500px;">
    <ul>
        <li><a href="#tabs-1">Benefici&aacute;rio</a></li>
        <li><a href="#tabs-2">Documentos</a></li>
        <li><a href="#tabs-3">Contatos</a></li>
        <li><a href="#tabs-4">Endere&ccedil;o</a></li>
        <li><a href="#tabs-5" id="tab4">Empresa</a></li>
    </ul>

    <div id="tabs-1" class="nopadding first" style="min-height: 280px;">
      <p>
          <label>Nome do benefici&aacute;rio</label>
          <span class="field">
              <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => '', 'style' => 'width: 330px;']); ?>
          </span>
      </p>
      <p>
          <label>M&atilde;e do benefici&aacute;rio</label>
          <span class="field">
              <?php echo $this->Form->input('mae', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 330px;']); ?>
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
    </div>

    <div id="tabs-2" class="nopadding" style="min-height: 280px;">
      <p>
          <label>CPF</label>
          <span class="field">
              <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium', 'onblur' => 'campo_cpf(this)']); ?>
          </span>
      </p>
      <p>
          <label>Documentos pessoais</label>
          <span class="field">
              <small>Identidade</small><small>Expedidor</small><br/>
              <?php echo $this->Form->input('identidade', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;
              <?php echo $this->Form->input('orgao_expedidor', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> <br/>
              <small>PIS</small><small>CTPS</small><br/>
              <?php echo $this->Form->input('pis', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;
              <?php echo $this->Form->input('ctps_serie', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?>
      </p>
  </div>

    <div id="tabs-3" class="nopadding" style="min-height: 280px;">
      <p>
          <label>Contatos</label>
          <span class="field">
              <small>Celular</small><small>Celular / Telefone</small><small>Celular / Recado</small><br/>
              <?php $beneficiario->celular ? $beneficiario->celular = formatFone($beneficiario->celular) : null; ?>
              <?php echo $this->Form->input('celular', ['label' => false, 'type' => 'text', 'required' => false, 'class' => ' fone', 'style' => 'width: 90px;']); ?>  &nbsp;&nbsp;
              <?php $beneficiario->telefone ? $beneficiario->telefone = formatFone($beneficiario->telefone) : null; ?>
              <?php echo $this->Form->input('telefone', ['label' => false, 'type' => 'text', 'required' => false, 'class' => ' fone', 'style' => 'width: 90px;']); ?> &nbsp;&nbsp;
              <?php $beneficiario->recado ? $beneficiario->recado = formatFone($beneficiario->recado) : null; ?>
              <?php echo $this->Form->input('recado', ['label' => false, 'type' => 'text', 'required' => false, 'class' => ' fone', 'style' => 'width: 90px;']); ?> <br/>
              <!-- <small>Email</small><br/> -->
              <?php //echo $this->Form->input('email', ['label' => false, 'type' => 'email', 'required' => false, 'class' => '', 'style' => 'width: 330px;']); ?>
          </span>
      </p>
      <p>
          <label>Email</label>
          <span class="field">
              <?php echo $this->Form->input('email', ['label' => false, 'type' => 'email', 'required' => false, 'class' => '', 'style' => 'width: 330px;']); ?>
          </span>
      </p>
</div>

    <div id="tabs-4"  class="nopadding" style="min-height: 280px;">
      <p>
          <label>Endere&ccedil;o</label>
          <span class="field">
              <small>CEP</small><small>Logradouro</small><br/>
              <?php echo $this->Form->input('cep', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 90px;']); ?>  &nbsp;&nbsp;
              <?php echo $this->Form->input('logradouro', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 200px;']); ?> <br/>
              <small>N&uacute;mero</small><small>Complemento</small><br/>
              <?php echo $this->Form->input('numero', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 90px;']); ?> &nbsp;&nbsp;
              <?php echo $this->Form->input('complemento', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 200px;']); ?><br/>
              <small>Bairro</small><br/>
              <?php echo $this->Form->input('bairro', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 330px;']); ?><br/>
              <small>Cidade</small><br/>
              <?php echo $this->Form->input('cidade', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 200px;']); ?> &nbsp;&nbsp;
              <?php echo $this->Form->input('estado', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => '', 'style' => 'width: 100px;', 'options' => $estados]); ?>
          </span>
      </p>
    </div>

    <div id="tabs-5" class="nopadding" style="min-height: 280px;">
      <p>
          <label>Empresa</label>
          <span class="field">
              <?php echo $this->Form->input('empresas', ['label' => false, 'type' => 'select', 'required' => false, 'class' => '', 'style' => 'width: 330px;', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione os itens', 'empty' => true, 'options' => $empresas, 'default' => $selecionadas]); ?>
          </span>
      </p>
      <p>
          <label>Fun&ccedil;&atilde;o</label>
          <span class="field">
              <?php echo $this->Form->input('funcoes', ['label' => false, 'type' => 'select', 'required' => false, 'class' => '', 'style' => 'width: 330px;', 'multiple' => 'multiple', 'data-placeholder'=>'Selecione os itens', 'empty' => true, 'options' => $funcoes, 'default' => $selecionados]); ?>
          </span>
      </p>
    </div>
    <?php echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>"; ?>
</div>
<?php echo $this->Form->end(); ?>
</div>

<style>.field small { min-width: 92px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery('#cep').mask('99.999-999');
        jQuery('#cpf').mask('999.999.999-99');
        jQuery('#data-nascimento').mask('99/99/9999');
        jQuery('#cpf').blur();
        jQuery('#tabs').tabs();

        jQuery('#tab4').click(function() {
          jQuery("#empresas, #funcoes").chosen('destroy').attr('multiple', true).chosen({disable_search: true});
        });

        jQuery("form").submit(function (e) {
          jQuery("button.btn").attr('disabled', true);
        });
    });
</script>
