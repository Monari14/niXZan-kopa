<div class="col-md-4 p-3 carrinho carrinho-container">
    <div class="d-flex justify-content-between align-items-center carrinho-header">
        <h4 class="m-0">Carrinho</h4>
        <h4 id="valorTotal">R$0,00</h4>
    </div>

    <div id="carrinho" class="row row-cols-1 row-cols-sm-2 g-3 mt-3"></div>

    <div id="erro-pedido" class="erro-pedido mt-3"></div>

    <div class="text-end mt-4">
        <button class="button" onclick="finalizarPedido()">Continuar</button>
    </div>
</div>

@if(session('success'))
<script>
    sessionStorage.removeItem('carrinho');
</script>
@endif
