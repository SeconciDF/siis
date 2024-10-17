<ul class="nav nav-list">
    <li class="nav-header">RELAT&Oacute;RIOS M&Eacute;DICOS</li>
    <li class="<?php echo $this->request->action == 'relatorioReembolsos' ? 'active' : null; ?>"><?php echo $this->Html->link('Relat&oacute;rio de reembolso', ['controller' => 'RelatorioReembolsos', 'action' => 'index'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'relatorioMedicoMensal' ? 'active' : null; ?>"><?php echo $this->Html->link('Relat&oacute;rio m&eacute;dico mensal', ['controller' => 'RelatorioMedicoMensal', 'action' => 'index'], ['escape'=>false]) ?></li>
    <li class="nav-header">RELAT&Oacute;RIOS ESTAT&Iacute;STICOS</li>
    <li class="<?php echo $this->request->action == 'estatisticoAnualMedico' ? 'active' : null; ?>"><?php echo $this->Html->link('Relat&oacute;rio m&eacute;dico anual', ['controller' => 'RelatoriosMedicos', 'action' => 'estatistico-anual-medico'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'produtividadeMedica' ? 'active' : null; ?>"><?php echo $this->Html->link('Relat&oacute;rio de produtividade M&eacute;dica', ['controller' => 'RelatoriosMedicos', 'action' => 'produtividade-medica'], ['escape'=>false]) ?></li>

</ul>
