'server action';
'use server';

import { PrismaClient } from '@prisma/client';
import { revalidatePath } from 'next/cache';

const prisma = new PrismaClient();

export async function getAvisos() {
  return await prisma.aviso.findMany({
    orderBy: { createdAt: 'desc' },
  });
}

export async function criarAviso(formData: FormData) {
  const titulo = formData.get('titulo') as string;
  const conteudo = formData.get('conteudo') as string;

  if (!titulo || !conteudo) return;

  await prisma.aviso.create({
    data: { titulo, conteudo },
  });

  revalidatePath('/dashboard/avisos');
}