(function () {
  const componente = document.querySelector('[data-autor-api]');
  if (!componente) return;

  const busca = componente.querySelector('[data-autor-busca]');
  const resultados = componente.querySelector('[data-autor-resultados]');
  const obras = componente.querySelector('[data-autor-obras]');
  const formulario = busca.closest('form');
  let temporizador;
  let controlador;

  function escapar(texto) {
    return String(texto || '').replace(/[&<>'"]/g, function (caractere) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#039;',
        '"': '&quot;'
      }[caractere];
    });
  }

  function normalizarData(valor) {
    if (!valor) return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(valor)) return valor;

    const data = new Date(valor);
    if (Number.isNaN(data.getTime())) return '';

    return [
      data.getUTCFullYear(),
      String(data.getUTCMonth() + 1).padStart(2, '0'),
      String(data.getUTCDate()).padStart(2, '0')
    ].join('-');
  }

  function preencherCampo(nome, valor) {
    const campo = formulario.elements[nome];
    if (campo && valor) campo.value = valor;
  }

  function mostrarMensagem(elemento, mensagem) {
    elemento.textContent = mensagem;
    elemento.hidden = !mensagem;
  }

  function renderizarResultados(lista) {
    resultados.innerHTML = '';

    if (!lista.length) {
      mostrarMensagem(resultados, 'Nenhum autor encontrado.');
      return;
    }

    resultados.hidden = false;
    lista.forEach(function (autor) {
      const botao = document.createElement('button');
      botao.type = 'button';
      botao.className = 'autor-api__resultado';
      botao.innerHTML = '<strong>' + escapar(autor.name) + '</strong>'
        + (autor.work_count ? '<small>' + escapar(autor.work_count + ' obras catalogadas') + '</small>' : '');
      botao.addEventListener('click', function () {
        selecionarAutor(autor);
      });
      resultados.appendChild(botao);
    });
  }

  async function selecionarAutor(autor) {
    busca.value = autor.name || '';
    resultados.hidden = true;
    obras.innerHTML = '<div class="autor-api__estado">Carregando informações do autor e dos livros...</div>';
    obras.hidden = false;

    preencherCampo('nome', autor.name);
    preencherCampo('data_nascimento', normalizarData(autor.birth_date));
    preencherCampo('data_falecimento', normalizarData(autor.death_date));

    try {
      const chave = String(autor.key || '').replace(/^\//, '');
      const respostas = await Promise.all([
        fetch('https://openlibrary.org/authors/' + encodeURIComponent(chave) + '.json'),
        fetch('https://openlibrary.org/authors/' + encodeURIComponent(chave) + '/works.json?limit=12')
      ]);
      const detalhes = await respostas[0].json();
      const listaObras = await respostas[1].json();
      const biografia = typeof detalhes.bio === 'string' ? detalhes.bio : detalhes.bio?.value;

      preencherCampo('biografia', biografia);
      preencherCampo('descricao', biografia);
      renderizarObras(listaObras.entries || []);
    } catch (erro) {
      obras.innerHTML = '<div class="autor-api__estado autor-api__estado--erro">Não foi possível carregar as obras deste autor.</div>';
    }
  }

  function renderizarObras(lista) {
    if (!lista.length) {
      obras.innerHTML = '<div class="autor-api__estado">Nenhum livro encontrado para este autor.</div>';
      return;
    }

    obras.innerHTML = '<div class="autor-api__obras">' + lista.map(function (livro) {
      const capa = livro.covers?.[0];
      const imagem = capa ? 'https://covers.openlibrary.org/b/id/' + capa + '-M.jpg' : '';
      const assuntos = (livro.subjects || []).slice(0, 3).join(', ');
      return '<article class="autor-api__livro">'
        + (imagem ? '<img src="' + imagem + '" alt="" loading="lazy">' : '<span class="autor-api__sem-capa"><i class="bx bx-book"></i></span>')
        + '<div><strong>' + escapar(livro.title) + '</strong>'
        + (livro.first_publish_date ? '<small>' + escapar(livro.first_publish_date) + '</small>' : '')
        + (assuntos ? '<small>' + escapar(assuntos) + '</small>' : '')
        + '</div></article>';
    }).join('') + '</div>';
  }

  busca.addEventListener('input', function () {
    const termo = busca.value.trim();
    clearTimeout(temporizador);
    if (controlador) controlador.abort();

    if (termo.length < 3) {
      resultados.hidden = true;
      return;
    }

    temporizador = setTimeout(async function () {
      controlador = new AbortController();
      mostrarMensagem(resultados, 'Buscando autores...');
      try {
        const resposta = await fetch('https://openlibrary.org/search/authors.json?q=' + encodeURIComponent(termo) + '&limit=8', {
          signal: controlador.signal
        });
        const dados = await resposta.json();
        renderizarResultados(dados.docs || []);
      } catch (erro) {
        if (erro.name !== 'AbortError') mostrarMensagem(resultados, 'Não foi possível consultar a API.');
      }
    }, 350);
  });
}());
