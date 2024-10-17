<?php
$perfil = array_keys($this->request->session()->read('Auth.User.perfil'));

function formatFone($fone) {
    $fone = preg_replace('/[^0-9]/', '', $fone);
    return $fone;
}
?>
<div class="mediamgr_head">
  <ul class=" mediamgr_menu">
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
    </li>
    <li class="marginleft15">
      <?php echo $this->Html->link('<span class="icon-edit"></span> Nova', ['action' => 'add'], ['class' => 'btn selectall', 'escape'=>false]) ?>
    </li>
  </ul>
  <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Empresa - <?php echo $empresa['nome']; ?></h4>

<?php if(in_array('9', $perfil)) { ?>
  <div class="navbar">
      <div class="navbar-inner">
          <ul class="nav">
              <li class="active"> <?php echo $this->Html->link(__('Empresa'), ['controller' => 'Empresas', 'action' => 'edit', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Setor'), ['controller' => 'EmpresasSetores', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Jornada'), ['controller' => 'EmpresasJornadas', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Lota&ccedil;&atilde;o'), ['controller' => 'EmpresasLotacoes', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Frentes de trabalho'), ['controller' => 'EmpresasObras', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
          </ul>
      </div>
  </div>
<?php } ?>

<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($empresa, ['class' => 'stdform stdform2']);
  echo $this->Form->input('update_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
  echo $this->Form->input('data_hora_atualizacao', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
  echo $this->Form->input('situacao_seconci', ['type' => 'hidden']);
  echo $this->Form->input('situacao', ['type' => 'hidden']);
  ?>
  <p>
    <label>Situa&ccedil;&otilde;es</label>
    <span class="field">
      <?php
      if($empresa['situacao_seconci'] == 'C' && $empresa['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao-seconci\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inadimplente'>ADIMPLENTE</button>";
      else echo "<button type='submit' onclick='javascript: $(\"#situacao-seconci\").val(\"C\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px;  background: red;' title='Tornar adimplente'> INADIMPLENTE </button>";
      ?>

      <small style="float: right; text-align: right;">
        <?php
        if($empresa['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao,#situacao-seconci\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
        else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px;  background: red;' title='Tornar ativo'> INATIVO </button>";
        ?>
      </small>
    </span>
  </p>

  <?php if(in_array('8', $perfil)) { ?>
    <p>
      <label>Bloqueios</label>
      <span class="field">
        <?php echo $this->Form->input('bloqueio_seconci_manual', ['label' => false, 'type' => 'checkbox', 'required' => false]); ?> Bloqueia Assistencial Manualmente <br/>
        <?php echo $this->Form->input('bloqueio_medicina_ocupacional_manual', ['label' => false, 'type' => 'checkbox', 'required' => false]); ?> Bloqueia Ocupacional Manualmente <br/>
      </span>
    </p>
    <p>
      <label>Data de in&iacute;cio da cobran&ccedil;a</label>
      <span class="field">
        <?php $empresa->data_inicio_cobranca ? $empresa->data_inicio_cobranca = date('d/m/Y', strtotime($empresa->data_inicio_cobranca)) : null; ?>
        <?php echo $this->Form->input('data_inicio_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?> &nbsp;
        <?php echo $this->Form->input('efetuar_cobranca', ['label' => false, 'type' => 'checkbox', 'required' => false]); ?>  Será gerada cobrança para esta empresa?
      </span>
    </p>
  <?php } ?>

  <p>
    <label>Data Associa&ccedil;&atilde;o / CNPJ</label>
    <span class="field">
      <small>Data Associa&ccedil;&atilde;o</small><small>Tipo de Registro</small><small>N&uacute;mero</small><br/>
      <?php $empresa->data_inicio_atividade ? $empresa->data_inicio_atividade = date('d/m/Y', strtotime($empresa->data_inicio_atividade)) : null; ?>
      <?php echo $this->Form->input('data_inicio_atividade', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small mask-date', 'placeholder' => 'dd/mm/aaaa']); ?> &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('tipo_identificacao', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'style' => 'min-width: 100px;', 'options' => ['PJ'=>'CNPJ', 'PF'=>'CPF', 'CE'=>'CEI']]); ?> &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('identificacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?>
    </span>
  </p>

  <?php if(in_array('9', $perfil)) { ?>
    <p>
      <label>Inscri&ccedil;&atilde;o estadual / municipal</label>
      <span class="field">
        <small style="width: 160px;">Inscri&ccedil;&atilde;o estadual</small><small>Inscri&ccedil;&atilde;o municipal</small><br/>
        <?php echo $this->Form->input('inscricao_estadual', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('inscricao_municipal', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium']); ?>
      </span>
    </p>
  <?php } ?>

  <p>
    <label>Nome da Empresa</label>
    <span class="field">
      <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Nome Fantasia</label>
    <span class="field">
      <?php echo $this->Form->input('nome_fantasia', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>

  <?php if(in_array('9', $perfil)) { ?>
    <p>
      <label>Ramo de atividade</label>
      <span class="field">
        <?php echo $this->Form->input('ramo_atividade', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
    </p>
    <p>
      <label>CNAE / Grau de risco (NR 4)</label>
      <span class="field">
        <small>CNAE</small><small>Grau de risco (NR 4)</small><br/>
        <?php echo $this->Form->input('cnae', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('grau_risco', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?>
      </span>
    </p>
    <p>
      <label>Porte / Total de trabalhadores</label>
      <span class="field">
        <small>Porte</small><small>Total de trabalhadores</small><br/>
        <?php echo $this->Form->input('porte', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('total_trabalhadores', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?>
      </span>
    </p>
    <p>
      <label>Quantidade de trabalhadores</label>
      <span class="field">
        <small>Homens</small><small>Mulheres</small><small>Menores 18 anos</small><br/>
        <?php echo $this->Form->input('quantidade_homens', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('quantidade_mulheres', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('quantidade_menores', ['label' => false, 'type' => 'number', 'required' => false, 'class' => 'input-small']); ?>
      </span>
    </p>
    <p>
      <label>SESMT / CIPA</label>
      <span class="field">
        <small>SESMT</small><small>CIPA</small><small>Designado da CIPA</small><br/>
        <?php echo $this->Form->input('sesmt', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('cipa', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('designado_cipa', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>
      </span>
    </p>
  <?php } ?>

  <p class='stdformbutton' style="padding: 0;">
    <h4 style="padding: 15px;">ENDERE&Ccedil;O DE LOCALIZA&Ccedil;&Atilde;O</h4>
  </p>

  <p>
    <label>CEP / Logradouro</label>
    <span class="field">
      <small>CEP</small><small>Logradouro</small><br/>
      <?php echo $this->Form->input('cep_localizacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('logradouro_localizacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>N&uacute;mero / Complemento</label>
    <span class="field">
      <small>N&uacute;mero</small><small>Complemento</small><br/>
      <?php echo $this->Form->input('numero_localizacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('complemento_localizacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Bairro</label>
    <span class="field">
      <?php echo $this->Form->input('bairro_localizacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Cidade / UF</label>
    <span class="field">
      <?php echo $this->Form->input('cidade_localizacao', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xlarge']); ?> &nbsp;&nbsp;&nbsp;
      <?php echo $this->Form->input('estado_localizacao', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'options' => $estados]); ?>
    </span>
  </p>

  <?php if(in_array('8', $perfil)) { ?>
    <p class='stdformbutton'  style="padding: 0;">
      <h4 style="padding: 15px;">ENDERE&Ccedil;O DE COBRAN&Ccedil;A</h4>
    </p>

    <p>
      <label>Mesmo endere&ccedil;o de cobran&ccedil;a</label>
      <span class="field">
        <?php echo $this->Form->input('mesmo_endereco_cobranca', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'style' => 'min-width: 100px;', 'options' => ['S'=>'SIM', 'N'=>'N&Atilde;O'], 'escape' => false]); ?> &nbsp;&nbsp;&nbsp;
      </span>
    </p>

    <p>
      <label>CEP / Logradouro</label>
      <span class="field">
        <small>CEP</small><small>Logradouro</small><br/>
        <?php echo $this->Form->input('cep_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('logradouro_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
    </p>
    <p>
      <label>N&uacute;mero / Complemento</label>
      <span class="field">
        <small>N&uacute;mero</small><small>Complemento</small><br/>
        <?php echo $this->Form->input('numero_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('complemento_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
    </p>
    <p>
      <label>Bairro</label>
      <span class="field">
        <?php echo $this->Form->input('bairro_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
      </span>
    </p>
    <p>
      <label>Cidade / UF</label>
      <span class="field">
        <?php echo $this->Form->input('cidade_cobranca', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xlarge']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('estado_cobranca', ['label' => false, 'type' => 'select', 'empty' => true, 'class' => 'input-small', 'options' => $estados]); ?>
      </span>
    </p>
  <?php } ?>

  <p class='stdformbutton' style="padding: 0;">
    <h4 style="padding: 15px;">CONTATO DO  RESPONS&Aacute;VEL</h4>
  </p>

  <p>
    <label>Nome do Respons&aacute;vel</label>
    <span class="field">
      <?php echo $this->Form->input('programa_no_pessoa_contato', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Email do Respons&aacute;vel</label>
    <span class="field">
      <?php echo $this->Form->input('programa_email_responsavel', ['label' => false, 'type' => 'email', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Telefone do Respons&aacute;vel</label>
    <span class="field">
      <?php echo $this->Form->input('programa_tel_comercial', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-medium fone']); ?>
    </span>
  </p>

  <p class='stdformbutton' style="padding: 0;">
    <h4 style="padding: 15px;">CONTATO PRINCIPAL</h4>
  </p>

  <p>
    <label>Nome do contato</label>
    <span class="field">
      <?php echo $this->Form->input('nome_contato', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Email do contato</label>
    <span class="field">
      <?php echo $this->Form->input('email_contato', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
  </p>
  <p>
    <label>Contatos</label>
    <span class="field" id="content-contatos">
      <small style="">Tipo do contato</small>
      <small style="">N&uacute;mero</small>
      <br/>
      <?php foreach ($contatos as $key => $value) { ?>
        <span style="width: 100%;">
          <select name="tipos[]" class="input-small" style="min-width: 110px;">
            <option value=""></option>
            <option value="CE" <?php echo $value['tipo_contato'] == 'CE' ? 'selected' : ''; ?>>Celular</option>
            <option value="CO" <?php echo $value['tipo_contato'] == 'CO' ? 'selected' : ''; ?>>Comercial</option>
            <option value="FA" <?php echo $value['tipo_contato'] == 'FA' ? 'selected' : ''; ?>>Fax</option>
            <option value="GE" <?php echo $value['tipo_contato'] == 'GE' ? 'selected' : ''; ?>>Contabilidade</option>
          </select>  &nbsp;&nbsp;
          <input type="text" name="contatos[]" class="input-medium fone" value="<?php echo formatFone($value['contato']); ?>" /> &nbsp;&nbsp;
          <a onclick="javascript: removeContato(this); return false;" style="cursor: pointer;" ><span class="icon-trash"></span></a><br/><br/>
        </span>
      <?php } ?>

      <span style="width: 100%;">
        <select name="tipos[]" class="input-small" style="min-width: 110px;">
          <option value=""></option>
          <option value="CE">Celular</option>
          <option value="CO">Comercial</option>
          <option value="FA">Fax</option>
          <option value="GE">Contabilidade</option>
        </select>  &nbsp;&nbsp;
        <input type="text" name="contatos[]" class="input-medium fone" /> &nbsp;&nbsp;
        <a onclick="javascript: addContato(this); return false;" style="cursor: pointer;" ><span class="icon-plus"></span></a><br/><br/>
      </span>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
  echo $this->Form->end();
  ?>
</div>

<style> .field small { min-width: 100px; display: inline-block; margin-right: 20px;} </style>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  jQuery('#data-inicio-atividade').mask('99/99/9999');
  jQuery('#data-inicio-cobranca').mask('99/99/9999');
  jQuery('#cep-localizacao').mask('99.999-999');
  jQuery('#cep-cobranca').mask('99.999-999');

  $('#tipo-identificacao').change(function() {
    $('#identificacao').parent().find('em').remove();
    if($(this).val() === 'PJ') {
      $('#identificacao').attr('onblur', 'campo_cnpj(this)').mask('99.999.999/9999-99');
    }
    if($(this).val() === 'PF') {
      $('#identificacao').attr('onblur', 'campo_cpf(this)').mask('999.999.999-99');
    }
    if($(this).val() === 'CE') {
      $('#identificacao').attr('onblur', false).mask(false);
      //$('#identificacao').mask('99.999.999/9999-99');
    }
  }).change();
  $('#identificacao').blur();

  jQuery(".chzn-select").chosen('destroy').attr('multiple', true).chosen({disable_search: true});
});

function addContato(self) {
  var novo = jQuery(jQuery(self).parent()).clone().appendTo("#content-contatos");
  jQuery(self).attr('onclick', 'javascript: removeContato(this); return false;');
  jQuery(self).html('<span class="icon-trash"></span></a>');
  jQuery(novo).find('input').val('');
}

function removeContato(self) {
  jQuery(self).parent().remove();
}
</script>
