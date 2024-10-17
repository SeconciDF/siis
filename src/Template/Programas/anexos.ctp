<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <li class="marginleft15 novo">
            <?php echo $this->Html->link('<span class="icon-arrow-left"></span> Voltar', ['controller' => 'programas', 'action' => 'edit', $programa->id], ['class' => 'btn selectall', 'escape'=>false]) ?>
        </li>

    </ul>
    <span class="clearall"></span>
</div>

<h4 class="widgettitle nomargin ">Anexos do programa</h4>
<div class="widgetcontent" style="background: #fff;">
    <div class="widgetcontent" style="padding: 15px;">
        <div class="row-fluid">
            <div class="span9" >
                <div class="widgetcontent">
                    <ul class="listfile">
                    <?php foreach ($anexos as $value) { ?>
                        <li>
                            <a href="<?php echo $this->Url->build(["controller" => "anexos", "action" => "edit", $programa->id, $value->id, json_encode(['controlle' => 'programas', 'action' => 'anexos', 'id' => $programa->id, 'local' => null])], true); ?>" data-rel="doc" title="<?php echo $value->nome; ?>"><?php echo $this->Html->image('doc.png'); ?></a>
                            <span class="filename"><?php echo substr($value->nome, 0, 10); ?>...</span>
                        </li>
                    <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="span3">
                <h4 class="widgettitle">Upload</h4>
                <div class="widgetcontent">
                  <?php echo $this->Html->link('Atualizar',['action' => 'anexos', $programa->id], ['class' => 'btn btn-default btn-large btn-block', 'style' => 'display: none;', 'id' => 'btn-atualizar']) ?>
                  <span class="btn btn-success btn-large btn-block fileinput-button" id="btn-upload" >
                      <span>Selecionar Arquivos</span>
                      <input id="fileupload" type="file" name="files" accept="image/*" multiple>
                  </span>

                  <div  id="progress" class="progress">
                      <div class="progress-bar" style="width: 0%; background: #3a87ad;">&nbsp;</div>
                  </div>

                  <ul id="files" class="files menuright">

                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->Html->css('/bootstrap/js/upload/css/jquery.fileupload.css'); ?>
<?php echo $this->Html->script('/bootstrap/js/upload/js/jquery.fileupload.js'); ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        var url = '<?php echo $this->Url->build(["controller" => "anexos", "action" => "upload", '1', $programa->id], true); ?>';
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            done: function (e, data) {

              console.log(data);

                $('#btn-upload').hide();
                $('#btn-atualizar').show();
                $.each(data.result.files, function (index, file) {
                    var color = file.error ? 'red' : '';
                    var html = '<a style="color: ' + color + '">';
                    html += file.name.substring(0, 25);
                    if(file.error) {
                        html += '<br/><b>';
                        html += file.error;
                        html += '</b>';
                    }
                    html += '</a>';


                    //'<a>'+file.name.substring(0, 25)+'...<br/>' + file.error + '</a>'
                    $('<li/>').html(html).appendTo('#files');
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css('width', progress + '%');
            }
        }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

        jQuery(".listfile a").colorbox({
            onLoad: function() {
                jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
            }
        });

    });
</script>
