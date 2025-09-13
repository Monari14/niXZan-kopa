const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

async function carregarPedidos() {
  try {
    const tabelaEl = document.getElementById('meus_pedidos');
    if (!tabelaEl) {
      console.error('Elemento #meus_pedidos não encontrado!');
      return;
    }

    // 🔹 Verifica cache
    const cache = localStorage.getItem("pedidos_cache");
    const cacheTime = localStorage.getItem("pedidos_cache_time");
    const expirou = !cacheTime || (Date.now() - parseInt(cacheTime) > 30 * 1000); // expira em 30s

    if (cache && !expirou) {
      const pedidos = JSON.parse(cache);
      renderTabelaPedidos(pedidos, tabelaEl);
      return;
    }

    // 🔹 Busca API
    const res = await fetch(`${API_URL}/cliente/pedidos/meus`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);

    const dados = await res.json();
    console.log('Resposta da API:', dados);

    const pedidos = dados.info_pedidos || [];

    // 🔹 Salva no cache
    localStorage.setItem("pedidos_cache", JSON.stringify(pedidos));
    localStorage.setItem("pedidos_cache_time", Date.now());

    renderTabelaPedidos(pedidos, tabelaEl);

  } catch (erro) {
    console.error('Erro ao carregar pedidos:', erro);
  }
}

function renderTabelaPedidos(pedidos, tabelaEl) {
  const tabela = tabelaEl.getElementsByTagName('tbody')[0];
  tabela.innerHTML = '';

  if (pedidos.length > 0) {
    pedidos.forEach(pedido => {
      const tr = document.createElement('tr');

      // Forma de pagamento legível
      let pagamento = pedido.forma_pagamento === "pix" ? "Pix" :
                      pedido.forma_pagamento === "dinheiro" ? "Dinheiro" : "-";

      // Status legível
      const statusMap = {
        pendente: "Pendente",
        preparando: "Preparando",
        esperando_retirada: "Esperando retirada",
        entregue: "Entregue",
        cancelado: "Cancelado"
      };
      const status = statusMap[pedido.status] || pedido.status;

      // Lista de itens
      const itensHtml = (pedido.itens_pedido || [])
        .map(item => `<li>${item.nome} (${item.quantidade}x)</li>`)
        .join('');

      tr.innerHTML = `
        <td>${pedido.id_pedido}</td>
        <td>${pedido.endereco}</td>
        <td>${pagamento}</td>
        <td>${pedido.troco != null && !isNaN(pedido.troco) ? 'R$ ' + Number(pedido.troco).toFixed(2) : '-'}</td>
        <td>R$ ${Number(pedido.total).toFixed(2)}</td>
        <td>${status}</td>
        <td><ul style="padding-left: 15px; margin:0;">${itensHtml}</ul></td>
        <td>${pedido.created_at}</td>
      `;
      tabela.appendChild(tr);
    });
  } else {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td colspan="8">Nenhum pedido encontrado</td>`;
    tabela.appendChild(tr);
  }
}

window.addEventListener('DOMContentLoaded', carregarPedidos);