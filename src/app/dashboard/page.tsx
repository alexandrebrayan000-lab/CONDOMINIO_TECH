import { redirect } from 'next/navigation';
import Link from 'next/link';
import { getUsuarioLogado } from '@/lib/auth';
import { prisma } from '@/lib/prisma';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

export const dynamic = 'force-dynamic';

export default async function DashboardMorador() {
  const usuarioLogado = await getUsuarioLogado();

  if (!usuarioLogado) {
    redirect('/login');
  }

  // Busca as informações completas do usuário no banco (incluindo bloco e apto)
  const usuario = await prisma.user.findUnique({
    where: { id: usuarioLogado.id },
  });

  if (!usuario) {
    redirect('/login');
  }

  // 1. Busca as reservas reais do morador logado
  const minhasReservas = await prisma.reserva.findMany({
    where: {
      userId: usuario.id,
    },
    include: {
      espaco: true,
    },
    orderBy: {
      dataInicio: 'asc',
    },
  });

  // 2. Descobre a próxima reserva
  const proximaReserva = minhasReservas[0];

  // Monta o texto de identificação (Bloco e Apto) com fallback caso não esteja preenchido
  const infoApartamento = usuario.bloco || usuario.apartamento
    ? `${usuario.bloco ? `Bloco ${usuario.bloco}` : ''}${usuario.bloco && usuario.apartamento ? ', ' : ''}${usuario.apartamento ? `Apto ${usuario.apartamento}` : ''}`
    : 'Condomínio Tech';

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'PORTARIA' | 'SINDICO'}>
      {/* Cabeçalho de Boas-vindas */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h1 className="text-2xl font-bold text-white">Olá, {usuario.nome}</h1>
          <p className="text-sm text-slate-400 mt-1">
            Condomínio Tech • {infoApartamento}
          </p>
        </div>
        <Link href="/dashboard/reservas">
          <Button size="sm">+ Nova Reserva</Button>
        </Link>
      </div>

      {/* Cards de Métricas e Atalhos Rápidos */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
            Próxima Reserva
          </span>
          <p className="text-lg font-bold text-white mt-2">
            {proximaReserva?.espaco?.nome || 'Nenhuma reserva'}
          </p>
          <p className="text-xs text-cyan-400 mt-1">
            {proximaReserva
              ? new Date(proximaReserva.dataInicio).toLocaleDateString('pt-BR', {
                  timeZone: 'UTC',
                })
              : 'Faça um agendamento'}
          </p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
            Avisos Não Lidos
          </span>
          <p className="text-lg font-bold text-white mt-2">2 Novos Avisos</p>
          <p className="text-xs text-slate-400 mt-1">Manutenção do elevador agendada</p>
        </Card>

        <Card>
          <span className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
            Espaços Livres Hoje
          </span>
          <p className="text-lg font-bold text-white mt-2">Churrasqueira & Quadra</p>
          <p className="text-xs text-emerald-400 mt-1">Disponíveis para agendamento</p>
        </Card>
      </div>

      {/* Seção das Minhas Reservas (Dinâmica) */}
      <div className="mb-8">
        <h2 className="text-lg font-semibold text-white mb-4">Minhas Reservas</h2>

        {minhasReservas.length === 0 ? (
          <Card className="p-6 text-center">
            <p className="text-slate-400 text-sm">
              Você ainda não possui reservas agendadas.
            </p>
            <Link href="/dashboard/reservas" className="text-cyan-400 text-xs hover:underline mt-2 inline-block">
              Clique aqui para reservar um espaço
            </Link>
          </Card>
        ) : (
          <div className="flex flex-col gap-3">
            {minhasReservas.map((reserva) => (
              <Card key={reserva.id} className="p-0 overflow-hidden">
                <div className="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-900/30">
                  <div>
                    <p className="font-semibold text-sm text-white">
                      {reserva.espaco?.nome || 'Espaço do Condomínio'}
                    </p>
                    <p className="text-xs text-slate-400">
                      Data:{' '}
                      {new Date(reserva.dataFim).toLocaleDateString('pt-BR', {
                        timeZone: 'UTC',
                      })}{' '}
                      • Capacidade: {reserva.espaco?.capacidade || 50} pessoas
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
    </DashboardLayout>
  );
}