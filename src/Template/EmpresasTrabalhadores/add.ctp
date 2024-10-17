<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Novo Benefici&aacute;rio</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($beneficiario, ['class' => 'stdform stdform2']);
  echo $this->Form->input('make_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
  echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
  echo $this->Form->input('situacao', ['type' => 'hidden', 'value' => 'A']);
  echo $this->Form->input('empresas[]', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.empresas_id')]);
  ?>
  <p>
    <label>CPF</label>
    <span class="field">
      <?php echo $this->Form->input('cpf', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium', 'onblur' => 'campo_cpf(this)']); ?>
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
      <?php echo $this->Form->input('orgao_expedidor', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
    </span>
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
      <?php echo $this->Form->input('data_nascimento', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
    </span>
  </p>
  <p>
      <label>Fun&ccedil;&atilde;o</label>
      <span class="field">
          <?php echo $this->Form->input('funcoes[]', ['label' => false, 'type' => 'select', 'required' => false, 'class' => 'input-xlarge', 'empty' => true, 'options' => $funcoes]); ?>
      </span>
  </p>

  <p class='stdformbutton'  style="padding: 0;">
    <h4 style="padding: 15px;">CONTATOS</h4>
  </p>

  <p>
    <label>Contatos</label>
    <span class="field">
      <small>Celular</small><small>Celular / Telefone</small><small>Celular / Recado</small><br/>
      <?php echo $this->Form->input('celular', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?>  &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('telefone', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?> &nbsp;&nbsp;&nbsp;
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
      <?php echo $this->Form->input('estado', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small']); ?>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large'])}</p>";
  echo $this->Form->end();
  ?>
</div>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  jQuery('#cep').mask('99.999-999');
  jQuery('#cpf').mask('999.999.999-99');
  jQuery('#data-nascimento').mask('99/99/9999');

  jQuery(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: false});

  jQuery('#cpf').blur(function() {
    jQuery.get('<?php echo $this->Url->build(['action' => 'consulta-cpf'], true); ?>/'+jQuery(this).val(), function (data) {
      if(data.hasOwnProperty('id')) {
        jConfirm('CPF cadastrado como '+data.nome+'\nDeseja visualizar os dados deste benefici&aacute;rio?', 'Registro Encontrado', function (r) {
          if (r) {
            location.href = '<?php echo $this->Url->build(["action" => "edit"], true); ?>/'+data.id;
          } else {
            jQuery('#cpf').val('');
          }
        });
      }
    }, 'json');
  });
});
</script>
