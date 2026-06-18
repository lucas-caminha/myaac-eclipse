# Mapa LGPD do Eclipse OT

Este documento descreve os principais tratamentos de dados pessoais do site Eclipse OT e serve como guia tecnico para manutencao, auditoria e proximas alteracoes.

Base legal de referencia: Lei 13.709/2018 (LGPD), especialmente os principios de finalidade, adequacao, necessidade, livre acesso, transparencia, seguranca e prevencao.

## Dados Coletados

| Area | Dados | Finalidade | Observacao |
|------|-------|------------|------------|
| Conta | nome da conta, email, senha hash, data de criacao, ultimo login | autenticar, recuperar e proteger a conta | senha nunca deve ser exibida ou logada |
| Personagens | nome, vocacao, level, guild, outfit, status | operacao publica do jogo e rankings | dados de jogo sao publicos por natureza |
| Perfil publico | localizacao/pais quando preenchidos | exibicao opcional no perfil | manter opcional |
| Perfil para doacoes | nome completo, data de nascimento, CPF | validacao de doacoes/pagamentos | coletar somente quando necessario para apoiar o servidor |
| Doacoes | account_id, pacote, valor, coins, status, gateway, referencia Pix | processar e auditar apoio ao servidor | evitar duplicar CPF quando ja existir na conta |
| Logs de seguranca | IP, acao, data | prevencao de fraude e protecao da conta | reter pelo menor prazo operacional viavel |
| Mercado de personagens | vendedor, comprador, personagem, preco | executar transacao interna de coins | nao expor dados reais do vendedor/comprador |

## Regras Tecnicas

- Coletar o minimo necessario para cada fluxo.
- CPF, nascimento e nome completo nao devem ser exigidos para jogar.
- CPF deve ser mascarado em telas e logs sempre que possivel.
- Dados de pagamento devem ser acessados somente por quem administra suporte financeiro.
- Tabelas com dados pessoais precisam de backup protegido e nao devem aparecer em dumps publicos.
- Qualquer nova integracao externa deve documentar quais dados sao enviados ao operador.

## Direitos do Titular

O site deve oferecer caminho simples para o usuario:

- consultar os dados associados a conta;
- corrigir dados incompletos, inexatos ou desatualizados;
- solicitar exclusao, bloqueio ou anonimizacao quando aplicavel;
- saber com quem seus dados podem ser compartilhados;
- entender as consequencias de nao fornecer dados opcionais.

## Retencao Recomendada

| Dado | Retencao sugerida |
|------|-------------------|
| Intencoes de doacao pendentes/canceladas | 90 dias |
| Logs de conta e seguranca | 180 dias |
| Solicitudes LGPD | 5 anos, apenas como prova de atendimento |
| Dados fiscais/pagamento aprovado | prazo legal aplicavel |
| Conta sem uso e sem pendencias | avaliar anonimizacao mediante solicitacao |

## Pontos de Atencao

- A tabela `eclipse_donation_intents` tinha campo `payer_cpf`; novos registros devem evitar duplicar CPF e usar o cadastro da conta quando estritamente necessario.
- O aceite de privacidade precisa ser gravado em tabela propria quando o hook/plugin de criacao de conta estiver ativo no ambiente.
- Ao alterar `config.local.php`, nunca copiar credenciais reais para a documentacao.

