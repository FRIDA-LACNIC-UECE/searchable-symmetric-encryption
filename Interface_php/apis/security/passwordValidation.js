function verifica(form)
{
  var senha1 = form.senha1.value;
  var senha2 = form.senha2.value;

  if (senha1 != senha2) {
    alert("As senhas não coincidem!");
    return false;
  }

  if (senha1.length < 8 || senha1.length > 15) {
    alert("A senha deve ter no mínimo 8 e no máximo 15 caracteres.")
    return false;
  }

  if (senha1.search(/[a-z]/) < 0) {
    alert("Sua senha deve conter pelo menos uma letra minúscula.")
    return false;
  }

  if (senha1.search(/[A-Z]/) < 0) {
    alert("Sua senha deve conter pelo menos uma letra maiúscula.")
    return false;
  }

  if (senha1.search(/[0-9]/) < 0) {
    alert("Sua senha deve conter pelo menos um número.")
    return false;
  }

  if (senha1.search(/[!@#$%^&*]/) < 0) {
    alert("Sua senha deve conter pelo menos um caractere especial !@#$%^&*")
    return false;
  }

  return true;
}
