const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_id');
    localStorage.removeItem('user_name');
    localStorage.removeItem('role');
    window.location.href = '../../index.php';
}

function getCopoesSessao() {
  return JSON.parse(sessionStorage.getItem("copoes")) || [];
}

function salvarCopoesSessao(lista) {
  sessionStorage.setItem("copoes", JSON.stringify(lista));
  renderCarrinho();
}

function criarCopao() {
  let copoes = getCopoesSessao();
  const novoCopao = {
    id: Date.now() + Math.floor(Math.random() * 1000),
    nome: `Copão ${copoes.length + 1}`,
    produtos: [],
    fechado: false
  };
  copoes.push(novoCopao);
  salvarCopoesSessao(copoes);
}

function adicionarProdutoAoCopao(produto, copaoId) {
  let copoes = getCopoesSessao();
  let copao = copoes.find(c => c.id === copaoId);
  if (!copao) return;

  let existente = copao.produtos.find(p => p.id === produto.id);
  if (existente) {
    existente.quantidade += 1;
  } else {
    copao.produtos.push({ ...produto, quantidade: 1 });
  }

  const tiposNecessarios = ["energetico", "bebida", "gelo"];
  const tiposPresentes = copao.produtos.map(p => p.tipo);
  if (tiposNecessarios.every(t => tiposPresentes.includes(t)) && !copao.fechado) {
    copao.fechado = true;
    alert(`${copao.nome} foi fechado! 🎉`);
  }

  salvarCopoesSessao(copoes);
}

function removerProdutoDoCopao(produtoId, copaoId) {
  let copoes = getCopoesSessao();
  let copao = copoes.find(c => c.id === copaoId);
  if (!copao) return;

  let produto = copao.produtos.find(p => p.id === produtoId);
  if (!produto) return;

  if (produto.quantidade > 1) {
    produto.quantidade -= 1;
  } else {
    copao.produtos = copao.produtos.filter(p => p.id !== produtoId);
  }

  copao.fechado = false;
  salvarCopoesSessao(copoes);
}

function removerCopao(copaoId) {
  let copoes = getCopoesSessao();
  copoes = copoes.filter(c => c.id !== copaoId);
  salvarCopoesSessao(copoes);
}

function renderCarrinho() {
  const carrinho = document.getElementById("carrinho");
  const copoes = getCopoesSessao();
  carrinho.innerHTML = "";

  if (copoes.length === 0) {
    carrinho.innerHTML = "<li>Nenhum copão criado</li>";
    document.getElementById("total").textContent = 0;
    return;
  }

  let totalProdutos = 0;

  copoes.forEach(c => {
    const item = document.createElement("li");
    item.innerHTML = `
      <h3>${c.nome} ${c.fechado ? "✔️ Fechado" : ""}</h3>
      <ul></ul>
      <div class="btns">
        <button class="remove">Remover Copão</button>
      </div>
    `;
    item.querySelector(".remove").onclick = () => removerCopao(c.id);

    const listaProdutos = item.querySelector("ul");
    c.produtos.forEach(p => {
      totalProdutos += p.quantidade;
      const li = document.createElement("li");
      li.innerHTML = `
        <strong>${p.nome}</strong>
        <p>Tipo: ${p.tipo}</p>
        <p>Quantidade: ${p.quantidade}</p>
        <p>Preço Base: R$ ${p.preco_base}</p>
        <div class="btns">
          <button class="add">+</button>
          <button class="remove">-</button>
        </div>
      `;
      li.querySelector(".add").onclick = () => adicionarProdutoAoCopao({ ...p }, c.id);
      li.querySelector(".remove").onclick = () => removerProdutoDoCopao(p.id, c.id);
      listaProdutos.appendChild(li);
    });

    carrinho.appendChild(item);
  });

  document.getElementById("total").textContent = totalProdutos;
}

function meusPedidos() {
  fetch(`${API_URL}/cliente/pedidos/meus`, {
    method: 'GET',
    headers: { 
      'Content-Type': 'application/json', 
      'Authorization': `Bearer ${token}` 
    },
  })
  .then(res => {
    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    return res.json();
  })
  .then(dados => {
    const lista = document.getElementById("pedidos");
    lista.innerHTML = "";

    const pedidos = dados.pedidos?.data || [];
    if (pedidos.length > 0) {
      pedidos.forEach(pedido => {
        const item = document.createElement("li");
        item.innerHTML = `
          <strong>Pedido #${pedido.id}</strong>
          <p>Status: ${pedido.status}</p>
          <p>Total: R$ ${pedido.total}</p>
        `;
        lista.appendChild(item);
      });
    } else {
      lista.innerHTML = "<li>Nenhum pedido encontrado</li>";
    }
  })
  .catch(erro => console.error("Erro ao carregar pedidos:", erro));
}

function finalizarPedido() {
  const copoes = getCopoesSessao().filter(c => c.fechado);
  if (copoes.length === 0) {
    alert("Nenhum copão fechado para finalizar o pedido.");
    return;
  }

  // Preparar os arrays que a API espera
  const energeticos = {};
  const bebidas = {};
  const gelos = {};

  copoes.forEach(copao => {
    copao.energeticos?.forEach(item => {
      energeticos[item.id] = (energeticos[item.id] || 0) + item.quantidade;
    });
    copao.bebidas?.forEach(item => {
      bebidas[item.id] = (bebidas[item.id] || 0) + item.quantidade;
    });
    copao.gelos?.forEach(item => {
      gelos[item.id] = (gelos[item.id] || 0) + item.quantidade;
    });
  });

  const pedidoData = { energeticos, bebidas, gelos };

  fetch(`${API_URL}/cliente/pedidos/novo`, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json', 
      'Authorization': `Bearer ${token}` 
    },
    body: JSON.stringify(pedidoData)
  })
  .then(res => {
    if (!res.ok) return res.json().then(err => { throw new Error(err.error || 'Erro desconhecido'); });
    return res.json();
  })
  .then(data => {
    alert(data.message || "Pedido realizado com sucesso!");
    sessionStorage.removeItem("copoes");
    renderCarrinho();
    meusPedidos();
  })
  .catch(erro => console.error("Erro ao finalizar pedido:", erro));
}


async function carregarProdutos() {
  try {
    const res = await fetch(`${API_URL}/admin/produtos`, {
      method: 'GET',
      headers: { 
        'Content-Type': 'application/json', 
        'Authorization': `Bearer ${token}` 
      },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const dados = await res.json();

    const lista = document.getElementById("produtos");
    lista.innerHTML = "";

    const produtos = dados.produtos?.data || [];
    if (produtos.length > 0) {
      produtos.forEach(p => {
        const item = document.createElement("li");
        item.innerHTML = `
          <strong>${p.nome}</strong>
          <img src="${p.imagem}" alt="${p.nome}">
          <div class="btns">
            <button class="add">Adicionar ao Copão</button>
          </div>
        `;

        item.querySelector(".add").onclick = () => {
          let copoes = getCopoesSessao();
          if (copoes.length === 0) criarCopao();
          const ultimoCopao = getCopoesSessao().slice(-1)[0];
          adicionarProdutoAoCopao({ ...p }, ultimoCopao.id);
        };

        lista.appendChild(item);
      });
    } else {
      lista.innerHTML = "<li>Nenhum produto encontrado</li>";
    }

  } catch (erro) {
    console.error("Erro ao carregar produtos:", erro);
  }
}

window.onload = () => {
  carregarProdutos();
  renderCarrinho();
  meusPedidos();
};
