<?php
    /*
     *
     */
?>

<h4 class="widgettitle nomargin">Anexos do Edital</h4>
<div class="widgetcontent bordered" style="background: #fff; padding: 15px;">

        <div class="mediamgr_rightinner">
            <ul class="menuright">
                <?php if($this->request->session()->read('Auth.User.idConvenio') == '1') { ?>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_i_-_confirmacao_de_interesse_em_participar_e_indicacao_responsavel_tecnico.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo I - Confirma&ccedil;&atilde;o de Interesse em Participar e Indica&ccedil;&atilde;o Respons&aacute;vel T&eacute;cnico</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_ii_-_oficio_de_encaminhamento.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo II - Of&iacute;cio de encaminhamento</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_iii_-_proposta.pdf"><span class="iconsweets-download2"></span> &nbsp; Anexo III - Proposta  </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_iv_-_tabela_de_valores_de_referencia_rh.pdf"><span class="iconsweets-download2"></span> &nbsp; Anexo IV - Tabela de Valores de Refer&ecirc;ncia RH</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_v_-filiacao_com_a_erad_ou_enad.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo V- Filia&ccedil;&atilde;o com a ERAD ou ENAD</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_vi_-capacidade_tecnica.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo VI - Capacidade t&eacute;cnica</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_vii_-que_as_informacoes_prestadas_serem_verdadeiras.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo VII  -Que as informa&ccedil;&otilde;es prestadas serem verdadeiras</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_viii_-declaracao_de_vedacao_ou_impedimento.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo VIII - Declara&ccedil;&atilde;o de veda&ccedil;&atilde;o ou impedimento</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_ix_-nao_recebe_outros_recursos.docx"><span class="iconsweets-download2"></span> &nbsp; Anexo IX - N&atilde;o recebe outros recursos</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_x_-convenio_de_colaboracao_minuta_edital_ 6_ 22_ 06_ 16_versao_final.pdf"><span class="iconsweets-download2"></span> &nbsp; Anexo X  -Conv&ecirc;nio de Colabora&ccedil;&atilde;o - Minuta do Edital</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_xi_-_plano_de_trabalho.pdf"><span class="iconsweets-download2"></span> &nbsp; Anexo XI -  Plano de trabalho </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo_xii_-plano_simplificado_de_aplicacao.pdf"><span class="iconsweets-download2"></span> &nbsp; Anexo XII - Plano Simplificado de Aplica&ccedil;&atilde;o</a></li>

                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>declaracao-cumprimento-carga-horaria-mensal.docx"><span class="iconsweets-download2"></span> &nbsp; Declara&ccedil;&atilde;o - Cumprimento de Carga Hor&aacute;ria mensal</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>declaracao-liquidacao-bancaria-inss-fgts-ir-anual.docx"><span class="iconsweets-download2"></span> &nbsp; Declara&ccedil;&atilde;o - Liquida&ccedil;&atilde;o Banc&aacute;ria INSS, FGTS e IR anual </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>declaracao-registro-conselho-classe-profissional-anual.docx"><span class="iconsweets-download2"></span> &nbsp; Declara&ccedil;&atilde;o - Registro no Conselho de Classe Profissional anual</a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>relatorio-mensal-atividades.doc"><span class="iconsweets-download2"></span> &nbsp; Relat&oacute;rio Mensal de Atividades </a></li>	
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/anexos/'); ?>anexo-termo-guarda-documentos.docx"><span class="iconsweets-download2"></span> &nbsp; Termo de Guarda dos Documentos </a></li>	
				
                <?php } ?>

                <?php if($this->request->session()->read('Auth.User.idConvenio') == '2') { ?>

                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-3-oficio-manifestacao-de-interesse-projetos-de-campeonatos.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 3 -  Of&iacute;cio manifesta&ccedil;&atilde;o de interesse - Projetos de Campeonatos </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-4-oficio-manifestacao-de-interesse-projetos-de-equipamentos.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 4 -  Of&iacute;cio manifesta&ccedil;&atilde;o de interesse - Projeto Equipamentos </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-5-oficio-de-encaminhamento-dos-projetos-campeonatos.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 5 - Of&iacute;co de encaminhamento do Projeto - Campeonatos </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-6-oficio-de-encaminhamento-dos-projetos-equipamentos.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 6 - Of&iacute;co de encaminhamento do Projeto - Equipamentos </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-7-declaracao-de-filiacao-do-clube-junto-a-erad.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 7 - Declara&ccedil;&atilde;o de Filia&ccedil;&atilde;o do Clube junto &agrave; ERAD </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-8-declaracao-de-capacidade-tecnica-e-operacional.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 8 - Declara&ccedil;&atilde;o de Capacidade T&eacute;cnica e Operacional </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-9-declaracao-de-veracidade-de-informacoes-art.-299.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 9 - Declara&ccedil;&atilde;o de veracidade de informa&ccedil;&otilde;es - Art. 299 </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-10-declaracao-de-nao-recebimento-de-outros-recursos.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 10 - Declara&ccedil;&atilde;o de n&atilde;o recebimento de outros recursos </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-11-termo-de-indicacao-do-responsavel-tecnico.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 11 - Termo de indica&ccedil;&atilde;o do respons&aacute;vel t&eacute;cnico </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-12-termo-de-guarda-de-documentos-originais.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 12 - Termo de guarda de documentos originais </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-13-declaracao-de-nfraestrutura-propria.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 13 - Declara&ccedil;&atilde;o de Infraestrutura própria </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-14-termo-de-cessao-de-espaco-fisico.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 14 - Termo de Cess&atilde;o de Espa&ccedil;o F&iacute;sico </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-15-declaracao-da-enad-informando-que-campeonato-esta-previst-no-calendario-anual.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 15 -  Declara&ccedil;&atilde;o da ENAD informando que  Campeonato est&aacute; Previsto no Calend&aacute;rio Anual </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-16-declaracao-da-enad-autorizando-epd-filiada.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 16 - Declara&ccedil;&atilde;o da ENAD autorizando EPD filiada </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-17-declaracao-da-enad-autorizando-outra-entidade.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 17 -  Declara&ccedil;&atilde;o da ENAD  autorizando outra entidad </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-18-declaracao-da-outra-entidade-autorizando-a-epd-filiada.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 18 -  Declara&ccedil;&atilde;o da outra entidade autorizando a EPD filiada </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-19-declaracao-de-compromisso-com-outras-despesas.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 19 - Declara&ccedil;&atilde;o de compromisso com outras despesas </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-20-declaracao-para-instalacao-de-equipamentos.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 20 - Declara&ccedil;&atilde;o para instala&ccedil;&atilde;o de equipamentos </a></li>
                    <li><a download href="<?php echo $this->Url->build('/webroot/download/edital07/competicoes/'); ?>anexo-21-modelo-de-orcamento.docx"><span class="iconsweets-download2"></span> &nbsp; ANEXO 21 - Modelo de Or&ccedil;amento </a></li>
	

                <?php } ?>
            </ul>
        </div>
</div>
