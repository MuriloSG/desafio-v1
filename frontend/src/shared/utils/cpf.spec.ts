import { describe, expect, it } from 'vitest'
import { isValidCpf, maskCpf, stripCpf } from './cpf'

describe('stripCpf', () => {
  it('remove máscara e caracteres não numéricos', () => {
    expect(stripCpf('529.982.247-25')).toBe('52998224725')
  })
})

describe('maskCpf', () => {
  it('formata progressivamente', () => {
    expect(maskCpf('529')).toBe('529')
    expect(maskCpf('529982')).toBe('529.982')
    expect(maskCpf('52998224725')).toBe('529.982.247-25')
  })

  it('ignora dígitos além de 11', () => {
    expect(maskCpf('5299822472599')).toBe('529.982.247-25')
  })
})

describe('isValidCpf', () => {
  it('aceita CPFs válidos com e sem máscara', () => {
    expect(isValidCpf('529.982.247-25')).toBe(true)
    expect(isValidCpf('52998224725')).toBe(true)
  })

  it('rejeita dígitos verificadores incorretos', () => {
    expect(isValidCpf('529.982.247-24')).toBe(false)
  })

  it('rejeita tamanho inválido', () => {
    expect(isValidCpf('123')).toBe(false)
    expect(isValidCpf('')).toBe(false)
  })

  it('rejeita sequências repetidas', () => {
    expect(isValidCpf('111.111.111-11')).toBe(false)
    expect(isValidCpf('00000000000')).toBe(false)
  })
})
