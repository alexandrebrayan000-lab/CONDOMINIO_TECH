'server action';
'use server';

import { revalidatePath } from 'next/cache';
import { prisma } from '@/lib/prisma';

export async function atualizarTipoUsuario(formData: FormData) {
  const userId = formData.get('userId') as string;
  const novoTipo = formData.get('tipo') as string;

  if (!userId || !novoTipo) return;

  await prisma.user.update({
    where: { id: userId },
    data: { tipo: novoTipo },
  });

  revalidatePath('/dashboard', 'layout');
}