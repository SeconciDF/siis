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
    echo $this->Form->create($dependente, ['class' => 'stdform stdform2']);
    echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'values' => $beneficiario['id']]);
    echo $this->Form->input('situacao', ['type' => 'hidden']);
?>

<div id="tabs"  class="nopadding" style="border: 0; min-width:500px;">
    <ul>
        <li><a href="#tabs-1">Dependente</a></li>
        <li><a href="#tabs-2">Contatos</a></li>
    </ul>

    <div id="tabs-1" class="nopadding first" style="min-height: 350px;">
      <p>
          <label>Dependente</label>
          <span class="field">
            <small style="margin-right: 130px;">Nome do dependente</small><br/>
            <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => '', 'style' => 'width: 330px;']); ?> <br/>
            <small>CPF</small><br/>
              <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => false, 'class' => '', 'style' => 'width: 100px;', 'onblur' => 'campo_cpf(this)']); ?>
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
          <label>Tipo de Depend&ecirc;ncia</label>
          <span class="field">
            <small style="margin-right: 130px;">Nome do benefici&aacute;rio</small><br/>
            <input type="text" style="width: 330px" value="<?php echo $beneficiario['nome']; ?>" /> <br/>
            <?php echo $this->Form->input('tipo_dependencias_id', ['label' => false, 'type' => 'select', 'class' => 'input-medium', 'options' => $dependencias]); ?> &nbsp;&nbsp;&nbsp;
          </span>
      </p>
    </div>

    <div id="tabs-2" class="nopadding first" style="min-height: 350px;">
      <p>
          <label>Contatos</label>
          <span class="field">
              <small>Celular</small><small>Celular / Telefone</small><small>Celular / Recado</small><br/>
              <?php $dependente->celular ? $dependente->celular = formatFone($dependente->celular) : null; ?>
              <?php echo $this->Form->input('celular', ['label' => false, 'type' => 'text', 'required' => false, 'class' => ' fone', 'style' => 'width: 90px;']); ?>  &nbsp;&nbsp;
              <?php $dependente->telefone ? $dependente->telefone = formatFone($dependente->telefone) : null; ?>
              <?php echo $this->Form->input('telefone', ['label' => false, 'type' => 'text', 'required' => false, 'class' => ' fone', 'style' => 'width: 90px;']); ?> &nbsp;&nbsp;
              <?php $dependente->recado ? $dependente->recado = formatFone($dependente->recado) : null; ?>
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
    <?php echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>"; ?>
</div>
<?php echo $this->Form->end(); ?>
</div>

<style>.field small { min-width: 90px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery('#cpf').mask('999.999.999-99');
        jQuery('#data-nascimento').mask('99/99/9999');
        jQuery('#cpf').blur();
        jQuery('#tabs').tabs();
    });
</script>
