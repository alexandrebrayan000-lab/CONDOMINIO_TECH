import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { getAvisos } from './actions';

type AvisoType = {
  id: string;
  titulo: string;
  conteudo: string;
  createdAt: Date;
};

export default async function MuralAvisosPage() {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    redirect('/login');
  }

  const avisos: AvisoType[] = await getAvisos();

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'SINDICO' | 'PORTARIA'}>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Mural de Avisos</h1>
        <p className="text-sm text-slate-400 mt-1">
          Comunicações e informativos oficiais do condomínio.
        </p>
      </div>

      <div className="flex flex-col gap-4 max-w-4xl">
        {avisos.length === 0 ? (
          <Card className="p-6 text-center">
            <p className="text-sm text-slate-400">Nenhum aviso publicado até o momento.</p>
          </Card>
        ) : (
          avisos.map((aviso) => (
            <Card key={aviso.id} className="p-5 flex flex-col gap-2">
              <div className="flex justify-between items-start">
                <h3 className="text-base font-bold text-white">{aviso.titulo}</h3>
                <span className="text-[10px] text-slate-500">
                  {new Date(aviso.createdAt).toLocaleDateString('pt-BR')}
                </span>
              </div>
              <p className="text-xs text-slate-300 whitespace-pre-line">{aviso.conteudo}</p>
            </Card>
          ))
        )}
      </div>
    </DashboardLayout>
  );
}