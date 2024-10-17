<?php
$perfil = array_keys($this->request->session()->read('Auth.User.perfil'));
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $empresa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Frentes de trabalho</h4>

<?php if(in_array('9', $perfil)) { ?>
  <div class="navbar">
      <div class="navbar-inner">
          <ul class="nav">
              <li class=""> <?php echo $this->Html->link(__('Empresa'), ['controller' => 'Empresas', 'action' => 'edit', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Setor'), ['controller' => 'EmpresasSetores', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Jornada'), ['controller' => 'EmpresasJornadas', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class=""> <?php echo $this->Html->link(__('Lota&ccedil;&atilde;o'), ['controller' => 'EmpresasLotacoes', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
              <li class="active"> <?php echo $this->Html->link(__('Frentes de trabalho'), ['controller' => 'EmpresasObras', 'action' => 'index', $empresa['id']], ['escape' => false]); ?> </li>
          </ul>
      </div>
  </div>
<?php } ?>

<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($obra, ['class' => 'stdform stdform2']);
    echo $this->Form->input('make_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
    echo $this->Form->input('empresas_id', ['type' => 'hidden', 'value' => $empresa['id']]);
    echo $this->Form->input('situacao', ['type' => 'hidden', 'value' => 'A']);
?>
<p>
    <label>Empresa</label>
    <span class="field">
      <b><?php echo $empresa['nome']; ?></b>
    </span>
</p>
<p>
    <label>Nome da obra</label>
    <span class="field">
        <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
    </span>
</p>

<p class='stdformbutton'  style="padding: 0;">
    <h4 style="padding: 15px;">CONTATO</h4>
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
        <?php echo $this->Form->input('email_contato', ['label' => false, 'type' => 'email', 'required' => false, 'class' => 'input-xxlarge']); ?>
    </span>
</p>
<p>
    <label>Contatos</label>
    <span class="field">
        <small>Celular</small><small>Celular / Telefone</small><br/>
        <?php echo $this->Form->input('celular_contato', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?>  &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('telefone_contato', ['label' => false, 'type' => 'text', 'required' => false, 'class' => 'input-small fone']); ?> &nbsp;&nbsp;&nbsp;
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
    });
</script>
