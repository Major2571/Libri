
(function () {
  const genero = document.getElementById('genero');
  const generos = document.getElementById('autor-generos');
  const controleGeneros = generos.querySelector('.autor-generos__control');
  const chips = document.getElementById('autor-generos-chips');
  const menuGeneros = document.getElementById('autor-generos-menu');
  const dropzone = document.getElementById('autor-imagem-dropzone');
  const imagem = document.getElementById('imagem');
  const preview = document.getElementById('preview-imagem');
  const removerImagem = document.getElementById('remover-imagem');
  const avatarPadrao = preview.src;
  let imagemUrl;

  Array.from(genero.options).forEach(function (option) {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'autor-generos__option';
    item.dataset.value = option.value;
    item.textContent = option.textContent.trim();
    item.setAttribute('role', 'option');
    menuGeneros.appendChild(item);
  });

  function atualizarGeneros() {
    const selecionados = Array.from(genero.selectedOptions);
    chips.innerHTML = '';

    if (!selecionados.length) {
      chips.innerHTML = '<span class="autor-generos__placeholder">Escolha um ou mais gêneros</span>';
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
        atualizarGeneros();
      });

      chip.appendChild(remover);
      chips.appendChild(chip);
    });

    Array.from(menuGeneros.children).forEach(function (item) {
      const selecionado = Array.from(genero.selectedOptions).some(function (option) {
        return option.value === item.dataset.value;
      });
      item.classList.toggle('is-selected', selecionado);
      item.setAttribute('aria-selected', selecionado);
    });
  }

  controleGeneros.addEventListener('click', function () {
    const estaAberto = generos.classList.toggle('is-open');
    controleGeneros.setAttribute('aria-expanded', estaAberto);
  });

  controleGeneros.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      controleGeneros.click();
    }
  });

  menuGeneros.addEventListener('click', function (event) {
    const item = event.target.closest('.autor-generos__option');
    if (!item) return;
    const option = Array.from(genero.options).find(function (currentOption) {
      return currentOption.value === item.dataset.value;
    });
    option.selected = !option.selected;
    atualizarGeneros();
  });

  document.addEventListener('click', function (event) {
    if (!generos.contains(event.target)) {
      generos.classList.remove('is-open');
      controleGeneros.setAttribute('aria-expanded', 'false');
    }
  });

  function selecionarImagem(file) {
    if (!file || !file.type.match(/^image\/(png|jpeg|webp)$/)) return;
    if (imagemUrl) URL.revokeObjectURL(imagemUrl);
    imagemUrl = URL.createObjectURL(file);
    preview.src = imagemUrl;
    dropzone.classList.add('has-file');
  }

  dropzone.addEventListener('click', function (event) {
    if (event.target !== removerImagem) imagem.click();
  });

  dropzone.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      imagem.click();
    }
  });

  imagem.addEventListener('change', function () {
    selecionarImagem(imagem.files[0]);
  });

  ['dragenter', 'dragover'].forEach(function (eventName) {
    dropzone.addEventListener(eventName, function (event) {
      event.preventDefault();
      dropzone.classList.add('is-dragging');
    });
  });

  ['dragleave', 'drop'].forEach(function (eventName) {
    dropzone.addEventListener(eventName, function (event) {
      event.preventDefault();
      dropzone.classList.remove('is-dragging');
    });
  });

  dropzone.addEventListener('drop', function (event) {
    const file = event.dataTransfer.files[0];
    if (!file) return;
    const transferencia = new DataTransfer();
    transferencia.items.add(file);
    imagem.files = transferencia.files;
    selecionarImagem(file);
  });

  removerImagem.addEventListener('click', function (event) {
    event.stopPropagation();
    imagem.value = '';
    preview.src = avatarPadrao;
    dropzone.classList.remove('has-file');
    if (imagemUrl) URL.revokeObjectURL(imagemUrl);
    imagemUrl = null;
  });

  atualizarGeneros();
}());
