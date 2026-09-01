'use client';

import { criarReserva } from './actions';
import { Button } from '@/components/ui/button';

export default function FormReserva() {
  return (
    <form action={criarReserva} className="flex flex-col gap-4 bg-slate-900 p-6 rounded-lg border border-slate-800 mb-8 max-w-2xl">
      <div>
        <label className="block text-sm text-slate-400 mb-1">Espaço</label>
        <select name="espaco" required className="w-full p-2.5 rounded bg-slate-800 text-white border border-slate-700 text-sm focus:outline-none focus:border-cyan-500">
          <option value="">Selecione o local...</option>
          <option value="Salão de Festas">Salão de Festas</option>
          <option value="Churrasqueira">Churrasqueira</option>
          <option value="Quadra Poliesportiva">Quadra Poliesportiva</option>
        </select>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label className="block text-sm text-slate-400 mb-1">Data e Hora de Início</label>
          <input
            type="datetime-local"
            name="dataInicio"
            required
            className="w-full p-2.5 rounded bg-slate-800 text-white border border-slate-700 text-sm focus:outline-none focus:border-cyan-500"
          />
        </div>
        <div>
          <label className="block text-sm text-slate-400 mb-1">Data e Hora de Fim</label>
          <input
            type="datetime-local"
            name="dataFim"
            required
            className="w-full p-2.5 rounded bg-slate-800 text-white border border-slate-700 text-sm focus:outline-none focus:border-cyan-500"
          />
        </div>
      </div>

      <Button type="submit" variant="primary" className="self-start mt-2">
        Confirmar Reserva
      </Button>
    </form>
  );
}