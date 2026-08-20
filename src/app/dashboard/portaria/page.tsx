import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { getReservasDoDia } from './actions';

export default async function PortariaPage() {
  const usuario = await getUsuarioLogado();

  if (!usuario) redirect('/login');
  if (usuario.tipo !== 'PORTARIA' && usuario.tipo !== 'SINDICO') redirect('/dashboard');

  const reservas = await getReservasDoDia();

  return (
    <DashboardLayout userType={usuario.tipo as 'PORTARIA' | 'SINDICO'}>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Controle de Portaria</h1>
        <p className="text-sm text-slate-400 mt-1">Liberação e uso dos espaços na data de hoje.</p>
      </div>

      <Card className="p-6">
        <h2 className="text-base font-semibold text-white mb-4">
          Agenda de Hoje - {new Date().toLocaleDateString('pt-BR')}
        </h2>

        {reservas.length === 0 ? (
          <p className="text-xs text-slate-400 py-6 text-center">Nenhuma reserva para hoje.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm text-slate-300">
              <thead className="text-xs text-slate-400 uppercase bg-slate-900 border-b border-slate-800">
                <tr>
                  <th className="py-3 px-4">Morador</th>
                  <th className="py-3 px-4">Espaço</th>
                  <th className="py-3 px-4">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800">
                {reservas.map((r) => (
                  <tr key={r.id}>
                    <td className="py-3 px-4 font-medium text-white">{r.user?.nome || 'N/I'}</td>
                    <td className="py-3 px-4 capitalize font-semibold text-cyan-400">{r.espacoId}</td>
                    <td className="py-3 px-4">
                      <span className="px-2 py-1 text-[10px] font-bold bg-emerald-500/10 text-emerald-400 rounded border border-emerald-500/20">
                        LIBERADO
                      </span>
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