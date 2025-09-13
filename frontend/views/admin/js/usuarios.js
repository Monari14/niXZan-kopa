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
          <td>
            <select class="role-select" data-id="${user.id}">
              <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
              <option value="entregador" ${user.role === 'entregador' ? 'selected' : ''}>Entregador</option>
              <option value="cliente" ${user.role === 'cliente' ? 'selected' : ''}>Cliente</option>
            </select>
            <button class="delete-btn" data-id="${user.id}">Deletar</button>
          </td>
        `;
        tabela.appendChild(tr);
      });

      // Event listeners depois de criar os elementos
      document.querySelectorAll('.role-select').forEach(select => {
        select.onchange = async (e) => {
          const id = e.target.dataset.id;
          const novaRole = e.target.value;

          // Impede alterar a própria role
          const tbody = e.target.closest('tbody');
          const logadoId = parseInt(localStorage.getItem('user_id')); // ou pegue do token/variável global
          if (parseInt(id) === logadoId) {
            alert("Você não pode alterar a sua própria role!");
            carregarClientes();
            return;
          }

          if (!confirm(`Alterar role do usuário para "${novaRole}"?`)) return;

          try {
            const res = await fetch(`${API_URL}/admin/admins/atualizar/role/${id}`, {
              method: 'PATCH',
              headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ role: novaRole })
            });

            if (!res.ok) throw new Error(`Erro ${res.status}`);
            alert('Role atualizado com sucesso!');
            carregarClientes();
          } catch (erro) {
            console.error('Erro ao atualizar role:', erro);
            alert('Erro ao atualizar role!');
          }
        };
      });

      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.onclick = async (e) => {
          const id = e.target.dataset.id;

          // Impede deletar a si mesmo
          const logadoId = parseInt(localStorage.getItem('user_id'));
          if (parseInt(id) === logadoId) {
            alert("Você não pode deletar a si mesmo!");
            return;
          }

          if (!confirm('Deseja deletar este usuário?')) return;

          try {
            const res = await fetch(`${API_URL}/admin/admins/deletar/${id}`, {
              method: 'DELETE',
              headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!res.ok) throw new Error(`Erro ${res.status}`);
            alert('Usuário deletado com sucesso!');
            carregarClientes();
          } catch (erro) {
            console.error('Erro ao deletar usuário:', erro);
            alert('Erro ao deletar usuário!');
          }
        };
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
