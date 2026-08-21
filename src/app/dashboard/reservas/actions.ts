'use server';

import { prisma } from '@/lib/prisma';
import { getUsuarioLogado } from '@/lib/auth';
import { revalidatePath } from 'next/cache';

export async function criarReserva(formData: FormData) {
  const usuario = await getUsuarioLogado();
  if (!usuario) {
    throw new Error('Usuário não autenticado.');
  }

  const espacoId = formData.get('espacoId') as string;
  const dataString = formData.get('data') as string;

  if (!espacoId || !dataString) {
    throw new Error('Por favor, preencha todos os campos obrigatórios.');
  }

  // Evita problemas de fuso horário fixando o horário em meio-dia UTC
  const dataReserva = new Date(`${dataString}T12:00:00.000Z`);

  // Cria a reserva com o campo nomeMorador exigido pelo Prisma
  await prisma.reserva.create({
    data: {
      espacoId,
      userId: usuario.id,
      nomeMorador: usuario.nome,
      data: dataReserva,
    },
  });

  revalidatePath('/dashboard/reservas');
  revalidatePath('/dashboard');
}

export async function getDadosReservas() {
  const usuario = await getUsuarioLogado();
  if (!usuario) {
    throw new Error('Usuário não autenticado.');
  }

  const espacos = await prisma.espaco.findMany({
    orderBy: { nome: 'asc' },
  });

  const reservas = await prisma.reserva.findMany({
    include: {
      user: {
        select: {
          nome: true,
          email: true,
        },
      },
      espaco: true,
    },
    orderBy: {
      data: 'desc',
    },
  });

  return { espacos, reservas };
}