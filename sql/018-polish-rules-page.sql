-- Polish the public server rules page registered in MyAAC.
-- Mirrors the theme page at theme-canary/themes/canary/pages/rules.php.

UPDATE `myaac_pages`
SET
  `title` = 'Regras do Servidor',
  `php` = 0,
  `hide` = 0,
  `body` = '<style>
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
		content: "Regras do Servidor";
		display: flex;
		align-items: center;
		height: 100%;
		padding-left: 14px;
		color: #f7e7bd !important;
		font: 900 18px Georgia, "Times New Roman", serif;
		text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255, 176, 69, .55) !important;
	}

	.eclipse-rules-page .rules-shell {
		padding: 16px;
		border: 2px solid #a66a23;
		border-radius: 5px;
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
	}

	.eclipse-rules-page .rules-hero {
		position: relative;
		overflow: hidden;
		min-height: 154px;
		display: block;
		padding: 20px 22px;
		border: 1px solid rgba(255, 194, 86, .54);
		border-radius: 5px;
		background:
			linear-gradient(90deg, rgba(12, 3, 2, .96) 0%, rgba(47, 9, 4, .88) 48%, rgba(12, 33, 48, .72) 100%),
			url("/plugins/theme-canary/themes/canary/images/header/bgs/arise-red-fortress.png") center / cover no-repeat;
		box-shadow: inset 0 0 0 1px rgba(255, 232, 170, .18), 0 8px 20px rgba(45, 12, 5, .30);
	}

	.eclipse-rules-page .rules-hero::after {
		content: "";
		position: absolute;
		inset: 0;
		background: radial-gradient(circle at 15% 18%, rgba(255, 183, 61, .22), transparent 34%);
		pointer-events: none;
	}

	.eclipse-rules-page .rules-hero > * {
		position: relative;
		z-index: 1;
	}

	.eclipse-rules-page .rules-kicker {
		display: inline-block;
		margin-bottom: 8px;
		padding: 5px 9px;
		border: 1px solid rgba(255, 215, 124, .62);
		border-radius: 4px;
		background: rgba(18, 7, 4, .86);
		color: #fff0bf !important;
		-webkit-text-fill-color: #fff0bf !important;
		font: 900 10px Verdana, Arial, sans-serif;
		letter-spacing: .08em;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-rules-page .rules-title {
		margin: 0 0 8px;
		color: #fff7dc !important;
		-webkit-text-fill-color: #fff7dc !important;
		font: 900 28px/1.05 Georgia, "Times New Roman", serif;
		text-shadow: 0 2px 2px #000, 0 0 12px rgba(255, 189, 77, .44) !important;
	}

	.eclipse-rules-page .rules-lead {
		max-width: 650px;
		margin: 0;
		color: #ffe9bd !important;
		-webkit-text-fill-color: #ffe9bd !important;
		font: 800 13px/1.55 Verdana, Arial, sans-serif;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-rules-page .rules-summary-grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 10px;
		margin: 12px 0 14px;
	}

	.eclipse-rules-page .rules-summary-card {
		min-height: 72px;
		padding: 10px 12px;
		border: 1px solid rgba(137, 83, 33, .52);
		border-left: 4px solid #8e2d13;
		border-radius: 4px;
		background: linear-gradient(180deg, #fff0bf 0%, #e8c37a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 250, 221, .65), 0 3px 8px rgba(55, 24, 5, .18);
	}

	.eclipse-rules-page .rules-summary-card span,
	.eclipse-rules-page .rules-summary-card strong {
		display: block;
		text-shadow: none !important;
	}

	.eclipse-rules-page .rules-summary-card span {
		color: #5e170d !important;
		-webkit-text-fill-color: #5e170d !important;
		font: 900 9px Verdana, Arial, sans-serif;
		text-transform: uppercase;
	}

	.eclipse-rules-page .rules-summary-card strong {
		margin-top: 5px;
		color: #180904 !important;
		-webkit-text-fill-color: #180904 !important;
		font: 900 16px Georgia, "Times New Roman", serif;
	}

	.eclipse-rules-page .rules-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 12px;
	}

	.eclipse-rules-page .rules-card,
	.eclipse-rules-page .rules-note {
		overflow: hidden;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 224, .85), 0 6px 16px rgba(71, 39, 9, .22);
	}

	.eclipse-rules-page .rules-card h3 {
		display: flex;
		align-items: center;
		gap: 9px;
		margin: 0;
		padding: 10px 12px;
		background: linear-gradient(180deg, #5d1007 0%, #250402 100%);
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		font: 900 16px Georgia, "Times New Roman", serif;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-rules-page .rules-index {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 25px;
		height: 25px;
		border: 1px solid rgba(255, 226, 153, .56);
		border-radius: 50%;
		background: linear-gradient(180deg, #d48b24 0%, #7d2508 100%);
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		font: 900 12px Arial, sans-serif;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-rules-page .rules-card ul {
		margin: 0;
		padding: 13px 16px 13px 31px;
	}

	.eclipse-rules-page .rules-card li {
		margin: 0 0 8px;
		color: #24100a !important;
		-webkit-text-fill-color: #24100a !important;
		font: 800 12px/1.5 Verdana, Arial, sans-serif;
	}

	.eclipse-rules-page .rules-card li:last-child {
		margin-bottom: 0;
	}

	.eclipse-rules-page .rules-note {
		display: grid;
		grid-template-columns: auto minmax(0, 1fr);
		gap: 12px;
		align-items: center;
		margin-top: 14px;
		padding: 13px 14px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
	}

	.eclipse-rules-page .rules-note-badge {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-height: 30px;
		padding: 5px 10px;
		border: 1px solid rgba(255, 221, 128, .58);
		border-radius: 4px;
		background: linear-gradient(180deg, #a92f12 0%, #681507 100%);
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		font: 900 10px Verdana, Arial, sans-serif;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #2d0400 !important;
		white-space: nowrap;
	}

	.eclipse-rules-page .rules-note p {
		margin: 0;
		color: #24100a !important;
		-webkit-text-fill-color: #24100a !important;
		font: 800 12px/1.55 Verdana, Arial, sans-serif;
	}

	.eclipse-rules-page .rules-note strong {
		color: #4d1209 !important;
		-webkit-text-fill-color: #4d1209 !important;
		font-weight: 900;
	}

	html body #ContentColumn #News .eclipse-rules-page .rules-kicker,
	html body #ContentColumn #News .eclipse-rules-page .rules-title,
	html body #ContentColumn #News .eclipse-rules-page .rules-lead,
	html body #ContentColumn #News .eclipse-rules-page .rules-card h3,
	html body #ContentColumn #News .eclipse-rules-page .rules-card h3 span,
	html body #ContentColumn #News .eclipse-rules-page .rules-note-badge {
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		text-shadow: 0 1px 2px #000, 0 0 8px rgba(255, 185, 72, .45) !important;
	}

	html body #ContentColumn #News .eclipse-rules-page .rules-title {
		color: #fff4c4 !important;
		-webkit-text-fill-color: #fff4c4 !important;
	}

	@media (max-width: 860px) {
		.eclipse-rules-page .rules-hero,
		.eclipse-rules-page .rules-summary-grid,
		.eclipse-rules-page .rules-grid,
		.eclipse-rules-page .rules-note {
			grid-template-columns: 1fr;
		}
	}
</style>

<div class="eclipse-rules-page">
	<div class="rules-shell">
		<section class="rules-hero">
			<div>
				<span class="rules-kicker">Conduta no Eclipse OT</span>
				<h2 class="rules-title">Jogue forte, jogue limpo</h2>
				<p class="rules-lead">
					As regras existem para manter o servidor competitivo, justo e saud&aacute;vel para todos.
					Ao jogar no Eclipse OT, voc&ecirc; concorda em respeitar outros jogadores, a equipe e a integridade do jogo.
				</p>
			</div>
		</section>

		<div class="rules-summary-grid">
			<div class="rules-summary-card">
				<span>Base</span>
				<strong>Respeito e seguran&ccedil;a</strong>
			</div>
			<div class="rules-summary-card">
				<span>Proibido</span>
				<strong>Bots, bugs e golpes</strong>
			</div>
			<div class="rules-summary-card">
				<span>Suporte</span>
				<strong>Provas e informa&ccedil;&otilde;es reais</strong>
			</div>
		</div>

		<div class="rules-grid">
			<section class="rules-card">
				<h3><span class="rules-index">1</span>Conta e identidade</h3>
				<ul>
					<li>Use nomes adequados, sem ofensas, imita&ccedil;&atilde;o de staff ou tentativa de enganar outros jogadores.</li>
					<li>N&atilde;o compartilhe, venda, empreste ou negocie contas, personagens, senhas ou recovery keys.</li>
					<li>Cada jogador &eacute; respons&aacute;vel pela seguran&ccedil;a da pr&oacute;pria conta.</li>
				</ul>
			</section>

			<section class="rules-card">
				<h3><span class="rules-index">2</span>Jogo justo</h3>
				<ul>
					<li>Macros, bots, automa&ccedil;&otilde;es e softwares que jogam por voc&ecirc; n&atilde;o s&atilde;o permitidos.</li>
					<li>Explorar bugs, duplicar itens ou abusar de falhas deve ser reportado imediatamente.</li>
					<li>Manipula&ccedil;&atilde;o do cliente ou do tr&aacute;fego do jogo pode resultar em puni&ccedil;&atilde;o permanente.</li>
				</ul>
			</section>

			<section class="rules-card">
				<h3><span class="rules-index">3</span>Comunidade</h3>
				<ul>
					<li>Evite ass&eacute;dio, discurso de &oacute;dio, amea&ccedil;as reais ou ataques pessoais.</li>
					<li>Spam, golpes, phishing e links maliciosos n&atilde;o s&atilde;o tolerados.</li>
					<li>Conflitos de PvP fazem parte do jogo; abuso fora do jogo n&atilde;o faz.</li>
				</ul>
			</section>

			<section class="rules-card">
				<h3><span class="rules-index">4</span>Staff e suporte</h3>
				<ul>
					<li>N&atilde;o finja ser membro da equipe nem prometa influ&ecirc;ncia em decis&otilde;es administrativas.</li>
					<li>Ao abrir um ticket ou report, envie informa&ccedil;&otilde;es reais, completas e verific&aacute;veis.</li>
					<li>Desrespeitar ou amea&ccedil;ar a equipe pode gerar san&ccedil;&otilde;es na conta.</li>
				</ul>
			</section>
		</div>

		<div class="rules-note">
			<span class="rules-note-badge">Penalidades</span>
			<p>
				Viola&ccedil;&otilde;es podem resultar em <strong>aviso, mute, jail, perda de itens, banimento tempor&aacute;rio ou permanente</strong>.
				A equipe pode alterar estas regras quando necess&aacute;rio para proteger o servidor.
			</p>
		</div>
	</div>
</div>'
WHERE `name` = 'rules';
