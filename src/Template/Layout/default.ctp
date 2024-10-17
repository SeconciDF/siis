<?php

/**
 * @author Adriano Maltha <adriano.maltha@gmail.com>
 * @version 2.0
 */

$name = 'SIIS';
$description = 'Sistema Integrado de Informa&ccedil;&otilde;es Seconci-DF';

?>
<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?php echo "{$name} - {$description}"; ?>
        </title>
        <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css('/bootstrap/css/style.default');
        echo $this->Html->css('/bootstrap/css/style.siis');
        echo $this->Html->css('/bootstrap/css/chosen');
        echo $this->Html->css('style');

        echo $this->Html->script('/bootstrap/prettify/prettify');
        echo $this->Html->script('/bootstrap/js/jquery-1.9.1.min');
        echo $this->Html->script('/bootstrap/js/jquery-migrate-1.1.1.min');
        echo $this->Html->script('/bootstrap/js/jquery-ui-1.9.2.min');
        echo $this->Html->script('/bootstrap/js/jquery.flot.min');
        echo $this->Html->script('/bootstrap/js/jquery.flot.resize.min');
        echo $this->Html->script('/bootstrap/js/jquery.mask.min');
        echo $this->Html->script('/bootstrap/js/jquery.alerts');
        echo $this->Html->script('/bootstrap/js/bootstrap.min');
        echo $this->Html->script('/bootstrap/js/chosen.jquery.min');
        echo $this->Html->script('/bootstrap/js/jquery.numeric.min');
        echo $this->Html->script('/bootstrap/js/jquery.price_format.2.0.min');
        echo $this->Html->script('/bootstrap/js/jquery.jgrowl');
        echo $this->Html->script('/bootstrap/js/jquery.colorbox-min');
        echo $this->Html->script('/bootstrap/js/jquery.uniform.min');
        echo $this->Html->script('custom');
        echo $this->Html->script('mascaras');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>
        <script type="text/javascript">
            var url = '<?php echo $this->Url->build('/', true); ?>';
            jQuery(document).ready(function () {
                jQuery('#'+jQuery('#menu-active').val()).addClass('active');
                if(jQuery('#'+jQuery('#menu-active').val()).hasClass("dropdown")) {
                    jQuery('#'+jQuery('#menu-active').val()).find('ul').attr('style', 'display: block');
                }

                jQuery(document).on("keydown", function (e) {
                    if (e.which === 8 && !$(e.target).is("input, textarea")) {
                        e.preventDefault();
                    }
                });

                jQuery('.sair').click(function () {
                    jConfirm('Sair do sistema?', 'Sair', function (r) {
                        if (r) {
                            location.href = '<?php echo $this->Url->build(["controller" => "seguranca-colaboradores", "action" => "logout"], true); ?>';
                        }
                    });
                });

                jQuery('#changeEntidade').change(function () {
                    jConfirm('Deseja trocar de entidade?', 'Entidades', function (r) {
                        if (r) {
                            location.href = '<?php echo $this->Url->build(["controller" => "usuarios", "action" => "trocar"], true); ?>/' + $('#changeEntidade').val();
                        }
                    });
                });

                jQuery("form").submit(function (e) {
                  jQuery("form").find("button").attr('disabled', true);
                  lock();
                });
            });

            function lock() {
              jQuery('#lockscreen').html('<?php echo $this->Html->image('loading.gif', array('style' => 'position: absolute; left: 45%; top: 40%; width: 10%;')); ?>');
              jQuery("#lockscreen").css("width", "100%");
              jQuery("#lockscreen").css("height", "100%");
              jQuery("#lockscreen").css("z-index", "1000");
              jQuery("#lockscreen").fadeIn();
            }
        </script>
    </head>
    <body>
    <?php if ($this->request->session()->read('Auth.User.id')): ?>
        <div id="lockscreen"></div>
        <input type="hidden" id="menu-active" value="<?php echo $this->request->session()->read('Auth.User.MenuActive'); ?>">
        <div class="mainwrapper fullwrapper">
            <div class="leftpanel">
                <div class="logopanel">
                    <?php echo $this->Html->image('seconci_logo.png', array('style' => 'width: 110px; margin-left: 10px;')); ?>
                </div>
                <div class="datewidget">&Uacute;ltimo Acesso: <?php echo date("d/m/Y H:i", strtotime($this->request->session()->read('Auth.User.data_ultimo_acesso'))); ?><b></b></div>

                <div class="searchwidget" style="height: 36px;">
                    <h1>SIIS <span style="font-size: 10px;"> v2.0</span></h1>
                </div>

                <input type="hidden" id="filter-unidade" value="<?php echo $this->request->query('unidade') ? $this->request->query('unidade') : $this->request->session()->read('Auth.User.unidades_id'); ?>" />
                <input type="hidden" id="filter-especialidade" value="<?php echo $this->request->query('especialidade'); ?>" />
                <input type="hidden" id="filter-profissional" value="<?php echo $this->request->query('profissional'); ?>" />
                <input type="hidden" id="filter-turno" value="<?php echo $this->request->query('turno'); ?>" />
                <?php if(in_array($this->request->controller, ['Consultas', 'Agendas']) && $this->request->action == 'index') { ?>
                    <input type="hidden" id="selectedDate" value="<?php echo $this->request->query('date'); ?>">
                    <script type="text/javascript">
                      jQuery(function() {
                        jQuery("#datepicker-agenda").datepicker({
                          dateFormat: 'yy-mm-dd',
                          onSelect: function(date) {
                              var parameter = '?date='+date;
                              if(jQuery('#filter-especialidade').val()) {
                                parameter += '&especialidade='+jQuery('#filter-especialidade').val();
                              }
                              if(jQuery('#filter-profissional').val()) {
                                parameter += '&profissional='+jQuery('#filter-profissional').val();
                              }
                              if(jQuery('#filter-unidade').val()) {
                                parameter += '&unidade='+jQuery('#filter-unidade').val();
                              }
                              if(jQuery('#filter-turno').val()) {
                                parameter += '&turno='+jQuery('#filter-turno').val();
                              }

                              lock();
                              location.href = '<?php echo $this->Url->build(["controller" => $this->request->controller, "action" => "index"], true); ?>'+parameter;
                          }
                        });

                        if(jQuery('#selectedDate').val()) {
                          var dateParts = jQuery('#selectedDate').val().match(/(\d+)/g);
                          jQuery("#datepicker-agenda").datepicker('setDate', new Date(dateParts[0], dateParts[1] - 1, dateParts[2]));
                        }
                      });
                    </script>
                    <div class="">
                      <div id="datepicker-agenda"></div>
                    </div>
                <?php } ?>

                <?php if(in_array($this->request->controller, ['Atendimentos'])) { ?>
                  <div class="plainwidget" style="font-size: 10px;">
                    <p>
                      <b>Prontu&aacute;rio:</b>
                      <?php
                        $prontuario = '';
                        if($consulta['Dependente']['id']) {
                          $prontuario = array_search($consulta['Dependente']['id'], explode(',',$consulta['dependentes']));
                          $prontuario = '.' . ++ $prontuario;
                        }
                        echo $consulta['Beneficiario']['id'] . $prontuario;
                      ?>
                    </p>
                    <p> <b>Paciente:</b>
                      <?php
                        if($consulta['Dependente']['id']) {
                          echo $this->Html->link("<span class='iconfa-pencil'></span> (D) {$consulta['paciente']}", ['controller' => 'beneficiarios', 'action' => 'view-dependente', $consulta['Beneficiario']['id'], $consulta['Dependente']['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]);
                        } else {
                          echo $this->Html->link("<span class='iconfa-pencil'></span> {$consulta['paciente']}", ['controller' => 'beneficiarios', 'action' => 'view', $consulta['Beneficiario']['id']], ['style' => 'color: #000000;', 'class' => 'btn-popup', 'title' => 'Edi&ccedil;&atilde;o R&aacute;pida', 'escape'=>false]);
                        }
                      ?>
                    <p>
                    <div style="width: 80%; float: left;">
                      <b>Fun&ccedil;&atilde;o:</b> <?php echo str_replace(',',', ',$consulta['funcoes']); ?>
                    </div>
                    <div style="width: 20%; float: right;">
                      <?php $idade = new \DateTime($consulta['nascimento']); ?>
                      <b>Idade:</b> <?php echo $idade->diff(new \DateTime('now'))->y; ?>
                    </div>
                    </p>
                    <p> <b>Empresa:</b> <?php echo $consulta['Empresa']['nome']; ?> </p>
                  </div>

                  <div class="plainwidget" style="font-size: 10px;">
                    <p> <b>Examinador:</b> <?php echo $consulta['Profissional']['nome']; ?> </p>
                    <p> <b>Especialidade:</b> <?php echo $consulta['Especialidade']['descricao']; ?> </p>
                    <p> <b>Motivo da Consulta:</b> <?php echo $consulta['Motivo']['descricao']; ?> </p>
                  </div>
                <?php } ?>

                <div class="leftmenu">
                    <ul class="nav nav-tabs nav-stacked">
                    <?php
                        if ($this->request->session()->read('Auth.User.menu')):
                            foreach ($this->request->session()->read('Auth.User.menu') as $id => $menu):
                                if(isset($menu['submenu'])):
                                    echo "<li class='dropdown' id='{$id}'>";
                                        echo "<a href='#'> <span class='{$menu['icon']}'></span> {$menu['name']}</a>";
                                        echo "<ul>";
                                            foreach ($menu['submenu'] as $submenu):
                                                if($submenu['view']) {
                                                  echo "<li>". $this->Html->link("<span class='{$submenu['icon']}' style='font-size: large;'></span> {$submenu['name']}", $submenu['params'], ['onclick' => 'javascript: lock();', 'escape'=>false]) ."</li>";
                                                }
                                            endforeach;
                                        echo "</ul>";
                                    echo "</li>";
                                else:
                                    if ($menu['view']):
                                        echo "<li id='{$id}' >" . $this->Html->link("<span class='{$menu['icon']}'></span> {$menu['name']}", $menu['params'], ['escape'=>false]) . "</li>";
                                    endif;
                                endif;
                            endforeach;
                        endif;
                    ?>
                    </ul>
                </div>
            </div>

            <div class="rightpanel">
                <div class="headerpanel">
                    <a href="" class="showmenu"></a>

                    <div class="headerright">

                        <div class="dropdown notification">
                            <a id="notification" class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#" title="Notifica&ccedil;&otilde;es">
                                <span class="iconsweets-alarm iconsweets-white"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-header">Notifica&ccedil;&otilde;es</li>
                                <li class="viewmore"><?php //echo $this->Html->link('Ver todas as Notifica&ccedil;&otilde;es', ['controller' => 'mains', 'action' => 'notificacoes'], ['escape'=>false]); ?></li>
                            </ul>
                        </div>

                        <div class="dropdown userinfo">
                            <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#"><?php echo $this->request->session()->read('Auth.User.nome'); ?><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li class="alterar"><?php echo $this->Html->link('<span class="iconsweets-key2"></span> Alterar Senha', ['controller' => 'seguranca-colaboradores', 'action' => 'alterar-senha'], ['escape'=>false]); ?></li>
                                <li class="divider"></li>
                                <li><a class="sair"><span class="icon-off"></span> Sair</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="breadcrumbwidget">
                    <ul class="skins">
                        <li></li>
                    </ul>
                    <ul class="breadcrumb">
                        <li></li>
                    </ul>
                </div>

                <div class="pagetitle" style="height: 38px;">
                    <h1><?php echo $description; ?></h1> <span></span>
                </div>

                <div class="maincontent">
                    <div class="contentinner <?php echo $this->request->session()->read('Auth.User.animateContent') ? 'content-dashboard' : ''; ?>" >
                        <?php echo $this->Flash->render(); ?>
                        <?php echo $this->fetch('content'); ?>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="footer">
                <div class="footerleft"></div>
                <div class="footerright"> <a href=""><?= $name; ?></a> - <?= $description; ?></div>
            </div>
        </div>
        <?php else: ?>
        <div class="loginwrapper">
            <div class="loginwrap zindex100 animate2 bounceInDown">
                <h1 class="logintitle" style="height: 40px;">
                    <?php echo $this->Html->image('seconci_logo.png', array('style' => 'position: absolute; right: 15px; width: 100px;')); ?>
                    <span class="iconfa-lock"></span> SIIS <span class="subtitle" style="font-size: 0.5em;" ><?= $description; ?></span>
                </h1>
                <?php echo $this->fetch('content'); ?>
            </div>
            <div class="loginshadow animate3 fadeInUp"></div>
        </div>
        <?php endif; ?>
    </body>
</html>
