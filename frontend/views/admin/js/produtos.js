const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

const modal = document.getElementById("produtoModal");
const adicionarModal = document.getElementById("adicionarProdutoModal");

const closeModalBtn = document.getElementById("closeModal");
const adicionarCloseModalBtn = document.getElementById("adicionarCloseModal");

const modalNome = document.getElementById("modalNome");
const modalImagem = document.getElementById("modalImagem");
const modalTipo = document.getElementById("modalTipo");
const modalPreco = document.getElementById("modalPreco");
const modalEstoque = document.getElementById("modalEstoque");

const editarBtn = document.getElementById("editarBtn");
const deletarBtn = document.getElementById("deletarBtn");

// abrir no modo de visualização
function abrirModal(produto) {
  const content = modal.querySelector(".modal-content");

  // recria o modal no modo de visualização SEMPRE que abre
  content.innerHTML = `
    <span id="closeModal" class="close">&times;</span>
    <h3 id="modalNome">${produto.nome}</h3>
    <img id="modalImagem" src="${produto.imagem}" alt="${produto.nome}" style="max-width: 200px; display: block; margin-bottom: 10px;">
    <p><strong>Tipo:</strong> <span id="modalTipo">${produto.tipo}</span></p>
    <p><strong>Preço:</strong> R$ <span id="modalPreco">${produto.preco_base}</span></p>
    <p><strong>Estoque:</strong> <span id="modalEstoque">${produto.estoque}</span></p>
    <div class="modal-actions">
      <button id="editarBtn">Editar</button>
      <button id="deletarBtn">Deletar</button>
    </div>
  `;

  // eventos
  content.querySelector("#closeModal").onclick = () => fecharModal();
  content.querySelector("#editarBtn").onclick = () => editarProduto(produto);
  content.querySelector("#deletarBtn").onclick = () => deletarProduto(produto.id);

  modal.style.display = "block";
}

function editarProduto(produto) {
  const content = modal.querySelector(".modal-content");

  content.innerHTML = `
    <span id="closeModal" class="close">&times;</span>
    <h3>Editar Produto</h3>
    <form id="editarForm" enctype="multipart/form-data">
      <label>Nome:</label>
      <input type="text" name="nome" value="${produto.nome}" required>
      
      <label>Tipo:</label>
      <select name="tipo" required>
        <option value="energetico" ${produto.tipo === "energetico" ? "selected" : ""}>Energético</option>
        <option value="bebida" ${produto.tipo === "bebida" ? "selected" : ""}>Bebida</option>
        <option value="gelo" ${produto.tipo === "gelo" ? "selected" : ""}>Gelo</option>
        <option value="copao" ${produto.tipo === "copao" ? "selected" : ""}>Copão</option>
      </select>
      
      <label>Preço:</label>
      <input type="number" step="0.01" name="preco_base" value="${produto.preco_base}" required>
      
      <label>Estoque:</label>
      <input type="number" name="estoque" value="${produto.estoque}" required>
      
      <label>Imagem:</label>
      <input type="file" name="imagem" accept="image/*">
      
      <button type="submit">Salvar Alterações</button>
      <button type="button" id="cancelarEdicao">Cancelar</button>
    </form>
  `;

  // Eventos de fechar/cancelar
  content.querySelector("#closeModal").onclick = () => fecharModal();
  content.querySelector("#cancelarEdicao").onclick = () => abrirModal(produto);

  const form = document.getElementById("editarForm");
  form.onsubmit = async (e) => {
    e.preventDefault();

    // Cria FormData do formulário
    const formData = new FormData(form);
    formData.append("_method", "PUT"); // para Laravel aceitar PUT via POST

    // Remove o campo imagem se nenhum arquivo foi selecionado
    const file = formData.get("imagem");
    if (!file || file.size === 0) {
      formData.delete("imagem");
    }

    try {
      const res = await fetch(`${API_URL}/admin/produtos/${produto.id}`, {
        method: "POST", // Laravel interpreta como PUT por causa de _method
        headers: {
          Authorization: `Bearer ${token}`,
        },
        body: formData,
      });

      if (!res.ok) {
        const texto = await res.text(); // resposta da API
        console.error("Erro da API (status " + res.status + "):", texto);
        alert("Erro ao atualizar produto (status " + res.status + ")");
        return;
      }

      alert("Produto atualizado com sucesso!");
      fecharModal();
      carregarProdutos();
    } catch (erro) {
      console.error("Erro:", erro);
      alert("Erro ao atualizar produto!");
    }
  };
}

function adicionarProduto() {
  // Seleciona o modal
  const content = adicionarModal.querySelector(".modal-content-adicionar");

  // Cria o formulário de adicionar produto
  content.innerHTML = `
    <span id="adicionarCloseModal" class="close">&times;</span>
    <h3>Adicionar Produto</h3>
    <form id="adicionarForm" enctype="multipart/form-data">
      <label>Nome:</label>
      <input type="text" name="nome" required>
      
      <label>Tipo:</label>
      <select name="tipo" required>
        <option value="energetico">Energético</option>
        <option value="bebida">Bebida</option>
        <option value="gelo">Gelo</option>
        <option value="copao">Copão</option>
      </select>
      
      <label>Preço:</label>
      <input type="number" step="0.01" name="preco_base" required>
      
      <label>Estoque:</label>
      <input type="number" name="estoque" required>
      
      <label>Imagem:</label>
      <input type="file" name="imagem" accept="image/*">
      
      <button type="submit">Adicionar</button>
      <button type="button" id="cancelarAdicao">Cancelar</button>
    </form>
  `;

  // Eventos de fechar modal
  content.querySelector("#adicionarCloseModal").onclick = () => fecharModalAdicionar();
  content.querySelector("#cancelarAdicao").onclick = () => fecharModalAdicionar();

  // Seleciona o formulário
  const form = document.getElementById("adicionarForm");
  
  // Evento ao enviar o formulário
  form.onsubmit = async (e) => {
    e.preventDefault(); // impede reload da página

    const formData = new FormData(form);

    // Remove imagem se nenhum arquivo for selecionado
    const file = formData.get("imagem");
    if (!file || file.size === 0) formData.delete("imagem");

    try {
      const res = await fetch(`${API_URL}/admin/produtos/`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`, // seu token
        },
        body: formData,
      });

      if (!res.ok) {
        const texto = await res.text();
        console.error("Erro da API (status " + res.status + "):", texto);
        alert("Erro ao adicionar produto (status " + res.status + ")");
        return;
      }

      alert("Produto adicionado com sucesso!");
      fecharModalAdicionar();
      carregarProdutos();
    } catch (erro) {
      console.error("Erro:", erro);
      alert("Erro ao adicionar produto!");
    }
  };
}
function fecharModalAdicionar(){
  adicionarModal.style.display = "none";
}

function fecharModal() {
  modal.style.display = "none";
}

// evento global para fechar ao clicar fora
window.onclick = (e) => {
  if (e.target === modal) fecharModal();
  if (e.target === adicionarModal) fecharModalAdicionar();
};

async function carregarProdutos() {
  try {
    const res = await fetch(`${API_URL}/admin/produtos`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    if (!res.ok) throw new Error("Erro ao carregar produtos");

    const dados = await res.json();
    const lista = document.getElementById("produtos");
    lista.innerHTML = "";

    const produtos = dados.produtos?.data || dados.data || [];
    produtos.forEach(p => {
      const item = document.createElement("li");
      item.innerHTML = `
        <strong>${p.nome}</strong>
        <img src="${p.imagem}" alt="${p.nome}">
      `;
      item.onclick = () => abrirModal(p);
      lista.appendChild(item);
    });
  } catch (erro) {
    console.error("Erro:", erro);
  }
}

async function deletarProduto(id) {
  if (!confirm("Tem certeza que deseja excluir este produto?")) return;
  try {
    const res = await fetch(`${API_URL}/admin/produtos/${id}`, {
      method: "DELETE",
      headers: { Authorization: `Bearer ${token}` }
    });
    if (!res.ok) throw new Error("Erro ao deletar produto");

    alert("Produto excluído com sucesso!");
    fecharModal();
    carregarProdutos();
  } catch (erro) {
    console.error("Erro:", erro);
  }
}

document.getElementById("adicionarBtn").onclick = () => {
  adicionarModal.style.display = "block";
  adicionarProduto();
};

carregarProdutos();
