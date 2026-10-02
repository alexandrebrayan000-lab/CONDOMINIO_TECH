import nodemailer from 'nodemailer';

/**
 * Cria a instância de transporte do Nodemailer de acordo com as variáveis do ambiente (.env).
 * Suporta tanto EMAIL_SERVICE (ex: "gmail", "hotmail") quanto host SMTP customizado.
 */
export function criarTransporter() {
  const service = process.env.EMAIL_SERVICE?.trim();
  const host = process.env.EMAIL_SERVER_HOST?.trim();
  const port = Number(process.env.EMAIL_SERVER_PORT) || 587;
  const secure = process.env.EMAIL_SERVER_SECURE === 'true' || port === 465;
  const user = process.env.EMAIL_SERVER_USER?.trim();
  // Se for senha de app do Google (16 caracteres com espaços), removemos os espaços automaticamente
  const pass = process.env.EMAIL_SERVER_PASSWORD?.trim().replace(/\s+/g, '');

  if (service) {
    return nodemailer.createTransport({
      service,
      auth: user && pass ? { user, pass } : undefined,
    });
  }

  return nodemailer.createTransport({
    host: host || 'smtp.gmail.com',
    port,
    secure,
    auth: user && pass ? { user, pass } : undefined,
  });
}

export const mailTransporter = criarTransporter();

/**
 * Verifica se as credenciais mínimas de envio de e-mail estão preenchidas no .env
 */
export function isEmailConfigured(): boolean {
  const user = process.env.EMAIL_SERVER_USER?.trim();
  const pass = process.env.EMAIL_SERVER_PASSWORD?.trim();
  return Boolean(user && pass);
}

/**
 * Testa a conexão e autenticação com o servidor SMTP.
 */
export async function verificarConexaoEmail(): Promise<{ ok: boolean; message: string }> {
  if (!isEmailConfigured()) {
    return {
      ok: false,
      message: 'Credenciais de e-mail não configuradas no arquivo .env (EMAIL_SERVER_USER ou EMAIL_SERVER_PASSWORD vazios).',
    };
  }

  try {
    const transporter = criarTransporter();
    await transporter.verify();
    return { ok: true, message: 'Conexão e autenticação com o servidor de e-mail estabelecidas com sucesso!' };
  } catch (err: unknown) {
    const error = err as { code?: string; message?: string; response?: string };
    console.error('❌ Falha ao verificar conexão SMTP:', error);
    let detalhe = error?.message || 'Erro desconhecido ao conectar ao servidor SMTP.';

    if (error?.code === 'EAUTH' || detalhe.includes('Username and Password not accepted')) {
      detalhe = 'Falha de autenticação. Verifique se o e-mail e a Senha de Aplicativo (App Password) estão corretos no .env.';
    } else if (error?.code === 'ESOCKET' || error?.code === 'ETIMEDOUT') {
      detalhe = `Não foi possível conectar ao servidor SMTP (${process.env.EMAIL_SERVER_HOST || 'smtp.gmail.com'}:${process.env.EMAIL_SERVER_PORT || 587}). Verifique a porta e as regras de firewall.`;
    }

    return { ok: false, message: detalhe };
  }
}

/**
 * Envia o e-mail com link e token de redefinição de senha.
 */
export async function enviarEmailRedefinicao(email: string, token: string) {
  let baseUrl = process.env.NEXT_PUBLIC_APP_URL?.trim();
  if (!baseUrl && process.env.VERCEL_PROJECT_PRODUCTION_URL) {
    baseUrl = `https://${process.env.VERCEL_PROJECT_PRODUCTION_URL}`;
  } else if (!baseUrl && process.env.VERCEL_URL) {
    baseUrl = `https://${process.env.VERCEL_URL}`;
  } else if (!baseUrl) {
    baseUrl = 'http://localhost:3000';
  }
  baseUrl = baseUrl.replace(/\/$/, '');
  const resetUrl = `${baseUrl}/redefinir-senha?token=${token}`;
  const from = process.env.EMAIL_FROM || (process.env.EMAIL_SERVER_USER ? `"CondomínioTech" <${process.env.EMAIL_SERVER_USER}>` : '"CondomínioTech" <nao-responda@condominiotech.com>');

  const html = `
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Redefinição de Senha - CondomínioTech</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #020617; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f8fafc;">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #020617; padding: 40px 20px;">
        <tr>
          <td align="center">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 520px; background-color: #0f172a; border: 1px solid #1e293b; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);">
              <tr>
                <td style="padding: 32px 32px 24px; text-align: center; border-bottom: 1px solid #1e293b;">
                  <div style="display: inline-block; width: 44px; height: 44px; line-height: 44px; border-radius: 12px; background-color: #06b6d4; color: #020617; font-weight: bold; font-size: 20px; margin-bottom: 12px;">
                    CT
                  </div>
                  <h1 style="margin: 0; font-size: 22px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px;">
                    Condomínio<span style="color: #22d3ee;">Tech</span>
                  </h1>
                </td>
              </tr>
              <tr>
                <td style="padding: 32px;">
                  <h2 style="margin: 0 0 12px; font-size: 18px; font-weight: 600; color: #ffffff;">
                    Redefinição de Senha
                  </h2>
                  <p style="margin: 0 0 20px; font-size: 14px; line-height: 22px; color: #94a3b8;">
                    Recebemos uma solicitação para redefinir a senha da sua conta vinculada ao e-mail <strong style="color: #f1f5f9;">${email}</strong>.
                  </p>
                  <p style="margin: 0 0 28px; font-size: 14px; line-height: 22px; color: #94a3b8;">
                    Clique no botão abaixo para escolher uma nova senha. Este link é válido por <strong>1 hora</strong>.
                  </p>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
                    <tr>
                      <td align="center">
                        <a href="${resetUrl}" target="_blank" style="display: inline-block; background-color: #06b6d4; color: #020617; font-weight: 700; font-size: 14px; text-decoration: none; padding: 14px 28px; border-radius: 10px; box-shadow: 0 4px 14px rgba(6, 182, 212, 0.35);">
                          Redefinir Minha Senha
                        </a>
                      </td>
                    </tr>
                  </table>
                  
                  <div style="background-color: #020617; border: 1px solid #1e293b; border-radius: 8px; padding: 12px; margin-bottom: 24px; word-break: break-all;">
                    <p style="margin: 0 0 6px; font-size: 11px; color: #64748b;">Se o botão não funcionar, copie e cole o link no seu navegador:</p>
                    <a href="${resetUrl}" target="_blank" style="font-size: 12px; color: #38bdf8; text-decoration: underline;">
                      ${resetUrl}
                    </a>
                  </div>

                  <p style="margin: 0; font-size: 12px; line-height: 18px; color: #64748b;">
                    Se você não solicitou a redefinição de senha, ignore esta mensagem. Sua conta permanece segura.
                  </p>
                </td>
              </tr>
              <tr>
                <td style="padding: 20px 32px; background-color: #0b1120; border-top: 1px solid #1e293b; text-align: center;">
                  <p style="margin: 0; font-size: 11px; color: #475569;">
                    © ${new Date().getFullYear()} CondomínioTech • Gestão Inteligente para Condomínios
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>
  `;

  // Se não houver credenciais configuradas no .env
  if (!isEmailConfigured()) {
    console.warn('\n⚠️ [CONDOMINIO-TECH AVISO DE E-MAIL]');
    console.warn('As credenciais de envio de e-mail (EMAIL_SERVER_USER / EMAIL_SERVER_PASSWORD) não estão preenchidas no arquivo .env.');
    console.warn('O link gerado para redefinição foi:');
    console.warn(`🔗 ${resetUrl}\n`);

    if (process.env.NODE_ENV !== 'production') {
      return { mock: true, resetUrl };
    }
    throw new Error('Serviço de envio de e-mail não configurado no servidor.');
  }

  try {
    const transporter = criarTransporter();
    const info = await transporter.sendMail({
      from,
      to: email,
      subject: 'Redefinição de Senha - CondomínioTech',
      html,
    });

    console.log(`✅ [EMAIL ENVIADO COM SUCESSO] Destinatário: ${email} | MessageID: ${info.messageId}`);
    return { success: true, messageId: info.messageId, resetUrl };
  } catch (error: unknown) {
    console.error('❌ [ERRO AO ENVIAR E-MAIL VIA SMTP]:', error);
    throw error;
  }
}
