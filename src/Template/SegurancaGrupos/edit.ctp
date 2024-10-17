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

<h4 class="widgettitle nomargin shadowed">editar Grupo</h4>
<div class="widgetcontent bordered shadowed nopadding">
<?php
    echo $this->element('template');
    echo $this->Form->create($grupo, ['class' => 'stdform stdform2']);
    echo $this->Form->input('make_colaboradores_id', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.id')]);
    echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d')]);
    echo $this->Form->input('ativo', ['type' => 'hidden']);

    echo $this->Form->input('descricao', ['label' => ['text' => 'Grupo'], 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']);
    echo $this->Form->input('info', ['label' => ['text' => 'Descri&ccedil;&atilde;o', 'escape' => false],'type' => 'textarea', 'required' => true, 'class' => 'input-xxlarge']);
?>

    <p>
        <label>Situa&ccedil;&atilde;o</label>
        <span class="field">
            <?php
                if($grupo['ativo'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#ativo\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green;' title='Tornar inativo'>ATIVO</button>";
                else echo "<button type='submit' onclick='javascript: $(\"#ativo\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red;' title='Tornar ativo'> INATIVO </button>";
            ?>
        </span>
    </p>

    <p>
        <div id="accordion2" class="accordion faq" style="width: 100%;" >
            <h3 style="display: none;"> <a href="#" style="display: none;"> </a> </h3>
            <div style="text-align: center;">selecione as funcionalidades para este grupo</div>
            <?php foreach ($acessos as $modulo) { ?>
                <h3> <a href="#"> <?php echo $modulo['modulo']; ?> </a> </h3>
                <div>
                  <?php foreach ($modulo['funcionalidades'] as $key => $value) { ?>
                      <?php $selected = $value['permissoes']['id'] ? 'background: #A2CD5A; color: #fff;' : 'background: #E8E8E8; color: #666'; ?>
                      <p style=" <?php echo $selected; ?> border: 0px; padding: 5px; margin: 5px; font-weight: bold;" >
                        <span class="funcionalidade" style="cursor: pointer; display: inline-block; width: 100%;">
                            <input type="checkbox" name="permissoes[<?php echo $key; ?>][funcionalidades_id]" value="<?php echo $key; ?>" <?php echo $value['permissoes']['id'] ? 'checked' : 'disabled'; ?> />
                            <?php echo $value['info']; ?>
                        </span><br/>

                        <span style="width: 100%; display: inline-block; text-align: right;">
                            <input type="checkbox" name="permissoes[<?php echo $key; ?>][visualizar]" value="S" <?php echo $value['permissoes']['visualizar'] == 'S' ? 'checked' : ''; ?> <?php echo $value['permissoes']['id'] ? '' : 'disabled'; ?>/> visualizar
                            <input type="checkbox" name="permissoes[<?php echo $key; ?>][bloquear]" value="S" <?php echo $value['permissoes']['bloquear'] == 'S' ? 'checked' : ''; ?> <?php echo $value['permissoes']['id'] ? '' : 'disabled'; ?>/> bloquear
                            <input type="checkbox" name="permissoes[<?php echo $key; ?>][editar]" value="S" <?php echo $value['permissoes']['editar'] == 'S' ? 'checked' : ''; ?> <?php echo $value['permissoes']['id'] ? '' : 'disabled'; ?>/> editar
                            <input type="checkbox" name="permissoes[<?php echo $key; ?>][excluir]" value="S" <?php echo $value['permissoes']['excluir'] == 'S' ? 'checked' : ''; ?> <?php echo $value['permissoes']['id'] ? '' : 'disabled'; ?>/> excluir
                            <input type="checkbox" name="permissoes[<?php echo $key; ?>][criar]" value="S" <?php echo $value['permissoes']['criar'] == 'S' ? 'checked' : ''; ?> <?php echo $value['permissoes']['id'] ? '' : 'disabled'; ?>/> criar &nbsp;
                        </span>
                      </p>
                  <?php } ?>
                </div>
            <?php } ?>
        </div>
    </p>

<?php
    echo $this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large gravar']);
    echo $this->Form->end();
?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#accordion2').find('a').css('text-decoration', 'none');
        jQuery('#accordion2').accordion({heightStyle: "content"});
        jQuery('span.funcionalidade').click(function() {
            if(jQuery(this).find('input').is(':checked')) {
                jQuery(this).find('input').attr('checked', false).attr('disabled', true);
                jQuery(this).parent().find('input').attr('checked', false).attr('disabled', true);
                jQuery(this).parent().css('background', '#E8E8E8');
                jQuery(this).parent().css('color', '#666');
            } else {
                jQuery(this).find('input').attr('checked', true).attr('disabled', false);
                jQuery(this).parent().find('input').attr('disabled', false);
                jQuery(this).parent().css('background', '#A2CD5A');
                jQuery(this).parent().css('color', '#fff');
            }
        });
    });
</script>
