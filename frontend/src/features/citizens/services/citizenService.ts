import { ApiError } from '@/shared/api/apiError'
import type { Citizen, CreateCitizenPayload } from '../types/citizen'

/** Consome os endpoints REST de cidadãos via fetch. */
const baseUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:8000'
const resource = '/api/v1/citizens'

async function readBody(response: Response) {
  const body = await response.json().catch(() => null)

  if (!response.ok) {
    throw new ApiError(body?.error?.message ?? 'Erro inesperado ao comunicar com o servidor.', response.status)
  }

  return body
}

export const citizenService = {
  async create(payload: CreateCitizenPayload): Promise<Citizen> {
    const response = await fetch(`${baseUrl}${resource}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })

    return readBody(response)
  },

  async search(term: string): Promise<Citizen[]> {
    const query = term.trim() ? `?search=${encodeURIComponent(term.trim())}` : ''
    const response = await fetch(`${baseUrl}${resource}${query}`)

    return readBody(response)
  },
}
