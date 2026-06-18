<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Privacidade e LGPD';
?>
<div class="eclipse-privacy-page">
	<section class="privacy-hero">
		<span>LGPD</span>
		<h2>Privacidade e Proteção de Dados</h2>
		<p>Esta página resume como o Eclipse OT trata dados pessoais usados para conta, segurança, jogo, doações e suporte.</p>
	</section>

	<section class="privacy-section">
		<h3>Quais dados usamos</h3>
		<div class="privacy-grid">
			<article><strong>Conta</strong><p>Nome da conta, e-mail, senha protegida por hash, data de criação, último login e logs de segurança.</p></article>
			<article><strong>Jogo</strong><p>Personagens, level, vocação, guild, outfit, rankings e informações públicas relacionadas ao servidor.</p></article>
			<article><strong>Doações</strong><p>Nome completo, data de nascimento e CPF somente quando necessários para validação de apoio financeiro.</p></article>
			<article><strong>Pagamentos</strong><p>Pacote escolhido, valor, coins, status de pagamento, gateway e referência de transação.</p></article>
		</div>
	</section>

	<section class="privacy-section">
		<h3>Por que usamos esses dados</h3>
		<ul>
			<li>Autenticar e proteger sua conta.</li>
			<li>Permitir criação, exibição e evolução de personagens.</li>
			<li>Prevenir fraude, abuso, chargeback e acesso indevido.</li>
			<li>Processar doações e creditar coins quando o pagamento for aprovado.</li>
			<li>Cumprir obrigações legais, regulatórias e de segurança quando aplicável.</li>
		</ul>
	</section>

	<section class="privacy-section">
		<h3>Dados opcionais e doações</h3>
		<p>Nome completo, data de nascimento e CPF não são necessários para jogar. Esses dados são solicitados apenas quando você deseja realizar doações ou usar fluxos que dependam de validação de pagamento.</p>
		<p>Quando exibidos dentro da conta, dados sensíveis devem aparecer mascarados sempre que possível.</p>
	</section>

	<section class="privacy-section">
		<h3>Compartilhamento</h3>
		<p>Podemos compartilhar dados mínimos com operadores de pagamento, hospedagem, banco de dados, segurança e infraestrutura, sempre conforme a finalidade do serviço. Não vendemos dados pessoais.</p>
	</section>

	<section class="privacy-section">
		<h3>Seus direitos</h3>
		<p>Você pode solicitar acesso, correção, informação sobre uso, eliminação quando aplicável, anonimização, portabilidade e revogação de consentimentos opcionais.</p>
		<div class="privacy-actions">
			<a class="eclipse-btn" href="<?= getLink('account/privacy') ?>">Gerenciar dados da conta</a>
			<a class="eclipse-btn eclipse-btn-secondary" href="<?= getLink('account/change-info') ?>">Atualizar cadastro</a>
		</div>
	</section>

	<section class="privacy-section">
		<h3>Contato</h3>
		<p>Para assuntos de privacidade, use o canal oficial do servidor ou envie sua solicitação pelo painel da conta. Ao pedir suporte, nunca envie sua senha ou Recovery Key.</p>
	</section>
</div>
