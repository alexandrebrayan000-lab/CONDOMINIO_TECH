'use server';

import { PrismaClient } from '@prisma/client';
import { revalidatePath } from 'next/cache';

const prisma = new PrismaClient();

// Popula espaços básicos caso o banco esteja vazio
async function popularEspacosSeVazio() {
  const count = await prisma.espaco.count();
  if (count === 0) {
    await prisma.espaco.createMany({
      data: [
        { nome: 'Salão de Festas', descricao: 'Espaço amplo com churrasqueira e freezer.', capacidade: 50 },
        { nome: 'Churrasqueira Gourmet', descricao: 'Área coberta próxima à piscina.', capacidade: 20 },
        { nome: 'Quadra Poliesportiva', descricao: 'Quadra para futebol, basquete e vôlei.', capacidade: 15 },
      ],
    });
  }
}

export async function getEspacos() {
  await popularEspacosSeVazio();
  return await prisma.espaco.findMany({
    orderBy: { nome: 'asc' },
  });
}

export async function getReservas() {
  return await prisma.reserva.findMany({
    include: {
      espaco: true,
    },
    orderBy: { data: 'desc' },
  });
}

export async function criarReserva(formData: FormData) {
  const nomeMorador = formData.get('nomeMorador') as string;
  const espacoId = formData.get('espacoId') as string;
  const dataString = formData.get('data') as string;

  if (!nomeMorador || !espacoId || !dataString) {
    throw new Error('Todos os campos são obrigatórios.');
  }

  await prisma.reserva.create({
    data: {
      nomeMorador,
      espacoId,
      data: new Date(dataString),
    },
  });

  // Atualiza a tela instantaneamente
  revalidatePath('/dashboard');
}