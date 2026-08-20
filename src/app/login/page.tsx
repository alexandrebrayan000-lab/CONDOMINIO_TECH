'use client';

import { useRouter } from 'next/navigation';

export default function LoginPage() {
  const router = useRouter();

  async function handleLogin(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);

    const res = await fetch('/api/auth/login', {
      method: 'POST',
      body: JSON.stringify({
        email: formData.get('email'),
        senha: formData.get('senha'),
      }),
      headers: { 'Content-Type': 'application/json' },
    });

    if (res.ok) {
      router.push('/dashboard');
      router.refresh();
    } else {
      alert('E-mail ou senha incorretos.');
    }
  }

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4">
      {/* VINCULAR O onSubmit AQUI */}
      <form onSubmit={handleLogin} className="bg-slate-900 border border-slate-800 p-8 rounded-xl w-full max-w-md flex flex-col gap-4">
        <h1 className="text-xl font-bold text-white mb-2">Entrar no CondomínioTech</h1>
        
        <div>
          <label className="text-xs font-medium text-slate-300 block mb-1">E-mail</label>
          <input 
            type="email" 
            name="email" 
            required 
            className="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500"
          />
        </div>

        <div>
          <label className="text-xs font-medium text-slate-300 block mb-1">Senha</label>
          <input 
            type="password" 
            name="senha" 
            required 
            className="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-sm text-slate-100 focus:outline-none focus:border-cyan-500"
          />
        </div>

        <button 
          type="submit" 
          className="w-full mt-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold py-3 rounded-lg transition"
        >
          Entrar
        </button>
      </form>
    </div>
  );
}