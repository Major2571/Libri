'use strict';

(function () {
  const operacoes = {
    inserir: salvarRegistro,
    editar: editarRegistro,
    excluir: excluirRegistro
  };

  function mostrarMensagem(retorno, sucesso) {
    return Swal.fire({
      toast: true,
      position: 'top-end',
      icon: sucesso ? 'success' : 'error',
      iconColor: 'white',
      customClass: {
        popup: 'colored-toast',
      },
      title: retorno.mensagem || 'Não foi possível concluir a operação.',
      showConfirmButton: false,
      timer: 1500,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });
  }

  function obterId(elemento) {
    return elemento.dataset.id || elemento.id.replace(/^(excluir|editar)[-_]?/, '');
  }

  async function requisitar(elemento, limparFormulario) {
    const formulario = elemento instanceof HTMLFormElement ? elemento : elemento.closest('form');
    const dados = formulario ? new FormData(formulario) : new FormData();
    const id = obterId(elemento);
    const url = elemento.dataset.url || formulario?.action;

    if (!formulario && id) {
      dados.append('id', id);
    }

    if (!formulario) {
      const token = document.querySelector('meta[name="csrf-token"]')?.content;

      if (token) {
        dados.append('csrf-token', token);
      }
    }

    if (!url) {
      throw new Error('Nenhum endpoint foi definido para a operação.');
    }

    const resposta = await fetch(url, {
      method: formulario?.method || 'POST',
      body: dados,
      headers: {
        Accept: 'application/json'
      }
    });
    const retorno = await resposta.json();
    const sucesso = resposta.ok && retorno.tipo === 'sucesso';
    const modal = formulario?.closest('.modal');
    const mensagem = mostrarMensagem(retorno, sucesso);

    if (sucesso && limparFormulario && formulario) {
      formulario.reset();

      if (modal && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(modal).hide();
      }
    }

    await mensagem;

    if (sucesso && (limparFormulario && modal || elemento.id.startsWith('excluir'))) {
      window.location.reload();
    }
  }

  async function executar(elemento, limparFormulario) {
    const controle = elemento.querySelector('[type="submit"]') || elemento;
    controle.disabled = true;

    try {
      await requisitar(elemento, limparFormulario);
    } catch (error) {
      await Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        iconColor: 'white',
        customClass: {
          popup: 'colored-toast',
        },
        title: 'Não foi possível comunicar com o servidor.',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
    } finally {
      controle.disabled = false;
    }
  }

  function salvarRegistro(formulario) {
    return executar(formulario, true);
  }

  function editarRegistro(formulario) {
    return executar(formulario, false);
  }

  async function excluirRegistro(botao) {
    const confirmacao = await Swal.fire({
      title: 'Excluir registro?',
      text: 'Essa ação não poderá ser desfeita.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Excluir',
      cancelButtonText: 'Cancelar'
    });

    if (confirmacao.isConfirmed) {
      await executar(botao, false);
    }
  }

  window.salvarRegistro = salvarRegistro;
  window.editarRegistro = editarRegistro;
  window.excluirRegistro = excluirRegistro;

  document.addEventListener('submit', function (event) {
    const operacao = operacoes[event.target.id];

    if (operacao && event.target instanceof HTMLFormElement) {
      event.preventDefault();
      operacao(event.target);
    }
  });

  document.addEventListener('click', function (event) {
    const botao = event.target.closest(
      'button[id^="editar"], button[id^="excluir"], a[id^="editar"], a[id^="excluir"], [role="button"][id^="editar"], [role="button"][id^="excluir"]'
    );

    if (botao && botao.type !== 'submit') {
      event.preventDefault();
      const operacao = botao.id.startsWith('excluir') ? excluirRegistro : editarRegistro;
      operacao(botao);
    }
  });
})();