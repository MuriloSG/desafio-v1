import { createContext, useContext } from 'react'
import type { Citizen, CreateCitizenPayload } from '../types/citizen'

/** Resultado do cadastro: o cidadão criado ou a mensagem de erro da API. */
export type RegisterResult = { ok: true; citizen: Citizen } | { ok: false; error: string }

/** Contrato do estado de cidadãos exposto pelo provider. */
export interface CitizensContextValue {
  citizens: Citizen[]
  searchedTerm: string
  loading: boolean
  error: string | null
  total: number
  load: (term?: string) => Promise<void>
  register: (payload: CreateCitizenPayload) => Promise<RegisterResult>
}

export const CitizensContext = createContext<CitizensContextValue | null>(null)

/** Acessa o estado de cidadãos; falha se usado fora do provider. */
export function useCitizens(): CitizensContextValue {
  const context = useContext(CitizensContext)
  if (!context) {
    throw new Error('useCitizens deve ser usado dentro de <CitizensProvider>.')
  }
  return context
}
