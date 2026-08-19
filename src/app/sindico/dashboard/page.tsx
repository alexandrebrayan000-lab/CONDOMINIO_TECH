import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Link from 'next/link';

export default function DashboardSindico() {
  return (
    <DashboardLayout userType="sindico">
      {/* Cabeçalho */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h1 className="text-2xl font-bold text-white">Painel Administrativo</h1>
          <p className="text-sm text-slate-400 mt-1">
            Visão geral e gestão do Condomínio Tech
          </p>
        </div>
        <div className="flex gap-3">
          <Link href="/sindico/espacos">
            <Button variant="outline" size="sm">+ Espaço</Button>
          </Link>
          <Link href="/avisos">
            <Button size="sm">+ Novo Aviso</Button>
          </Link>
        </div>
      </div>

      {/* Indicadores Principais */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Moradores</span>
          <p className="text-2xl font-extrabold text-white mt-2">48</p>
          <p className="text-xs text-slate-500 mt-1">Cadastrados no sistema</p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Espaços Ativos</span>
          <p className="text-2xl font-extrabold text-cyan-400 mt-2">4</p>
          <p className="text-xs text-slate-500 mt-1">Disponíveis para agendamento</p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Reservas Este Mês</span>
          <p className="text-2xl font-extrabold text-white mt-2">12</p>
          <p className="text-xs text-emerald-400 mt-1">+3 solicitadas hoje</p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Avisos Ativos</span>
          <p className="text-2xl font-extrabold text-white mt-2">3</p>
          <p className="text-xs text-slate-500 mt-1">Visíveis no mural</p>
        </Card>
      </div>

      {/* Próximas Solicitções e Reservas */}
      <div>
        <h2 className="text-lg font-semibold text-white mb-4">Acompanhamento de Reservas</h2>
        <Card className="p-0 overflow-hidden">
          <div className="p-4 border-b border-slate-800/80 flex justify-between items-center bg-slate-900/30">
            <div>
              <p className="font-semibold text-sm text-white">Salão de Festas</p>
              <p className="text-xs text-slate-400">Morador: João Silva (Bloco A - Apt 102) • Data: 20/08/2026</p>
            </div>
            <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
              Confirmada
            </span>
          </div>
          <div className="p-4 flex justify-between items-center bg-slate-900/10">
            <div>
              <p className="font-semibold text-sm text-white">Churrasqueira Gourmet</p>
              <p className="text-xs text-slate-400">Moradora: Maria Souza (Bloco B - Apt 304) • Data: 22/08/2026</p>
            </div>
            <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
              Confirmada
            </span>
          </div>
        </Card>
      </div>
    </DashboardLayout>
  );
}