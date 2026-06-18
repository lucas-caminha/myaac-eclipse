<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Privacidade e LGPD';
?>
<div class="eclipse-privacy-page">
	<section class="privacy-hero">
		<span>LGPD</span>
		<h2>Privacidade e Protecao de Dados</h2>
		<p>Esta pagina resume como o Eclipse OT trata dados pessoais usados para conta, seguranca, jogo, doacoes e suporte.</p>
	</section>

	<section class="privacy-section">
		<h3>Quais dados usamos</h3>
		<div class="privacy-grid">
			<article><strong>Conta</strong><p>Nome da conta, email, senha protegida por hash, data de criacao, ultimo login e logs de seguranca.</p></article>
			<article><strong>Jogo</strong><p>Personagens, level, vocacao, guild, outfit, rankings e informacoes publicas relacionadas ao servidor.</p></article>
			<article><strong>Doacoes</strong><p>Nome completo, data de nascimento e CPF somente quando necessarios para validacao de apoio financeiro.</p></article>
			<article><strong>Pagamentos</strong><p>Pacote escolhido, valor, coins, status de pagamento, gateway e referencia de transacao.</p></article>
		</div>
	</section>

	<section class="privacy-section">
		<h3>Por que usamos esses dados</h3>
		<ul>
			<li>Autenticar e proteger sua conta.</li>
			<li>Permitir criacao, exibicao e evolucao de personagens.</li>
			<li>Prevenir fraude, abuso, chargeback e acesso indevido.</li>
			<li>Processar doacoes e creditar coins quando o pagamento for aprovado.</li>
			<li>Cumprir obrigacoes legais, regulatorias e de seguranca quando aplicavel.</li>
		</ul>
	</section>

	<section class="privacy-section">
		<h3>Dados opcionais e doacoes</h3>
		<p>Nome completo, data de nascimento e CPF nao sao necessarios para jogar. Esses dados sao solicitados apenas quando voce deseja realizar doacoes ou usar fluxos que dependam de validacao de pagamento.</p>
		<p>Quando exibidos dentro da conta, dados sensiveis devem aparecer mascarados sempre que possivel.</p>
	</section>

	<section class="privacy-section">
		<h3>Compartilhamento</h3>
		<p>Podemos compartilhar dados minimos com operadores de pagamento, hospedagem, banco de dados, seguranca e infraestrutura, sempre conforme a finalidade do servico. Nao vendemos dados pessoais.</p>
	</section>

	<section class="privacy-section">
		<h3>Seus direitos</h3>
		<p>Voce pode solicitar acesso, correcao, informacao sobre uso, eliminacao quando aplicavel, anonimizacao, portabilidade e revogacao de consentimentos opcionais.</p>
		<div class="privacy-actions">
			<a class="eclipse-btn" href="<?= getLink('account/privacy') ?>">Gerenciar dados da conta</a>
			<a class="eclipse-btn eclipse-btn-secondary" href="<?= getLink('account/change-info') ?>">Atualizar cadastro</a>
		</div>
	</section>

	<section class="privacy-section">
		<h3>Contato</h3>
		<p>Para assuntos de privacidade, use o canal oficial do servidor ou envie sua solicitacao pelo painel da conta. Ao pedir suporte, nunca envie sua senha ou Recovery Key.</p>
	</section>
</div>
