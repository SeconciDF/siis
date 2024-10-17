<h4 class="widgettitle nomargin shadowed">observa&ccedil;&otilde;es</h4>
<div class="widgetcontent bordered shadowed nopadding">
  <?php
  //echo $this->element('template');
  $this->Form->templates(['inputContainer' => '{{content}}']);
  echo $this->Form->create($observacao, ['class' => 'stdform stdform2']);  
  echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
  echo $this->Form->input('colaborador', ['type' => 'hidden', 'value' => $this->request->session()->read('Auth.User.nome')]);
  ?>

  <p>
    <label>Observa&ccedil;&atilde;o</label>
    <span class="field">
        <?php echo $this->Form->input('observacao', ['label' => false, 'type' => 'textarea', 'required' => true, 'class' => 'input-xlarge', 'escape' => false]); ?>
    </span>
  </p>

  <?php
  echo "<p class='stdformbutton'>{$this->Form->button(__('Confirmar'), ['type' => 'submit', 'class' => 'btn btn-success btn-large'])} <button type='button' class='btn btn-large' onclick='location.reload();'>Cancelar</button></p>";
  echo $this->Form->end();
  ?>
</div>


<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 100px;">Data Hora</th>
            <th>Observa&ccedil;&otilde;es</th>
        </tr>
    </thead>
    <tbody  style="background: #fff;">
        <?php foreach ($observacoes as $observacao): ?>
        <tr>
            <td><?= date('d/m/Y H:i', strtotime($observacao['data_hora_registro'])) ?></td>
            <td><?= h($observacao['observacao']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot style="background: #fff;">
        <tr>
            <td colspan="8">
                <div style="float: left;">
                    <?php echo $this->Paginator->counter('Pagina {{page}} de {{pages}}, mostrando {{current}} de {{count}} ({{start}} ate {{end}})'); ?>
                </div>
                <div class="pagination" style="float: right; margin: 0;">
                    <ul>
                        <?php
                        echo $this->Paginator->prev('< ' . __('anterior'), array('tag' => 'li'), null, array('class' => 'disabled'));
                        echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li'));
                        echo $this->Paginator->next(__('próximo') . ' >', array('tag' => 'li'), null, array('class' => 'disabled'));
                        ?>
                    </ul>
                </div>
            </td>
        </tr>
    </tfoot>
</table>

<style>.field small { min-width: 100px; display: inline-block; margin-right: 20px; }</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery("div.pagination ul li a").addClass('btn-popup');
        jQuery("a.btn-popup").colorbox({
            onLoad: function() {
                jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
            }
        });
    });
</script>
