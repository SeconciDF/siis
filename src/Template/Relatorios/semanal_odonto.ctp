<div class="row-fluid">
  <div class="span4">
    <div class="widgetcontent ">
      <?php echo $this->element('relatorio'); ?>
    </div><!--widgetcontent-->
  </div>

  <div class="span8">
    <h4 class="widgettitle nomargin">Evolu&ccedil;&atilde;o Semanal de Desempenho</h4>
    <div class="widgetcontent bordered shadowed" >
      <form method="post" autocomplete="off">
        <p>
          <label >M&ecirc;s/Ano</label>
          <span class="field">
            <input type="text" name="date" id="date" class="input-small" maxlength="7" autocomplete="off" required placeholder="mm/aaaa" />
            <!-- <input type="text" name="fim" class="input-small mask-date" autocomplete="off" required placeholder="Fim" /> -->
          </span>
        </p>

        <p>
          <label >Semana</label>
          <span class="field">
            <select class="input-medium" name="semana" required >
              <option value=""></option>
              <option value="0">Primeira semana</option>
              <option value="1">Segunda semana</option>
              <option value="2">Terceira semana</option>
              <option value="3">Quarta semana</option>
              <option value="4">Quinta semana</option>
            </select>
          </span>
        </p>

        <p>
          <label >SEDE</label>
          <span class="field">
            <input type="number" name="referencia[1][dentista]" class="input-small" required placeholder="Dentistas" />
            <input type="number" name="referencia[1][atendimentos]" class="input-small" required placeholder="Atendimento" />
            <input type="number" name="referencia[1][pontos]" class="input-small" required placeholder="Pontua&ccedil;&atilde;o" />
          </span>
        </p>

        <p>
          <label >Asa Norte</label>
          <span class="field">
            <input type="number" name="referencia[2][dentista]" class="input-small" required placeholder="Dentistas" />
            <input type="number" name="referencia[2][atendimentos]" class="input-small" required placeholder="Atendimento" />
            <input type="number" name="referencia[2][pontos]" class="input-small" required placeholder="Pontua&ccedil;&atilde;o" />
          </span>
        </p>

        <p>
          <label >Trailler</label>
          <span class="field">
            <input type="number" name="referencia[0][dentista]" class="input-small" required placeholder="Dentistas" />
            <input type="number" name="referencia[0][atendimentos]" class="input-small" required placeholder="Atendimento" />
            <input type="number" name="referencia[0][pontos]" class="input-small" required placeholder="Pontua&ccedil;&atilde;o" />
          </span>
        </p>

        <p>
          <label >Ocorr&ecirc;ncia da semana</label>
          <span class="field">
            <textarea name="ocorrencia" rows="5" class="input-xxlarge"></textarea>
          </span>
        </p>

        <p class='stdformbutton'>
          <button type="submit" class="btn btn-success btn-large">Gerar Relat&oacute;rio</button>
        </p>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#date').mask('99/9999');
    });
</script>
