<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery.get('<?php echo $this->Url->build(['controller' => 'consultas', 'action' => 'autofaltas'], true); ?>', function (data) {
    if(data) {
      jQuery.jGrowl(data, {theme: 'siis', sticky: true, position: 'bottom-right'});
    }
  });
});
</script>
