<?php
  $bloqueado = $this->request->session()->read('Auth.User.bloqueado');
?>
<div class="mediamgr_head">
    <ul class=" mediamgr_menu">
        <?php if(!$bloqueado) { ?>
          <li class="marginleft15 right">
            <?php echo $this->Html->link('<span class="iconfa-ok" style="font-size: medium;"></span> Finalizar Atendimento', ['action' => 'finalizar', $consulta['id']], ['class' => 'btn btn-popup', 'escape'=>false]); ?>
          </li>
        <?php } ?>

        <li class="marginleft15 right">
          <?php echo $this->Html->link('<span class="iconfa-print" style="font-size: medium;"></span> Imprimir', ['action' => 'imprimir', $consulta['id']], ['class' => 'btn', 'target' => 'blank', 'escape'=>false]); ?>
        </li>
    </ul>
    <span class="clearall"></span>
</div>

<?php
if(sizeof($referencia) >= 2 && $this->request->query('referencia')) {
  $bloqueado = true;
} else if(isset($referencia[0]) && $this->request->query('referencia')) {
  if(!$referencia[0]['referencia']) {
    $bloqueado = true;
  }
}
?>

<h4 class="widgettitle nomargin shadowed">Consulta</h4>
<div class="navbar">
  <div class="navbar-inner">
    <ul class="nav">
      <li class=""> <?php echo $this->Html->link(__('Anamnese'), ['action' => 'anamnese', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Prontu&aacute;rio'), ['action' => 'prontuario', $consulta['id']], ['escape' => false]); ?> </li>
      <li class="<?php echo $this->request->query('referencia') ? 'active' : '' ?>"> <?php echo $this->Html->link(__('Odontograma de Referência'), ['action' => 'odontograma', $consulta['id'], 'referencia' => '1'], ['escape' => false]); ?> </li>
      <li class="<?php echo $this->request->query('referencia') ? '' : 'active' ?>"> <?php echo $this->Html->link(__('Odontograma'), ['action' => 'odontograma', $consulta['id']], ['escape' => false]); ?> </li>
      <li class=""> <?php echo $this->Html->link(__('Conclusão'), ['action' => 'conclusao', $consulta['id']], ['escape' => false]); ?> </li>
    </ul>
  </div>
</div>

<div class="widgetcontent bordered shadowed padding">
  <table id="odontograma" class="odontograma" style="width: 100%;">
    <tr>
      <td style="text-align: right;" colspan="16">
          <small style="width: 12px; height: 12px; background: #6B8F59; display: inline-block;"></small> Procedimentos Realizados &nbsp;
          <small style="width: 12px; height: 12px; background: #E35313; display: inline-block;"></small> Procedimentos Previstos <br/><br/>
      </td>
    </tr>
    <tr>
      <td colspan="16" style="text-align: left;"> <h3>ODONTOGRAMA</h3> </td>
    </tr>
    <tr>
      <td>18</td> <td>17</td> <td>16</td> <td>15</td> <td>14</td> <td>13</td> <td>12</td> <td>11</td>
      <td style="border-left: solid 1px #000;">21</td> <td>22</td> <td>23</td> <td>24</td> <td>25</td> <td>26</td> <td>27</td> <td>28</td>
    </tr>
    <tr>
      <td><?php echo $this->Html->image('dentes/dente_18.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_17.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_16.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_15.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_14.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_13.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_12.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_11.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td style="border-left: solid 1px #000;"><?php echo $this->Html->image('dentes/dente_21.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_22.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_23.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_24.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_25.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_26.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_27.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_28.png', ['style' => 'margin: 0 auto;']); ?></td>
    </tr>
    <tr>
      <?php
      for ($i=18; $i >= 11; $i--) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='V{$i}'></div> <div style='float: left' class='D{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='M{$i}'></div> <div style='margin: 12px auto;' class='P{$i}'></div> </div> </td>";
      }
      for ($i=21; $i <= 28; $i++) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='V{$i}'></div> <div style='float: left' class='M{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='D{$i}'></div> <div style='margin: 12px auto;' class='P{$i}'></div> </div> </td>";
      }
      ?>
    </tr>
    <tr>
      <td></td> <td></td> <td></td> <td>55</td> <td>54</td> <td>53</td> <td>52</td> <td>51</td>
      <td style="border-left: solid 1px #000;">61</td> <td>62</td> <td>63</td> <td>64</td> <td>65</td> <td></td> <td></td> <td></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td><?php echo $this->Html->image('dentes/dente_55.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_54.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_53.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_52.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_51.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td style="border-left: solid 1px #000;"><?php echo $this->Html->image('dentes/dente_61.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_62.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_63.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_64.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_65.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <?php
      for ($i=55; $i >= 51; $i--) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='V{$i}'></div> <div style='float: left' class='D{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='M{$i}'></div> <div style='margin: 12px auto;' class='P{$i}'></div> </div> </td>";
      }
      for ($i=61; $i <= 65; $i++) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='V{$i}'></div> <div style='float: left' class='M{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='D{$i}'></div> <div style='margin: 12px auto;' class='P{$i}'></div> </div> </td>";
      }
      ?>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr style="border-top: solid 1px #000;">
      <td></td> <td></td> <td></td> <td>85</td> <td>84</td> <td>83</td> <td>82</td> <td>81</td>
      <td style="border-left: solid 1px #000;">71</td> <td>72</td> <td>73</td> <td>74</td> <td>75</td> <td></td> <td></td> <td></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td><?php echo $this->Html->image('dentes/dente_85.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_84.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_83.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_82.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_81.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td style="border-left: solid 1px #000;"><?php echo $this->Html->image('dentes/dente_71.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_72.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_73.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_74.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_75.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <?php
      for ($i=85; $i >= 81; $i--) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='L{$i}'></div> <div style='float: left' class='D{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='M{$i}'></div> <div style='margin: 12px auto;' class='V{$i}'></div> </div> </td>";
      }
      for ($i=71; $i <= 75; $i++) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='L{$i}'></div> <div style='float: left' class='M{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='D{$i}'></div> <div style='margin: 12px auto;' class='V{$i}'></div> </div> </td>";
      }
      ?>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>48</td> <td>47</td> <td>46</td> <td>45</td> <td>44</td> <td>43</td> <td>42</td> <td>41</td>
      <td style="border-left: solid 1px #000;">31</td> <td>32</td> <td>33</td> <td>34</td> <td>35</td> <td>36</td> <td>37</td> <td>38</td>
    </tr>
    <tr>
      <td><?php echo $this->Html->image('dentes/dente_48.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_47.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_46.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_45.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_44.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_43.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_42.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_41.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td style="border-left: solid 1px #000;"><?php echo $this->Html->image('dentes/dente_31.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_32.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_33.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_34.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_35.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_36.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_37.png', ['style' => 'margin: 0 auto;']); ?></td>
      <td><?php echo $this->Html->image('dentes/dente_38.png', ['style' => 'margin: 0 auto;']); ?></td>
    </tr>
    <tr>
      <?php
      for ($i=48; $i >= 41; $i--) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='L{$i}'></div> <div style='float: left' class='D{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='M{$i}'></div> <div style='margin: 12px auto;' class='V{$i}'></div> </div> </td>";
      }
      for ($i=31; $i <= 38; $i++) {
        echo "<td> <div style='width: 34px; margin: 0 auto;'> <div style='margin: 0 auto;' class='L{$i}'></div> <div style='float: left' class='M{$i}'></div><div  style='float: left' class='O{$i}'></div><div  style='float: left;' class='D{$i}'></div> <div style='margin: 12px auto;' class='V{$i}'></div> </div> </td>";
      }
      ?>
    </tr>
    <?php if(!$bloqueado) { ?>
      <tr>
        <td colspan="16" style="text-align: left; background-color: #E8E8E8; padding: 5px 0 0 15px; ">
          <?php if(!$bloqueado) { ?>
            <?php //echo $this->Html->link('Adicionar Procedimento', ['action' => 'procedimento', $consulta['id'], 'referencia' => $this->request->query('referencia')], ['class' => 'btn btn-inverse btn-popup', 'escape'=>false]) ?>
            <?php if($this->request->query('referencia')) { ?>
              <?php echo $this->Form->postLink('Encerrar Odontograma', ['action' => 'encerrar', $consulta['id'], 'referencia' => $this->request->query('referencia')], ['class' => 'btn btn-inverse', 'style' => 'float: right; margin: 25px 15px 0 0;', 'confirm' => "Deseja realmente encerrar o Odontograma de Referência?", 'escape'=>false]); ?>
            <?php } ?>
          <?php } ?>

          <?php
            echo $this->Form->create('OdontoOdontogramas', ['id' => 'procedimento-form', 'url' => ['controller' => 'Atendimentos', 'action' => 'procedimento', $consulta['id'], 'referencia' => $this->request->query('referencia')]]);
            echo $this->Form->input('data_hora_registro', ['type' => 'hidden', 'value' => date('Y-m-d H:i:s')]);
            echo $this->Form->input('profissionais_id', ['type' => 'hidden', 'value' => $consulta['profissionais_id']]);
            echo $this->Form->input('beneficiarios_id', ['type' => 'hidden', 'value' => $consulta['beneficiarios_id']]);
            echo $this->Form->input('dependentes_id', ['type' => 'hidden', 'value' => $consulta['dependentes_id']]);
            echo $this->Form->input('consultas_id', ['type' => 'hidden', 'value' => $consulta['id']]);
            if($this->request->query('referencia')) {
              echo $this->Form->input('referencia', ['type' => 'hidden', 'value' => $this->request->query('referencia')]);
            } else {
                echo $this->Form->input('referencia', ['type' => 'hidden', 'value' => '0']);
            }
          ?>
          <p>
            <span class="field">
                <input type="hidden" id="boca-dente" name="procedimento[boca_dente]" />
                <small style="width: 100px;">Dente</small><small style="width: 60px;">Faces</small><small style="width: 160px;">Procedimento</small><small>Quantidade</small><br/>
                <input type="number" id="procedimento-dente" min="11" max="85"  name="procedimento[dentes_id]" class="input-mini" value="<?php echo $this->request->session()->read('Auth.User.filtro.dente'); ?>"/>

                <div class="faces">
                  <p style="border: 0; margin-bottom: -22px; text-align: center;">
                    <input type="checkbox" name="" class="top" value="1" style="margin: 0;" />
                  </p>
                  <p style="border: 0; padding-left: 2px; text-align: center;">
                    <input type="checkbox" name="" class="left" value="1" style="margin: -1px;" />
                    <input type="checkbox" name="" class="center" value="1" style="margin: -2px;" />
                    <input type="checkbox" name="" class="right" value="1" style="margin: 0;" />
                  </p>
                  <p style="border: 0; margin-top: -22px; text-align: center;">
                    <input type="checkbox" name="" class="bottom" value="1" style="margin: 0;" />
                  </p>
                </div>

                <input type="text" id="procedimento-id" min="0" name="procedimento[procedimentos_id]"  class="input-small" required style="margin: -45px 0 0 158px; float: left;" />
                <input type="number" id="procedimento-previsto" min="0" name="procedimento[total_previsto]" class="input-mini" required style="margin: -45px 0 0 320px; float: left;" />
                <a href="#" id="btn-search-procedimento" onclick="javascript: return false;" class="btn" style="margin: -45px 0 0 260px; float: left;" ><span class="icon-search"></span></a>
                <div id="procedimento-nome" style="height: 15px; margin: -12px 0 0 160px;"></div>
            </span>
          </p>
          <?php echo $this->Form->end(); ?>
        </td>
      </tr>
    <?php } ?>
  </table>

  <br/>

  <h3>PROCEDIMENTOS PREVISTOS</h3>

  <?php foreach ($odontogramas as $dente => $odontograma) { ?>
    <div style="padding: 5px; font-size: 1.2em; color: #000;">
      <?php if($dente) { ?>
        Procedimento no dente: <b><?php echo $dente; ?></b>
      <?php } else { ?>
        Procedimento na boca
      <?php } ?>
    </div>
    <table class="table table-bordered procedimentos" style="width: 100%;">
      <tr>
        <th style="width: 60px; text-align: center;">C&oacute;digo</th>
        <th>Procedimento</th>
        <?php if($dente) { ?>
          <th style="width: 70px; text-align: center;">Faces</th>
        <?php } ?>
        <th style="width: 80px; text-align: center;">Qt. Prevista</th>
        <?php if(!$this->request->query('referencia')) { ?>
          <th style="width: 80px; text-align: center;">Feito Hoje</th>
          <th style="width: 80px; text-align: center;">Realizado</th>
        <?php } ?>

        <?php if(!$bloqueado) { ?>
          <th style="width: 60px; text-align: center;">A&ccedil;&otilde;es</th>
        <?php } ?>
      </tr>

      <?php foreach ($odontograma as $value) { ?>
        <tr>
          <td style="text-align: center;"><?php echo $value['codigo']; ?></td>
          <td><?php echo $value['procedimento']; ?></td>
          <?php if($dente) { ?>
            <td style="text-align: center;"><?php echo $value['face']; ?></td>
          <?php } ?>

          <?php if(!$bloqueado) { ?>
            <td style="text-align: center;">
              <input type="number" class="previsto field" min="1" data-procedimento="<?php echo $value['id']; ?>" data-value="<?php echo $value['previsto'] ? $value['previsto'] : ''; ?>" value="<?php echo $value['previsto'] ? $value['previsto'] : ''; ?>" />
            </td>
          <?php } else { ?>
            <td style="text-align: center;" class="previsto"><?php echo $value['previsto'] ? $value['previsto'] : ''; ?></td>
          <?php } ?>

          <?php if(!$this->request->query('referencia') && !$bloqueado) { ?>
            <td style="text-align: center;">
              <input type="number" class="feito-hoje field" min="1" data-procedimento="<?php echo $value['id']; ?>" data-value="<?php echo $value['feito_hoje'] ? $value['feito_hoje'] : ''; ?>"  value="<?php echo $value['feito_hoje'] ? $value['feito_hoje'] : ''; ?>" />
            </td>
            <td style="text-align: center;" class="realizado"><?php echo $value['realizado'] ? $value['realizado'] : ''; ?></td>
          <?php } else if (!$this->request->query('referencia')) { ?>
            <td style="text-align: center;" class="feito-hoje"><?php echo $value['feito_hoje'] ? $value['feito_hoje'] : ''; ?></td>
            <td style="text-align: center;" class="realizado"><?php echo $value['realizado'] ? $value['realizado'] : ''; ?></td>
          <?php } ?>

          <?php if(!$bloqueado) { ?>
            <td style="text-align: center;">
              <?php if(!$this->request->query('referencia')) { ?>
                <?php echo $this->Html->link('<span class="icon-edit"></span>', ['action' => 'procedimento', $consulta['id'], $value['id']], ['class' => 'btn-popup', 'style' => 'font-size: medium;', 'escape'=>false]); ?> &nbsp;&nbsp;&nbsp;
              <?php } ?>
              <?php echo $this->Form->postLink('<span class="iconfa-trash"></span>', ['action' => 'delete', $value['id'], $consulta['id'], 'referencia' => $this->request->query('referencia')], ['confirm' => "Deseja excluir {$value['procedimento']}?", 'title' => 'Deletar', 'style' => 'color: red; font-size: large;', 'escape'=>false]); ?>
            </td>
          <?php } ?>
        </tr>
      <?php } ?>
    </table>
  <?php } ?>
</div>

<?php echo $this->Html->css('jquery-ui'); ?>
<input type="hidden" id="profissional" value="<?php echo $consulta['Profissional']['nome']; ?>" />
<textarea id="procedimentos" style="display: none;"><?php echo json_encode($procedimentos); ?></textarea>
<textarea id="dentes" style="display: none;"><?php echo json_encode($dentes); ?></textarea>
<?php
  $procs=[];
  foreach ($procedimentos as $key => $value) {
    $procs[$value['procedimento']] = $key;
  }
?>
<textarea id="procs" style="display: none;"><?php echo json_encode($procs); ?></textarea>
<style media="screen">
table.odontograma tr td { text-align: center; color: #000; }
table.odontograma tr td div div { border: 1px solid #000; width: 8px; height: 8px; margin: 1px 0 0 1px; }
div.faces { position: absolute; margin: -54px 0 0 90px; width: 50px; height: 50px; }
span.field small { display: inline-block; }
input.field { width: 60px; text-align: center; border: none; outline:none; border-bottom: 1px solid #000; }
input.success { border-bottom: 2px solid #00FF00 !important; }
input.error { border-bottom: 2px solid #FF0000 !important; }
.jGrowl .siis { background-color: #FFF1C2; color: #FFFFFF; }
</style>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  let procedimentos = JSON.parse(jQuery('#procedimentos').val());
  let dentes = JSON.parse(jQuery('#dentes').val());

  jQuery("a.btn-popup").colorbox({
    escKey: false,
    overlayClose: false,
    onLoad: function() {
      jQuery('#cboxClose, #cboxTitle, #cboxCurrent, #cboxPrevious, #cboxNext').remove();
    }
  });

  jQuery('#btn-search-procedimento').click(function() {
    var options = '<option value="">Nenhum</option>';
    jQuery.each(JSON.parse(jQuery('#procs').val()), function(i, item) {
      options += '<option value="'+item+'">'+i+'</option>';
    });

    jAlert('<select id="procedimentos" style="width: 100%;" onclick="carregarProceimento(this.value);">'+options+'</select>', 'Procedimentos');
  });

  jQuery.each(dentes, function(i, item) {
    jQuery.each(item.previsto, function( index, value ) {
      jQuery('.'+value+i).css('background', '#E35313');
    });
    jQuery.each(item.executado, function( index, value ) {
      jQuery('.'+value+i).css('background', '#6B8F59');
    });
  });

  var procedimentoTags = [];
  jQuery.each(procedimentos, function(i, item) {
    procedimentoTags.push(("000000" + i).slice (-6));
  });

  jQuery('#procedimento-id').autocomplete({
    source: procedimentoTags
  });

  jQuery('img').click(function() {
    let dente = jQuery(this).attr('src').match(/\d+/)[0];
    jQuery("#procedimento-dente").val(dente).focus();
  });

  jQuery("#procedimento-id, #procedimento-dente, #procedimento-previsto").onEnter( function() {
    let faces = false;
    jQuery.each(jQuery("input[type=checkbox]"), function(i, item) {
      if(jQuery(item).is(':checked')) {
        faces = jQuery(item).is(':checked');
      }
    });

    if(!jQuery("#procedimento-id").val()) {
      jQuery("#procedimento-id").focus();
    } else if(!jQuery("#procedimento-previsto").val()) {
      jQuery("#procedimento-previsto").focus();
    } else if(!jQuery("#procedimento-dente").val() && jQuery("#boca-dente").val() !== '1') {
      jQuery("#procedimento-dente").focus();
    } else if(faces === false && jQuery("#boca-dente").val() !== '1') {
      jQuery("input[type=checkbox]").focus();
    } else {
      jQuery('#procedimento-form').submit();
    }
  });

  jQuery("#procedimento-id").blur(function() {
    let key = jQuery(this).val();
    if(procedimentos.hasOwnProperty(parseInt(key))) {
      jQuery("#boca-dente").val(procedimentos[parseInt(key)].dente);
      jQuery("#procedimento-nome").html(procedimentos[parseInt(key)].procedimento);
      jQuery('#procedimento-dente').attr('required', true);
      if(procedimentos[parseInt(key)].dente === '1') {
        jQuery('#procedimento-dente').val('').attr('required', false);
        jQuery('input[type=checkbox]').attr('checked', false);
      }
    } else {
      jQuery("#procedimento-id").val('');
    }
  });

  jQuery("input.previsto").onEnter(function() {
    var self = this;
    if(!jQuery(self).val()) {
      return false;
    }

    if(parseInt(jQuery(self).data('value')) === parseInt(jQuery(self).val())) {
      return false;
    }

    jQuery(self).parent().append('<?php echo $this->Html->image('loader1.gif', array('style' => 'position: absolute; margin: -24px 0 0 8px;')); ?>');
    jQuery.post('<?php echo $this->Url->build(["action" => "atualizar-previsto"], true); ?>', {
        id: jQuery(self).data('procedimento'),
        total_previsto: jQuery(self).val()
    },function(data, status) {
        jQuery(self).parent().find('img').remove();
        if(data.success) {
          jQuery(self).removeClass('error').addClass('success');
        } else if(data.error) {
          jQuery(self).removeClass('success').addClass('error');
          if(data.hasOwnProperty('msg')) {
            jQuery.jGrowl(data.msg, {theme: 'siis', sticky: true, position: 'bottom-right'});
          }
        }
    }, 'json');
  });

  jQuery("input.feito-hoje").onEnter(function() {
    var self = this;
    if(!jQuery(self).val()) {
      return false;
    }

    if(parseInt(jQuery(self).data('value')) === parseInt(jQuery(self).val())) {
      return false;
    }

    jQuery(self).parent().append('<?php echo $this->Html->image('loader1.gif', array('style' => 'position: absolute; margin: -24px 0 0 8px;')); ?>');
    jQuery.post('<?php echo $this->Url->build(["action" => "atualizar-feito-hoje"], true); ?>', {
        id: jQuery(self).data('procedimento'),
        total_realizado_hoje: jQuery(self).val(),
        profissionais_id: jQuery('#profissionais-id').val(),
        profissional: jQuery('#profissional').val()
    },function(data, status) {
        jQuery(self).parent().find('img').remove();
        if(data.success) {
          jQuery(self).removeClass('error').addClass('success');
          if(data.hasOwnProperty('total_executado')) {
            jQuery(self).parent().parent().find('td.realizado').html(data.total_executado);
          }
        } else if(data.error) {
          jQuery(self).removeClass('success').addClass('error');
          if(data.hasOwnProperty('msg')) {
            jQuery.jGrowl(data.msg, {theme: 'siis', sticky: true, position: 'bottom-right'});
          }
        }
    }, 'json');
  });

  jQuery("#procedimento-dente").blur(function() {
    if(!jQuery("#procedimento-dente").val()) {
      return false;
    }
    let dente = jQuery("#procedimento-dente").val();
    jQuery('input[type=checkbox]').attr('name', '');

    if(jQuery.inArray(dente, ['11','12','13','14','15','16','17','18','51','52','53','54','55']) >= 0) {
      jQuery('input[type=checkbox].top').attr('name', 'procedimento[face_vestibular]');
      jQuery('input[type=checkbox].left').attr('name', 'procedimento[face_distal]');
      jQuery('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]');
      jQuery('input[type=checkbox].right').attr('name', 'procedimento[face_mesial]');
      jQuery('input[type=checkbox].bottom').attr('name', 'procedimento[face_palatina]');
    }

    if(jQuery.inArray(dente, ['21','22','23','24','25','26','27','28','61','62','63','64','65']) >= 0) {
      jQuery('input[type=checkbox].top').attr('name', 'procedimento[face_vestibular]');
      jQuery('input[type=checkbox].left').attr('name', 'procedimento[face_mesial]');
      jQuery('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]');
      jQuery('input[type=checkbox].right').attr('name', 'procedimento[face_distal]');
      jQuery('input[type=checkbox].bottom').attr('name', 'procedimento[face_palatina]');
    }

    if(jQuery.inArray(dente, ['41','42','43','44','45','46','47','48','81','82','83','84','85']) >= 0) {
      jQuery('input[type=checkbox].top').attr('name', 'procedimento[face_lingual]');
      jQuery('input[type=checkbox].left').attr('name', 'procedimento[face_distal]');
      jQuery('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]');
      jQuery('input[type=checkbox].right').attr('name', 'procedimento[face_mesial]');
      jQuery('input[type=checkbox].bottom').attr('name', 'procedimento[face_vestibular]');
    }

    if(jQuery.inArray(dente, ['31','32','33','34','35','36','37','38','71','72','73','74','75']) >= 0) {
      jQuery('input[type=checkbox].top').attr('name', 'procedimento[face_lingual]');
      jQuery('input[type=checkbox].left').attr('name', 'procedimento[face_mesial]');
      jQuery('input[type=checkbox].center').attr('name', 'procedimento[face_oclusal]');
      jQuery('input[type=checkbox].right').attr('name', 'procedimento[face_distal]');
      jQuery('input[type=checkbox].bottom').attr('name', 'procedimento[face_vestibular]');
    }
  }).blur();

});

function carregarProceimento(e) {
  jQuery("#procedimento-id").val(e).blur();
}
</script>
