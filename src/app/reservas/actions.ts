'use server';

import { prisma } from '@/lib/prisma';
import { revalidatePath } from 'next/cache';

export async function getEspacos() {
  return await prisma.espaco.findMany({
    orderBy: { nome: 'asc' },
  });
}

export async function getReservas() {
  return await prisma.reserva.findMany({
    include: { espaco: true },
    orderBy: { data: 'asc' },
  });
}

export async function criarReserva(formData: FormData): Promise<void> {
  const espacoId = formData.get('espacoId') as string;
  const nomeMorador = formData.get('nomeMorador') as string;
  const dataString = formData.get('data') as string;

  if (!espacoId || !nomeMorador || !dataString) {
    return;
  }

  const dataReserva = new Date(dataString);

  // Validação de conflito de data
  const reservaExistente = await prisma.reserva.findFirst({
    where: {
      espacoId: espacoId,
      data: dataReserva,
    },
  });

  if (reservaExistente) {
    return;
  }

  await prisma.reserva.create({
    data: {
      espacoId,
      nomeMorador,
      data: dataReserva,
    },
  });

  revalidatePath('/reservas');
  revalidatePath('/dashboard');
  revalidatePath('/sindico/dashboard');
}