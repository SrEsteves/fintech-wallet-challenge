# Fintech Wallet Challenge - Teck Soluções

MVP de uma carteira digital P2P onde usuários podem enviar e receber dinheiro entre si de forma simples e segura.

## Decisões Técnicas 

- **Service Layer**: Toda a lógica de transferência foi isolada em `TransferService`, mantendo os controllers limpos e focados exclusivamente em orquestração de requisição/resposta.
- **Atomicidade & Transações**: Implementação de `DB::transaction` para garantir a integridade dos dados (ACID). No driver MySQL, utilizei `lockForUpdate` para prevenir condições de corrida (race conditions) em acessos simultâneos ao saldo.
- **API RESTful**: Endpoints seguindo padrões REST, com respostas JSON consistentes e uso correto dos status HTTP.
- **Testes Automatizados**: Suite de testes de Feature cobrindo os cenários críticos: sucesso na transferência, falha por saldo insuficiente, proibição de transferência para si mesmo e validação de valores negativos.
- **Frontend Moderno**: Interface construída com Vue.js 3 utilizando a **Composition API** para melhor reuso de lógica e **Pinia** para um gerenciamento de estado global robusto.

## Tecnologias

- **Backend**: Laravel 10+, MySQL, Laravel Sanctum (Autenticação via Token).
- **Frontend**: Vue.js 3, Vite, Pinia, Vue Router, Axios.
- **Ambiente**: Laravel Sail (Docker).

## Como Rodar Localmente

### Pré-requisitos

- Docker Desktop (rodando)
- Node.js (versão 20 ou superior)
- Git

### Passo a passo

**1. Clone o repositório:**

```bash
git clone https://github.com/SrEsteves/fintech-wallet-challenge.git
cd fintech-wallet-challenge
```

**2. Backend (`fintech-wallet-api`):**

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

**3. Frontend (`fintech-wallet-web`):**

```bash
npm install
npm run dev
```

## Testes

Para validar as regras de negócio e a segurança das operações:

```bash
./vendor/bin/sail artisan test
```

## Credenciais para Teste

Utilize estas credenciais para testar as funcionalidades imediatamente na versão em produção ou após rodar os seeders localmente:

**Conta Principal (Com saldo):**
- **E-mail**: `teste@teck.com`
- **Senha**: `password`
- **Saldo Inicial**: R$ 1.000,00

**Conta de Destino (Para testar o envio de dinheiro):**
- **E-mail**: `recebedor@teck.com`
- **Senha**: `password`
- **Saldo Inicial**: R$ 0,00

## Link do Deploy

https://fintech-wallet-challenge-five.vercel.app/login
