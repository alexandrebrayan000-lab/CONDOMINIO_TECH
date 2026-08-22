import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { prisma } from '@/lib/prisma';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { FormReserva } from './form-reserva'; // Componente do formulário
import { Card } from '@/components/ui/card';

export const dynamic = 'force-dynamic';

export default async function ReservasPage() {
  const usuario = await getUsuarioLogado();
  if (!usuario) redirect('/login');

  // 1. Busca APENAS as reservas do morador logado para a listagem
  const minhasReservas = await prisma.reserva.findMany({
    where: { userId: usuario.id },
    include: { espaco: true },
    orderBy: { data: 'asc' },
  });

  // 2. Busca TODAS as reservas cadastradas para calcular ocupação de horários
  const todasReservas = await prisma.reserva.findMany({
    select: { espacoId: true, data: true, horario: true },
  });

  // 3. Busca lista de espaços disponíveis
  const espacos = await prisma.espaco.findMany();

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'PORTARIA' | 'SINDICO'}>
      <h1 className="text-2xl font-bold text-white mb-6">Agendamento de Espaços</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Formulário com seleção de horários disponíveis */}
        <div className="lg:col-span-1">
          <FormReserva espacos={espacos} todasReservas={todasReservas} />
        </div>

        {/* Minhas Reservas (Apenas do Morador Logado) */}
        <div className="lg:col-span-2">
          <h2 className="text-lg font-semibold text-white mb-4">Minhas Reservas</h2>
          {minhasReservas.length === 0 ? (
            <Card className="p-6 text-center text-slate-400 text-sm">
              Você não possui agendamentos ativos.
            </Card>
          ) : (
            <div className="flex flex-col gap-3">
              {minhasReservas.map((reserva) => (
                <Card key={reserva.id} className="p-4 bg-slate-900 border-slate-800">
                  <div className="flex justify-between items-center">
                    <div>
                      <p className="font-semibold text-white">{reserva.espaco.nome}</p>
                      <p className="text-xs text-slate-400 mt-1">
                        Data:{' '}
                        {new Date(reserva.data).toLocaleDateString('pt-BR', {
                          timeZone: 'UTC',
                        })}{' '}
                        • Horário: <span className="text-cyan-400">{reserva.horario}</span>
                      </p>
                    </div>
                    <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      Confirmada
                    </span>
                  </div>
                </Card>
              ))}
            </div>
          )}
        </div>
      </div>
    </DashboardLayout>
  );
}