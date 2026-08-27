import { redirect } from 'next/navigation';
import { getUsuarioLogado } from '@/lib/auth';
import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { criarChamado, getChamadosMorador } from './actions';

export default async function ChamadosPage() {
  const usuario = await getUsuarioLogado();

  if (!usuario) {
    redirect('/login');
  }

  const chamados = await getChamadosMorador();

  return (
    <DashboardLayout userType={usuario.tipo as 'MORADOR' | 'SINDICO' | 'PORTARIA'}>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Abrir Chamado / Ocorrência</h1>
        <p className="text-sm text-slate-400 mt-1">
          Registre problemas ou solicitações diretamente para a administração do condomínio.
        </p>
      </div>

      <form action={criarChamado} className="flex flex-col gap-4 bg-slate-900 p-6 rounded-lg border border-slate-800 mb-8 max-w-2xl">
        <input
          name="titulo"
          placeholder="Título da ocorrência"
          required
          className="p-2.5 rounded bg-slate-800 text-white border border-slate-700 text-sm focus:outline-none focus:border-blue-500"
        />
        <textarea
          name="descricao"
          placeholder="Descreva o problema em detalhes..."
          rows={3}
          required
          className="p-2.5 rounded bg-slate-800 text-white border border-slate-700 text-sm focus:outline-none focus:border-blue-500"
        />
        <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded font-medium text-sm self-start transition">
          Enviar Chamado
        </button>
      </form>

      <div className="space-y-4 max-w-4xl">
        <h2 className="text-lg font-semibold text-white">Histórico de Ocorrências</h2>
        {chamados.length === 0 ? (
          <Card className="p-6 text-center">
            <p className="text-sm text-slate-400">Nenhum chamado aberto até o momento.</p>
          </Card>
        ) : (
          chamados.map((item) => (
            <Card key={item.id} className="p-4 bg-slate-900 border-slate-800 flex justify-between items-start">
              <div>
                <h3 className="font-bold text-white">{item.titulo}</h3>
                <p className="text-sm text-slate-400 mt-1">{item.descricao}</p>
                <span className="text-xs text-slate-500 mt-2 block">
                  {new Date(item.createdAt).toLocaleDateString('pt-BR')}
                </span>
              </div>
              <span className={`text-xs px-2.5 py-1 rounded font-semibold ${
                item.visto ? 'bg-green-900/50 text-green-400 border border-green-800' : 'bg-yellow-900/50 text-yellow-400 border border-yellow-800'
              }`}>
                {item.visto ? 'Visto pelo Síndico' : 'Pendente'}
              </span>
            </Card>
          ))
        )}
      </div>
    </DashboardLayout>
  );
}