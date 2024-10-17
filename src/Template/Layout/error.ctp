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
    echo $this->Html->css('style');

    echo $this->Html->script('/bootstrap/prettify/prettify');
    echo $this->Html->script('/bootstrap/js/jquery-1.9.1.min');

    echo $this->Html->script('custom');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
    <div id="container">
      <div class="loginwrapper">
          <div class="loginwrap zindex100  bounceInDown">
              <?php echo $this->fetch('content'); ?>
          </div>
      </div>
    </div>
</body>
</html>
