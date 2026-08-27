'server action';
'use server';

import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export async function getEstatisticasAdmin() {
  const [totalMoradores, totalReservas, totalAvisos, ultimasReservas] = await Promise.all([
    prisma.user.count({ where: { tipo: 'MORADOR' } }),
    prisma.reserva.count(),
    prisma.aviso.count(),
    prisma.reserva.findMany({
      take: 5,
      orderBy: { dataInicio: 'desc' },
      include: {
        user: {
          select: { nome: true, email: true },
        },
      },
    }),
  ]);

  return {
    totalMoradores,
    totalReservas,
    totalAvisos,
    ultimasReservas,
  };
}