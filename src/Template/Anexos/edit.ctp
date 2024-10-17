<?php
    $editEnable = true;
?>

<div class="mediaWrapper row-fluid">
    <div class="span5 imginfo">
        <p style="width: 100%;">
          <img src="<?php echo "{$this->request->webroot}anexos/{$anexo['tag']['id']}/{$programa['id']}/{$anexo['id']}.jpg"; ?>" alt=""/>
        </p>
        <p>
            <strong>Upload por:</strong> <?php echo $anexo['usuario']['nome']; ?> <br />
            <strong>Data do Upload:</strong> <?php echo $anexo['data_envio']->format('d/m/Y H:i') ?>h <br />
            <strong>Data Altera&ccedil;&atilde;o:</strong> <?php echo $anexo['data_alteracao']->format('d/m/Y H:i'); ?>h <br />
            <strong>Tag:</strong> <?php echo $anexo['tag']['descricao']; ?> <br />
        </p>
        <p>
            <?php
                if($editEnable) {
                    echo $this->Form->postLink('excluir arquivo', ['controller' => 'anexos', 'action' => 'delete', $programa->id, $anexo['id'], $json], ['confirm' => 'Deseja excluir este arquivo?', 'title' => 'Excluir', 'style' => 'color: red; float: right;']);
                }
            ?>
            <a download="<?= $anexo['nome']; ?>.jpg" href="<?php echo "{$this->request->webroot}anexos/{$anexo['tag']['id']}/{$programa['id']}/{$anexo['id']}.jpg"; ?>" class="btn"><span class="icon-download"></span> Download</a>
        </p>
    </div>

    <div class="span7 imgdetails">
        <?php
            $this->Form->templates(['inputContainer' => '{{content}}']);
            echo $this->Form->create($anexo, ['class' => '']);
            echo $this->Form->input('programas_id', ['type' => 'hidden', 'value' => $programa->id]);
            echo $this->Form->input('data_alteracao', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
        ?>
        <p>
            <label>Nome do Arquivo</label>
            <?php echo $this->Form->input('nome', ['label' => false, 'required' => true, 'style' => 'width: 90%', 'disabled' => !$editEnable]); ?>
        </p>
        <p>
            <label>Descri&ccedil;&atilde;o:</label>
            <?php echo $this->Form->input('descricao', ['label' => false, 'style' => 'width: 90%', 'disabled' => !$editEnable]); ?>
        </p>
    <?php
        echo "<br/><p class='stdformbutton'>{$this->Form->button(__('Salvar'), ['class' => 'btn btn-default btn-large gravar'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
        echo $this->Form->end();
    ?>
    </div>
</div>
