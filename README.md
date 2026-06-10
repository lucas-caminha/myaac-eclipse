# MyAAC Eclipse OT

Fork/customizacao do [MyAAC](https://github.com/slawkens/myaac) para o site do **Eclipse OT**, usando o tema Canary como base visual.

Este repositorio guarda a parte publica e versionavel da adaptacao do site: tema, assets, paginas customizadas, scripts SQL, exemplos de configuracao e documentacao operacional. Ele nao deve conter segredos de producao, dados reais de jogadores ou arquivos gerados em runtime.

## Base do Projeto

O Eclipse OT parte destes projetos:

- [MyAAC](https://github.com/slawkens/myaac): CMS/base original para servidores Open Tibia.
- [MyAAC Canary Theme](https://github.com/opentibiabr/myaac-canary-theme): tema Canary usado como ponto de partida visual.

Este projeto aplica uma camada propria por cima dessa base para atender ao servidor Eclipse OT.

## O Que Muda Em Relacao Ao MyAAC

| Area | MyAAC original | Eclipse OT |
|------|----------------|------------|
| Identidade visual | Visual generico/configuravel por tema | Rebrand Eclipse OT com logo, favicon, fundos e paleta dark fantasy |
| Tema | Temas padrao ou instalados separadamente | Tema Canary versionado em `theme-canary/themes/canary/` com overrides proprios |
| Menus | Estrutura padrao do MyAAC | Menu publico simplificado, com secoes ajustadas para o servidor |
| Conteudo | Noticias, paginas e configuracoes criadas no painel/admin | SQL versionado para news, menus e paginas customizadas |
| Paginas customizadas | Depende da instalacao e plugins | Paginas PHP dedicadas para downloads, regras, informacoes do servidor, eventos e VIP/Loyalty |
| Boxes laterais | Boxes padrao do tema | Boxes configurados para donate, boosted, rank, Discord, busca e outros atalhos do Eclipse OT |
| Operacao | Instalacao MyAAC comum | Documentacao de deploy, Nginx, cache, seguranca e manutencao do VPS |
| IA/agents | Sem instrucoes locais | `AGENTS.md` por area para guiar agentes de IA durante manutencao e desenvolvimento |

## Principais Customizacoes

- Tema Canary adaptado para o Eclipse OT.
- Logo, favicon, touch icons e imagens de marca proprias.
- Background e camada CSS customizada em `arise-overrides.css`.
- Layout e menu ajustados para a experiencia do servidor.
- Pagina de downloads com versoes do launcher e cliente.
- Paginas de regras, informacoes do OT, eventos e VIP/Loyalty.
- SQLs numerados para conteudo e configuracoes do MyAAC.
- Exemplos de Nginx, configuracao local e scripts operacionais.
- Documentacao em `docs/` para instalacao, deploy, seguranca e manutencao.

## Estrutura

```text
myaac-eclipse/
|-- AGENTS.md                    # Instrucoes gerais para agentes de IA
|-- config.local.php.example     # Exemplo de config local do MyAAC
|-- docs/                        # Documentacao do projeto
|-- nginx/                       # Exemplo de configuracao Nginx
|-- scripts/                     # Scripts operacionais de exemplo
|-- sql/                         # SQLs versionados para conteudo/config
`-- theme-canary/
    `-- themes/canary/
        |-- boxes/               # Boxes laterais
        |-- images/              # Assets do tema
        |-- pages/               # Paginas PHP customizadas
        |-- config.ini           # Configuracao visual
        |-- config.php           # Configuracao PHP do tema
        |-- menus.php            # Estrutura de menus
        |-- arise-overrides.css  # CSS customizado Eclipse OT
        `-- *.html.twig          # Templates Twig
```

## Documentacao

A documentacao completa fica em [docs/](docs/):

- [Introducao](docs/introduction.md)
- [Requisitos](docs/requirements.md)
- [Instalacao](docs/install.md)
- [Configuracao](docs/configuration.md)
- [Tema Canary](docs/theme.md)
- [SQL e migrations](docs/sql-migrations.md)
- [Operacoes](docs/operations.md)
- [Seguranca](docs/security.md)
- [Changelog](docs/changelog.md)

## Desenvolvimento Com IA

Este repositorio possui arquivos `AGENTS.md` em pontos importantes da arvore para orientar agentes de IA.

Antes de pedir uma alteracao grande, prefira tarefas pequenas e objetivas, por exemplo:

```text
Adicione uma pagina em theme-canary/themes/canary/pages/ seguindo o estilo das paginas existentes.
```

```text
Crie uma migration SQL segura para atualizar o menu do MyAAC e documente o impacto.
```

Os agentes devem respeitar as instrucoes locais, evitar segredos, manter o padrao MyAAC/Canary e atualizar a documentacao quando o comportamento mudar.

## O Que Nao Deve Ser Commitado

Nao commitar segredos, runtime ou dados reais:

- `config.local.php`
- senhas de banco de dados
- credenciais do painel MyAAC
- chaves SSH
- cache, logs e backups
- dumps de banco de producao
- dados reais de jogadores, contas ou personagens

## Deploy Rapido Do Tema

```bash
sudo rsync -a theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo find /var/www/html/system/cache -type f -delete
```

Depois, aplique apenas os SQLs necessarios para a mudanca:

```bash
mysql canary < sql/001-eclipse-news.sql
```

Veja o fluxo completo em [docs/install.md](docs/install.md) e [docs/operations.md](docs/operations.md).

## Compatibilidade

- MyAAC 2.x ou superior
- Tema Canary para MyAAC
- Canary ou TFS 1.x no lado do servidor OT
- PHP 8.2 ou superior
- MySQL/MariaDB
- Nginx recomendado

## Licenca e Creditos

Este projeto customiza o ecossistema MyAAC para o Eclipse OT. Consulte os projetos originais para detalhes de licenca e creditos:

- [MyAAC](https://github.com/slawkens/myaac)
- [MyAAC Canary Theme](https://github.com/opentibiabr/myaac-canary-theme)
