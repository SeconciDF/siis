<?php if (!$this->request->session()->read('Auth.User.id')): ?>
<div class="loginwrapperinner">
    <?php echo $this->Flash->render(); ?>
    <div class="usuarios form">
        <?php echo $this->Form->create('SegurancaColaboradores'); ?>
        <p class="animate4 bounceIn"><h4 style="color: #fff;">Voc&ecirc; receber&aacute; uma nova senha em seu email.</h4></p>
        <?php echo $this->Form->input('login', ['placeholder' => 'Informe o email utilizado para login', 'type' => 'email', 'required' => true, 'label' => false, 'templates' => ['inputContainer' => '<p class="animate5 bounceIn">{{content}}</p>']]) ?>
        <p class="animate6 fadeIn"><?php echo $this->Form->button(__('Enviar'), ['class' => 'btn btn-success btn-block']); ?></p>
        <?php echo $this->Form->end(); ?>
        <p class="animat7 fadeIn" style="text-align: right;"><a href="./login" style="color: #fff;">Voltar para o login</a></p>
    </div>
</div>
<?php endif; ?>
