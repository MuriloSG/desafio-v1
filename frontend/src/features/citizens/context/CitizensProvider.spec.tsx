import type { ReactNode } from 'react'
import { act, renderHook } from '@testing-library/react'
import { afterEach, describe, expect, it, vi } from 'vitest'
import { ApiError } from '@/shared/api/apiError'
import { citizenService } from '../services/citizenService'
import type { Citizen } from '../types/citizen'
import { CitizensProvider } from './CitizensProvider'
import { useCitizens } from './citizensContext'

function makeCitizen(overrides: Partial<Citizen> = {}): Citizen {
  return {
    id: '1',
    name: 'Maria da Silva',
    cpf: '529.982.247-25',
    createdAt: '2026-01-01T00:00:00+00:00',
    ...overrides,
  }
}

function wrapper({ children }: { children: ReactNode }) {
  return <CitizensProvider>{children}</CitizensProvider>
}

describe('CitizensProvider', () => {
  afterEach(() => vi.restoreAllMocks())

  it('carrega a lista e calcula o total', async () => {
    vi.spyOn(citizenService, 'search').mockResolvedValue([
      makeCitizen({ id: '1' }),
      makeCitizen({ id: '2' }),
    ])
    const { result } = renderHook(() => useCitizens(), { wrapper })

    await act(() => result.current.load())

    expect(result.current.total).toBe(2)
  })

  it('retorna o cidadão cadastrado e recarrega a lista', async () => {
    const created = makeCitizen()
    vi.spyOn(citizenService, 'create').mockResolvedValue(created)
    vi.spyOn(citizenService, 'search').mockResolvedValue([created])
    const { result } = renderHook(() => useCitizens(), { wrapper })

    let outcome!: Awaited<ReturnType<typeof result.current.register>>
    await act(async () => {
      outcome = await result.current.register({ name: 'Maria da Silva', cpf: '52998224725' })
    })

    expect(outcome).toEqual({ ok: true, citizen: created })
    expect(result.current.total).toBe(1)
  })

  it('retorna a mensagem da API em caso de erro (ex.: CPF duplicado) sem afetar o erro da listagem', async () => {
    vi.spyOn(citizenService, 'create').mockRejectedValue(new ApiError('CPF já cadastrado.', 409))
    const { result } = renderHook(() => useCitizens(), { wrapper })

    let outcome!: Awaited<ReturnType<typeof result.current.register>>
    await act(async () => {
      outcome = await result.current.register({ name: 'Maria', cpf: '52998224725' })
    })

    expect(outcome).toEqual({ ok: false, error: 'CPF já cadastrado.' })
    expect(result.current.error).toBeNull()
  })
})
