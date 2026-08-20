import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  // 1. Limpa o banco respeitando as FKs
  await prisma.aviso.deleteMany();
  await prisma.reserva.deleteMany();
  await prisma.espaco.deleteMany();

  // 2. Garante a criação do Síndico
  await prisma.user.upsert({
    where: { email: 'sindico@condominio.com' },
    update: { tipo: 'SINDICO' },
    create: {
      nome: 'Síndico Principal',
      email: 'sindico@condominio.com',
      senha: '123',
      tipo: 'SINDICO',
    },
  });

  // 3. Garante a criação da Portaria
  await prisma.user.upsert({
    where: { email: 'portaria@condominio.com' },
    update: { tipo: 'PORTARIA' },
    create: {
      nome: 'Portaria 24h',
      email: 'portaria@condominio.com',
      senha: '123',
      tipo: 'PORTARIA',
    },
  });

  // 4. Cadastra os Espaços com a propriedade 'descricao' inclusa
  const espacos = [
    {
      id: 'salao-festas',
      nome: 'Salão de Festas',
      capacidade: 50,
      descricao: 'Espaço amplo equipado com freezer, mesas e sistema de som.',
    },
    {
      id: 'churrasqueira',
      nome: 'Churrasqueira',
      capacidade: 20,
      descricao: 'Área gourmet coberta ao ar livre para eventos menores.',
    },
    {
      id: 'academia',
      nome: 'Academia',
      capacidade: 10,
      descricao: 'Espaço com aparelhos para musculação e exercícios aeróbicos.',
    },
  ];

  for (const espaco of espacos) {
    await prisma.espaco.upsert({
      where: { id: espaco.id },
      update: espaco,
      create: espaco,
    });
  }

  console.log('Seed executado com sucesso!');
}

main()
  .then(async () => {
    await prisma.$disconnect();
  })
  .catch(async (e) => {
    console.error('Erro ao rodar seed:', e);
    await prisma.$disconnect();
    process.exit(1);
  });