'use server';

import crypto from 'crypto';
import { prisma } from '@/lib/prisma';
import { enviarEmailRedefinicao } from '@/lib/mail';

export async function solicitarRedefinicaoSenha(formData: FormData) {
  const email = (formData.get('email') as string)?.trim().toLowerCase();

  if (!email) {
    return { error: 'Por favor, informe seu e-mail cadastrado.' };
  }

  try {
    // 1. Valida se o e-mail inserido existe no banco
    const usuario = await prisma.user.findUnique({
      where: { email },
    });

    // Para evitar enumeração de usuários, retornamos mensagem amigável mesmo se não existir
    if (!usuario) {
      return {
        success: true,
        message: 'Se o e-mail informado estiver cadastrado, você receberá um link para redefinir sua senha.',
      };
    }

    // 2. Remove tokens anteriores não utilizados deste e-mail
    try {
      if ('passwordResetToken' in prisma && typeof (prisma as unknown as { passwordResetToken?: { deleteMany: unknown } }).passwordResetToken?.deleteMany === 'function') {
        await (prisma as unknown as { passwordResetToken: { deleteMany: (args: { where: { email: string } }) => Promise<unknown> } }).passwordResetToken.deleteMany({
          where: { email },
        });
      } else {
        await prisma.$executeRaw`DELETE FROM "PasswordResetToken" WHERE email = ${email}`;
      }
    } catch {
      await prisma.$executeRaw`DELETE FROM "PasswordResetToken" WHERE email = ${email}`;
    }

    // 3. Gera um token aleatório e expiração de 1 hora
    const token = crypto.randomBytes(32).toString('hex');
    const expiresAt = new Date(Date.now() + 1000 * 60 * 60); // 1 hora
    const id = crypto.randomUUID();

    // 4. Salva no banco (compatível com prisma client estático ou consulta direta)
    try {
      if ('passwordResetToken' in prisma && typeof (prisma as unknown as { passwordResetToken?: { create: unknown } }).passwordResetToken?.create === 'function') {
        await (prisma as unknown as { passwordResetToken: { create: (args: { data: { id: string; email: string; token: string; expiresAt: Date } }) => Promise<unknown> } }).passwordResetToken.create({
          data: {
            id,
            email,
            token,
            expiresAt,
          },
        });
      } else {
        await prisma.$executeRaw`INSERT INTO "PasswordResetToken" ("id", "email", "token", "expiresAt", "createdAt") VALUES (${id}, ${email}, ${token}, ${expiresAt}, NOW())`;
      }
    } catch {
      await prisma.$executeRaw`INSERT INTO "PasswordResetToken" ("id", "email", "token", "expiresAt", "createdAt") VALUES (${id}, ${email}, ${token}, ${expiresAt}, NOW())`;
    }

    // 5. Dispara o e-mail
    await enviarEmailRedefinicao(email, token);

    return {
      success: true,
      message: 'Se o e-mail informado estiver cadastrado, você receberá um link para redefinir sua senha.',
    };
  } catch (error) {
    console.error('Erro ao solicitar redefinição de senha:', error);
    return { error: 'Ocorreu um erro ao processar sua solicitação. Tente novamente mais tarde.' };
  }
}
