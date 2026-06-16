import { Landmark } from 'lucide-react'

export function AppHeader() {
  return (
    <header className="bg-slate-800 text-white">
      <div className="mx-auto flex max-w-7xl items-center gap-3 px-6 py-4">
        <span className="flex h-10 w-10 items-center justify-center rounded-md bg-white/15">
          <Landmark className="h-5 w-5" />
        </span>
        <div>
          <p className="text-lg font-semibold leading-tight">Portal de Cidadãos</p>
        </div>
      </div>
    </header>
  )
}
