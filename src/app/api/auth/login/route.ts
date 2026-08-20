import { NextResponse } from 'next/server';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export async function POST(request: Request) {
  try {
    const { email, password } = await request.json();

    const user = await prisma.user.findUnique({
      where: { email },
    });

    if (!user || user.senha !== password) {
      return NextResponse.json({ message: 'E-mail ou senha incorretos' }, { status: 401 });
    }

    return NextResponse.json({ message: 'Login realizado com sucesso', user }, { status: 200 });
  } catch (error) {
    return NextResponse.json({ message: 'Erro interno no servidor' }, { status: 500 });
  }
}