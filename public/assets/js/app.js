const descricao = document.querySelector('#descricao');
const contador = document.querySelector('[data-contador]');

if (descricao && contador) {
  const atualizarContador = () => {
    contador.textContent = descricao.value.length;
  };

  descricao.addEventListener('input', atualizarContador);
  atualizarContador();
}

const formulario = document.querySelector('[data-form-solicitacao]');

if (formulario) {
  formulario.addEventListener('submit', (evento) => {
    if (!formulario.checkValidity()) {
      evento.preventDefault();
      formulario.reportValidity();
    }
  });
}
