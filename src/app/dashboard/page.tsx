import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Link from 'next/link';

export default function DashboardMorador() {
  return (
    <DashboardLayout userType="morador">
      {/* Cabeçalho de Boas-vindas */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h1 className="text-2xl font-bold text-white">Olá, Morador(a)</h1>
          <p className="text-sm text-slate-400 mt-1">
            Condomínio Tech • Bloco A, Apto 102
          </p>
        </div>
        <Link href="/reservas">
          <Button size="sm">+ Nova Reserva</Button>
        </Link>
      </div>

      {/* Cards de Métricas e Atalhos Rápidos */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Próxima Reserva</span>
          <p className="text-lg font-bold text-white mt-2">Salão de Festas</p>
          <p className="text-xs text-cyan-400 mt-1">20 de Agosto às 19:00</p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Avisos Não Lidos</span>
          <p className="text-lg font-bold text-white mt-2">2 Novos Avisos</p>
          <p className="text-xs text-slate-400 mt-1">Manutenção do elevador agendada</p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Espaços Livres Hoje</span>
          <p className="text-lg font-bold text-white mt-2">Churrasqueira & Quadra</p>
          <p className="text-xs text-emerald-400 mt-1">Disponíveis para agendamento</p>
        </Card>
      </div>

      {/* Seção das Minhas Reservas */}
      <div className="mb-8">
        <h2 className="text-lg font-semibold text-white mb-4">Minhas Agendadas</h2>
        <Card className="p-0 overflow-hidden">
          <div className="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-900/30">
            <div>
              <p className="font-semibold text-sm text-white">Salão de Festas Principal</p>
              <p className="text-xs text-slate-400">Data: 20/08/2026 • Capacidade: 50 pessoas</p>
            </div>
            <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
              Confirmada
            </span>
          </div>
        </Card>
      </div>
    </DashboardLayout>
  );
}