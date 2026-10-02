'use server';

import { prisma } from '@/lib/prisma';

interface ResetTokenRecord {
  id: string;
  email: string;
  token: string;
  expiresAt: Date;
}

async function buscarTokenNoBanco(token: string): Promise<ResetTokenRecord | null> {
  try {
    if ('passwordResetToken' in prisma && typeof (prisma as unknown as { passwordResetToken?: { findUnique: unknown } }).passwordResetToken?.findUnique === 'function') {
      const record = await (prisma as unknown as { passwordResetToken: { findUnique: (args: { where: { token: string } }) => Promise<ResetTokenRecord | null> } }).passwordResetToken.findUnique({
        where: { token },
      });
      if (record) return record;
    }
  } catch {
    // fallback para raw SQL
  }

  const results = await prisma.$queryRaw<ResetTokenRecord[]>`
    SELECT id, email, token, "expiresAt" FROM "PasswordResetToken" WHERE token = ${token} LIMIT 1
  `;
  return results[0] || null;
}

async function removerTokenNoBanco(id: string): Promise<void> {
  try {
    if ('passwordResetToken' in prisma && typeof (prisma as unknown as { passwordResetToken?: { delete: unknown } }).passwordResetToken?.delete === 'function') {
      await (prisma as unknown as { passwordResetToken: { delete: (args: { where: { id: string } }) => Promise<unknown> } }).passwordResetToken.delete({
        where: { id },
      });
      return;
    }
  } catch {
    // fallback para raw SQL
  }

  await prisma.$executeRaw`
    DELETE FROM "PasswordResetToken" WHERE id = ${id}
  `;
}

export async function redefinirSenha(formData: FormData) {
  const token = formData.get('token') as string;
  const novaSenha = formData.get('novaSenha') as string;
  const confirmarSenha = formData.get('confirmarSenha') as string;

  if (!token) {
    return { error: 'Token de redefinição não informado ou inválido.' };
  }

  if (!novaSenha || !confirmarSenha) {
    return { error: 'Por favor, preencha todos os campos.' };
  }

  if (novaSenha !== confirmarSenha) {
    return { error: 'As senhas informadas não coincidem.' };
  }

  if (novaSenha.length < 3) {
    return { error: 'A nova senha deve ter no mínimo 3 caracteres.' };
  }

  try {
    // 1. Valida se o token existe
    const resetToken = await buscarTokenNoBanco(token);

    if (!resetToken) {
      return { error: 'Link de redefinição inválido ou já utilizado.' };
    }

    // 2. Valida se não está expirado
    if (new Date() > new Date(resetToken.expiresAt)) {
      await removerTokenNoBanco(resetToken.id);
      return { error: 'Este link expirou. Por favor, solicite uma nova redefinição de senha.' };
    }

    // 3. Atualiza a senha do usuário
    await prisma.user.update({
      where: { email: resetToken.email },
      data: { senha: novaSenha },
    });

    // 4. Remove o token utilizado
    await removerTokenNoBanco(resetToken.id);

    return {
      success: true,
      message: 'Senha alterada com sucesso! Você já pode entrar com sua nova senha.',
    };
  } catch (error) {
    console.error('Erro ao redefinir senha:', error);
    return { error: 'Ocorreu um erro ao atualizar sua senha. Tente novamente.' };
  }
}

export async function verificarTokenValido(token: string) {
  if (!token) return false;
  try {
    const resetToken = await buscarTokenNoBanco(token);
    if (!resetToken) return false;
    if (new Date() > new Date(resetToken.expiresAt)) return false;
    return true;
  } catch {
    return false;
  }
}
