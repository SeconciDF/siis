<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-edit"></span> Novo', ['action' => 'add', $programa['id']], ['class' => 'btn', 'escape'=>false]) ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin">Medidas de controle e Avalia&ccedil;&atilde;o dos riscos</h4>
<?php echo $this->element("abas{$programa['apoio_programas_id']}"); ?>

<div class="navbar nopadding">
    <div class="navbar-inner nopadding">
       <ul class="nav">
          <li class="active"><?php echo $this->Html->link('Medida de Controle Existente', ['controller' => 'ProgramasMedidasControles', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('EPI Existente', ['controller' => 'ProgramasEpi', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Perfil da Exposi&ccedil;&atilde;o Existente', ['controller' => 'ProgramasPerfilExposicoes', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Avalia&ccedil;&atilde;o de Risco', ['controller' => 'ProgramasAvaliacoesRiscos', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
          <li><?php echo $this->Html->link('Plano de a&ccedil;&atilde;o', ['controller' => 'ProgramasPlanosAcoes', 'action' => 'index', $programa['id']], ['escape'=>false]); ?></li>
       </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed nopadding">
  <table class="table table-bordered">
      <thead>
          <tr>
            <th><?= $this->Paginator->sort('Ambiente.descricao', 'Setor', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('Grupo.nome', 'Nome do GHE'); ?></th>
            <th><?= $this->Paginator->sort('Processo.processo', 'Processo', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('Agente.descricao', 'Agente', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('FatorRisco.descricao', 'Perigo', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('Perigo.possivel_dano', 'Poss&iacute;vel dano', ['escape' => false]); ?></th>
            <th><?= $this->Paginator->sort('ProgramasMedidasControles.descricao', 'Medida de Controle', ['escape' => false]); ?></th>
            <th class="actions" style="width: 100px;"></th>
          </tr>
      </thead>
      <tbody  style="background: #fff;">
          <?php foreach ($medidas as $medida): ?>
          <tr>
              <td><?= h($medida['Ambiente']['descricao']); ?></td>
              <td><?= h($medida['Grupo']['nome']); ?></td>
              <td><?= str_replace(',','<br/>',$medida['processos']); ?></td>
              <td><?= h($medida['Agente']['descricao']); ?></td>
              <td><?= h($medida['FatorRisco']['codigo']); ?> - <?= h($medida['FatorRisco']['descricao']); ?></td>
              <td><?= h($medida['Perigo']['possivel_dano']); ?></td>
              <td><?= h($medida['descricao']); ?></td>
              <td style="width: 190px; text-align: center;">
                <?php echo $this->Html->link('<span class="icon-edit"></span> Editar', ['action' => 'edit', $medida['id']], ['class' => 'btn', 'escape'=>false]); ?>
                <?php echo $this->Form->postLink('<span class="iconfa-trash"></span> Deletar', ['action' => 'delete', $medida['id']], ['confirm' => 'Deseja excluir?', 'class' => 'btn btn-danger', 'escape'=>false]); ?>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
      <tfoot style="background: #fff;">
          <tr>
              <td colspan="8">
                  <?php echo $this->element('paginacao'); ?>
              </td>
          </tr>
      </tfoot>
  </table>
</div>
