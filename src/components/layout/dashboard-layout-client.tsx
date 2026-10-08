'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { fazerLogout } from '@/lib/auth-actions';
import { NotificationsDropdown, NotificacaoItem } from './notifications-dropdown';

interface DashboardLayoutClientProps {
  children: React.ReactNode;
  userType: 'MORADOR' | 'SINDICO' | 'PORTARIA';
  userName: string;
  userBloco: string | null;
  userApto: string | null;
  chamadosPendentesCount: number;
  avisosRecentesCount: number;
  notificacoes: NotificacaoItem[];
}

export function DashboardLayoutClient({
  children,
  userType,
  userName,
  userBloco,
  userApto,
  chamadosPendentesCount,
  avisosRecentesCount,
  notificacoes,
}: DashboardLayoutClientProps) {
  const pathname = usePathname();
  // No mobile inicia fechada para não atrapalhar a visão inicial; no desktop inicia aberta para facilidade de navegação
  const [mobileOpen, setMobileOpen] = useState(false);
  const [desktopOpen, setDesktopOpen] = useState(true);

  // Fecha a sidebar mobile ao mudar de página
  useEffect(() => {
    setMobileOpen(false);
  }, [pathname]);

  const toggleSidebar = () => {
    if (typeof window !== 'undefined' && window.innerWidth < 768) {
      setMobileOpen((prev) => !prev);
    } else {
      setDesktopOpen((prev) => !prev);
    }
  };

  const closeSidebar = () => {
    setMobileOpen(false);
    setDesktopOpen(false);
  };

  const menus = {
    MORADOR: [
      { label: '📊 Painel Geral', href: '/dashboard', badge: 0 },
      { label: '📅 Minhas Reservas', href: '/dashboard/reservas', badge: 0 },
      { label: '📝 Abrir Ocorrência', href: '/dashboard/chamadas', badge: 0 },
      { label: '📢 Mural de Avisos', href: '/dashboard/avisos', badge: avisosRecentesCount },
      { label: '⚙️ Minha Conta', href: '/dashboard/configuracoes', badge: 0 },
    ],
    SINDICO: [
      { label: '📊 Painel Geral', href: '/dashboard/admin', badge: 0 },
      { label: '📋 Ocorrências', href: '/dashboard/chamadas/sindico', badge: chamadosPendentesCount },
      { label: '📅 Gestão de Reservas', href: '/dashboard/reservas', badge: 0 },
      { label: '📝 Publicar Avisos', href: '/dashboard/admin/avisos', badge: 0 },
      { label: '📢 Mural de Avisos', href: '/dashboard/avisos', badge: avisosRecentesCount },
      { label: '⚙️ Minha Conta', href: '/dashboard/configuracoes', badge: 0 },
    ],
    PORTARIA: [
      { label: '📋 Reservas do Dia', href: '/dashboard/portaria', badge: 0 },
      { label: '📅 Todas as Reservas', href: '/dashboard/reservas', badge: 0 },
      { label: '📢 Mural de Avisos', href: '/dashboard/avisos', badge: avisosRecentesCount },
      { label: '⚙️ Minha Conta', href: '/dashboard/configuracoes', badge: 0 },
    ],
  };

  const navItems = menus[userType] || menus.MORADOR;
  const unreadTotal = userType === 'SINDICO' ? chamadosPendentesCount : 0;

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col md:flex-row relative">
      {/* Overlay escuro de fundo no mobile */}
      <div
        className={`fixed inset-0 z-40 bg-black/60 backdrop-blur-xs md:hidden transition-opacity duration-300 ${
          mobileOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'
        }`}
        onClick={() => setMobileOpen(false)}
        aria-hidden="true"
      />

      {/* Sidebar: Ocultável e expansível tanto no mobile quanto no desktop */}
      <aside
        className={`
          /* Mobile */
          fixed inset-y-0 left-0 z-50 bg-slate-900/95 border-r border-slate-800 backdrop-blur-md transition-all duration-300 ease-in-out overflow-y-auto
          ${mobileOpen ? 'translate-x-0 w-64 p-5' : '-translate-x-full w-64 p-5'}

          /* Desktop */
          md:static md:translate-x-0
          ${
            desktopOpen
              ? 'md:w-64 md:p-5 md:border-r md:opacity-100'
              : 'md:w-0 md:p-0 md:border-r-0 md:opacity-0 md:overflow-hidden md:pointer-events-none'
          }
        `}
      >
        <div className="w-[216px] flex flex-col justify-between min-h-full">
          <div>
            {/* Logo & Perfil de Operador */}
            <div className="mb-6 flex items-center justify-between">
              <Link
                href={userType === 'SINDICO' ? '/dashboard/admin' : userType === 'PORTARIA' ? '/dashboard/portaria' : '/dashboard'}
                className="flex items-center gap-2.5 group"
                onClick={() => setMobileOpen(false)}
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src="/favicon.ico"
                  alt="Logo CondomínioTech"
                  className="w-7 h-7 rounded-lg object-contain drop-shadow-md group-hover:scale-105 transition-transform"
                />
                <span className="text-xl font-extrabold bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 bg-clip-text text-transparent tracking-tight">
                  CondomínioTech
                </span>
              </Link>

              {/* Botão para ocultar/fechar a sidebar (visível em ambos) */}
              <button
                type="button"
                onClick={closeSidebar}
                className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/80 transition cursor-pointer"
                aria-label="Ocultar menu lateral"
                title="Ocultar menu lateral"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div className="mt-2.5 mb-6 flex items-center gap-2">
              <span
                className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold border tracking-wider uppercase ${
                  userType === 'SINDICO'
                    ? 'bg-amber-500/10 text-amber-400 border-amber-500/30'
                    : userType === 'PORTARIA'
                    ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                    : 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30'
                }`}
              >
                ● {userType}
              </span>
            </div>

            {/* Navegação Principal */}
            <nav className="flex flex-col gap-1.5">
              {navItems.map((item) => {
                const isActive = pathname === item.href;
                return (
                  <Link
                    key={item.href}
                    href={item.href}
                    onClick={() => setMobileOpen(false)}
                    className={`px-3.5 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-between ${
                      isActive
                        ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 font-semibold shadow-inner'
                        : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
                    }`}
                  >
                    <span className="truncate">{item.label}</span>
                    {item.badge > 0 && (
                      <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 ml-2 animate-pulse">
                        {item.badge}
                      </span>
                    )}
                  </Link>
                );
              })}
            </nav>
          </div>

          {/* Rodapé da Sidebar */}
          <div className="mt-8 pt-4 border-t border-slate-800/80 flex flex-col gap-3">
            <div className="px-2">
              <p className="text-xs font-semibold text-white truncate">{userName}</p>
              <p className="text-[11px] text-slate-400">
                {userBloco && userApto ? `Bloco. ${userBloco} • Apt ${userApto}` : 'Condomínio Tech'}
              </p>
            </div>

            <button
              onClick={async () => {
                setMobileOpen(false);
                await fazerLogout();
              }}
              className="text-left text-xs text-red-400 hover:text-red-300 font-medium px-3 py-2 rounded-lg hover:bg-red-500/10 transition w-full cursor-pointer flex items-center gap-2"
            >
              <span>🚪</span>
              <span>Sair do Sistema</span>
            </button>
          </div>
        </div>
      </aside>

      {/* Main Wrapper com Topbar */}
      <div className="flex-1 flex flex-col min-w-0 transition-all duration-300">
        {/* Topbar com z-index adequado para dropdown */}
        <header className="relative z-30 h-16 bg-slate-900/80 border-b border-slate-800/80 px-4 sm:px-8 flex items-center justify-between backdrop-blur-md">
          <div className="flex items-center gap-3">
            {/* Botão Hambúrguer acessível em ambos os formatos (mobile e desktop) */}
            <button
              type="button"
              onClick={toggleSidebar}
              className="p-2 -ml-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/60 focus:outline-none transition cursor-pointer flex items-center justify-center"
              aria-label="Alternar menu lateral"
              title="Alternar menu lateral"
            >
              <svg
                className="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </button>

            <span className="text-xs text-slate-400 hidden sm:inline">Portal do Condomínio</span>
            <span className="text-xs text-slate-600 hidden sm:inline">/</span>
            <span className="text-xs font-semibold text-cyan-400 capitalize">
              {pathname.replace('/dashboard', '').replace('/', '') || 'Início'}
            </span>
          </div>

          <div className="flex items-center gap-4">
            <NotificationsDropdown
              notificacoes={notificacoes}
              unreadCount={unreadTotal}
            />

            <div className="h-4 w-px bg-slate-800" />

            <div className="flex items-center gap-2.5">
              <div className="w-8 h-8 rounded-full bg-gradient-to-tr from-cyan-600 to-blue-600 flex items-center justify-center text-xs font-bold text-white shadow-md">
                {userName.charAt(0).toUpperCase()}
              </div>
              <div className="hidden lg:block">
                <span className="text-xs font-medium text-slate-200 block truncate max-w-[120px]">
                  {userName}
                </span>
                <span className="text-[10px] text-slate-400 block -mt-0.5 capitalize">
                  {userType.toLowerCase()}
                </span>
              </div>
            </div>
          </div>
        </header>

        {/* Conteúdo Principal */}
        <main className="flex-1 p-6 md:p-8 overflow-y-auto">{children}</main>
      </div>
    </div>
  );
}
