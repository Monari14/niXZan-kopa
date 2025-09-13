<h2>Configurações</h2>
<br>
<div class="tabela-responsive">
  <table id="settings" border="1" style="width:100%; border-collapse:collapse;">
    <thead>
      <tr>
        <th style="display:none;">ID</th>
        <th>Status loja</th>
        <th>Valor Adicional</th>
        <th>Abertura</th>
        <th>Fechamento</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

<button id="editarSettingsBtn" style="margin-top:10px;">Editar Configurações</button>

<div id="editarSettingsModal" style="display:none; margin-top:10px; border:1px solid #ccc; padding:10px;">
<span class="close" id="fecharEditarSettings">&times;</span>
<br>
  <form id="editarSettingsForm">
    <label>
      Status loja:
      <select id="status_loja" name="status_aberto">
        <option value="1">Aberto</option>
        <option value="0">Fechado</option>
      </select>
    </label>

    <label>
      Valor Adicional:
      <input type="number" step="0.01" id="valor_adicional" name="valor_adicional_pedido" required>
    </label>

    <label>
      Horário Abertura:
      <input type="time" id="horario_abertura" name="horario_abertura">
    </label>

    <label>
      Horário Fechamento:
      <input type="time" id="horario_fechamento" name="horario_fechamento">
    </label>

    <button type="submit">Salvar</button>
  </form>
</div>
