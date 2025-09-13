const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

async function carregarPedidos() {
  try {
    const tabelaEl = document.getElementById('todos_pedidos');
    if (!tabelaEl) {
      console.error('Elemento #todos_pedidos não encontrado!');
      return;
    }

    const res = await fetch(`${API_URL}/admin/pedidos`, {
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
    const tabela = tabelaEl.getElementsByTagName('tbody')[0];
    tabela.innerHTML = '';

    if (pedidos.length > 0) {
      pedidos.forEach(pedido => {
        const tr = document.createElement('tr');
        let pagamento = pedido.forma_pagamento === "pix" ? "Pix" : "Dinheiro";

        let status = '';
        switch(pedido.status) {
          case "pendente": status = "Pendente"; break;
          case "preparando": status = "Preparando"; break;
          case "esperando_retirada": status = "Esperando retirada"; break;
          case "entregue": status = "Entregue"; break;
          case "cancelado": status = "Cancelado"; break;
        }

        tr.innerHTML = `
          <td style="display:none;">${pedido.id_pedido}</td>
          <td>${pedido.nome_cliente}</td>
          <td>${pedido.endereco}</td>
          <td>${pagamento}</td>
          <td>${pedido.troco !== null ? 'R$ ' + pedido.troco : '-'}</td>
          <td>R$ ${pedido.total}</td>
          <td>${(pedido.itens_pedido || []).map(item => `<li>${item.nome} (${item.quantidade}x)</li>`).join('<br>')}</td>
          <td>${pedido.created_at}</td>
          <td>
            <select class="status-select" data-id="${pedido.id_pedido}">
              <option value="preparando" ${pedido.status === 'preparando' ? 'selected' : ''}>Preparando</option>
              <option value="esperando_retirada" ${pedido.status === 'esperando_retirada' ? 'selected' : ''}>Esperando retirada</option>
              <option value="saiu_para_entrega" ${pedido.status === 'saiu_para_entrega' ? 'selected' : ''}>Saiu para entrega</option>
              <option value="entregue" ${pedido.status === 'entregue' ? 'selected' : ''}>Entregue</option>
              <option value="cancelado" ${pedido.status === 'cancelado' ? 'selected' : ''}>Cancelado</option>
            </select>
          </td>
        `;
        tabela.appendChild(tr);
      });

      // Event listeners para alterar status
      document.querySelectorAll('.status-select').forEach(select => {
        select.onchange = async (e) => {
          const id = e.target.dataset.id;
          const novoStatus = e.target.value;

          if (!confirm(`Alterar status do pedido para "${novoStatus}"?`)) return;

          try {
            const res = await fetch(`${API_URL}/admin/admins/atualizar/status/pedido/${id}`, {
              method: 'PATCH',
              headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ status: novoStatus }) // corrigido
            });

            if (!res.ok) throw new Error(`Erro ${res.status}`);
            alert('Status atualizado com sucesso!');
            carregarPedidos(); // recarrega tabela
          } catch (erro) {
            console.error('Erro ao atualizar status:', erro);
            alert('Erro ao atualizar status!');
          }
        };
      });
    } else {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td colspan="9">Nenhum pedido encontrado</td>`;
      tabela.appendChild(tr);
    }
  } catch (erro) {
    console.error('Erro ao carregar pedidos:', erro);
  }
}

window.addEventListener('DOMContentLoaded', carregarPedidos);