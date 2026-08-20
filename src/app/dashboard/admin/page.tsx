import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { getEstatisticasAdmin } from './actions';

type ReservaComUser = {
  id: string;
  espacoId: string;
  data: Date;
  user?: {
    nome: string;
    email: string;
  } | null;
};

export default async function AdminDashboardPage() {
  const stats = await getEstatisticasAdmin();

  return (
    <DashboardLayout userType="SINDICO">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Painel Geral do Síndico</h1>
        <p className="text-sm text-slate-400 mt-1">
          Visão panorâmica de indicadores, usuários e atividades do condomínio.
        </p>
      </div>

      {/* Cards de Métricas */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card className="p-6 border-l-4 border-l-cyan-500">
          <p className="text-xs font-medium text-slate-400 uppercase tracking-wider">Total de Moradores</p>
          <h2 className="text-3xl font-extrabold text-white mt-2">{stats.totalMoradores}</h2>
          <span className="text-[11px] text-slate-500 mt-1 block">Usuários cadastrados</span>
        </Card>

        <Card className="p-6 border-l-4 border-l-blue-500">
          <p className="text-xs font-medium text-slate-400 uppercase tracking-wider">Reservas Totais</p>
          <h2 className="text-3xl font-extrabold text-white mt-2">{stats.totalReservas}</h2>
          <span className="text-[11px] text-slate-500 mt-1 block">Solicitações registradas</span>
        </Card>

        <Card className="p-6 border-l-4 border-l-purple-500">
          <p className="text-xs font-medium text-slate-400 uppercase tracking-wider">Avisos no Mural</p>
          <h2 className="text-3xl font-extrabold text-white mt-2">{stats.totalAvisos}</h2>
          <span className="text-[11px] text-slate-500 mt-1 block">Comunicados ativos</span>
        </Card>
      </div>

      {/* Tabela de Atividades Recentes */}
      <Card className="p-6">
        <h2 className="text-base font-semibold text-white mb-4">Últimas Reservas Efetuadas</h2>
        
        {stats.ultimasReservas.length === 0 ? (
          <p className="text-xs text-slate-400 py-4 text-center">Nenhuma reserva cadastrada no sistema.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm text-slate-300">
              <thead className="text-xs text-slate-400 uppercase bg-slate-900/50 border-b border-slate-800">
                <tr>
                  <th className="py-3 px-4">Morador</th>
                  <th className="py-3 px-4">Espaço (ID)</th>
                  <th className="py-3 px-4">Data</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800">
                {stats.ultimasReservas.map((reserva: ReservaComUser) => (
                  <tr key={reserva.id} className="hover:bg-slate-900/30 transition">
                    <td className="py-3 px-4 font-medium text-white">{reserva.user?.nome || 'Não identificado'}</td>
                    <td className="py-3 px-4 capitalize">{reserva.espacoId}</td>
                    <td className="py-3 px-4 text-slate-400">
                      {new Date(reserva.data).toLocaleDateString('pt-BR')}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Card>
    </DashboardLayout>
  );
}