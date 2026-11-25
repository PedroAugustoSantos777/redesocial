
const API = "http://localhost/redesocial/back/api.php";

// MOSTRAR MENSAGEM
function mostrarMensagem(texto, erro = false) {
    const elemento = document.getElementById("mensagem");

    if (elemento) {
        elemento.textContent = texto;
        elemento.style.color = erro ? "red" : "green";
    }
}

// CADASTRO
const formCadastro = document.getElementById("formCadastro");

if (formCadastro) {
    formCadastro.addEventListener("submit", async function(e) {
        e.preventDefault();

        const dados = {
            nome: document.getElementById("nome").value,
            username: document.getElementById("username").value,
            email: document.getElementById("email").value,
            senha: document.getElementById("senha").value
        };

        try {
            const resposta = await fetch(`${API}?rota=usuario`, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();

            if (!resposta.ok) {
                throw new Error(resultado.erro || "Erro no cadastro");
            }

            mostrarMensagem("Cadastro realizado! Redirecionando...");

            setTimeout(() => {
                window.location.href = "index.html";
            }, 1500);

        } catch (erro) {
            mostrarMensagem(erro.message, true);
        }
    });
}

// LOGIN
const formLogin = document.getElementById("formLogin");

if (formLogin) {
    formLogin.addEventListener("submit", async function(e) {
        e.preventDefault();

        const dados = {
            email: document.getElementById("email").value,
            senha: document.getElementById("senha").value
        };

        try {
            const resposta = await fetch(`${API}?rota=login`, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();

            if (!resposta.ok) {
                throw new Error(resultado.erro || "Erro no login");
            }

            localStorage.setItem(
                "usuario",
                JSON.stringify(resultado.usuario)
            );

            window.location.href = "feed.html";

        } catch (erro) {
            mostrarMensagem(erro.message, true);
        }
    });
}

// VERIFICAR USUARIO LOGADO
function obterUsuario() {
    return JSON.parse(localStorage.getItem("usuario") || "null");
}

// SAIR
function sair() {
    localStorage.removeItem("usuario");
    window.location.href = "index.html";
}

// FEED
async function carregarFeed() {
    const area = document.getElementById("publicacoes");

    if (!area) return;

    const usuario = obterUsuario();

    if (!usuario) {
        window.location.href = "index.html";
        return;
    }

    try {
        const resposta = await fetch(`${API}?rota=feed`);
        const publicacoes = await resposta.json();

        if (!resposta.ok) {
            throw new Error("Não foi possível carregar o feed");
        }

        area.innerHTML = "";

        if (publicacoes.length === 0) {
            area.textContent = "Ainda não existem publicações.";
            return;
        }

        publicacoes.forEach(pub => {
            const card = document.createElement("article");
            card.className = "publicacao";

            const titulo = document.createElement("h3");
            titulo.textContent = "@" + pub.username;

            const legenda = document.createElement("p");
            legenda.textContent = pub.legenda;

            card.appendChild(titulo);
            card.appendChild(legenda);

            if (pub.imagem_url) {
                const imagem = document.createElement("img");
                imagem.src = pub.imagem_url;
                imagem.alt = "Imagem da publicação";
                imagem.onerror = () => imagem.remove();
                card.appendChild(imagem);
            }

            const curtidas = document.createElement("p");
            curtidas.textContent = "❤️ " + pub.total_curtidas + " curtidas";
            card.appendChild(curtidas);

            const botao = document.createElement("button");
            const chave = `curtida_${usuario.id_usuario}_${pub.id_publicacao}`;

            botao.textContent = localStorage.getItem(chave)
                ? "Descurtir"
                : "Curtir";

            botao.addEventListener("click", async () => {
                await alternarCurtida(pub.id_publicacao, chave);
            });

            card.appendChild(botao);
            area.appendChild(card);
        });

    } catch (erro) {
        area.textContent = erro.message;
    }
}

// CRIAR PUBLICACAO
const formPublicacao = document.getElementById("formPublicacao");

if (formPublicacao) {
    formPublicacao.addEventListener("submit", async function(e) {
        e.preventDefault();

        const usuario = obterUsuario();

        if (!usuario) {
            window.location.href = "index.html";
            return;
        }

        const dados = {
            id_usuario: usuario.id_usuario,
            legenda: document.getElementById("legenda").value,
            imagem_url: document.getElementById("imagem_url").value
        };

        try {
            const resposta = await fetch(`${API}?rota=publicacao`, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();

            if (!resposta.ok) {
                throw new Error(resultado.erro || "Erro ao publicar");
            }

            formPublicacao.reset();
            mostrarMensagem("Publicação criada com sucesso!");
            carregarFeed();

        } catch (erro) {
            mostrarMensagem(erro.message, true);
        }
    });
}

// CURTIR E DESCURTIR
async function alternarCurtida(idPublicacao, chave) {
    const usuario = obterUsuario();

    const jaCurtiu = localStorage.getItem(chave);

    try {
        const resposta = await fetch(`${API}?rota=curtida`, {
            method: jaCurtiu ? "DELETE" : "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                id_usuario: usuario.id_usuario,
                id_publicacao: idPublicacao
            })
        });

        const resultado = await resposta.json();

        if (!resposta.ok) {
            throw new Error(resultado.erro || "Erro ao curtir");
        }

        if (jaCurtiu) {
            localStorage.removeItem(chave);
        } else {
            localStorage.setItem(chave, "true");
        }

        carregarFeed();

    } catch (erro) {
        alert(erro.message);
    }
}

// CARREGAR FEED AO ABRIR
carregarFeed();
