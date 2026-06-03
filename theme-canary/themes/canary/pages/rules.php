<?php
/**
 * Custom server rules page for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Eclipse OT Rules';
?>

<style>
	.eclipse-rules-page,
	.eclipse-rules-page * {
		box-sizing: border-box;
		color: #1f0804 !important;
		font-weight: 800;
		text-shadow: none !important;
	}

	#News:has(.eclipse-rules-page) > img.Title[src*="headline-rules"],
	#News:has(.eclipse-rules-page) > img.Title {
		display: none !important;
	}

	#News:has(.eclipse-rules-page) > .BorderTitleText::after {
		content: "Eclipse OT Rules";
		display: flex;
		align-items: center;
		height: 100%;
		padding-left: 14px;
		color: #f7e7bd !important;
		font: 900 18px Georgia, "Times New Roman", serif;
		text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255, 176, 69, .55) !important;
	}

	.eclipse-rules-page .rules-shell {
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		border: 2px solid #a66a23;
		border-radius: 5px;
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
		padding: 16px;
	}

	.eclipse-rules-page .rules-hero,
	.eclipse-rules-page .rules-note,
	.eclipse-rules-page .rules-card {
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 224, .85), 0 6px 16px rgba(71, 39, 9, .22);
	}

	.eclipse-rules-page .rules-hero {
		padding: 18px;
	}

	.eclipse-rules-page .rules-title {
		margin: 0 0 8px;
		color: #4d1209 !important;
		font: 900 24px Georgia, "Times New Roman", serif;
	}

	.eclipse-rules-page .rules-lead {
		margin: 0;
		max-width: 720px;
		font-size: 14px;
		line-height: 1.55;
	}

	.eclipse-rules-page .rules-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 12px;
		margin-top: 14px;
	}

	.eclipse-rules-page .rules-card {
		padding: 14px;
	}

	.eclipse-rules-page .rules-card h3 {
		display: flex;
		align-items: center;
		gap: 8px;
		margin: 0 0 10px;
		color: #4d1209 !important;
		font: 900 16px Georgia, "Times New Roman", serif;
	}

	.eclipse-rules-page .rules-index {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 25px;
		height: 25px;
		border-radius: 50%;
		background: #5a130a;
		color: #fff0b8 !important;
		font: 900 13px Arial, sans-serif;
	}

	.eclipse-rules-page .rules-card ul {
		margin: 0;
		padding-left: 18px;
	}

	.eclipse-rules-page .rules-card li {
		margin: 0 0 8px;
		font-size: 13px;
		line-height: 1.45;
	}

	.eclipse-rules-page .rules-card li:last-child {
		margin-bottom: 0;
	}

	.eclipse-rules-page .rules-note {
		margin-top: 14px;
		padding: 13px 14px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
		font-size: 13px;
		line-height: 1.5;
	}

	.eclipse-rules-page .rules-note strong {
		color: #4d1209 !important;
		font-weight: 900;
	}

	@media (max-width: 860px) {
		.eclipse-rules-page .rules-grid {
			grid-template-columns: 1fr;
		}
	}
</style>

<div class="eclipse-rules-page">
	<div class="rules-shell">
		<div class="rules-hero">
			<h2 class="rules-title">Eclipse OT Rules</h2>
			<p class="rules-lead">
				As regras existem para manter o servidor competitivo, justo e saudavel para todos.
				Ao jogar no Eclipse OT, voce concorda em respeitar outros jogadores, a equipe e a integridade do jogo.
			</p>
		</div>

		<div class="rules-grid">
			<section class="rules-card">
				<h3><span class="rules-index">1</span>Conta e identidade</h3>
				<ul>
					<li>Use nomes adequados, sem ofensas, imitacao de staff ou tentativa de enganar outros jogadores.</li>
					<li>Nao compartilhe, venda, empreste ou negocie contas, personagens, senhas ou recovery keys.</li>
					<li>Cada jogador e responsavel pela seguranca da propria conta.</li>
				</ul>
			</section>

			<section class="rules-card">
				<h3><span class="rules-index">2</span>Jogo justo</h3>
				<ul>
					<li>Macros, bots, automacoes e softwares que jogam por voce nao sao permitidos.</li>
					<li>Explorar bugs, duplicar itens ou abusar de falhas deve ser reportado imediatamente.</li>
					<li>Manipulacao do cliente ou do trafego do jogo pode resultar em punicao permanente.</li>
				</ul>
			</section>

			<section class="rules-card">
				<h3><span class="rules-index">3</span>Comunidade</h3>
				<ul>
					<li>Evite assedio, discurso de odio, ameacas reais ou ataques pessoais.</li>
					<li>Spam, golpes, phishing e links maliciosos nao sao tolerados.</li>
					<li>Conflitos de PvP fazem parte do jogo; abuso fora do jogo nao faz.</li>
				</ul>
			</section>

			<section class="rules-card">
				<h3><span class="rules-index">4</span>Staff e suporte</h3>
				<ul>
					<li>Nao finja ser membro da equipe nem prometa influencia em decisoes administrativas.</li>
					<li>Ao abrir um ticket ou report, envie informacoes reais e completas.</li>
					<li>Desrespeitar ou ameacar a equipe pode gerar sancoes na conta.</li>
				</ul>
			</section>
		</div>

		<div class="rules-note">
			<strong>Penalidades:</strong>
			violacoes podem resultar em aviso, mute, jail, perda de itens, banimento temporario ou banimento permanente.
			A equipe pode alterar estas regras quando necessario para proteger o servidor.
		</div>
	</div>
</div>
