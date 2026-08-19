import Link from 'next/link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';

export default function CadastroPage() {
  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-center items-center px-4 py-12 selection:bg-cyan-500 selection:text-slate-950">
      
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
          <h1 className="text-xl font-semibold text-white">Crie sua conta</h1>
          <p className="text-xs text-slate-400 mt-1">
            Preencha os dados abaixo para se cadastrar na plataforma
          </p>
        </div>

        {/* Card do Formulário */}
        <Card className="p-6">
          <form className="flex flex-col gap-4">
            <Input 
              label="Nome Completo"
              type="text"
              placeholder="Seu nome"
              required
            />

            <Input 
              label="E-mail"
              type="email"
              placeholder="seu@email.com"
              required
            />

            <div className="grid grid-cols-2 gap-3">
              <Input 
                label="Bloco / Torre"
                type="text"
                placeholder="Ex: Bloco A"
              />
              <Input 
                label="Unidade / Apto"
                type="text"
                placeholder="Ex: 102"
              />
            </div>

            <Input 
              label="Senha"
              type="password"
              placeholder="••••••••"
              required
            />

            <Input 
              label="Confirmar Senha"
              type="password"
              placeholder="••••••••"
              required
            />

            <Button type="submit" size="lg" className="w-full mt-2">
              Cadastrar
            </Button>
          </form>

          {/* Divisor */}
          <div className="relative my-6 text-center text-xs">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-slate-800" />
            </div>
            <span className="relative bg-slate-900 px-3 text-slate-500 font-medium">
              Já possui uma conta?
            </span>
          </div>

          {/* Link para Login */}
          <Link href="/login">
            <Button variant="outline" className="w-full">
              Fazer Login
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