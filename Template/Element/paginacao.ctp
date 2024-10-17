<div style="float: left;">
    <?php //echo $this->Paginator->counter('P&aacute;gina {{page}} de {{pages}}'); ?>
    <?php echo $this->Paginator->counter('P&aacute;gina {{page}} de {{pages}}, mostrando {{current}} de {{count}} ({{start}} at&eacute; {{end}})'); ?>
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
