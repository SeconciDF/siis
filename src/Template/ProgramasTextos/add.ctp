<?php
echo $this->Html->script('/bootstrap/js/tinymce/jquery.tinymce');
echo $this->Html->script('/bootstrap/js/tinymce/tiny_mce');
echo $this->Html->script('/bootstrap/js/wysiwyg');
?>

<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index', $programa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Cadastro de Documento Base</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do programa', ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Respons&aacute;veis', ['controller' => 'programas', 'action' => 'responsaveis', $programa['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Textos', ['controller' => 'programas-textos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
        </ul>
    </div>
</div>


<div class="widgetcontent bordered shadowed nopadding">
<?php
    //echo $this->element('template');
    $this->Form->templates(['inputContainer' => '{{content}}']);
    echo $this->Form->create($texto, ['class' => 'stdform stdform2']);
    echo $this->Form->input('colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa['id']]);
?>
<p>
    <label>Empresa</label>
    <span class="field">
        <b><?php echo $empresa['nome']; ?></b>
    </span>
</p>
<p>
    <label>Tipo</label>
    <span class="field">
        <?php echo $this->Form->input('tipo', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-medium', 'options' => $tipos, 'default' => '3', 'escape' => false ]); ?>
        <?php echo $this->Form->input('pagebreak', ['label' => false, 'type' => 'checkbox', 'required' => false]); ?> Pular p&aacute;gina
    </span>
</p>
<p>
    <label>T&iacute;tulo</label>
    <span class="field">
        <?php echo $this->Form->input('titulo', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?> <br/>
        <?php echo $this->Form->input('show_titulo', ['label' => false, 'type' => 'checkbox', 'required' => false]); ?> Exibir t&iacute;tulo
    </span>
</p>
<p>
    <label>Descri&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('texto', ['label' => false, 'class' => 'tinymce', 'style' => 'width: 100%; height: 300px;']); ?>
    </span>
</p>

<?php
    echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
    echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('form').submit(function () {
            $('#texto').val(tinyMCE.activeEditor.getContent());
            return true;
        });
    });
</script>
