'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { fazerLogout } from '@/lib/auth-actions';

type DashboardLayoutProps = {
  children: React.ReactNode;
  userType?: 'MORADOR' | 'SINDICO' | 'PORTARIA';
};

export function DashboardLayout({ children, userType = 'MORADOR' }: DashboardLayoutProps) {
  const pathname = usePathname();

  const menus = {
    MORADOR: [
      { label: '📊 Painel Geral', href: '/dashboard' },
      { label: '📅 Minhas Reservas', href: '/dashboard/reservas' },
      { label: '📝 Abrir ocorrência', href: '/dashboard/ocorrencias' },
      { label: '📢 Mural de Avisos', href: '/dashboard/avisos' },
      { label: '⚙️ Configurações', href: '/dashboard/configuracoes' },
    ],
    SINDICO: [
      { label: '📊 Painel Geral', href: '/dashboard/admin' },
      { label: '📅 Todas as Reservas', href: '/dashboard' },
      { label: '📋 Ocorrências', href: '/dashboard/admin/ocorrencias' },
      { label: '📝 Publicar Avisos', href: '/dashboard/admin/avisos' },
      { label: '📢 Mural de Avisos', href: '/dashboard/avisos' },
      { label: '⚙️ Configurações', href: '/dashboard/configuracoes' },
    ],
    PORTARIA: [
      { label: '📋 Reservas do Dia', href: '/dashboard/portaria' },
      { label: '📝 Publicar Avisos', href: '/dashboard/admin/avisos' },
      { label: '📢 Mural de Avisos', href: '/dashboard/avisos' },
      { label: '⚙️ Configurações', href: '/dashboard/configuracoes' },
    ],
  };

  const navItems = menus[userType] || menus.MORADOR;

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex">
      <aside className="w-64 bg-slate-900 border-r border-slate-800 p-6 flex flex-col justify-between">
        <div>
          <div className="mb-6">
            <span className="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
              CondomínioTech
            </span>
            <div className="mt-2 px-2 py-0.5 bg-cyan-500/10 border border-cyan-500/20 rounded text-[10px] font-semibold text-cyan-400 w-fit uppercase">
              Operador: {userType}
            </div>
          </div>

          <nav className="flex flex-col gap-2">
            {navItems.map((item) => {
              const isActive = pathname === item.href;
              return (
                <Link
                  key={item.href}
                  href={item.href}
                  className={`px-3 py-2.5 rounded-lg text-sm font-medium transition ${
                    isActive
                      ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30'
                      : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50'
                  }`}
                >
                  {item.label}
                </Link>
              );
            })}
          </nav>
        </div>

        {/* Botão de Logout Funcional */}
        <button
        onClick={async () => await fazerLogout()}
        className="text-left text-xs text-red-400 hover:text-red-300 font-medium px-3 py-2 rounded-lg hover:bg-red-500/10 transition w-full cursor-pointer"
        >
        🚪 Sair do Sistema
        </button>
      </aside>

      <main className="flex-1 p-8 overflow-y-auto">{children}</main>
    </div>
  );
}