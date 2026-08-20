'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Erro ao realizar login');
      }

      // Redireciona diretamente para a Dashboard
      router.push('/dashboard');
      router.refresh();
    } catch (err: unknown) {
      if (err instanceof Error) {
        setError(err.message);
      } else {
        setError('Ocorreu um erro inesperado.');
      }
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-center items-center px-4 selection:bg-cyan-500 selection:text-slate-950">
      
      {/* Container Principal */}
      <div className="w-full max-w-md">
        
        {/* Logo e Título */}
        <div className="text-center mb-8">
          <Link href="/" className="inline-flex items-center gap-2 mb-4 group">
            <div className="w-10 h-10 rounded-xl bg-cyan-500 flex items-center justify-center text-slate-950 font-bold text-xl shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
              CT
            </div>
            <span className="font-bold text-2xl tracking-tight text-white">
              Condomínio<span className="text-cyan-400">Tech</span>
            </span>
          </Link>
          <h1 className="text-xl font-semibold text-white">Acesse sua conta</h1>
          <p className="text-xs text-slate-400 mt-1">
            Entre com suas credenciais para acessar o painel
          </p>
        </div>

        {/* Card do Formulário */}
        <Card className="p-6">
          <form onSubmit={handleSubmit} className="flex flex-col gap-4">
            
            {error && (
              <div className="p-3 rounded bg-red-500/10 border border-red-500/20 text-red-400 text-xs">
                {error}
              </div>
            )}

            <Input 
              label="E-mail"
              type="email"
              placeholder="seu@email.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />

            <div className="flex flex-col gap-1">
              <div className="flex justify-between items-center">
                <span className="text-xs font-medium text-slate-300">Senha</span>
                <a href="#" className="text-xs text-cyan-400 hover:underline">
                  Esqueceu a senha?
                </a>
              </div>
              <Input 
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>

            <Button type="submit" size="lg" className="w-full mt-2" disabled={loading}>
              {loading ? 'Entrando...' : 'Entrar'}
            </Button>
          </form>

          {/* Divisor */}
          <div className="relative my-6 text-center text-xs">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-slate-800" />
            </div>
            <span className="relative bg-slate-900 px-3 text-slate-500 font-medium">
              Ainda não tem conta?
            </span>
          </div>

          {/* Link para Cadastro */}
          <Link href="/cadastro">
            <Button variant="outline" className="w-full">
              Criar nova conta
            </Button>
          </Link>
        </Card>

        {/* Link para voltar à Home */}
        <div className="text-center mt-6">
          <Link href="/" className="text-xs text-slate-500 hover:text-slate-300 transition-colors">
            ← Voltar para a página inicial
          </Link>
        </div>

      </div>

    </div>
  );
}