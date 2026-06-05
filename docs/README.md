# MyAAC Eclipse OT - Documentacao

Bem-vindo a documentacao do **MyAAC Eclipse OT**, um fork personalizado do [MyAAC](https://my-aac.org/) com o tema Canary, customizado para o servidor Eclipse OT.

## Indice

- [Introducao](./introduction.md) - Visao geral do projeto
- [Requisitos](./requirements.md) - Requisitos de sistema
- [Instalacao](./install.md) - Guia de instalacao completo
- [Configuracao](./configuration.md) - Configuracao do tema e site
- [Tema Canary](./theme.md) - Personalizacao do tema
- [Migrações SQL](./sql-migrations.md) - Scripts de banco de dados
- [Operacoes](./operations.md) - Manutencao e operacoes do servidor
- [Seguranca](./security.md) - Boas praticas de seguranca
- [Changelog](./changelog.md) - Historico de alteracoes

## Links Uteis

- [Documentacao Oficial MyAAC](https://docs.my-aac.org/)
- [GitHub MyAAC](https://github.com/slawkens/myaac)
- [Tema Canary Original](https://github.com/opentibiabr/myaac-canary-theme)

## Sobre o Projeto

O MyAAC Eclipse OT e uma customizacao do sistema de gerenciamento de conteudo MyAAC, projetado especificamente para servidores Open Tibia (OTS). Este fork inclui:

- Tema Canary personalizado com identidade visual Eclipse OT
- Esquema de cores vermelho/preto estilo dark fantasy
- Logo e favicon customizados
- CSS adicional para branding
- Scripts SQL para conteudo inicial
- Documentacao de deploy e operacoes

## Inicio Rapido

```bash
# Clone o repositorio
git clone https://github.com/lucas-caminha/myaac-eclipse.git

# Deploy do tema
sudo rsync -a theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/

# Limpar cache
sudo find /var/www/html/system/cache -type f -delete
```

Consulte o [Guia de Instalacao](./install.md) para instrucoes detalhadas.
