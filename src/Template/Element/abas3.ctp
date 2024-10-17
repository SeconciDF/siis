<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class="<?php echo $this->request->controller == 'Programas' ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Programa'), ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape' => false]); ?> </li>
            <!-- <li class="<?php echo $this->request->controller == 'Ambientes' ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Ambientes de Trabalho'), ['controller' => 'ambientes', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li> -->
            <!-- <li class="<?php echo $this->request->controller == 'Condicoes' ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Condi&ccedil;&otilde;es Ambientais'), ['controller' => 'condicoes', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li> -->
            <li class="<?php echo $this->request->controller == 'ProgramasTextos' ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Documento Base'), ['controller' => 'ProgramasTextos', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>
