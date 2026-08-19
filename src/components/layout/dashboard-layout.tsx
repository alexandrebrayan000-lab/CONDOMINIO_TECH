import React from 'react';
import Link from 'next/link';

interface DashboardLayoutProps {
  children: React.ReactNode;
  userType?: 'morador' | 'sindico';
}

export function DashboardLayout({ children, userType = 'morador' }: DashboardLayoutProps) {
  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col md:flex-row">
      
      {/* Sidebar Lateral */}
      <aside className="w-full md:w-64 bg-slate-900/60 border-r border-slate-800/80 p-6 flex flex-col justify-between shrink-0">
        <div>
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2 mb-8">
            <div className="w-8 h-8 rounded-lg bg-cyan-500 flex items-center justify-center text-slate-950 font-bold text-base shadow-lg shadow-cyan-500/20">
              CT
            </div>
            <span className="font-bold text-lg tracking-tight text-white">
              Condomínio<span className="text-cyan-400">Tech</span>
            </span>
          </Link>

          {/* Tag de Perfil */}
          <div className="mb-6 px-3 py-1.5 rounded-lg bg-slate-800/60 border border-slate-700/50 text-xs font-medium text-slate-400">
            Perfil: <span className="text-cyan-400 capitalize">{userType}</span>
          </div>

          {/* Links de Navegação */}
          <nav className="flex flex-col gap-1 text-sm font-medium">
            {userType === 'morador' ? (
              <>
                <Link href="/dashboard" className="px-3 py-2 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                  Dashboard
                </Link>
                <Link href="/reservas" className="px-3 py-2 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 transition-colors">
                  Reservas
                </Link>
                <Link href="/avisos" className="px-3 py-2 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 transition-colors">
                  Avisos
                </Link>
              </>
            ) : (
              <>
                <Link href="/sindico/dashboard" className="px-3 py-2 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                  Painel Síndico
                </Link>
                <Link href="/sindico/espacos" className="px-3 py-2 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 transition-colors">
                  Gerenciar Espaços
                </Link>
                <Link href="/sindico/reservas" className="px-3 py-2 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800/50 transition-colors">
                  Todas as Reservas
                </Link>
              </>
            )}
          </nav>
        </div>

        {/* Rodapé da Sidebar */}
        <div className="pt-6 border-t border-slate-800/80">
          <Link href="/login" className="text-xs text-rose-400 hover:underline flex items-center gap-1">
            Sair da conta
          </Link>
        </div>
      </aside>

      {/* Conteúdo Principal */}
      <main className="flex-1 p-6 md:p-10 max-w-7xl">
        {children}
      </main>

    </div>
  );
}