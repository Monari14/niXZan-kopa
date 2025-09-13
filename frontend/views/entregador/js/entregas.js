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
      tabela.innerHTML = `<tr><td colspan="7">Nenhum pedido encontrado</td></tr>`;
      return;
    }

    pedidos.forEach(pedido => {
      const tr = document.createElement('tr');

      const statusMap = {
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
        <td>R$ ${Number(pedido.troco || 0).toFixed(2)}</td>
        <td>${status}</td>
        <td>${pedido.created_at ? new Date(pedido.created_at).toLocaleString() : '-'}</td>
        <td>
          <button class="aceitar-btn">Aceitar</button>
        </td>
      `;
      tabela.appendChild(tr);

      const aceitarBtn = tr.querySelector('.aceitar-btn');
      if (aceitarBtn) {
        aceitarBtn.addEventListener('click', async () => {
          aceitarBtn.disabled = true;
          await aceitarPedido(pedido.id_pedido);
          aceitarBtn.disabled = false;
        });
      }
    });

  } catch (erro) {
    console.error('Erro ao carregar pedidos:', erro);
    alert('Não foi possível carregar os pedidos. Verifique o console.');
  }
}

async function aceitarPedido(id) {
  console.log('Aceitando pedido:', id);
  try {
    const res = await fetch(`${API_URL}/entregador/pedidos/aceitar`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({ id_pedido: id })
    });

    if (!res.ok) {
      const errData = await res.json().catch(() => ({}));
      const msg = errData.error || `Erro ${res.status}: ${res.statusText}`;
      alert(msg);
      throw new Error(msg);
    }

    const data = await res.json();
    alert(data.message);
    carregarPedidos();
  } catch (erro) {
    console.error('Erro ao aceitar pedido:', erro);
  }
}

window.addEventListener('DOMContentLoaded', carregarPedidos);
