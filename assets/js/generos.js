let botaoGeneroEditado;

$('#modalEditar').on('show.bs.modal', function (event) {
    const botao = $(event.relatedTarget);
    const formulario = $('#editar-modal');

    botaoGeneroEditado = botao;

    formulario.attr('action', botao.data('url'));
    $('#editar-nome').val(botao.data('nome'));
    $('#editar-descricao').val(botao.data('descricao'));
    $('#editar-genero-pai').val(botao.data('genero-pai'));
});

$('#editar-modal').on('submit', function (event) {
    event.preventDefault();

    const formulario = this;
    const nome = $('#editar-nome').val();
    const descricao = $('#editar-descricao').val();
    const generoPai = $('#editar-genero-pai');
    const generoPaiNome = generoPai.val()
        ? generoPai.find('option:selected').text()
        : "";

    editarRegistroModal(formulario, function () {
        const linha = botaoGeneroEditado.closest('tr');

        linha.find('td:first-child strong').text(nome);
        linha.find('td:nth-child(2) span').text(generoPaiNome);

        botaoGeneroEditado.data('nome', nome);
        botaoGeneroEditado.data('descricao', descricao);
        botaoGeneroEditado.data('genero-pai', generoPai.val());
    });
});

$('#busca-generos').on('input', function () {
    const termo = $(this).val().toLowerCase();

    $('#tabela-generos tbody tr').each(function () {
        $(this).toggle(
            $(this).text().toLowerCase().includes(termo)
        );
    });
});