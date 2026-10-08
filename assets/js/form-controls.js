(function (window) {
  function initMultiSelect(container) {
    if (!container || container.dataset.initialized === 'true') return;

    const select = container.querySelector('select[multiple]');
    const control = container.querySelector('[data-multi-select-control]');
    const chips = container.querySelector('[data-multi-select-chips]');
    const menu = container.querySelector('[data-multi-select-menu]');
    if (!select || !control || !chips || !menu) return;

    container.dataset.initialized = 'true';

    Array.from(select.options).forEach(function (option) {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'autor-generos__option';
      item.dataset.value = option.value;
      item.dataset.multiSelectOption = 'true';
      item.textContent = option.textContent.trim();
      item.setAttribute('role', 'option');
      menu.appendChild(item);
    });

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

      Array.from(menu.children).forEach(function (item) {
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
    });

    control.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        control.click();
      }
    });

    menu.addEventListener('click', function (event) {
      const item = event.target.closest('[data-multi-select-option]');
      if (!item) return;
      const option = Array.from(select.options).find(function (current) {
        return current.value === item.dataset.value;
      });
      if (option) option.selected = !option.selected;
      atualizar();
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

  function inicializar() {
    document.querySelectorAll('[data-multi-select]').forEach(initMultiSelect);
    document.querySelectorAll('[data-dropzone]').forEach(initDropzone);
  }

  window.FormControls = { initMultiSelect, initDropzone, inicializar };
  inicializar();
}(window));
