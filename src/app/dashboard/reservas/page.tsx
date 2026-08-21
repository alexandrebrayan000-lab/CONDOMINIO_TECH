import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { criarReserva, getDadosReservas } from './actions';

export default async function ReservasPage() {
  const usuario = await getUsuarioLogado();
  if (!usuario) redirect('/login');

  const { espacos, reservas } = await getDadosReservas();

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'PORTARIA' | 'SINDICO'}>
      <div className="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-white">Reservas de Espaços</h1>
          <p className="text-sm text-slate-400 mt-1">
            Agende salão de festas, churrasqueira e outros ambientes do condomínio.
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Formulário de Nova Reserva */}
        <Card className="p-6 lg:col-span-1 h-fit">
          <h2 className="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <span className="w-2 h-2 rounded-full bg-cyan-400"></span>
            Nova Reserva
          </h2>

          <form action={criarReserva} className="flex flex-col gap-4">
            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-medium text-slate-300">Escolha o Espaço</label>
              <select
                name="espacoId"
                required
                className="w-full bg-slate-900 border border-slate-800 rounded-lg p-2.5 text-sm text-slate-100 focus:outline-none focus:border-cyan-500"
              >
                <option value="">Selecione um espaço...</option>
                {espacos.map((e) => (
                  <option key={e.id} value={e.id}>
                    {e.nome} (Capacidade: {e.capacidade} pessoas)
                  </option>
                ))}
              </select>
            </div>

            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-medium text-slate-300">Data da Reserva</label>
              <input
                type="date"
                name="data"
                required
                min={new Date().toISOString().split('T')[0]}
                className="w-full bg-slate-900 border border-slate-800 rounded-lg p-2.5 text-sm text-slate-100 focus:outline-none focus:border-cyan-500"
              />
            </div>

            <Button type="submit" size="md" className="w-full mt-2">
              Confirmar Reserva
            </Button>
          </form>
        </Card>

        {/* Tabela de Reservas Agendadas */}
        <Card className="p-6 lg:col-span-2">
          <h2 className="text-lg font-semibold text-white mb-4">Minhas Reservas e Próximos Eventos</h2>

          {reservas.length === 0 ? (
            <div className="py-12 text-center text-slate-500 text-sm">
              Nenhuma reserva cadastrada até o momento.
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm text-slate-300">
                <thead className="text-xs text-slate-400 uppercase bg-slate-900 border-b border-slate-800">
                  <tr>
                    <th className="py-3 px-4">Espaço</th>
                    <th className="py-3 px-4">Morador</th>
                    <th className="py-3 px-4">Data</th>
                    <th className="py-3 px-4">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {reservas.map((r) => (
                    <tr key={r.id} className="hover:bg-slate-900/40">
                      <td className="py-3 px-4 font-semibold text-cyan-400 capitalize">
                        {r.espaco?.nome || r.espacoId}
                      </td>
                      <td className="py-3 px-4 font-medium text-white">{r.user?.nome || 'Morador'}</td>
                      <td className="py-3 px-4 text-slate-300">
                        {new Date(r.data).toLocaleDateString('pt-BR', { timeZone: 'UTC' })}
                      </td>
                      <td className="py-3 px-4">
                        <span className="px-2 py-1 text-[10px] font-bold bg-emerald-500/10 text-emerald-400 rounded border border-emerald-500/20">
                          CONFIRMADA
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </Card>
      </div>
    </DashboardLayout>
  );
}