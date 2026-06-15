# Plugins do Eclipse OT

Os plugins customizados ficam em `plugins/` e devem ser copiados para a pasta de plugins do MyAAC junto com seus manifests JSON.

## Biblioteca do jogo

- `lua-monsters`: bestiário Canary sem dependência da extensão PHP Lua.
- `lua-spells`: lista magias e runas dos scripts RevScript.
- `powerful-guilds`: exibe as cinco guildas mais fortes na página de notícias.
- `character-sale`: mercado transacional de personagens usando a coluna `accounts.coins`.

Depois do deploy, aplique `sql/009-add-game-library-plugins.sql`, limpe o cache do MyAAC e use os botões administrativos nas páginas de monstros e magias para fazer a primeira carga.

O mercado valida propriedade, personagem offline e saldo novamente dentro de uma transação antes de transferir o personagem. As ofertas não movem antecipadamente o personagem para uma conta intermediária.

## Deploy

```bash
sudo rsync -a plugins/ /var/www/html/plugins/
mysql canary < sql/009-add-game-library-plugins.sql
sudo find /var/www/html/system/cache -type f -delete
```

Não sobrescreva configurações locais nem execute a migration sem conferir o banco de destino.
