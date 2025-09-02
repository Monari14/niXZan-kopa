let carrinho = JSON.parse(sessionStorage.getItem('carrinho')) || {};

function salvarCarrinhoNaSessao() {
    sessionStorage.setItem('carrinho', JSON.stringify(carrinho));
}

function removerProduto(id) {
    id = String(id);
    if (carrinho[id]) {
        carrinho[id].quantidade -= 1;
        if (carrinho[id].quantidade <= 0) {
            delete carrinho[id];
        }
        salvarCarrinhoNaSessao();
        renderCarrinho();
    }
}

function adicionarProduto(id, nome, imagem, tipo, preco_base, estoque_total) {
    id = String(id);

    if (carrinho[id]) {
        if (carrinho[id].quantidade >= carrinho[id].estoque_total) {
            alert(`Estoque máximo atingido para ${nome}`);
            return;
        }
        carrinho[id].quantidade += 1;
    } else {
        carrinho[id] = {
            nome,
            imagem,
            tipo,
            preco_base: parseFloat(preco_base),
            quantidade: 1,
            estoque_total: parseInt(estoque_total)
        };
    }

    salvarCarrinhoNaSessao();
    renderCarrinho();
}


function exibirErro(msg) {
    const erroDiv = document.getElementById('erro-pedido');
    if (erroDiv) {
        erroDiv.textContent = msg;
        erroDiv.style.display = 'block';
    } else {
        alert(msg);
    }
}

function ocultarErro() {
    const erroDiv = document.getElementById('erro');
    if (erroDiv) {
        erroDiv.textContent = '';
        erroDiv.style.display = 'none';
    }
}

function finalizarPedido() {
    const energeticos = {};
    const bebidas = {};
    const gelos = {};

    let qtdEnergeticos = 0;
    let qtdBebidas = 0;
    let qtdGelos = 0;

    ocultarErro();

    Object.entries(carrinho).forEach(([id, item]) => {
        const tipo = item.tipo;
        const qtd = item.quantidade;

        if (qtd > 0) {
            if (tipo === "energetico") {
                energeticos[id] = qtd;
                qtdEnergeticos += qtd;
            } else if (tipo === "bebida") {
                bebidas[id] = qtd;
                qtdBebidas += qtd;
            } else if (tipo === "gelo") {
                gelos[id] = qtd;
                qtdGelos += qtd;
            }
        }
    });

    const quantidades = [qtdEnergeticos, qtdBebidas, qtdGelos];

    if (Math.min(...quantidades) < 1) {
        exibirErro("❌ É necessário ter pelo menos 1 kit.");
        return;
    }

    const unicos = new Set(quantidades);
    if (unicos.size !== 1) {
        exibirErro("⚠️ Há produtos sem Kit.");
        return;
    }

    fetch("/concluir-pedido", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({
            energeticos: energeticos,
            bebidas: bebidas,
            gelos: gelos,
        }),
    })
    .then((response) => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            return response.json();
        }
    })
    .then((data) => {
        if (data?.erro) {
            exibirErro("⚠️ " + data.erro);
        }
    })
    .catch((error) => {
        console.error(error);
        exibirErro("❌ Ocorreu um erro ao enviar o pedido.");
    });
}

function renderCarrinho() {
    const div = document.getElementById("carrinho");
    const valorTotal = document.getElementById("valorTotal");
    div.innerHTML = "";

    let total = 0;

    if (!carrinho.kits) {
        carrinho.kits = [];
    }

    const energeticos = [];
    const bebidas = [];
    const gelos = [];

    Object.entries(carrinho).forEach(([id, item]) => {
        if (id !== 'kits') {
            for (let i = 0; i < item.quantidade; i++) {
                if (item.tipo === "energetico") energeticos.push({ ...item, id });
                if (item.tipo === "bebida") bebidas.push({ ...item, id });
                if (item.tipo === "gelo") gelos.push({ ...item, id });
            }
        }
    });

    const usedCounts = {};
    carrinho.kits.forEach(kit => {
        [kit.energetico, kit.bebida, kit.gelo].forEach(item => {
            usedCounts[item.id] = (usedCounts[item.id] || 0) + 1;
        });
    });

    const contarDisponiveis = (lista) => {
        const contagem = {};
        lista.forEach(item => {
            contagem[item.id] = (contagem[item.id] || 0) + 1;
        });
        return contagem;
    };

    const descontarUsados = (contagem, usados) => {
        const resultado = [];
        Object.entries(contagem).forEach(([id, qtd]) => {
            const usadosQtd = usados[id] || 0;
            const disponiveis = qtd - usadosQtd;
            for (let i = 0; i < disponiveis; i++) {
                resultado.push({ ...carrinho[id], id });
            }
        });
        return resultado;
    };

    const energeticosDisponiveis = descontarUsados(contarDisponiveis(energeticos), usedCounts);
    const bebidasDisponiveis = descontarUsados(contarDisponiveis(bebidas), usedCounts);
    const gelosDisponiveis = descontarUsados(contarDisponiveis(gelos), usedCounts);

    const kitsCompletosPossiveis = Math.min(
        energeticosDisponiveis.length,
        bebidasDisponiveis.length,
        gelosDisponiveis.length
    );

    for (let i = 0; i < kitsCompletosPossiveis; i++) {
        const energetico = energeticosDisponiveis[i];
        const bebida = bebidasDisponiveis[i];
        const gelo = gelosDisponiveis[i];

        carrinho.kits.push({ energetico, bebida, gelo });

        usedCounts[energetico.id] = (usedCounts[energetico.id] || 0) + 1;
        usedCounts[bebida.id] = (usedCounts[bebida.id] || 0) + 1;
        usedCounts[gelo.id] = (usedCounts[gelo.id] || 0) + 1;
    }

    const todosItens = [...energeticos, ...bebidas, ...gelos];
    const itensRestantes = [];

    const contagemTodos = contarDisponiveis(todosItens);
    Object.entries(contagemTodos).forEach(([id, qtd]) => {
        const usadosQtd = usedCounts[id] || 0;
        const restante = qtd - usadosQtd;
        if (restante > 0) {
            itensRestantes.push({ ...carrinho[id], id, quantidade: restante });
        }
    });

    if (itensRestantes.length > 0) {
        const restosContainer = document.createElement('div');
        restosContainer.className = 'col-md-12 p-1 d-flex flex-column gap-2';
        const listGroupRestos = document.createElement('div');
        listGroupRestos.className = 'list-group mt-2';

        itensRestantes.forEach(item => {
            const detalhe = document.createElement('div');
            detalhe.className = 'list-group-item border rounded shadow-sm p-3 bg-white';

            let botaoAdicionar = '';
            const estoqueAtual = item.estoque_total ?? 99; // fallback caso não venha o valor

            if (!carrinho[item.id] || carrinho[item.id].quantidade < estoqueAtual) {
                botaoAdicionar = `
                    <button class="btn btn-sm btn-outline-success"
                        onclick="adicionarProduto(
                            '${item.id}',
                            '${item.nome.replace(/'/g, "\\'")}',
                            '${item.imagem}',
                            '${item.tipo}',
                            '${item.preco_base}',
                            ${estoqueAtual}
                        )">+</button>`;
            }
            detalhe.innerHTML = `
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <img src="storage/${item.imagem}" alt="${item.nome}" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px;">
                        <span class="flex-grow-1 text-dark">${item.nome}</span>
                        <span style="color: blueviolet;" class="flex-grow-1 fw-semibold">(x${item.quantidade})</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-danger" onclick="removerProduto(${item.id})">-</button>
                        ${botaoAdicionar}
                    </div>
                </div>
            `;
            listGroupRestos.appendChild(detalhe);
        });

        restosContainer.appendChild(listGroupRestos);
        div.appendChild(restosContainer);
    }

    if (carrinho.kits.length > 0) {
        carrinho.kits.forEach((kit, index) => {
            const kitsContainer = document.createElement('div');
            kitsContainer.className = 'col-md-12 p-1 d-flex flex-column gap-2';

            const listGroup = document.createElement('div');
            listGroup.className = 'list-group';

            const detalhe = document.createElement('details');
            detalhe.className = 'list-group-item border rounded shadow-sm p-3 bg-white';
            detalhe.open = true;

            const precoKit = kit.energetico.preco_base + kit.bebida.preco_base + kit.gelo.preco_base;
            const totalKit = precoKit + 35;

            total += totalKit;

            const resumo = document.createElement('summary');
            resumo.className = 'd-flex justify-content-between align-items-center fw-semibold text-dark';
            resumo.innerHTML = `
                <span>Kit <span style="color: blueviolet;">#${index + 1}</span></span>
                <div class="d-flex align-items-center gap-2">
                    <span style="color: blueviolet;" class="fw-bold">R$ ${totalKit.toFixed(2).replace('.', ',')}</span>
                    <button class="btn btn-sm btn-outline-danger" style="margin-left: 10px;">Remover Kit</button>
                </div>
            `;

            const btnRemoverKit = resumo.querySelector('button');
            btnRemoverKit.onclick = () => {
                carrinho.kits.splice(index, 1);
                [kit.energetico, kit.bebida, kit.gelo].forEach(item => {
                    const id = String(item.id);
                    if (carrinho[id]) {
                        carrinho[id].quantidade -= 1;
                        if (carrinho[id].quantidade <= 0) {
                            delete carrinho[id];
                        }
                    }
                });
                salvarCarrinhoNaSessao();
                renderCarrinho();
            };

            detalhe.appendChild(resumo);

            const conteudo = document.createElement('div');
            conteudo.className = 'mt-2';

            const lista = document.createElement('ul');
            lista.className = 'list-unstyled mt-2 ps-2';

            [kit.energetico, kit.bebida, kit.gelo].forEach(item => {
                const li = document.createElement('li');
                li.className = 'd-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2';
                li.innerHTML = `
                    <div class="d-flex align-items-center gap-3 w-100">
                        <img src="storage/${item.imagem}" alt="${item.nome}" style="width: 45px; height: 45px; object-fit: contain; border-radius: 8px;">
                        <span class="flex-grow-1">${item.nome}</span>
                        <span style="color: blueviolet;" class="flex-grow-1 fw-semibold">(x1)</span>
                    </div>
                `;
                lista.appendChild(li);
            });

            conteudo.appendChild(lista);
            detalhe.appendChild(conteudo);
            listGroup.appendChild(detalhe);
            kitsContainer.appendChild(listGroup);
            div.appendChild(kitsContainer);
        });
    }

    valorTotal.className = 'fw-bold fs-5 text-end mt-3';
    valorTotal.innerHTML = `R$ ${total.toFixed(2).replace('.', ',')}`;
    salvarCarrinhoNaSessao();
    document.querySelectorAll('#carrinho details').forEach(d => d.open = true);
}

window.addEventListener('load', () => {
    renderCarrinho();
});
