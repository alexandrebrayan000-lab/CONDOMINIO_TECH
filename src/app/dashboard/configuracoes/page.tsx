import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';

export default async function ConfiguracoesPage() {
  const usuarioAtual = await getUsuarioLogado();

  if (!usuarioAtual) {
    redirect('/login');
  }

  const isSindico = usuarioAtual.tipo === 'SINDICO';

  return (
    <DashboardLayout userType={usuarioAtual.tipo as 'MORADOR' | 'SINDICO' | 'PORTARIA'}>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Minha Conta</h1>
      </div>

      <Card className="p-6 max-w-xl">
        <h2 className="text-lg font-semibold text-white mb-4">Dados do Perfil</h2>
        <div className="space-y-3 text-sm text-slate-300">
          <p><strong>Nome:</strong> {usuarioAtual.nome}</p>
          <p><strong>E-mail:</strong> {usuarioAtual.email}</p>
          <p>
            <strong>Perfil de Acesso:</strong>{' '}
            <span className="px-2 py-0.5 rounded text-xs bg-cyan-500/20 text-cyan-400 font-bold">
              {usuarioAtual.tipo}
            </span>
          </p>
        </div>

        {!isSindico && (
          <p className="text-xs text-slate-500 mt-6 border-t border-slate-800 pt-4">
            Apenas a administração/síndico pode alterar níveis de permissão do sistema.
          </p>
        )}
      </Card>
    </DashboardLayout>
  );
}