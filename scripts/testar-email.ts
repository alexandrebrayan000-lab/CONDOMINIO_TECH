import fs from 'fs';
import path from 'path';

// Carrega o .env manualmente para garantir execução direta via tsx/node
function carregarEnv() {
  const envPath = path.resolve(process.cwd(), '.env');
  if (!fs.existsSync(envPath)) return;

  const content = fs.readFileSync(envPath, 'utf-8');
  for (const line of content.split('\n')) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) continue;
    const eqIdx = trimmed.indexOf('=');
    if (eqIdx === -1) continue;
    const key = trimmed.slice(0, eqIdx).trim();
    let val = trimmed.slice(eqIdx + 1).trim();
    if ((val.startsWith('"') && val.endsWith('"')) || (val.startsWith("'") && val.endsWith("'"))) {
      val = val.slice(1, -1);
    }
    if (!process.env[key]) {
      process.env[key] = val;
    }
  }
}

carregarEnv();

import { isEmailConfigured, verificarConexaoEmail, enviarEmailRedefinicao } from '../src/lib/mail';

async function main() {
  console.log('\n======================================================');
  console.log('🔍 [DIAGNÓSTICO DE CONFIGURAÇÃO DE E-MAIL - CONDOMÍNIOTECH]');
  console.log('======================================================\n');

  console.log('Configurações atuais detectadas no .env:');
  console.log('  • EMAIL_SERVICE:        ', process.env.EMAIL_SERVICE || '(não definido - usando host SMTP)');
  console.log('  • EMAIL_SERVER_HOST:   ', process.env.EMAIL_SERVER_HOST || 'smtp.gmail.com (padrão)');
  console.log('  • EMAIL_SERVER_PORT:   ', process.env.EMAIL_SERVER_PORT || '587 (padrão)');
  console.log('  • EMAIL_SERVER_SECURE: ', process.env.EMAIL_SERVER_SECURE || 'false');
  console.log('  • EMAIL_SERVER_USER:   ', process.env.EMAIL_SERVER_USER ? `"${process.env.EMAIL_SERVER_USER}"` : '❌ [NÃO DEFINIDO]');
  console.log('  • EMAIL_SERVER_PASSWORD:', process.env.EMAIL_SERVER_PASSWORD ? '•••••••••••••••• (definido)' : '❌ [NÃO DEFINIDO]');
  console.log('  • EMAIL_FROM:          ', process.env.EMAIL_FROM || '(padrão)');
  console.log('  • NEXT_PUBLIC_APP_URL: ', process.env.NEXT_PUBLIC_APP_URL || 'http://localhost:3000');
  console.log('');

  if (!isEmailConfigured()) {
    console.log('⚠️  ATENÇÃO: As variáveis EMAIL_SERVER_USER e/ou EMAIL_SERVER_PASSWORD estão vazias.');
    console.log('Para que os e-mails com links e tokens cheguem na caixa de entrada:');
    console.log('');
    console.log('👉 Se você usa GMAIL:');
    console.log('   1. Acesse: https://myaccount.google.com/apppasswords');
    console.log('   2. Crie uma "Senha de app" com o nome "CondominioTech"');
    console.log('   3. Preencha no seu .env:');
    console.log('      EMAIL_SERVICE="gmail"');
    console.log('      EMAIL_SERVER_USER="seu-email@gmail.com"');
    console.log('      EMAIL_SERVER_PASSWORD="xxxx xxxx xxxx xxxx"');
    console.log('');
    console.log('👉 Se você usa outro servidor SMTP (Brevo, Hostinger, Outlook, etc.):');
    console.log('   Preencha EMAIL_SERVER_HOST, EMAIL_SERVER_PORT, EMAIL_SERVER_USER e EMAIL_SERVER_PASSWORD no .env.');
    console.log('');
    process.exit(1);
  }

  console.log('⏳ Testando conexão e autenticação com o servidor SMTP...');
  const teste = await verificarConexaoEmail();

  if (!teste.ok) {
    console.error('\n❌ ERRO NA CONEXÃO COM O SERVIDOR DE E-MAIL:');
    console.error(teste.message);
    console.log('\nVerifique suas credenciais no arquivo .env e tente novamente.\n');
    process.exit(1);
  }

  console.log('✅ ' + teste.message);

  // Destinatário para envio de teste
  const destinatario = process.argv[2] || process.env.EMAIL_SERVER_USER;

  if (destinatario) {
    console.log(`\n📧 Disparando e-mail de teste real com token para: ${destinatario}...`);
    try {
      const tokenTeste = 'token_teste_' + Math.random().toString(36).substring(2, 10);
      const resultado = await enviarEmailRedefinicao(destinatario, tokenTeste);
      console.log('\n🎉 E-MAIL ENVIADO COM SUCESSO!');
      console.log('Detalhes:', resultado);
      console.log(`\nVerifique a caixa de entrada (e pasta de spam) de ${destinatario} para confirmar o recebimento.`);
    } catch (err: unknown) {
      console.error('\n❌ Falha no disparo do e-mail de teste:', err);
      process.exit(1);
    }
  }

  console.log('\n======================================================\n');
}

main().catch((err) => {
  console.error('Erro inesperado no diagnóstico:', err);
  process.exit(1);
});
