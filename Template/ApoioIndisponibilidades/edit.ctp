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

<h4 class="widgettitle nomargin shadowed">Feriados e Folgas</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
      //echo $this->element('template');
      $this->Form->templates(['inputContainer' => '{{content}}']);
      echo $this->Form->create($indisponibilidade, ['class' => 'stdform stdform2']);
      echo $this->Form->input('situacao', ['type' => 'hidden']);
  ?>
  <p>
      <label>Descri&ccedil;&atilde;o</label>
      <span class="field">
          <?php echo $this->Form->input('descricao', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-large']); ?>
          <?php
              if($indisponibilidade['situacao'] == 'A') echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"I\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: green; float: right;' title='Tornar inativo'>ATIVO</button>";
              else echo "<button type='submit' onclick='javascript: $(\"#situacao\").val(\"A\");' style='color: #fff; border: 0; padding: 6px 15px 6px 15px; background: red; float: right;' title='Tornar ativo'> INATIVO </button>";
          ?>
      </span>
  </p>
  <p>
      <label>Data</label>
      <span class="field">
          <?php $indisponibilidade['data'] = date('d/m/Y', strtotime($indisponibilidade['data'])); ?>
          <?php echo $this->Form->input('data', ['label' => false, 'type' => 'text', 'required' => true, 'class' => 'input-medium mask-date']); ?>
      </span>
  </p>
  <?php
      echo "<p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['class' => 'btn btn-success btn-large'])}</p>";
      echo $this->Form->end();
  ?>
</div>
