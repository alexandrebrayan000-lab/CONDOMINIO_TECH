'use server';

import { prisma } from '@/lib/prisma';
import { getUsuarioLogado } from '@/lib/auth';
import { revalidatePath } from 'next/cache';

export async function criarChamado(formData: FormData) {
  const usuario = await getUsuarioLogado();
  if (!usuario) throw new Error('Não autorizado');

  const titulo = formData.get('titulo') as string;
  const descricao = formData.get('descricao') as string;

  if (!titulo || !descricao) return;

  await prisma.chamado.create({
    data: {
      titulo,
      descricao,
      userId: usuario.id,
    },
  });

  revalidatePath('/dashboard/chamados');
}

export async function getChamadosMorador() {
  const usuario = await getUsuarioLogado();
  if (!usuario) return [];

  return await prisma.chamado.findMany({
    where: { userId: usuario.id },
    orderBy: { createdAt: 'desc' },
  });
}

export async function getChamadosSindico() {
  return await prisma.chamado.findMany({
    include: {
      user: {
        select: { nome: true, bloco: true, apartamento: true },
      },
    },
    orderBy: { createdAt: 'desc' },
  });
}

export async function marcarComoVisto(chamadoId: string) {
  await prisma.chamado.update({
    where: { id: chamadoId },
    data: { visto: true, status: 'EM_ANALISE' },
  });

  revalidatePath('/dashboard/chamados');
}