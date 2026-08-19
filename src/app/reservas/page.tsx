import { DashboardLayout } from '@/components/layout/dashboard-layout';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { getEspacos, getReservas, criarReserva } from './actions';

type EspacoType = {
  id: string;
  nome: string;
  descricao: string;
  capacidade: number;
};

type ReservaType = {
  id: string;
  espacoId: string;
  nomeMorador: string;
  data: Date;
  espaco: EspacoType;
};

export default async function ReservasPage() {
  const espacos: EspacoType[] = await getEspacos();
  const reservas: ReservaType[] = await getReservas();

  return (
    <DashboardLayout userType="morador">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white">Reservar Espaço</h1>
        <p className="text-sm text-slate-400 mt-1">
          Escolha o local desejado e selecione a data para o agendamento no sistema
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Formulário de Agendamento */}
        <Card className="lg:col-span-1 p-6 h-fit">
          <h2 className="text-base font-semibold text-white mb-4">Novo Agendamento</h2>
          <form action={criarReserva} className="flex flex-col gap-4">
            
            <Input 
              label="Nome do Morador"
              name="nomeMorador"
              type="text"
              placeholder="Ex: João Silva"
              required
            />

            <div className="flex flex-col gap-1.5">
              <label className="text-xs font-medium text-slate-300">Selecione o Espaço</label>
              <select 
                name="espacoId"
                className="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-cyan-500"
                required
              >
                {espacos.map((espaco: EspacoType) => (
                  <option key={espaco.id} value={espaco.id}>
                    {espaco.nome} (Até {espaco.capacidade} pessoas)
                  </option>
                ))}
              </select>
            </div>

            <Input 
              label="Data da Reserva"
              name="data"
              type="date"
              required
            />

            <Button type="submit" size="md" className="w-full mt-2">
              Confirmar Reserva
            </Button>
          </form>
        </Card>

        {/* Coluna da Direita: Espaços Cadastrados e Reservas Salvas */}
        <div className="lg:col-span-2 flex flex-col gap-6">
          
          {/* Reservas Gravadas no Banco */}
          <div>
            <h2 className="text-base font-semibold text-white mb-3">Agendamentos Gravados no Banco (SQLite)</h2>
            {reservas.length === 0 ? (
              <Card>
                <p className="text-xs text-slate-400">Nenhuma reserva cadastrada no banco até o momento.</p>
              </Card>
            ) : (
              <div className="flex flex-col gap-3">
                {reservas.map((reserva: ReservaType) => (
                  <Card key={reserva.id} className="flex justify-between items-center py-3 px-4">
                    <div>
                      <p className="font-semibold text-sm text-white">{reserva.espaco.nome}</p>
                      <p className="text-xs text-slate-400">
                        Morador: <span className="text-slate-200">{reserva.nomeMorador}</span> • Data: {new Date(reserva.data).toLocaleDateString('pt-BR')}
                      </p>
                    </div>
                    <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                      Salvo no Banco
                    </span>
                  </Card>
                ))}
              </div>
            )}
          </div>

          {/* Espaços do Condomínio vindo do Banco */}
          <div>
            <h2 className="text-base font-semibold text-white mb-3">Espaços Disponíveis</h2>
            <div className="flex flex-col gap-4">
              {espacos.map((espaco: EspacoType) => (
                <Card key={espaco.id} className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                  <div>
                    <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                      Capacidade: {espaco.capacidade} Pessoas
                    </span>
                    <h3 className="text-lg font-bold text-white mt-1">{espaco.nome}</h3>
                    <p className="text-xs text-slate-400 mt-1">{espaco.descricao}</p>
                  </div>
                </Card>
              ))}
            </div>
          </div>

        </div>
      </div>
    </DashboardLayout>
  );
}