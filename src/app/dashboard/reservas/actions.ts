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
  const horario = formData.get('horario') as string;

  if (!espacoId || !dataString || !horario) {
    throw new Error('Preencha todos os campos obrigatórios.');
  }

  const dataReserva = new Date(`${dataString}T00:00:00.000Z`);

  // Validação Anti-Duplicação de Horário
  const jaReservado = await prisma.reserva.findFirst({
    where: {
      espacoId,
      data: dataReserva,
      horario,
    },
  });

  if (jaReservado) {
    throw new Error('Este horário já está reservado por outro morador.');
  }

  await prisma.reserva.create({
    data: {
      espacoId,
      userId: usuario.id,
      nomeMorador: usuario.nome,
      data: dataReserva,
      horario,
    },
  });

  revalidatePath('/dashboard/reservas');
  revalidatePath('/dashboard');
}