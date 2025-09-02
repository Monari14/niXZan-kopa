<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Produtos</title>
</head>
<body>
  <?php 
    include '../public/nav.php'; 
    include 'painel/produtos.php';
  ?>

  <script>
    const token = localStorage.getItem('token');
    const API_URL = 'http://localhost:8000/api/v1';

    function logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('user_id');
      localStorage.removeItem('user_name');
      localStorage.removeItem('role');
      window.location.href = '../../index.php';
    }

    async function carregarProdutos() {
      try {
        const res = await fetch(`${API_URL}/admin/produtos`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
        });

        if (!res.ok) {
          throw new Error(`Erro ${res.status}: ${res.statusText}`);
        }

        const dados = await res.json();

        const lista = document.getElementById("produtos");
        lista.innerHTML = "";

        if (dados.produtos && dados.produtos.data) {
          const produto = dados.produtos.data;
          const produtos = Array.isArray(produto) ? produto : [produto];

          produtos.forEach(p => {
            const item = document.createElement("li");
            item.innerHTML = `
              <strong>${p.nome}</strong> <br>
              Tipo: ${p.tipo} <br>
              Preço Base: R$ ${p.preco_base} <br>
              Estoque: ${p.estoque} <br>
              <img src="${p.imagem}" alt="${p.nome}" width="120">
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

    window.onload = carregarProdutos;
  </script>
</body>
</html>
