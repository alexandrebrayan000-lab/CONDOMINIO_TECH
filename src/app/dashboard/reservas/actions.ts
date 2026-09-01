'use server';

import { prisma } from '@/lib/prisma';
import { getUsuarioLogado } from '@/lib/auth';
import { revalidatePath } from 'next/cache';

export async function getReservas() {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    return [];
  }

  try {
    const reservas = await prisma.reserva.findMany({
      where: {
        userId: usuario.id,
      },
      orderBy: {
        createdAt: 'desc',
      },
    });

    return reservas;
  } catch {
    return [];
  }
}

export async function criarReserva(formData: FormData) {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    throw new Error('Usuário não autenticado');
  }

  const espacoId = formData.get('espaco') as string;
  const dataInicioStr = formData.get('dataInicio') as string;
  const dataFimStr = formData.get('dataFim') as string;

  if (!espacoId || !dataInicioStr || !dataFimStr) {
    throw new Error('Todos os campos são obrigatórios');
  }

  await prisma.reserva.create({
    data: {
      espacoId,
      dataInicio: new Date(dataInicioStr),
      dataFim: new Date(dataFimStr),
      userId: usuario.id,
    },
  });

  revalidatePath('/dashboard/reservas');
}