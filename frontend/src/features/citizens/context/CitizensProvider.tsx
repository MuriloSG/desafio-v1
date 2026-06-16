import { useCallback, useState, type ReactNode } from 'react'
import { ApiError } from '@/shared/api/apiError'
import { citizenService } from '../services/citizenService'
import type { Citizen, CreateCitizenPayload } from '../types/citizen'
import { CitizensContext, type RegisterResult } from './citizensContext'

function extractMessage(err: unknown): string {
  return err instanceof ApiError ? err.message : 'Não foi possível completar a operação.'
}

/** Provê o estado de cidadãos: orquestra o service e expõe lista, contadores e status. */
export function CitizensProvider({ children }: { children: ReactNode }) {
  const [citizens, setCitizens] = useState<Citizen[]>([])
  const [searchedTerm, setSearchedTerm] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  // Estável para o useEffect de busca da listagem.
  const load = useCallback(async (term = '') => {
    setLoading(true)
    setError(null)
    try {
      setCitizens(await citizenService.search(term))
      setSearchedTerm(term)
    } catch (err) {
      setError(extractMessage(err))
    } finally {
      setLoading(false)
    }
  }, [])

  /** Cadastra um cidadão; devolve o criado ou a mensagem de erro (tratada no formulário). */
  async function register(payload: CreateCitizenPayload): Promise<RegisterResult> {
    setLoading(true)
    try {
      const citizen = await citizenService.create(payload)
      await load()
      return { ok: true, citizen }
    } catch (err) {
      return { ok: false, error: extractMessage(err) }
    } finally {
      setLoading(false)
    }
  }

  return (
    <CitizensContext
      value={{
        citizens,
        searchedTerm,
        loading,
        error,
        total: citizens.length,
        load,
        register,
      }}
    >
      {children}
    </CitizensContext>
  )
}
