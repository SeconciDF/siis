<?php if (!$this->request->session()->read('Auth.User.id')): ?>
<div class="loginwrapperinner">
    <?php echo $this->Flash->render(); ?>
    <div class="usuarios form">
        <?php echo $this->Form->create('SegurancaColaboradores'); ?>
        <?php echo $this->Form->input('login', ['placeholder' => 'Email', 'type' => 'email', 'required' => true, 'label' => false, 'templates' => ['inputContainer' => '<p class="animate5 bounceIn">{{content}}</p>']]) ?>
        <?php echo $this->Form->input('senha', ['placeholder' => 'Senha', 'type' => 'password', 'required' => true, 'label' => false, 'templates' => ['inputContainer' => '<p class="animate6 bounceIn">{{content}}</p>']]) ?>
        <center>
            <!--<div title="LocaWeb" class="g-recaptcha" data-sitekey="6LeANRAUAAAAAPCPlu6Fkd8PpPOycMrtUjeFfQ5S"></div>-->
            <!-- <div title="UOLHost" class="g-recaptcha" data-sitekey="6LfuPBAUAAAAAIhsuOAfAgaZncAIae05uKPohgeS"></div> -->
        </center>
        <p class="animate7 fadeIn"><?php echo $this->Form->button(__('Entrar'), ['class' => 'btn btn-success btn-block logar']); ?></p>
        <?php echo $this->Form->end(); ?>
        <p class="animate7 fadeIn"></p>

        <p class="animate7 fadeIn">
            <a href="./esqueci" style="color: #fff;">Esqueci minha senha</a>
        </p>
    </div>
</div>

<script language="javascript">
    jQuery(document).ready(function () {
        jQuery('#tt_emal').focus();
        jQuery('form').submit(function () {
            jQuery('button.logar').text('Acessando...').attr('disabled', true);
        });
    });
</script>
<?php endif; ?>
