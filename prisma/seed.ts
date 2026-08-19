import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  await prisma.espaco.deleteMany();

  await prisma.espaco.createMany({
    data: [
      {
        nome: 'Salão de Festas Principal',
        descricao: 'Ambiente climatizado, com freezer, mesas, cadeiras e sistema de som integrado.',
        capacidade: 50,
      },
      {
        nome: 'Churrasqueira Gourmet',
        descricao: 'Área coberta com churrasqueira a carvão, bancada em granito e utensílios básicos.',
        capacidade: 15,
      },
      {
        nome: 'Quadra Poliesportiva',
        descricao: 'Quadra para futebol de salão, basquete e vôlei.',
        capacidade: 20,
      },
    ],
  });

  console.log('Banco de dados semeado com sucesso!');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });