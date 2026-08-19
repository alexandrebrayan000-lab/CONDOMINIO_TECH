import React from 'react';

interface CardProps {
  children: React.ReactNode;
  className?: string;
}

export function Card({ children, className = '' }: CardProps) {
  return (
    <div className={`bg-slate-900/50 border border-slate-800/80 rounded-xl p-5 backdrop-blur-sm shadow-xl ${className}`}>
      {children}
    </div>
  );
}