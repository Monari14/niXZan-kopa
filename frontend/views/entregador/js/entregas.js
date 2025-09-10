const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

async function carregarPedidos() {
  if (!token) {
    console.error('Token não encontrado!');
    return;
  }

  try {
    const tabelaEl = document.getElementById('todos_pedidos');
    if (!tabelaEl) {
      console.error('Elemento #todos_pedidos não encontrado!');
      return;
    }

    const res = await fetch(`${API_URL}/entregador/pedidos/`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
    });

    console.log('Requisição enviada para:', `${API_URL}/entregador/pedidos/`);
    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);

    const dados = await res.json();
    console.log('Resposta da API:', dados);

    const pedidos = dados.pedidos || [];
    const tabela = tabelaEl.querySelector('tbody');
    tabela.innerHTML = '';

    if (pedidos.length === 0) {
      tabela.innerHTML = `<tr><td colspan="8">Nenhum pedido encontrado</td></tr>`;
      return;
    }

    pedidos.forEach(pedido => {
      const tr = document.createElement('tr');

      // Map status
      const statusMap = {
        "pendente": "Pendente",
        "preparando": "Preparando",
        "esperando_retirada": "Esperando retirada",
        "saiu_para_entrega": "Saiu para entrega",
        "entregue": "Entregue",
        "cancelado": "Cancelado"
      };
      const status = statusMap[pedido.status] || pedido.status;

      tr.innerHTML = `
        <td>${pedido.id_pedido}</td>
        <td>${pedido.cliente || '-'}</td>
        <td>${pedido.endereco || '-'}</td>
        <td>R$ ${Number(pedido.total || 0).toFixed(2)}</td>
        <td>${status}</td>
        <td>${pedido.created_at ? new Date(pedido.created_at).toLocaleString() : '-'}</td>
      `;
      tabela.appendChild(tr);

      // Eventos de botão
      const aceitarBtn = tr.querySelector('.aceitar-btn');
      if (aceitarBtn) aceitarBtn.addEventListener('click', () => aceitarPedido(pedido.id_pedido));
    });

  } catch (erro) {
    console.error('Erro ao carregar pedidos:', erro);
  }
}

async function aceitarPedido(id_pedido) {
  console.log('Aceitando pedido:', id_pedido);
  try {
    const res = await fetch(`${API_URL}/entregador/pedidos/aceitar`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({ id_pedido })
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const data = await res.json();
    alert(data.message);
    carregarPedidos();
  } catch (erro) {
    console.error('Erro ao aceitar pedido:', erro);
  }
}

window.addEventListener('DOMContentLoaded', carregarPedidos);
