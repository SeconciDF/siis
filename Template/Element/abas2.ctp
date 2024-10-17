<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li class="<?php echo in_array($this->request->controller, ['Programas', 'ProgramasTextos']) ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Programa'), ['controller' => 'programas', 'action' => 'edit', $programa['id']], ['escape' => false]); ?> </li>
            <li class="<?php echo in_array($this->request->controller, ['Ambientes','AmbientesSetores','AmbientesProcessos','AmbientesGrupos','AmbientesQuimicos']) ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Caracteriza&ccedil;&atilde;o do Ambiente'), ['controller' => 'Ambientes', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li>
            <li class="<?php echo in_array($this->request->controller, ['ProgramasPerigosDanos']) ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Identifica&ccedil;&atilde;o dos Perigos e Danos'), ['controller' => 'ProgramasPerigosDanos', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li>
            <li class="<?php echo in_array($this->request->controller, ['ProgramasMedidasControles','ProgramasPlanosAcoes','ProgramasPerfilExposicoes','ProgramasAvaliacoesRiscos','ProgramasEpi']) ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Medidas de controle e Avalia&ccedil;&atilde;o dos riscos'), ['controller' => 'ProgramasMedidasControles', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li>
            <li class="<?php echo in_array($this->request->controller, ['ProgramasAvaliacoesQuantitativas']) ? 'active' : ''; ?>"> <?php echo $this->Html->link(__('Avalia&ccedil;&atilde;o Quantitativa'), ['controller' => 'ProgramasAvaliacoesQuantitativas', 'action' => 'index', $programa['id']], ['escape' => false]); ?> </li>
        </ul>
    </div>
</div>
