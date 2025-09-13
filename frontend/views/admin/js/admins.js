const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

const modal = document.getElementById("entregadorModal");
const adicionarModal = document.getElementById("adicionarEntregadorModal");

async function carregarSettings() {
  try {
    const tabelaEl = document.getElementById('settings');
    if (!tabelaEl) return console.error('Elemento #settings não encontrado!');
    if (!token) return console.error("Token não encontrado!");

    const res = await fetch(`${API_URL}/admin/settings`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const settings = await res.json(); // objeto único

    let tabela = tabelaEl.querySelector('tbody');
    if (!tabela) tabela = tabelaEl.appendChild(document.createElement('tbody'));
    tabela.innerHTML = '';

    if (settings && settings.id) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="display:none;">${settings.id}</td>
        <td>${settings.status_aberto ? 'Aberto' : 'Fechado'}</td>
        <td>R$ ${settings.valor_adicional_pedido != null ? Number(settings.valor_adicional_pedido).toFixed(2) : '0.00'}</td>
        <td>${settings.horario_abertura ?? '-'}</td>
        <td>${settings.horario_fechamento ?? '-'}</td>
      `;
      tabela.appendChild(tr);
    } else {
      tabela.innerHTML = `<tr><td colspan="5">Configurações não encontradas</td></tr>`;
    }
  } catch (erro) {
    console.error('Erro ao carregar configurações:', erro);
  }
}

const editarBtn = document.getElementById('editarSettingsBtn');
const modalSettings = document.getElementById('editarSettingsModal');
const formSettings = document.getElementById('editarSettingsForm');
const fecharModalSettings = document.getElementById('fecharEditarSettings');

editarBtn.onclick = () => {
  // Pega os dados da tabela e preenche o formulário
  const tbody = document.querySelector('#settings tbody');
  const tr = tbody.querySelector('tr');
  if (!tr) return alert('Nenhuma configuração carregada!');

  const status = tr.children[1].textContent === 'Aberto' ? 1 : 0;
  const valor = parseFloat(tr.children[2].textContent.replace('R$', '').trim());
  const abertura = tr.children[3].textContent !== '-' ? tr.children[3].textContent : '';
  const fechamento = tr.children[4].textContent !== '-' ? tr.children[4].textContent : '';

  document.getElementById('status_loja').value = status;
  document.getElementById('valor_adicional').value = valor;
  document.getElementById('horario_abertura').value = abertura;
  document.getElementById('horario_fechamento').value = fechamento;

  modalSettings.style.display = 'block';
};

fecharModalSettings.onclick = () => {
  modalSettings.style.display = 'none';
};

// Submissão do formulário
formSettings.onsubmit = async (e) => {
  e.preventDefault();
  if (!token) return alert('Token não encontrado!');

  const formData = {
    status_aberto: parseInt(document.getElementById('status_loja').value),
    valor_adicional_pedido: parseFloat(document.getElementById('valor_adicional').value),
    horario_abertura: document.getElementById('horario_abertura').value || null,
    horario_fechamento: document.getElementById('horario_fechamento').value || null
  };

  try {
    const res = await fetch(`${API_URL}/admin/settings`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData)
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    alert('Configurações atualizadas com sucesso!');
    modalSettings.style.display = 'none';
    carregarSettings(); // Atualiza tabela
  } catch (erro) {
    console.error('Erro ao atualizar configurações:', erro);
    alert('Erro ao atualizar configurações!');
  }
};

async function carregarAdmins() {
  try {
    const tabelaEl = document.getElementById('todos_entregadores');
    if (!tabelaEl) return console.error('Elemento #todos_entregadores não encontrado!');
    if (!token) return console.error("Token não encontrado!");

    const res = await fetch(`${API_URL}/admin/admins/todos`, {
      method: 'GET',
      headers: { 'Authorization': `Bearer ${token}` },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const dados = await res.json();
    const usuarios = dados.users?.data || [];

    let tabela = tabelaEl.querySelector('tbody');
    if (!tabela) tabela = tabelaEl.appendChild(document.createElement('tbody'));
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
            carregarAdmins();
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
            carregarAdmins();
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
            carregarAdmins();
          } catch (erro) {
            console.error('Erro ao deletar usuário:', erro);
            alert('Erro ao deletar usuário!');
          }
        };
      });

    } else {
      tabela.innerHTML = `<tr><td colspan="7">Nenhum usuário encontrado</td></tr>`;
    }

  } catch (erro) {
    console.error('Erro ao carregar usuários:', erro);
  }
}

function adicionarAdmin() {
  const content = adicionarModal.querySelector(".modal-content-adicionar");
  content.innerHTML = `
    <span class="close" id="fecharModalAdicionar">&times;</span>
    <h3>Adicionar Admin</h3>
    <form id="adicionarForm" enctype="multipart/form-data">
        <label for="name">Nome completo:</label>
        <input type="text" id="name" name="name" placeholder="Seu nome completo" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Seu username" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" placeholder="Seu e-mail" required>

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" placeholder="Seu telefone" required>

        <input type="hidden" id="role" name="role" value="admin">

        <label for="password">Senha:</label>
        <input type="password" id="reg-password" name="password" placeholder="Sua senha" required>

        <label for="password_confirmation">Confirme sua senha:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme sua senha" required>

        <button type="submit">Registrar</button>
    </form>
  `;

  // Evento de fechar modal
  content.querySelector("#fecharModalAdicionar").onclick = fecharModalAdicionar;

  // Envio do formulário
  const form = document.getElementById("adicionarForm");
  form.onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    try {
      const res = await fetch(`${API_URL}/auth/register/`, {
        method: "POST",
        body: formData,
        headers: token ? { 'Authorization': `Bearer ${token}` } : {}
      });

      if (!res.ok) {
        const texto = await res.text();
        console.error(`Erro da API (status ${res.status}):`, texto);
        return alert(`Erro ao adicionar admin (status ${res.status})`);
      }

      alert("Admin adicionado com sucesso!");
      fecharModalAdicionar();
      carregarAdmins();
    } catch (erro) {
      console.error("Erro:", erro);
      alert("Erro ao adicionar admin!");
    }
  };
}

function fecharModalAdicionar() {
  adicionarModal.style.display = "none";
}

document.getElementById("adicionarBtn").onclick = () => {
  adicionarModal.style.display = "block";
  adicionarAdmin();
};

window.addEventListener('DOMContentLoaded', carregarAdmins);
window.addEventListener('DOMContentLoaded', carregarSettings);
