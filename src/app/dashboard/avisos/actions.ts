'use server';

import { prisma } from '@/lib/prisma';
import { getUsuarioLogado } from '@/lib/auth';
import { revalidatePath } from 'next/cache';

export async function getAvisos() {
  return await prisma.aviso.findMany({
    orderBy: { createdAt: 'desc' },
  });
}

export async function criarAviso(formData: FormData) {
  // 1. Busca o usuário logado para obter o id
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    throw new Error('Usuário não autenticado.');
  }

  const titulo = formData.get('titulo') as string;
  const conteudo = formData.get('conteudo') as string;

  if (!titulo || !conteudo) return;

  // 2. Salva o aviso relacionando com o userId obtido
  await prisma.aviso.create({
    data: {
      titulo,
      conteudo,
      userId: usuario.id,
    },
  });

  revalidatePath('/dashboard/avisos');
  revalidatePath('/dashboard');
}