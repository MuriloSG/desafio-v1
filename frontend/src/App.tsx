import { AppHeader } from '@/shared/components/AppHeader'
import { CitizensProvider } from '@/features/citizens/context/CitizensProvider'
import { CitizenForm } from '@/features/citizens/components/CitizenForm'
import { CitizenList } from '@/features/citizens/components/CitizenList'

export default function App() {
  return (
    <div className="min-h-screen bg-slate-100">
      <AppHeader />

      <main className="mx-auto max-w-7xl px-6 py-8">
        <header className="mb-6">
          <h1 className="text-2xl font-bold text-slate-900">Cadastro de Cidadãos</h1>
          <p className="text-slate-500">Registre e consulte cidadãos brasileiros pelo CPF.</p>
        </header>

        <CitizensProvider>
          <div className="grid items-start gap-6 md:grid-cols-[minmax(0,26rem)_1fr]">
            <CitizenForm />
            <CitizenList />
          </div>
        </CitizensProvider>
      </main>
    </div>
  )
}
