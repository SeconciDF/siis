<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

        <li class="marginleft15 right">
            <?php echo $this->Html->link('<span class="iconfa-paper-clip"></span> Anexos', ['controller' => 'programas', 'action' => 'anexos', $programa['id']], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Programa</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div id="tabs">
   <ul>
      <li><a href="#tabs1">Descri&ccedil;&atilde;o do programa </a></li>
      <li><a href="#tabs2">Respons&aacute;veis</a></li>
   </ul>
   <div id="tabs1" class="nopadding">
    <?php
        //echo $this->element('template');
        $this->Form->templates(['inputContainer' => '{{content}}']);
        echo $this->Form->create($programa, ['url' => ['action' => 'edit', $programa['id']], 'class' => 'stdform stdform2']);
    ?>
    <p>
        <label>Empresa</label>
        <span class="field">
            <?php echo $this->Form->input('empresas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-xxlarge', 'options' => $empresas]); ?>
        </span>
    </p>
    <p>
        <label>Programa</label>
        <span class="field">
            <?php echo $this->Form->input('apoio_programas_id', ['label' => false, 'type' => 'select', 'empty' => true, 'required' => true, 'class' => 'input-large', 'options' => $programas]); ?>
        </span>
    </p>
    <p>
        <label>Ano de refer&ecirc;ncia</label>
        <span class="field">
            <?php echo $this->Form->input('ano_referencia', ['label' => false, 'type' => 'number', 'min' => '1970', 'required' => true, 'class' => 'input-small']); ?>
        </span>
    </p>
    <p>
        <label>Data Inicial</label>
        <span class="field">
            <?php $programa->data_inicial = isset($programa->data_inicial) ? $programa->data_inicial->format('d/m/Y') : null; ?>
            <?php echo $this->Form->input('data_inicial', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
        </span>
    </p>
    <p>
        <label>Data Final</label>
        <span class="field">
            <?php $programa->data_final = isset($programa->data_final) ? $programa->data_final->format('d/m/Y') : null; ?>
            <?php echo $this->Form->input('data_final', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date', 'placeholder' => 'dd/mm/aaaa']); ?>
        </span>
    </p>

    <?php
        echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])}</p>";
        echo $this->Form->end();
    ?>
  </div>
  <div id="tabs2" class="nopadding">
    <table class="table table-bordered">
        <tbody  style="background: #fff;">
            <?php foreach ($responsaveis as $responsavel): ?>
            <tr>
                <td><?= h($responsavel['nome_responsaveil']); ?></td>
                <td><?= h($responsavel['nis_responsaveil']); ?></td>
                <td><?= h($responsavel['numero_orgao_classe']); ?></td>
                <td><?= h($responsavel['uf_orgao_classe']); ?></td>
                <td style="width: 100px; text-align: center;">
                    <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'responsaveis', $programa['id'], $responsavel['id'], 'tab' => '2'], ['class' => 'btn', 'escape'=>false]); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" style="text-align: right;">
              <?php echo $this->Html->link('<span class="icon-edit"></span> Adicionar respons&aacute;vel', ['action' => 'responsaveis', $programa['id'], 'tab' => '2'], ['class' => 'btn', 'escape'=>false]); ?>
            </td>
          </tr>
        </tfoot>
    </table>
 </div>
</div>

<input type="hidden" id="tab" value="<?php echo $this->request->query('tab'); ?>" />
<script type="text/javascript">
    jQuery(document).ready(function ($) {
      $('a[href="#tabs'+$('#tab').val()+'"]').parent().addClass('ui-tabs-active ui-state-active');
      // $('#tabs'+$('#tab').val()).addClass('in active');
      $('#tabs').tabs();



    });
</script>
