import { useEffect, useState } from 'react'
import { Search, Users } from 'lucide-react'
import { formatDateTime } from '@/shared/utils/date'
import { useCitizens } from '../context/citizensContext'

export function CitizenList() {
  const { citizens, searchedTerm, total, load } = useCitizens()
  const [term, setTerm] = useState('')

  useEffect(() => {
    const debounce = setTimeout(() => load(term), 300)
    return () => clearTimeout(debounce)
  }, [term, load])

  return (
    <section className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <header className="mb-4 flex flex-wrap items-start justify-between gap-2">
        <div>
          <h2 className="flex items-center gap-2 text-lg font-semibold text-slate-900">
            <Users className="h-5 w-5 text-slate-700" /> Cidadãos registrados
          </h2>
          <p className="text-sm text-slate-500">Pesquise por nome ou CPF.</p>
        </div>
        <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
          {total} {total === 1 ? 'cidadão' : 'cidadãos'}
        </span>
      </header>

      <div className="relative mb-4">
        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <input
          value={term}
          onChange={(event) => setTerm(event.target.value)}
          type="search"
          placeholder="Pesquisar por nome ou CPF"
          className="w-full rounded-md border border-slate-300 py-2 pl-9 pr-3 text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        />
      </div>

      {citizens.length === 0 ? (
        <p className="rounded-md bg-slate-50 px-3 py-6 text-center text-sm text-slate-600">
          {searchedTerm.trim() !== '' ? 'Cidadão não encontrado' : 'Nenhum cidadão cadastrado.'}
        </p>
      ) : (
        <table className="w-full text-left text-sm">
          <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
            <tr>
              <th className="w-1/2 px-3 py-2 font-medium">Nome</th>
              <th className="px-3 py-2 font-medium">CPF</th>
              <th className="px-3 py-2 text-right font-medium">Cadastro</th>
            </tr>
          </thead>
          <tbody>
            {citizens.map((citizen) => (
              <tr
                key={citizen.id}
                className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
              >
                <td className="px-3 py-3 font-semibold text-slate-900">{citizen.name}</td>
                <td className="whitespace-nowrap px-3 py-3 font-mono tabular-nums text-slate-700">{citizen.cpf}</td>
                <td className="whitespace-nowrap px-3 py-3 text-right text-slate-500">{formatDateTime(citizen.createdAt)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </section>
  )
}
