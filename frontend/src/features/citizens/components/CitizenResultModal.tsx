import { createPortal } from 'react-dom'
import { CheckCircle2, XCircle } from 'lucide-react'
import { formatDateTime } from '@/shared/utils/date'
import type { Citizen } from '../types/citizen'

/** Resultado do cadastro exibido no modal: o cidadão criado ou a mensagem de erro. */
export type CitizenResult =
  | { type: 'success'; citizen: Citizen }
  | { type: 'error'; message: string }

/** Modal com o desfecho do cadastro: sucesso (dados do cidadão) ou erro (mensagem da API). */
export function CitizenResultModal({ result, onClose }: { result: CitizenResult; onClose: () => void }) {
  return createPortal(
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
      onClick={(event) => {
        if (event.target === event.currentTarget) {
          onClose()
        }
      }}
    >
      <div className="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
        {result.type === 'success' ? (
          <>
            <div className="flex items-center gap-2 text-green-700">
              <CheckCircle2 className="h-6 w-6" />
              <h3 className="text-lg font-semibold">Cadastro realizado!</h3>
            </div>
            <dl className="mt-4 space-y-3 rounded-lg bg-slate-50 p-4">
              <div>
                <dt className="text-xs font-medium uppercase tracking-wide text-slate-500">Nome</dt>
                <dd className="font-semibold text-slate-900">{result.citizen.name}</dd>
              </div>
              <div>
                <dt className="text-xs font-medium uppercase tracking-wide text-slate-500">CPF</dt>
                <dd className="font-mono text-slate-900">{result.citizen.cpf}</dd>
              </div>
              <div>
                <dt className="text-xs font-medium uppercase tracking-wide text-slate-500">Cadastro</dt>
                <dd className="text-slate-700">{formatDateTime(result.citizen.createdAt)}</dd>
              </div>
            </dl>
          </>
        ) : (
          <>
            <div className="flex items-center gap-2 text-red-700">
              <XCircle className="h-6 w-6" />
              <h3 className="text-lg font-semibold">Não foi possível cadastrar</h3>
            </div>
            <p className="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">{result.message}</p>
          </>
        )}

        <div className="mt-6 flex justify-end">
          <button
            type="button"
            className="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-900"
            onClick={onClose}
          >
            Fechar
          </button>
        </div>
      </div>
    </div>,
    document.body,
  )
}
