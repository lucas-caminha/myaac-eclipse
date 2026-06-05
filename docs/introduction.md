# Introducao

## O que e o MyAAC Eclipse OT?

O **MyAAC Eclipse OT** e um fork personalizado do [MyAAC](https://my-aac.org/), um sistema de gerenciamento de conteudo (CMS) para servidores Open Tibia. Este projeto foi desenvolvido especificamente para o servidor **Eclipse OT**, trazendo uma identidade visual unica e customizacoes especificas.

## O que e o MyAAC?

MyAAC (My Automatic Account Creator) e um CMS poderoso e leve, totalmente responsivo, baseado em Bootstrap. Suas principais caracteristicas incluem:

- **Layout Responsivo**: Funciona perfeitamente em dispositivos moveis, tablets e desktops
- **Editor de Noticias**: Editor WYSIWYG baseado no TinyMCE com suporte a upload de imagens
- **Instalador Inteligente**: Ajusta automaticamente o banco de dados para seu servidor
- **Sistema de Plugins**: Extensivel atraves de temas e plugins
- **Painel Administrativo**: Interface completa para gerenciamento do servidor

## Caracteristicas do Eclipse OT

Este fork adiciona as seguintes personalizacoes:

### Identidade Visual
- Tema dark fantasy com esquema de cores vermelho/preto
- Logo exclusivo Eclipse OT com fundo transparente
- Background customizado estilo fortaleza sombria
- Favicon e touch icons personalizados

### Funcionalidades
- Menu simplificado sem itens desnecessarios (FAQ, Forum, etc.)
- Layout expandido para monitores grandes
- Boxes laterais configurados: donate, boosted, rank, discord
- Carousel de imagens na pagina inicial

### Conteudo
- News de boas-vindas pre-configurada
- Descricao das features do servidor (Eclipse Gates, PvP, Daily Objectives)

## Arquitetura do Projeto

```
myaac-eclipse/
├── docs/                    # Documentacao
├── nginx/                   # Configuracoes Nginx
├── scripts/                 # Scripts de operacao
├── sql/                     # Migracoes SQL
├── theme-canary/            # Arquivos do tema
│   └── themes/canary/
│       ├── boxes/           # Widgets laterais
│       ├── bootstrap/       # Framework CSS
│       ├── css/             # Estilos adicionais
│       ├── fonts/           # Fontes
│       ├── images/          # Imagens do tema
│       ├── config.ini       # Configuracao visual
│       ├── config.php       # Configuracao PHP
│       └── *.html.twig      # Templates Twig
└── config.local.php.example # Exemplo de configuracao
```

## Compatibilidade

O MyAAC Eclipse OT e compativel com:

- **MyAAC**: Versao 2.x ou superior
- **Open Tibia Servers**: Canary, TFS 1.x
- **PHP**: 8.2 ou superior
- **Banco de Dados**: MySQL/MariaDB
- **Servidor Web**: Nginx (recomendado) ou Apache

## Proximos Passos

1. Verifique os [Requisitos do Sistema](./requirements.md)
2. Siga o [Guia de Instalacao](./install.md)
3. Configure o [Tema](./theme.md) conforme necessario
