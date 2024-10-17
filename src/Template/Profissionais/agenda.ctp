<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15">
            <?php echo $this->Html->link('<span class="icon-list"></span> Listar', ['action' => 'index'], ['class' => 'btn', 'escape'=>false]) ?>
        </li>


        <li class="marginleft15 right">
            <a href="#myModal" data-toggle="modal" class="btn modal-agenda" ><span class="icon-calendar"></span> Agenda</a>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin shadowed" style="color: #000;"><?php echo $profissional['nome']; ?></h4>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class=""> <?php echo $this->Html->link(__('Dados do Profissional'), ['action' => 'edit', $profissional['id']], ['escape' => false]); ?> </li>
            <li class="active"> <?php echo $this->Html->link(__('Agenda'), ['action' => 'agenda', $profissional['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>

<div class="widgetcontent bordered shadowed">
    <div id='calendar'></div>
</div>

<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade in" id="myModal">
  <div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
    <h3 id="myModalLabel">Escolha o Tipo de Registro</h3>
  </div>
  <div class="modal-footer">
    <?php echo $this->Html->link('<span class="iconfa-calendar"></span> Agenda Programada', ['action' => 'programada', $profissional['id']], ['class' => 'btn btn-success btn-popup programada', 'data-dismiss' => 'modal', 'escape'=>false]) ?>
    <?php echo $this->Html->link('<span class="iconfa-calendar"></span> Agenda Extra', ['action' => 'extra', $profissional['id']], ['class' => 'btn btn-primary btn-popup extra', 'data-dismiss' => 'modal', 'escape'=>false]) ?>
    <?php echo $this->Html->link('<span class="iconfa-calendar"></span> Indisponibilidade', ['action' => 'indisponibilidade', $profissional['id']], ['class' => 'btn btn-danger btn-popup indisponibilidade', 'data-dismiss' => 'modal', 'escape'=>false]) ?>
  </div>
</div>

<?php echo $this->Html->script('/bootstrap/js/fullcalendar.min'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery("a.btn-popup").colorbox({
      onLoad: function() {
          jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
      }
  });

  var calendar = jQuery('#calendar').fullCalendar({
    allDaySlot: true,
    allDayText: 'Para hoje',
    axisFormat: 'H(:mm)',
    timeFormat: 'H(:mm)',
    editable: false,
    selectable: true,
    selectHelper: true,
    slotEventOverlap: false,
    events: "<?php echo $this->Url->build(["controller" => "profissionais", "action" => "agendas", $profissional['id']], true); ?>",
    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','S&aacute;bado'],
    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
    titleFormat: {
        month: 'MMMM/yyyy',
        day: 'dddd, d - MMMM - yyyy'
    },
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaDay'
    },
    buttonText: {
        prev: '&laquo;',
        next: '&raquo;',
        agendaDay: 'Agenda do dia',
        month: 'Agenda do m&ecirc;s',
        today: 'Todos de hoje'
    },
    select: function(start, end, allDay) {
        var href = '?start='+$.fullCalendar.formatDate(start, "yyyy-MM-dd");
        href += '&end='+$.fullCalendar.formatDate(end, "yyyy-MM-dd");

        jQuery('a.indisponibilidade').attr('href', '<?php echo $this->Url->build(['action' => 'indisponibilidade', $profissional['id']], true); ?>'+href);
        jQuery('a.programada').attr('href', '<?php echo $this->Url->build(['action' => 'programada', $profissional['id']], true); ?>'+href);
        jQuery('a.extra').attr('href', '<?php echo $this->Url->build(['action' => 'extra', $profissional['id']], true); ?>'+href);
        jQuery('a.modal-agenda').click();
    }
  });

});
</script>
