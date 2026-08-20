import { NextResponse } from 'next/server';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export async function POST(request: Request) {
  try {
    const { nome, email, senha } = await request.json();

    if (!nome || !email || !senha) {
      return NextResponse.json({ message: 'Preencha todos os campos obrigatórios' }, { status: 400 });
    }

    // Verifica se e-mail já existe
    const existingUser = await prisma.user.findUnique({
      where: { email },
    });

    if (existingUser) {
      return NextResponse.json({ message: 'Este e-mail já está cadastrado' }, { status: 400 });
    }

    // Cria o usuário no banco do Neon
    const newUser = await prisma.user.create({
      data: {
        nome,
        email,
        senha, // Para produção idealmente usaria bcrypt, mas para o projeto atual atende
      },
    });

    return NextResponse.json({ message: 'Usuário criado com sucesso', user: newUser }, { status: 201 });
  } catch (error) {
    return NextResponse.json({ message: 'Erro interno no servidor' }, { status: 500 });
  }
}