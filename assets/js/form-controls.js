(function (window) {
  function initMultiSelect(container) {
    if (!container || container.dataset.initialized === 'true') return;

    const select = container.querySelector('select[multiple]');
    const control = container.querySelector('[data-multi-select-control]');
    const chips = container.querySelector('[data-multi-select-chips]');
    const menu = container.querySelector('[data-multi-select-menu]');
    if (!select || !control || !chips || !menu) return;

    container.dataset.initialized = 'true';

    const search = document.createElement('input');
    search.type = 'search';
    search.className = 'form-control autor-generos__search';
    search.placeholder = 'Buscar opção...';
    search.autocomplete = 'off';
    search.setAttribute('aria-label', 'Buscar opção');
    const options = document.createElement('div');
    options.className = 'autor-generos__options';
    options.setAttribute('role', 'listbox');
    menu.append(search, options);

    Array.from(select.options).forEach(function (option) {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'autor-generos__option';
      item.dataset.value = option.value;
      item.dataset.multiSelectOption = 'true';
      item.textContent = option.textContent.trim();
      item.setAttribute('role', 'option');
      options.appendChild(item);
    });

    function normalizar(texto) {
      return String(texto || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    }

    function filtrar(filtro) {
      const termo = normalizar(filtro);
      let encontrados = 0;

      Array.from(options.children).forEach(function (item) {
        const corresponde = !termo || normalizar(item.textContent + ' ' + item.dataset.value).includes(termo);
        item.hidden = !corresponde;
        if (corresponde) encontrados += 1;
      });

      options.dataset.empty = encontrados === 0 ? 'true' : 'false';
    }

    function atualizar() {
      const selecionados = Array.from(select.selectedOptions);
      chips.innerHTML = '';

      if (!selecionados.length) {
        chips.innerHTML = '<span class="autor-generos__placeholder">'
          + (container.dataset.placeholder || 'Escolha uma ou mais opções') + '</span>';
      }

      selecionados.forEach(function (option) {
        const chip = document.createElement('span');
        chip.className = 'autor-generos__chip';
        chip.textContent = option.textContent.trim();

        const remover = document.createElement('button');
        remover.type = 'button';
        remover.className = 'autor-generos__remove';
        remover.setAttribute('aria-label', 'Remover ' + option.textContent.trim());
        remover.innerHTML = '&times;';
        remover.addEventListener('click', function (event) {
          event.stopPropagation();
          option.selected = false;
          atualizar();
        });

        chip.appendChild(remover);
        chips.appendChild(chip);
      });

      Array.from(options.children).forEach(function (item) {
        const selecionado = Array.from(select.selectedOptions).some(function (option) {
          return option.value === item.dataset.value;
        });
        item.classList.toggle('is-selected', selecionado);
        item.setAttribute('aria-selected', selecionado);
      });
    }

    control.addEventListener('click', function () {
      const aberto = container.classList.toggle('is-open');
      control.setAttribute('aria-expanded', aberto);
      if (aberto) {
        search.value = '';
        filtrar('');
        search.focus();
      }
    });

    control.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        control.click();
      }
    });

    options.addEventListener('click', function (event) {
      const item = event.target.closest('[data-multi-select-option]');
      if (!item) return;
      const option = Array.from(select.options).find(function (current) {
        return current.value === item.dataset.value;
      });
      if (option) option.selected = !option.selected;
      atualizar();
    });

    search.addEventListener('input', function () {
      filtrar(search.value);
    });

    select.addEventListener('change', atualizar);
    document.addEventListener('click', function (event) {
      if (!container.contains(event.target)) {
        container.classList.remove('is-open');
        control.setAttribute('aria-expanded', 'false');
      }
    });

    filtrar('');
    atualizar();
  }

  function initDropzone(container) {
    if (!container || container.dataset.initialized === 'true') return;

    const input = container.querySelector('input[type="file"]');
    const preview = container.querySelector('[data-dropzone-preview]');
    const remove = container.querySelector('[data-dropzone-remove]');
    if (!input || !preview || !remove) return;

    container.dataset.initialized = 'true';
    const fallback = preview.src;
    let previewUrl;

    function selecionar(file) {
      if (!file || !/^image\/(png|jpeg|webp)$/.test(file.type)) return;
      if (previewUrl) URL.revokeObjectURL(previewUrl);
      previewUrl = URL.createObjectURL(file);
      preview.src = previewUrl;
      container.classList.add('has-file');
    }

    container.addEventListener('click', function (event) {
      if (event.target !== remove) input.click();
    });

    container.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        input.click();
      }
    });

    input.addEventListener('change', function () {
      selecionar(input.files[0]);
    });

    ['dragenter', 'dragover'].forEach(function (nome) {
      container.addEventListener(nome, function (event) {
        event.preventDefault();
        container.classList.add('is-dragging');
      });
    });

    ['dragleave', 'drop'].forEach(function (nome) {
      container.addEventListener(nome, function (event) {
        event.preventDefault();
        container.classList.remove('is-dragging');
      });
    });

    container.addEventListener('drop', function (event) {
      const file = event.dataTransfer.files[0];
      if (!file) return;
      const transferencia = new DataTransfer();
      transferencia.items.add(file);
      input.files = transferencia.files;
      selecionar(file);
    });

    remove.addEventListener('click', function (event) {
      event.stopPropagation();
      input.value = '';
      preview.src = fallback;
      container.classList.remove('has-file');
      if (previewUrl) URL.revokeObjectURL(previewUrl);
      previewUrl = null;
    });
  }

  function initCountrySelect(container) {
    if (!container || container.dataset.initialized === 'true') return;

    const select = container.querySelector('select');
    const control = container.querySelector('[data-country-control]');
    const value = container.querySelector('[data-country-value]');
    const menu = container.querySelector('[data-country-menu]');
    const search = container.querySelector('[data-country-search]');
    const options = container.querySelector('[data-country-options]');
    if (!select || !control || !value || !menu || !search || !options) return;

    container.dataset.initialized = 'true';

    function normalizar(texto) {
      return String(texto || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    }

    function bandeira(codigo) {
      const imagem = document.createElement('img');
      imagem.className = 'autor-nacionalidade__flag me-2';
      imagem.src = 'https://flagcdn.com/w80/' + codigo.toLowerCase() + '.png';
      imagem.alt = '';
      imagem.loading = 'lazy';
      return imagem;
    }

    function atualizar() {
      const selecionada = select.selectedOptions[0];
      value.innerHTML = '';
      if (!selecionada || !selecionada.value) {
        value.textContent = 'Selecione a nacionalidade';
      } else {
        value.appendChild(bandeira(selecionada.value));
        value.appendChild(document.createTextNode(selecionada.textContent.trim()));
      }
    }

    function renderizar(filtro) {
      options.innerHTML = '';
      const termo = normalizar(filtro);
      let encontrados = 0;

      Array.from(select.options).forEach(function (option) {
        if (!option.value) return;
        const busca = normalizar(option.textContent + ' ' + option.value);
        if (termo && !busca.includes(termo)) return;

        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'autor-nacionalidade__option';
        item.dataset.value = option.value;
        item.setAttribute('role', 'option');
        item.appendChild(bandeira(option.value));
        item.appendChild(document.createTextNode(option.textContent.trim()));
        options.appendChild(item);
        encontrados += 1;
      });

      if (!encontrados) {
        const vazio = document.createElement('span');
        vazio.className = 'autor-nacionalidade__empty';
        vazio.textContent = 'Nenhum país encontrado.';
        options.appendChild(vazio);
      }
    }

    control.addEventListener('click', function () {
      const aberto = container.classList.toggle('is-open');
      control.setAttribute('aria-expanded', aberto);
      if (aberto) {
        search.value = '';
        renderizar('');
        search.focus();
      }
    });

    search.addEventListener('input', function () {
      renderizar(search.value);
    });

    options.addEventListener('click', function (event) {
      const item = event.target.closest('[data-value]');
      if (!item) return;
      select.value = item.dataset.value;
      select.dispatchEvent(new Event('change', { bubbles: true }));
      container.classList.remove('is-open');
      control.setAttribute('aria-expanded', 'false');
    });

    select.addEventListener('change', atualizar);
    document.addEventListener('click', function (event) {
      if (!container.contains(event.target)) {
        container.classList.remove('is-open');
        control.setAttribute('aria-expanded', 'false');
      }
    });

    atualizar();
  }

  function initSearchableSelect(select) {
    if (!select || select.multiple || select.dataset.searchableInitialized === 'true'
      || select.closest('[data-country-select]')) return;

    const container = document.createElement('div');
    container.className = 'autor-select';
    container.dataset.searchableSelect = 'true';
    select.parentNode.insertBefore(container, select);
    container.appendChild(select);
    select.dataset.searchableInitialized = 'true';
    select.classList.add('autor-select__native');

    const control = document.createElement('button');
    control.type = 'button';
    control.className = 'autor-select__control';
    control.setAttribute('aria-haspopup', 'listbox');
    control.setAttribute('aria-expanded', 'false');

    const value = document.createElement('span');
    value.className = 'autor-select__value';
    control.appendChild(value);

    const menu = document.createElement('div');
    menu.className = 'autor-select__menu';
    const search = document.createElement('input');
    search.type = 'search';
    search.className = 'form-control';
    search.placeholder = 'Buscar opção...';
    search.autocomplete = 'off';
    search.setAttribute('aria-label', 'Buscar opção');
    const options = document.createElement('div');
    options.className = 'autor-select__options';
    options.setAttribute('role', 'listbox');
    menu.append(search, options);
    container.append(control, menu);

    function normalizar(texto) {
      return String(texto || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    }

    function atualizar() {
      const selecionada = select.selectedOptions[0];
      value.textContent = selecionada ? selecionada.textContent.trim() : 'Selecione uma opção';
    }

    function renderizar(filtro) {
      options.innerHTML = '';
      const termo = normalizar(filtro);
      let encontrados = 0;

      Array.from(select.options).forEach(function (option) {
        if (termo && !normalizar(option.textContent + ' ' + option.value).includes(termo)) return;

        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'autor-select__option';
        item.dataset.value = option.value;
        item.textContent = option.textContent.trim();
        item.setAttribute('role', 'option');
        options.appendChild(item);
        encontrados += 1;
      });

      if (!encontrados) {
        const vazio = document.createElement('span');
        vazio.className = 'autor-select__empty';
        vazio.textContent = 'Nenhuma opção encontrada.';
        options.appendChild(vazio);
      }
    }

    control.addEventListener('click', function () {
      const aberto = container.classList.toggle('is-open');
      control.setAttribute('aria-expanded', aberto);
      if (aberto) {
        search.value = '';
        renderizar('');
        search.focus();
      }
    });

    search.addEventListener('input', function () {
      renderizar(search.value);
    });

    options.addEventListener('click', function (event) {
      const item = event.target.closest('[data-value]');
      if (!item) return;
      select.value = item.dataset.value;
      select.dispatchEvent(new Event('change', { bubbles: true }));
      container.classList.remove('is-open');
      control.setAttribute('aria-expanded', 'false');
    });

    select.addEventListener('change', atualizar);
    document.addEventListener('click', function (event) {
      if (!container.contains(event.target)) {
        container.classList.remove('is-open');
        control.setAttribute('aria-expanded', 'false');
      }
    });

    atualizar();
  }

  function inicializar() {
    document.querySelectorAll('[data-multi-select]').forEach(initMultiSelect);
    document.querySelectorAll('[data-dropzone]').forEach(initDropzone);
    document.querySelectorAll('[data-country-select]').forEach(initCountrySelect);
    document.querySelectorAll('select:not([multiple])').forEach(initSearchableSelect);
  }

  window.FormControls = { initMultiSelect, initDropzone, initCountrySelect, initSearchableSelect, inicializar };
  inicializar();
}(window));
