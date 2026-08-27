import { getUsuarioLogado } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { getChamadosSindico, marcarComoVisto } from '../actions';
import { Card } from '@/components/ui/card';

export default async function ChamadosSindicoPage() {
  const usuario = await getUsuarioLogado();
  if (!usuario || usuario.tipo !== 'SINDICO') redirect('/dashboard');

  const chamados = await getChamadosSindico();

  return (
    <div className="p-6 max-w-5xl mx-auto space-y-6">
      <h1 className="text-2xl font-bold text-white">Gestão de Chamados e Ocorrências</h1>

      <div className="space-y-4">
        {chamados.map((item) => (
          <Card key={item.id} className="p-4 bg-slate-900 border-slate-800 flex justify-between items-center">
            <div className="space-y-1">
              <div className="flex items-center gap-2">
                <h3 className="font-bold text-white text-lg">{item.titulo}</h3>
                <span className="text-xs text-slate-400 bg-slate-800 px-2 py-0.5 rounded">
                  B. {item.user.bloco} / Apt {item.user.apartamento} - {item.user.nome}
                </span>
              </div>
              <p className="text-sm text-slate-300">{item.descricao}</p>
              <span className="text-xs text-slate-500 block">
                Aberto em: {new Date(item.createdAt).toLocaleString('pt-BR')}
              </span>
            </div>

            <div>
              {item.visto ? (
                <span className="text-xs bg-slate-800 text-slate-400 px-3 py-1.5 rounded border border-slate-700">
                  ✓ Ocorrência Vistas
                </span>
              ) : (
                <form action={async () => {
                  'use server';
                  await marcarComoVisto(item.id);
                }}>
                  <button type="submit" className="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                    Marcar como Visto
                  </button>
                </form>
              )}
            </div>
          </Card>
        ))}
      </div>
    </div>
  );
}