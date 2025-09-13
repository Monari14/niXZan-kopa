const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

const formaPagamento = document.getElementById("forma_pagamento");
const campoTroco = document.getElementById("campo_troco");

formaPagamento.addEventListener("change", () => {
  campoTroco.style.display = formaPagamento.value === "dinheiro" ? "block" : "none";
});

async function resumoPedido() {
  try {
    const res = await fetch(`${API_URL}/cliente/pedidos/carrinho`, {
      method: 'GET',
      headers: { 
        'Content-Type': 'application/json', 
        'Authorization': `Bearer ${token}` 
      },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const dados = await res.json();

    const lista = document.getElementById("produtos_pedido");
    lista.innerHTML = "";

    const produtos = dados.carrinho?.itens || [];
    if (produtos.length > 0) {
      produtos.forEach(p => {
        console.log(JSON.stringify(p, null, 2)); // 2 espaços de indentação
        const item = document.createElement("li");
          item.style = `list-style:none; text-align:center; margin-bottom:10px;`;
          item.innerHTML = `
            <img src="${p.imagem}" alt="${p.nome}" style="display:block; margin:0 auto 5px;">
            <div style="font-weight:bold;">${p.nome} - ${p.quantidade}x</div>
          `;
          lista.appendChild(item);
        });

      // Mostrar total fora da UL
      const total = document.getElementById("total");
      total.innerHTML = `Total: <strong style="color: #bb86fc;">R$ ${dados.carrinho.total}</strong>`;

    } else {
      lista.innerHTML = "<li>Nenhum pedido encontrado</li>";
    }

  } catch (erro) {
    console.error("Erro ao carregar produtos:", erro);
  }
}

async function finalizarPedido(event) {
  event.preventDefault(); // Evita o reload da página

  // Captura os dados do formulário
  const endereco = document.getElementById('endereco').value;
  const forma_pagamento = document.getElementById('forma_pagamento').value;
  let valor_troco = 0;

  if (forma_pagamento === "dinheiro") {
    valor_troco = parseFloat(document.getElementById('valor_troco')?.value || 0);
  }

  try {
    // Envia os dados para a API
    const res = await fetch(`${API_URL}/cliente/pedidos/confirmar`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'Authorization': `Bearer ${token}` 
      },
      body: JSON.stringify({ endereco, forma_pagamento, valor_troco })
    });

    // Verifica se houve erro na resposta
    if (!res.ok) {
      const erroDetalhe = await res.text();
      throw new Error(`Erro ${res.status}: ${erroDetalhe}`);
    }

    // Converte a resposta em JSON
    const data = await res.json();
    console.log("Resposta do servidor:", data);

    // Redireciona se o pedido foi realizado com sucesso
    if (data.message && data.message.toLowerCase().includes("pedido_realizado")) {
      window.location.href = '../index.php';
    } else {
      alert(data.message || "O pedido não pôde ser finalizado.");
    }

  } catch (erro) {
    console.error("Erro ao finalizar pedido:", erro);
    alert("Erro ao finalizar pedido. Verifique o console para mais detalhes.");
  }
}

document.getElementById('finalizar_pedido').addEventListener('submit', finalizarPedido);

window.onload = () => {
  resumoPedido();
};