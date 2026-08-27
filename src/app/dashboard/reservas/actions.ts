'use server';

import { prisma } from '@/lib/prisma';
import { revalidatePath } from 'next/cache';

export async function getReservas() {
  return await prisma.reserva.findMany({
    include: {
      espaco: true,
      user: {
        select: {
          nome: true,
          bloco: true,
          apartamento: true,
        },
      },
    },
    orderBy: {
      dataInicio: 'asc',
    },
  });
}

export async function criarReserva(formData: FormData) {
  const espacoId = formData.get('espacoId') as string;
  const userId = formData.get('userId') as string;
  const dataInicioStr = formData.get('dataInicio') as string;
  const dataFimStr = formData.get('dataFim') as string;

  if (!espacoId || !userId || !dataInicioStr) {
    throw new Error('Campos obrigatórios ausentes.');
  }

  const dataInicio = new Date(dataInicioStr);
  const dataFim = dataFimStr
    ? new Date(dataFimStr)
    : new Date(new Date(dataInicioStr).setHours(23, 59, 59));

  await prisma.reserva.create({
    data: {
      espacoId,
      userId,
      dataInicio,
      dataFim,
    },
  });

  revalidatePath('/dashboard/reservas');
  revalidatePath('/dashboard');
}