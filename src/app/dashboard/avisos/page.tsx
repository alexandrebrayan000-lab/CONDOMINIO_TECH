import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { getAvisos, criarAviso } from './actions';

type AvisoType = {
  id: string;
  titulo: string;
  conteudo: string;
  createdAt: Date;
};

export default async function AvisosPage() {
  const avisos: AvisoType[] = await getAvisos();

  return (
    <DashboardLayout userType="morador">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Mural de Avisos</h1>
        <p className="text-sm text-slate-400 mt-1">
          Fique por dentro das comunicações e notícias importantes do condomínio
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Formulário para Novo Aviso */}
        <Card className="lg:col-span-1 p-6 h-fit">
          <h2 className="text-base font-semibold text-white mb-4">Novo Aviso</h2>
          <form action={criarAviso} className="flex flex-col gap-4">
            <Input 
              label="Título"
              name="titulo"
              type="text"
              placeholder="Ex: Manutenção no Elevador"
              required
            />

            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-medium text-slate-300">Mensagem</label>
              <textarea 
                name="conteudo"
                rows={4}
                className="w-full bg-slate-900 border border-slate-800 rounded-lg p-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500"
                placeholder="Escreva os detalhes do aviso..."
                required
              />
            </div>

            <Button type="submit" size="md" className="w-full mt-2">
              Publicar Aviso
            </Button>
          </form>
        </Card>

        {/* Lista de Avisos */}
        <div className="lg:col-span-2 flex flex-col gap-4">
          <h2 className="text-base font-semibold text-white mb-1">Avisos Recentes</h2>
          {avisos.length === 0 ? (
            <Card className="p-4 text-center">
              <p className="text-xs text-slate-400">Nenhum aviso publicado até o momento.</p>
            </Card>
          ) : (
            avisos.map((aviso: AvisoType) => (
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
      </div>
    </DashboardLayout>
  );
}