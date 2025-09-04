<h2>Resumo do pedido</h2>
<br>
<ul id="produtos_pedido"></ul>
<br>
<div id="total" style="font-size: 16pt;"></div>

<form id="finalizar_pedido" method="post">
  <div>
    <label for="endereco">Endereço de entrega:</label>
    <input type="text" name="endereco" id="endereco" required>
  </div>

  <div>
    <label for="forma_pagamento">Forma de pagamento:</label>
    <select name="forma_pagamento" id="forma_pagamento" required>
      <option value="pix">PIX</option>
      <option value="dinheiro">Dinheiro</option>
    </select>
  </div>

  <div id="campo_troco" style="display:none;">
    <label for="valor_troco">Troco para quanto?</label>
    <input type="number" name="valor_troco" id="valor_troco" step="0.01">
  </div>

  <button type="submit">Finalizar pedido</button>
</form>
