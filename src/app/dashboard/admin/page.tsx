import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';

export default async function AdminDashboardPage() {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    redirect('/login');
  }

  // TRAVA DE SEGURANÇA: Se não for SÍNDICO, bloqueia o acesso imediatamente
  if (usuario.tipo !== 'SINDICO') {
    redirect('/dashboard'); // Manda o Morador ou Portaria de volta para o painel padrão
  }

  return (
    <DashboardLayout userType="SINDICO">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Painel Administrativo do Síndico</h1>
        {/* Conteúdo restrito do admin */}
      </div>
    </DashboardLayout>
  );
}