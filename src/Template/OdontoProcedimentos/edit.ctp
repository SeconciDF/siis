<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar procedimento</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
      //echo $this->element('template');
      $this->Form->templates(['inputContainer' => '{{content}}']);
      echo $this->Form->create($procedimento, ['class' => 'stdform stdform2']);
      echo $this->Form->input('situacao', ['type' => 'hidden']);
  ?>
  <p>
      <label>Configura&ccedil;&atilde;o</label>
      <span class="field">
        <small>C&oacute;digo</small><small>Pontua&ccedil;&atilde;o</small><small>Boca ou Dente</small><br/>
        <?php echo $this->Form->input('id', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small']); ?> &nbsp;&nbsp;&nbsp;
        <?php $procedimento['ponto'] = number_format($procedimento['ponto'],2,'.',''); ?>
        <?php echo $this->Form->input('ponto', ['label' => false, 'type' => 'number', 'required' => true, 'class' => 'input-small ponto']); ?> &nbsp;&nbsp;&nbsp;
        <?php echo $this->Form->input('boca_dente', ['label' => false, 'type' => 'select', 'required' => true, 'class' => 'input-small', 'options' => ['0'=>'Dente', '1'=>'Boca']]); ?>
        <?php
            if($procedimento['situacao'] == '1') echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"0\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green; float: right;' title='Tornar inativo'>ATIVO</button>";
            else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"1\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red; float: right;' title='Tornar ativo'> INATIVO </button>";
        ?>
      </span>
  </p>
  <p>
      <label>Nome do procedimento</label>
      <span class="field">
          <?php echo $this->Form->input('nome', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-xxlarge']); ?>
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
      jQuery('.ponto').priceFormat({
        prefix: '',
        centsSeparator: '.',
        thousandsSeparator: '',
        clearOnEmpty: true,
        centsLimit: 2
      });
    });
</script>
