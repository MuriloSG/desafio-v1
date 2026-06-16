/** Normalização, máscara e validação de CPF (dígitos verificadores). */

/** Remove tudo que não for dígito. */
export function stripCpf(value: string): string {
  return value.replace(/\D/g, '')
}

/** Aplica a máscara 000.000.000-00 e limita a 11 dígitos. */
export function maskCpf(value: string): string {
  return stripCpf(value)
    .slice(0, 11)
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2')
}

/** Valida o CPF pelos dígitos verificadores; rejeita tamanho diferente de 11 e sequências repetidas. */
export function isValidCpf(value: string): boolean {
  const cpf = stripCpf(value)

  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
    return false
  }

  const digits = cpf.split('').map(Number)

  for (let position = 9; position < 11; position++) {
    let sum = 0
    for (let i = 0; i < position; i++) {
      sum += digits[i] * (position + 1 - i)
    }
    const checkDigit = (sum * 10) % 11 % 10
    if (checkDigit !== digits[position]) {
      return false
    }
  }

  return true
}
