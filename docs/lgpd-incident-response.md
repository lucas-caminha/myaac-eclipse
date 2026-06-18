# Resposta a Incidentes LGPD

Este roteiro orienta a resposta tecnica inicial em caso de suspeita de vazamento, acesso indevido, perda, alteracao ou exposicao de dados pessoais.

## 1. Conter

- Remover acesso publico ao arquivo, endpoint ou dump afetado.
- Revogar tokens, senhas, chaves SSH ou credenciais envolvidas.
- Preservar logs relevantes antes de reiniciar servicos.
- Se necessario, colocar funcionalidades sensiveis em manutencao.

## 2. Identificar

Levantar:

- quais dados foram afetados;
- quais contas podem ter sido impactadas;
- quando o incidente comecou;
- se houve acesso externo confirmado;
- quais sistemas ou operadores externos foram envolvidos.

## 3. Corrigir

- Aplicar patch de codigo ou configuracao.
- Limpar cache do MyAAC quando houver mudanca em template/configuracao.
- Rotacionar credenciais.
- Revisar permissoes de arquivos, backups e banco de dados.

## 4. Registrar

Criar registro interno contendo:

- data e hora da deteccao;
- resumo tecnico;
- dados afetados;
- medidas tomadas;
- responsavel pelo acompanhamento;
- decisao sobre comunicacao aos titulares e autoridade competente.

## 5. Comunicar

Quando houver risco ou dano relevante aos titulares, preparar comunicacao clara com:

- natureza dos dados afetados;
- riscos relacionados;
- medidas adotadas;
- orientacoes para os usuarios;
- canal de contato.

## 6. Aprender

Apos a correcao:

- revisar logs e monitoramentos;
- adicionar teste ou checklist preventivo;
- atualizar este documento se o fluxo real tiver mudado;
- registrar follow-ups no backlog.

