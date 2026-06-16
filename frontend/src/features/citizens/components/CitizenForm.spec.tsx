import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { afterEach, describe, expect, it, vi } from 'vitest'
import { citizenService } from '../services/citizenService'
import { CitizensProvider } from '../context/CitizensProvider'
import { CitizenForm } from './CitizenForm'

function renderForm() {
  return render(
    <CitizensProvider>
      <CitizenForm />
    </CitizensProvider>,
  )
}

describe('CitizenForm', () => {
  afterEach(() => vi.restoreAllMocks())

  it('ignora letras e limita o CPF a 11 dígitos com máscara', async () => {
    const user = userEvent.setup()
    renderForm()
    const cpf = screen.getByLabelText('CPF') as HTMLInputElement

    await user.type(cpf, 'abc52998224725999')

    expect(cpf.value).toBe('529.982.247-25')
  })

  it('mostra "CPF inválido." e desabilita o botão enquanto o CPF é inválido', async () => {
    const user = userEvent.setup()
    renderForm()

    await user.type(screen.getByLabelText('Nome completo'), 'Maria da Silva')
    await user.type(screen.getByLabelText('CPF'), '529.982.247-24')

    expect(screen.getByText('CPF inválido.')).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Cadastrar' })).toBeDisabled()
  })

  it('exibe o modal de sucesso com os dados cadastrados', async () => {
    const user = userEvent.setup()
    vi.spyOn(citizenService, 'create').mockResolvedValue({
      id: '1',
      name: 'Maria da Silva',
      cpf: '529.982.247-25',
      createdAt: '2026-01-01T00:00:00+00:00',
    })
    vi.spyOn(citizenService, 'search').mockResolvedValue([])
    renderForm()

    await user.type(screen.getByLabelText('Nome completo'), 'Maria da Silva')
    await user.type(screen.getByLabelText('CPF'), '52998224725')
    await user.click(screen.getByRole('button', { name: 'Cadastrar' }))

    expect(await screen.findByText('Cadastro realizado!')).toBeInTheDocument()
    expect(screen.getByText('529.982.247-25')).toBeInTheDocument()
  })
})
