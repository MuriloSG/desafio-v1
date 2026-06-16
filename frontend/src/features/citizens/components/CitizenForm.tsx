import { useState, type FormEvent } from 'react'
import { UserPlus } from 'lucide-react'
import { isValidCpf, maskCpf } from '@/shared/utils/cpf'
import { useCitizens } from '../context/citizensContext'
import { CitizenResultModal, type CitizenResult } from './CitizenResultModal'

export function CitizenForm() {
  const { register, loading } = useCitizens()

  const [name, setName] = useState('')
  const [cpf, setCpf] = useState('')
  const [result, setResult] = useState<CitizenResult | null>(null)

  const cpfInvalid = cpf.length > 0 && !isValidCpf(cpf)
  const canSubmit = name.trim().length > 0 && isValidCpf(cpf)

  async function onSubmit(event: FormEvent) {
    event.preventDefault()
    if (!canSubmit) {
      return
    }

    const outcome = await register({ name: name.trim(), cpf })

    if (outcome.ok) {
      setResult({ type: 'success', citizen: outcome.citizen })
      setName('')
      setCpf('')
    } else {
      setResult({ type: 'error', message: outcome.error })
    }
  }

  return (
    <section className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <header className="mb-4">
        <h2 className="flex items-center gap-2 text-lg font-semibold text-slate-900">
          <UserPlus className="h-5 w-5 text-green-600" /> Cadastrar cidadão
        </h2>
        <p className="text-sm text-slate-500">Informe o nome completo e o CPF para registrar.</p>
      </header>

      <form className="space-y-4" noValidate onSubmit={onSubmit}>
        <div>
          <label htmlFor="name" className="block text-sm font-medium text-slate-700">
            Nome completo
          </label>
          <input
            id="name"
            value={name}
            onChange={(event) => setName(event.target.value)}
            type="text"
            autoComplete="name"
            placeholder="Ex: Maria da Silva"
            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label htmlFor="cpf" className="block text-sm font-medium text-slate-700">
            CPF
          </label>
          <input
            id="cpf"
            value={cpf}
            onChange={(event) => setCpf(maskCpf(event.target.value))}
            inputMode="numeric"
            maxLength={14}
            placeholder="000.000.000-00"
            aria-invalid={cpfInvalid}
            className={`mt-1 w-full rounded-md border px-3 py-2 font-mono text-slate-900 focus:outline-none focus:ring-1 ${
              cpfInvalid
                ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500'
            }`}
          />
          {cpfInvalid && <p className="mt-1 text-sm text-red-600">CPF inválido.</p>}
        </div>

        <button
          type="submit"
          disabled={!canSubmit || loading}
          className="w-full rounded-md bg-green-600 px-4 py-2 font-medium text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
        >
          {loading ? 'Cadastrando...' : 'Cadastrar'}
        </button>
      </form>

      {result && <CitizenResultModal result={result} onClose={() => setResult(null)} />}
    </section>
  )
}
