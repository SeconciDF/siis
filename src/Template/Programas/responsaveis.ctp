<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo respons&aacute;vel', ['action' => 'responsavel', $programa['id']], ['class' => 'btn', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed">Editar Programa</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li><?php echo $this->Html->link('Descri&ccedil;&atilde;o do programa', ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape'=>false]); ?></li>
          <li class="active"><?php echo $this->Html->link('Respons&aacute;veis', ['controller' => 'programas', 'action' => 'responsaveis', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Textos', ['controller' => 'programas-textos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <table class="table table-bordered">
      <thead>
          <tr>
              <th>Nome</th>
              <th>CREA/MTE</th>
              <th>NIS</th>
              <th></th>
          </tr>
      </thead>
      <tbody  style="background: #fff;">
          <?php foreach ($responsaveis as $responsavel): ?>
          <tr>
              <td><?= h($responsavel['nome_responsaveil']); ?></td>
              <td><?= h($responsavel['crea_mte']); ?></td>
              <td><?= h($responsavel['nis_responsaveil']); ?></td>
              <td style="width: 100px; text-align: center;">
                  <?= $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'responsavel', $programa['id'], $responsavel['id']], ['class' => 'btn', 'escape'=>false]); ?>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
</div>
