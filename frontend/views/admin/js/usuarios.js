const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

async function carregarClientes() {
  try {
    const tabelaEl = document.getElementById('todos_clientes');
    if (!tabelaEl) {
      console.error('Elemento #todos_clientes não encontrado!');
      return;
    }

    if (!token) {
      console.error("Token não encontrado!");
      return;
    }

    const res = await fetch(`${API_URL}/admin/clientes/todos`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`
      },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const dados = await res.json();

    // Agora pega do lugar certo
    const usuarios = dados.users?.data || [];
    let tabela = tabelaEl.querySelector('tbody');
    if (!tabela) {
      tabela = tabelaEl.appendChild(document.createElement('tbody'));
    }
    tabela.innerHTML = '';

    if (usuarios.length > 0) {
      usuarios.forEach(user => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td style="display:none;">${user.id}</td>
          <td>${user.nome}</td>
          <td>${user.username}</td>
          <td>${user.email}</td>
          <td>${user.telefone}</td>
          <td>${user.role}</td>
          <td>${user.created_at}</td>
        `;
        tabela.appendChild(tr);
      });
    } else {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td colspan="7">Nenhum usuário encontrado</td>`;
      tabela.appendChild(tr);
    }
  } catch (erro) {
    console.error('Erro ao carregar usuários:', erro);
  }
}

window.addEventListener('DOMContentLoaded', carregarClientes);
