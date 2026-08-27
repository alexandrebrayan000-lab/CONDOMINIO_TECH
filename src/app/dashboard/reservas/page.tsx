import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { getReservas } from './actions';

type ReservaType = {
  id: string;
  dataInicio: Date;
  dataFim: Date;
  espaco: {
    nome: string;
  };
  user?: {
    nome: string;
    bloco?: string | null;
    apartamento?: string | null;
  };
};

export default async function ReservasPage() {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    redirect('/login');
  }

  const reservas: ReservaType[] = await getReservas();

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'SINDICO' | 'PORTARIA'}>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Minhas Reservas</h1>
        <p className="text-sm text-slate-400 mt-1">
          Acompanhe os agendamentos dos espaços comuns do condomínio.
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {reservas.length === 0 ? (
          <Card className="p-6 text-center col-span-2">
            <p className="text-sm text-slate-400">Nenhuma reserva encontrada.</p>
          </Card>
        ) : (
          reservas.map((reserva) => (
            <Card key={reserva.id} className="p-5 flex flex-col gap-2">
              <div className="flex justify-between items-center">
                <h3 className="font-bold text-white">{reserva.espaco.nome}</h3>
                <span className="text-xs text-slate-400 bg-slate-800 px-2 py-1 rounded">
                  {new Date(reserva.dataInicio).toLocaleDateString('pt-BR')}
                </span>
              </div>
              
              {reserva.user && (
                <p className="text-xs text-slate-400">
                  Reservado por: {reserva.user.nome} (B. {reserva.user.bloco ?? '-'} / Apt {reserva.user.apartamento ?? '-'})
                </p>
              )}
            </Card>
          ))
        )}
      </div>
    </DashboardLayout>
  );
}