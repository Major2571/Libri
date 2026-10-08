(function () {
  const componente = document.querySelector('[data-livro-api]');
  if (!componente) return;

  const busca = componente.querySelector('[data-livro-busca]');
  const resultados = componente.querySelector('[data-livro-resultados]');
  const formulario = busca.closest('form');
  const selectAutores = formulario.querySelector('select[name="autores[]"]');
  const modal = document.querySelector('[data-novo-autor-modal]');
  const formularioAutor = modal?.querySelector('[data-novo-autor-form]');
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

  function preencher(nome, valor) {
    const campo = formulario.elements[nome];
    if (campo && valor !== undefined && valor !== null && valor !== '') campo.value = valor;
  }

  function normalizar(valor) {
    return String(valor || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
  }

  async function cadastrarAutor(nome) {
    const dados = new FormData();
    dados.append('nome', nome);
    dados.append('csrf-token', formulario.elements['csrf-token']?.value || '');

    const resposta = await fetch(formularioAutor.action, {
      method: 'POST',
      body: dados,
      headers: { Accept: 'application/json' }
    });
    const retorno = await resposta.json();

    if (!resposta.ok || retorno.tipo !== 'sucesso' || !retorno.id) {
      throw new Error(retorno.mensagem || 'Não foi possível cadastrar o autor ' + nome + '.');
    }

    return retorno.id;
  }

  async function selecionarAutores(valores) {
    if (!selectAutores || !Array.isArray(valores)) return;

    const nomes = [...new Map(valores.filter(Boolean).map(function (nome) {
      return [normalizar(nome), nome];
    })).values()];

    for (const nome of nomes) {
      let opcao = Array.from(selectAutores.options).find(function (item) {
        return normalizar(item.textContent) === normalizar(nome);
      });

      if (!opcao) {
        const id = await cadastrarAutor(nome);
        opcao = new Option(nome, id);
        selectAutores.appendChild(opcao);
      }

      opcao.selected = true;
    }

    selectAutores.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function renderizar(lista) {
    resultados.innerHTML = '';
    resultados.hidden = false;

    if (!lista.length) {
      resultados.textContent = 'Nenhum livro encontrado.';
      return;
    }

    lista.forEach(function (livro) {
      const botao = document.createElement('button');
      botao.type = 'button';
      botao.className = 'autor-api__resultado';
      botao.innerHTML = '<strong>' + escapar(livro.title) + '</strong>'
        + '<small>' + escapar((livro.author_name || []).join(', ') || 'Autor não informado')
        + (livro.first_publish_year ? ' · ' + livro.first_publish_year : '') + '</small>';
      botao.addEventListener('click', async function () {
        try {
          await selecionar(livro);
        } catch (erro) {
          exibirResultado(erro.message, false);
        }
      });
      resultados.appendChild(botao);
    });
  }

  async function selecionar(livro) {
    busca.value = livro.title || '';
    resultados.hidden = true;

    preencher('titulo', livro.title);
    preencher('isbn', (livro.isbn || [])[0]);
    preencher('ano_publicacao', livro.first_publish_year ? livro.first_publish_year + '-01-01' : '');
    preencher('quantidade_disponivel', 1);
    preencher('status', 1);
    await selecionarAutores(livro.author_name || []);

    const capa = livro.cover_i;
    const preview = formulario.querySelector('[data-dropzone-preview]');
    if (capa && preview) preview.src = 'https://covers.openlibrary.org/b/id/' + capa + '-M.jpg';
  }

  function exibirResultado(mensagem, sucesso) {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: sucesso ? 'success' : 'error',
      title: mensagem,
      showConfirmButton: false,
      timer: 1600
    });
  }

  formularioAutor?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const botao = formularioAutor.querySelector('[type="submit"]');
    botao.disabled = true;

    try {
      const resposta = await fetch(formularioAutor.action, {
        method: formularioAutor.method || 'POST',
        body: new FormData(formularioAutor),
        headers: { Accept: 'application/json' }
      });
      const retorno = await resposta.json();
      if (!resposta.ok || retorno.tipo !== 'sucesso' || !retorno.id) {
        throw new Error(retorno.mensagem || 'Não foi possível cadastrar o autor.');
      }

      const nome = formularioAutor.elements.nome.value.trim();
      const opcao = new Option(nome, retorno.id, true, true);
      selectAutores.appendChild(opcao);
      selectAutores.dispatchEvent(new Event('change', { bubbles: true }));
      formularioAutor.reset();
      bootstrap.Modal.getOrCreateInstance(modal).hide();
      exibirResultado('Autor adicionado com sucesso!', true);
    } catch (erro) {
      exibirResultado(erro.message, false);
    } finally {
      botao.disabled = false;
    }
  });

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
      resultados.hidden = false;
      resultados.textContent = 'Buscando livros...';
      try {
        const url = 'https://openlibrary.org/search.json?q=' + encodeURIComponent(termo)
          + '&limit=8&fields=title,author_name,first_publish_year,isbn,cover_i';
        const resposta = await fetch(url, { signal: controlador.signal });
        const dados = await resposta.json();
        renderizar(dados.docs || []);
      } catch (erro) {
        if (erro.name !== 'AbortError') resultados.textContent = 'Não foi possível consultar a API.';
      }
    }, 350);
  });
}());
