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

    // Usando o novo formato do JSON
    const pedidos = dados.info_pedidos || [];
    const tabela = tabelaEl.getElementsByTagName('tbody')[0];
    tabela.innerHTML = '';

    if (pedidos.length > 0) {
      pedidos.forEach(pedido => {
        const tr = document.createElement('tr');
        let pagamento = "";
        if(pedido.forma_pagamento == "pix"){
            pagamento = "Pix";
        }else if(pedido.forma_pagamento == "dinheiro"){
            pagamento = "Dinheiro";
        }

        let status = "";
        if(pedido.status == "pendente"){
            status = "Pendente";
        }else if(pedido.status == "preparando"){
            status = "Preparando";
        }else if(pedido.status == "esperando_retirada"){
            status = "Esperando retirada";
        }else if(pedido.status == "entregue"){
            status = "Entregue";
        }else if(pedido.status == "cancelado"){
            status = "Cancelado";
        }
        
        tr.innerHTML = `
          <td style="display:none;">${pedido.id_pedido}</td>
          <td>${pedido.nome_cliente}</td>
          <td>${pedido.endereco}</td>
          <td>${pagamento}</td>
          <td>${pedido.troco !== null ? 'R$ ' + pedido.troco : '-'}<\/td>
          <td>R$ ${pedido.total}</td>
          <td>${status}</td>
          <td>
            ${(pedido.itens_pedido || []).map(item => 
              `<li>${item.nome} (${item.quantidade}x)</li>`
            ).join('<br>')}
          <\/td>
          <td>${pedido.created_at}</td>
        `;
        tabela.appendChild(tr);
      });
    } else {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td colspan=\"8\">Nenhum pedido encontrado</td>`;
      tabela.appendChild(tr);
    }
  } catch (erro) {
    console.error('Erro ao carregar pedidos:', erro);
  }
}

window.addEventListener('DOMContentLoaded', carregarPedidos);