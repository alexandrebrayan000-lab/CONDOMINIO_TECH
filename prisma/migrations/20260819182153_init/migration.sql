-- CreateTable
CREATE TABLE "Espaco" (
    "id" TEXT NOT NULL PRIMARY KEY,
    "nome" TEXT NOT NULL,
    "descricao" TEXT NOT NULL,
    "capacidade" INTEGER NOT NULL,
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- CreateTable
CREATE TABLE "Reserva" (
    "id" TEXT NOT NULL PRIMARY KEY,
    "espacoId" TEXT NOT NULL,
    "nomeMorador" TEXT NOT NULL,
    "data" DATETIME NOT NULL,
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "Reserva_espacoId_fkey" FOREIGN KEY ("espacoId") REFERENCES "Espaco" ("id") ON DELETE RESTRICT ON UPDATE CASCADE
);
