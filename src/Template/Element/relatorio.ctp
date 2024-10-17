<ul class="nav nav-list">
    <li class="nav-header">RELATÓRIOS DE CONSULTAS</li>
    <li class="<?php echo $this->request->action == 'consultaRealizadaEmpresa' ? 'active' : null; ?>"><?php echo $this->Html->link('Consultas realizadas por empresa', ['action' => 'consulta-realizada-empresa'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'pacientesFaltas' ? 'active' : null; ?>"><?php echo $this->Html->link('Falta &agrave;s consultas por empresa', ['action' => 'pacientes-faltas'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'pacientesEmpresa' ? 'active' : null; ?>"><?php echo $this->Html->link('Pacientes por empresa', ['action' => 'pacientes-empresa'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'pacientesProfissional' ? 'active' : null; ?>"><?php echo $this->Html->link('Pacientes por profissional', ['action' => 'pacientes-profissional'], ['escape'=>false]) ?></li>
    <li class="nav-header">RELATÓRIOS ESTAT&Iacute;STICOS</li>
    <li class="<?php echo $this->request->action == 'demonstrativoOdontologico' ? 'active' : null; ?>"><?php echo $this->Html->link('Demonstrativo Odontol&oacute;gico', ['action' => 'demonstrativo-odontologico'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'sinteticoAnualOdonto' ? 'active' : null; ?>"><?php echo $this->Html->link('Sintético Anual Odontol&oacute;gico', ['action' => 'sintetico-anual-odonto'], ['escape'=>false]) ?></li>
    <li class="nav-header">RELATÓRIOS DE PRODU&Ccedil;&Atilde;O</li>
    <li class="<?php echo $this->request->action == 'odontoProducaoIndividual' ? 'active' : null; ?>"><?php echo $this->Html->link('Produ&ccedil;&atilde;o Odontol&oacute;gico', ['action' => 'odonto-producao-individual'], ['escape'=>false]) ?></li>
    <li class="<?php echo $this->request->action == 'semanalOdonto' ? 'active' : null; ?>"><?php echo $this->Html->link('Semanal Atendimentos/Produ&ccedil;&atilde;o', ['action' => 'semanal-odonto'], ['escape'=>false]) ?></li>
</ul>
