import { getUsuarioLogado } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { criarChamado, getChamadosMorador } from '../actions';
import { Card } from '@/components/ui/card';

export default async function ChamadosMoradorPage() {
  const usuario = await getUsuarioLogado();
  if (!usuario) redirect('/login');

  const chamados = await getChamadosMorador();

  return (
    <div className="p-6 max-w-4xl mx-auto space-y-6">
      <h1 className="text-2xl font-bold text-white">Abrir Chamado / Ocorrência</h1>

      <form action={criarChamado} className="flex flex-col gap-4 bg-slate-900 p-4 rounded-lg border border-slate-800">
        <input
          name="titulo"
          placeholder="Título da ocorrência"
          required
          className="p-2 rounded bg-slate-800 text-white border border-slate-700"
        />
        <textarea
          name="descricao"
          placeholder="Descreva o problema em detalhes..."
          rows={3}
          required
          className="p-2 rounded bg-slate-800 text-white border border-slate-700"
        />
        <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-medium">
          Enviar Chamado
        </button>
      </form>

      <div className="space-y-4">
        <h2 className="text-lg font-semibold text-white">Meu Histórico de Ocorrências</h2>
        {chamados.map((item) => (
          <Card key={item.id} className="p-4 bg-slate-900 border-slate-800 flex justify-between items-start">
            <div>
              <h3 className="font-bold text-white">{item.titulo}</h3>
              <p className="text-sm text-slate-400 mt-1">{item.descricao}</p>
              <span className="text-xs text-slate-500 mt-2 block">
                {new Date(item.createdAt).toLocaleDateString('pt-BR')}
              </span>
            </div>
            <div className="flex flex-col items-end gap-2">
              <span className={`text-xs px-2 py-1 rounded font-semibold ${
                item.visto ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-400'
              }`}>
                {item.visto ? 'Visto pelo Síndico' : 'Pendente'}
              </span>
            </div>
          </Card>
        ))}
      </div>
    </div>
  );
}