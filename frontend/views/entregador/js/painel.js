const token = localStorage.getItem('token');
const API_URL = 'http://localhost:8000/api/v1';

function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user_id');
  localStorage.removeItem('user_name');
  localStorage.removeItem('role');
  window.location.href = '../../index.php';
}

// -------------------- CARRINHO --------------------

function getCarrinhoSessao() {
  return JSON.parse(sessionStorage.getItem("carrinho")) || [];
}

function salvarCarrinhoSessao(lista) {
  sessionStorage.setItem("carrinho", JSON.stringify(lista));
  renderCarrinho();
}

function adicionarProdutoAoCarrinho(produto) {
  let carrinho = getCarrinhoSessao();

  let existente = carrinho.find(p => p.id === produto.id);
  if (existente) {
    existente.quantidade += 1;
  } else {
    carrinho.push({ ...produto, quantidade: 1 });
  }

  salvarCarrinhoSessao(carrinho);
}

function removerProdutoDoCarrinho(produtoId) {
  let carrinho = getCarrinhoSessao();
  let produto = carrinho.find(p => p.id === produtoId);
  if (!produto) return;

  if (produto.quantidade > 1) {
    produto.quantidade -= 1;
  } else {
    carrinho = carrinho.filter(p => p.id !== produtoId);
  }

  salvarCarrinhoSessao(carrinho);
}

function limparCarrinho() {
  sessionStorage.removeItem("carrinho");
  renderCarrinho();
}

function renderCarrinho() {
  const carrinhoEl = document.getElementById("carrinho");
  const carrinho = getCarrinhoSessao();
  carrinhoEl.innerHTML = "";

  if (!carrinho || carrinho.length === 0) {
    carrinhoEl.innerHTML = "<p>Carrinho vazio</p>";
    return;
  }

  let totalProdutos = 0;

  carrinho.forEach(p => {
    totalProdutos += p.quantidade;

    const li = document.createElement("li");
    li.innerHTML = `
      <strong>${p.nome}</strong>
      <img src="${p.imagem}" alt="${p.nome}">
      <p>Quantidade: ${p.quantidade}</p>
      <div class="btns">
        <button class="add">+</button>
        <button class="remove">-</button>
      </div>
    `;

    li.querySelector(".add").onclick = () => adicionarProdutoAoCarrinho({ ...p });
    li.querySelector(".remove").onclick = () => removerProdutoDoCarrinho(p.id);

    carrinhoEl.appendChild(li);
  });

  document.getElementById("total").textContent = totalProdutos;
}

// -------------------- PEDIDOS --------------------

async function meusPedidos() {
  const cache = localStorage.getItem("pedidos_cache");
  const cacheTime = localStorage.getItem("pedidos_cache_time");

  const expirou = !cacheTime || (Date.now() - parseInt(cacheTime) > 30 * 1000);

  if (cache && !expirou) {
    const pedidos = JSON.parse(cache);
    renderPedidos(pedidos);
    return;
  }

  try {
    const res = await fetch(`${API_URL}/cliente/pedidos/meus`, {
      method: 'GET',
      headers: { 
        'Content-Type': 'application/json', 
        'Authorization': `Bearer ${token}` 
      },
    });

    if (!res.ok) throw new Error(`Erro ${res.status}: ${res.statusText}`);
    const dados = await res.json();
    const pedidos = dados.info_pedidos || [];

    localStorage.setItem("pedidos_cache", JSON.stringify(pedidos));
    localStorage.setItem("pedidos_cache_time", Date.now());

    renderPedidos(pedidos);

  } catch (erro) {
    console.error("Erro ao carregar pedidos:", erro);
  }
}

function renderPedidos(pedidos) {
  const lista = document.getElementById("meus_pedido");
  lista.innerHTML = "";

  if (pedidos.length > 0) {
    pedidos.forEach(pedido => {
      const details = document.createElement("details");
      details.style.marginBottom = "10px";

      const summary = document.createElement("summary");
      const statusMap = {
        pendente: "Pendente",
        preparando: "Preparando",
        esperando_retirada: "Esperando retirada",
        cancelado: "Cancelado",
        finalizado: "Finalizado",
        entregue: "Entregue"
      };
      const status_pedido = statusMap[pedido.status] || pedido.status;

      summary.innerHTML = `Pedido #${pedido.id_pedido} | ${status_pedido}`;
      details.appendChild(summary);

      const ulItens = document.createElement("ul");
      ulItens.innerHTML = `
        Endereço: ${pedido.endereco}<br>
        Forma de pagamento: ${pedido.forma_pagamento || "-"}<br>
        Total: R$ ${pedido.total}<br>
        ${pedido.troco ? `Troco: R$ ${pedido.troco}` : ""}
        <hr><br>
      `;

      pedido.itens_pedido.forEach(item => {
        const li = document.createElement("li");
        li.innerHTML = `${item.nome} - ${item.quantidade}x (${item.tipo})`;
        ulItens.appendChild(li);
      });

      details.appendChild(ulItens);
      lista.appendChild(details);
    });
  } else {
    lista.innerHTML = "<p>Faça um pedido</p>";
  }
}

function finalizarPedido() {
  const carrinho = getCarrinhoSessao();

  if (carrinho.length === 0) {
    alert("Carrinho vazio.");
    return;
  }

  const qtdEnergetico = carrinho
    .filter(p => p.tipo && p.tipo.toLowerCase() === 'energetico')
    .reduce((acc, p) => acc + p.quantidade, 0);

  const qtdBebida = carrinho
    .filter(p => p.tipo && p.tipo.toLowerCase() === 'bebida')
    .reduce((acc, p) => acc + p.quantidade, 0);

  const qtdGelo = carrinho
    .filter(p => p.tipo && p.tipo.toLowerCase() === 'gelo')
    .reduce((acc, p) => acc + p.quantidade, 0);

  if (qtdEnergetico === 0 || qtdBebida === 0 || qtdGelo === 0) {
    alert('O pedido deve conter pelo menos 1 energético, 1 bebida e 1 gelo.');
    return;
  }

  if (!(qtdEnergetico === qtdBebida && qtdBebida === qtdGelo)) {
    alert('A quantidade de energético, bebida e gelo deve ser igual.');
    return;
  }

  fetch(`${API_URL}/cliente/pedidos/novo`, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json', 
      'Authorization': `Bearer ${token}` 
    },
    body: JSON.stringify({ itens: carrinho })
  })
  .then(res => {
    if (!res.ok) return res.json().then(err => { throw new Error(err.error || 'Erro desconhecido'); });
    return res.json();
  })
  .then(data => {
    console.log("Resposta do servidor:", data);

    if (data.message && data.message.toLowerCase().includes("pedido_finalizado")) {
      window.location.href = 'resumo/resumo.php';
    }

    limparCarrinho();

    // limpa cache de pedidos para forçar recarregar
    localStorage.removeItem("pedidos_cache");
    localStorage.removeItem("pedidos_cache_time");

    meusPedidos();
  })
  .catch(erro => console.error("Erro ao finalizar pedido:", erro));
}

// -------------------- PRODUTOS --------------------

async function carregarProdutos() {
  const cache = localStorage.getItem("produtos_cache");
  if (cache) {
    const produtos = JSON.parse(cache);
    renderProdutos(produtos);
    return;
  }

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

    const produtos = dados.produtos?.data || [];
    localStorage.setItem("produtos_cache", JSON.stringify(produtos));

    renderProdutos(produtos);

  } catch (erro) {
    console.error("Erro ao carregar produtos:", erro);
  }
}

function renderProdutos(produtos) {
  const lista = document.getElementById("produtos");
  lista.innerHTML = "";

  if (produtos.length > 0) {
    produtos.forEach(p => {
      const item = document.createElement("li");
      item.innerHTML = `
        <strong>${p.nome}</strong>
        <img src="${p.imagem}" alt="${p.nome}">
        <div class="btns">
          <button style="margin-top: 0.7rem;" class="add">+</button>
        </div>
      `;

      item.querySelector(".add").onclick = () => adicionarProdutoAoCarrinho({ ...p });
      lista.appendChild(item);
    });
  } else {
    lista.innerHTML = "<li>Nenhum produto encontrado</li>";
  }
}

// -------------------- INIT --------------------

window.onload = () => {
  carregarProdutos();
  renderCarrinho();
  meusPedidos();
};
