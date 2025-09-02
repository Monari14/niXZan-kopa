const API_URL = 'http://localhost:8000/api/v1';

async function carregarProdutos() {
  try {
    const res = await fetch(`${API_URL}/admin/produtos`, {
      method: 'GET',
      headers: {'Content-Type': 'application/json'},
    });
    const dados = await res.json();

    const lista = document.getElementById("produtos");
    lista.innerHTML = ""; 

    if (dados.produtos && dados.produtos.data) {
      const produtos = Array.isArray(dados.produtos.data) 
        ? dados.produtos.data 
        : [dados.produtos.data];

      produtos.forEach(p => {
        const item = document.createElement("li");

        // Escapar aspas simples para não quebrar o onclick
        const nome = p.nome.replace(/'/g, "\\'");
        const imagem = p.imagem.replace(/'/g, "\\'");
        const tipo = p.tipo.replace(/'/g, "\\'");

        item.innerHTML = `
          <strong>${p.nome}</strong> <br>
          Tipo: ${p.tipo} <br>
          Preço Base: R$ ${p.preco_base} <br>
          Estoque: ${p.estoque} <br>
          <img src="${p.imagem}" alt="${p.nome}" width="120">
          <br>
          <button style="background-color: red;" onclick="removerProduto(${p.id})">-</button>
          <button style="background-color: green;" onclick="adicionarProduto(${p.id}, '${nome}', '${imagem}', '${tipo}', ${p.preco_base}, ${p.estoque})">+</button>
          <hr>
        `;
        lista.appendChild(item);
      });
    } else {
      lista.innerHTML = "<li>Nenhum produto encontrado</li>";
    }

    document.getElementById("total").textContent = dados.produtos?.total || 0;

  } catch (erro) {
    console.error("Erro ao carregar produtos:", erro);
  }
}

// === Funções do carrinho ===
function getCarrinho() {
  return JSON.parse(sessionStorage.getItem("carrinho")) || [];
}

function salvarCarrinho(carrinho) {
  sessionStorage.setItem("carrinho", JSON.stringify(carrinho));
}

function adicionarProduto(id, nome, imagem, tipo, preco_base, estoque) {
  let carrinho = getCarrinho();
  let item = carrinho.find(p => p.id === id);

  if (item) {
    item.qtd += 1;
  } else {
    carrinho.push({ id, nome, imagem, tipo, preco_base, estoque, qtd: 1 });
  }

  salvarCarrinho(carrinho);
  console.log("Carrinho atualizado:", carrinho);
}

function removerProduto(id) {
  let carrinho = getCarrinho();
  let item = carrinho.find(p => p.id === id);

  if (item) {
    item.qtd -= 1;
    if (item.qtd <= 0) {
      carrinho = carrinho.filter(p => p.id !== id);
    }
  }

  salvarCarrinho(carrinho);
  console.log("Carrinho atualizado:", carrinho);
}

window.onload = carregarProdutos;
