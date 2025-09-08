const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

const modal = document.getElementById("produtoModal1");
const closeModal = document.getElementById("closeModal1");
const modalNome = document.getElementById("modalNome1");
const modalImagem = document.getElementById("modalImagem1");
const modalTipo = document.getElementById("modalTipo1");
const modalPreco = document.getElementById("modalPreco1");
const modalEstoque = document.getElementById("modalEstoque1");
const editarBtn = document.getElementById("editarBtn1");
const deletarBtn = document.getElementById("deletarBtn1");

function abrirModal(produto) {
  modalNome.textContent = produto.nome;
  modalImagem.src = produto.imagem;
  modalImagem.alt = produto.nome;
  modalTipo.textContent = produto.tipo;
  modalPreco.textContent = produto.preco_base;
  modalEstoque.textContent = produto.estoque;

  editarBtn.onclick = () => editarProduto(produto);
  deletarBtn.onclick = () => deletarProduto(produto.id);

  modal.style.display = "block";
}

// tera um formulário que ja vem com os dados do produto selecionado
/* COMO A API ESPERA
'nome' => 'sometimes|required|string|max:255',
'tipo' => 'sometimes|required|string|max:100',
'preco_base' => 'sometimes|required|numeric|min:0',
'estoque' => 'sometimes|required|integer|min:0',
'imagem'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
*/
function editarProduto(produto) {
  // cria formulário dentro do modal
  const formHtml = `
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
    </form>
  `;

  // substitui conteúdo do modal por formulário
  const modalContent = modal.querySelector(".modal-content");
  modalContent.innerHTML = `
    <span id="closeModal" class="close">&times;</span>
    <h3>Editar Produto</h3>
    ${formHtml}
  `;

  // evento de fechar modal
  modal.querySelector("#closeModal").onclick = () => modal.style.display = "none";

  const form = document.getElementById("editarForm");
  form.onsubmit = async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    formData.append("_method", "PUT"); // Laravel espera isso

    try {
      const res = await fetch(`${API_URL}/admin/produtos/${produto.id}`, {
        method: "POST", // precisa ser POST com _method=PUT
        headers: { 'Authorization': `Bearer ${token}` },
        body: formData
      });

      if (!res.ok) throw new Error("Erro ao atualizar produto");
      alert("Produto atualizado com sucesso!");
      modal.style.display = "none";
      carregarProdutos();
    } catch (erro) {
      console.error("Erro:", erro);
      alert("Erro ao atualizar produto!");
    }
  };
}

closeModal.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };

async function carregarProdutos() {
  try {
    const res = await fetch(`${API_URL}/admin/produtos`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (!res.ok) throw new Error("Erro ao carregar produtos");
    const dados = await res.json();

    const lista = document.getElementById("produtos");
    lista.innerHTML = "";

    const produtos = dados.produtos?.data || [];
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
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (!res.ok) throw new Error("Erro ao deletar produto");
    alert("Produto excluído com sucesso!");
    modal.style.display = "none";
    carregarProdutos();
  } catch (erro) {
    console.error("Erro:", erro);
  }
}

carregarProdutos();
