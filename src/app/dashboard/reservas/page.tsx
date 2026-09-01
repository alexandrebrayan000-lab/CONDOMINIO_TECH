import { getUsuarioLogado } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import FormReserva from './form-reserva';
import { getReservas } from './actions';
import { Card } from '@/components/ui/card';

interface ReservaItem {
  id: string;
  espacoId: string;
  dataInicio: Date;
  dataFim: Date;
}

export default async function ReservasPage() {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    redirect('/login');
  }

  const reservas: ReservaItem[] = await getReservas();

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'SINDICO' | 'PORTARIA'}>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Reservas de Espaços</h1>
        <p className="text-sm text-slate-400 mt-1">
          Agende o salão de festas, churrasqueira ou quadra do condomínio.
        </p>
      </div>

      <FormReserva />

      <div className="space-y-4 max-w-4xl">
        <h2 className="text-lg font-semibold text-white">Sustentação de Reservas</h2>
        {reservas.length === 0 ? (
          <Card className="p-6 text-center text-slate-400">Nenhuma reserva encontrada.</Card>
        ) : (
          reservas.map((reserva) => (
            <Card key={reserva.id} className="p-4 bg-slate-900 border-slate-800 flex justify-between items-center">
              <div>
                <h3 className="font-bold text-white">{reserva.espacoId}</h3>
                <span className="text-sm text-slate-400 mt-1 block">
                  Início: {new Date(reserva.dataInicio).toLocaleString('pt-BR')} <br />
                  Fim: {new Date(reserva.dataFim).toLocaleString('pt-BR')}
                </span>
              </div>
              <span className="text-xs bg-cyan-900/50 text-cyan-400 border border-cyan-800 px-3 py-1.5 rounded font-semibold">
                Confirmada
              </span>
            </Card>
          ))
        )}
      </div>
    </DashboardLayout>
  );
}