<?php
/**
 * New player guide for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Guia Inicial';

function eclipseGuideEscape(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$imageBase = '/plugins/theme-canary/themes/canary/images/';

$guideSteps = [
	[
		'kicker' => 'Passo 1',
		'title' => 'Comece pelo basico',
		'image' => $imageBase . 'vocations/knightbanner.png',
		'image_alt' => 'Knight em destaque',
		'text' => 'Depois de criar sua conta e personagem, entre no jogo e use os comandos principais para se localizar. O Eclipse OT foi ajustado para deixar o inicio mais fluido, com skills base por vocacao e progressao tranquila nos primeiros levels.',
		'bullets' => [
			'Use !comandos para ver a lista em portugues.',
			'Use !rates para conferir suas rates atuais.',
			'Use !serverinfo para ver regras PvP, server save e dados gerais.',
		],
	],
	[
		'kicker' => 'Passo 2',
		'title' => 'Pegue suas recompensas',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-DailyReward.png',
		'image_alt' => 'Icone de recompensa diaria',
		'text' => 'O servidor tem recompensas por level e por vocacao. Se voce passou de algum marco e ainda nao recebeu, o sistema entrega as recompensas pendentes ate o seu level atual.',
		'bullets' => [
			'Use !rewards para ver os marcos disponiveis.',
			'Mages, Paladins, Knights e Monks recebem itens pensados para a propria progressao.',
			'As recompensas incluem equipamentos duraveis para nao perder utilidade rapido demais.',
		],
	],
	[
		'kicker' => 'Passo 3',
		'title' => 'Configure seu loot',
		'image' => $imageBase . 'themeboxes/premium/coins_consumables.png',
		'image_alt' => 'Itens e moedas',
		'text' => 'Antes de sair para hunt, configure seus recursos de loot. O auto loot ajuda a manter o ritmo, a gold pouch aceita qualquer item e moedas podem ir direto para o banco.',
		'bullets' => [
			'Use !autoloot para gerenciar sua lista.',
			'Gold pouch aceita qualquer item.',
			'Auto bank reduz peso e organiza moedas automaticamente.',
		],
	],
	[
		'kicker' => 'Passo 4',
		'title' => 'Entenda Premium, VIP e conforto',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-VIP.png',
		'image_alt' => 'Icone VIP',
		'text' => 'Premium e VIP sao coisas diferentes aqui. Premium e gratuito para liberar magias e recursos basicos. VIP e um sistema separado, com beneficios reais para quem quiser acelerar a jornada.',
		'bullets' => [
			'Premium account e free para todos.',
			'VIP pode dar bonus de exp, loot e skill.',
			'VIP tambem pode manter online, ajudar no auto loot e proteger house.',
		],
	],
	[
		'kicker' => 'Passo 5',
		'title' => 'Use a Store com calma',
		'image' => $imageBase . 'account/icon-tibiacoin.png',
		'image_alt' => 'Eclipse coin',
		'text' => 'A store tem boxes customizadas e bags especiais. Cada box sorteia itens ligados ao seu tema, como Cobra, Falcon, Naga, Lion, Gnome e outras familias.',
		'bullets' => [
			'Boxes tem precos graduais conforme o tier dos itens.',
			'Existem bags especiais como Bag You Desire, Primal Bag e Bag You Covet.',
			'Use a store como complemento, nao como unico caminho de progressao.',
		],
	],
	[
		'kicker' => 'Passo 6',
		'title' => 'NPCs e servicos uteis',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-Quests.png',
		'image_alt' => 'Icone de quest',
		'text' => 'Alguns NPCs foram posicionados para facilitar a vida de quem esta comecando. Eles ajudam com imbuments, venda de loot, refill, municoes e comerciantes classicos.',
		'bullets' => [
			'Scroll Sage vende imbuement scrolls intricate e powerful.',
			'Loot seller e refill ajudam no ciclo de hunt.',
			'Vincent vende arrows, bolts e spears.',
		],
	],
	[
		'kicker' => 'Passo 7',
		'title' => 'Explore boss rooms e eventos',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-Prey.png',
		'image_alt' => 'Icone de prey',
		'text' => 'O Eclipse OT tem portais mapeados para bosses e eventos agendados de fim de semana. A ideia e deixar o servidor vivo sem fazer o jogador novo se perder.',
		'bullets' => [
			'Boss room possui acessos para bosses como Oberon, Drume, Scarlett e Timira.',
			'Eventos aparecem na agenda do site.',
			'O sistema de Prey foi organizado por tiers de dificuldade.',
		],
	],
];
?>

<style>
	.eclipse-guide,
	.eclipse-guide * {
		box-sizing: border-box;
		color: #1f0804 !important;
		-webkit-text-fill-color: #1f0804 !important;
		font-family: Arial, Helvetica, sans-serif;
		font-weight: 800;
		text-shadow: none !important;
	}

	.eclipse-guide .guide-shell {
		overflow: hidden;
		border: 2px solid #a66a23;
		border-radius: 5px;
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
	}

	.eclipse-guide .guide-hero {
		padding: 18px;
		border-bottom: 1px solid rgba(112, 65, 24, .48);
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
	}

	.eclipse-guide .guide-eyebrow {
		display: inline-block;
		margin-bottom: 10px;
		padding: 5px 9px;
		border: 1px solid rgba(91, 22, 9, .35);
		border-radius: 4px;
		background: linear-gradient(180deg, #6d1a0e 0%, #2b0604 100%);
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		font: 900 11px Verdana, Arial, sans-serif;
		letter-spacing: .08em;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-guide h1 {
		margin: 0 0 8px;
		color: #4d1209 !important;
		font: 900 25px Georgia, "Times New Roman", serif;
	}

	.eclipse-guide .guide-lead {
		max-width: 720px;
		margin: 0;
		font-size: 14px;
		line-height: 1.5;
	}

	.eclipse-guide .guide-steps {
		display: flex;
		gap: 8px;
		align-items: center;
		justify-content: center;
		flex-wrap: wrap;
		padding: 14px 12px;
		background: rgba(81, 38, 9, .12);
	}

	.eclipse-guide .guide-step-button {
		width: 34px;
		height: 34px;
		border: 1px solid #8a4d17;
		border-radius: 50%;
		background: linear-gradient(180deg, #fff2bd 0%, #d5a456 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .64), 0 2px 5px rgba(61, 31, 5, .24);
		color: #4d1209 !important;
		cursor: pointer;
		font: 900 14px Arial, sans-serif;
	}

	.eclipse-guide .guide-step-button.active {
		border-color: #ffe0a0;
		background: linear-gradient(180deg, #ff9b1f 0%, #df6505 100%);
		color: #fff7d4 !important;
		-webkit-text-fill-color: #fff7d4 !important;
		text-shadow: 0 1px 1px #4c1600 !important;
	}

	.eclipse-guide .guide-track-wrap {
		overflow: hidden;
	}

	.eclipse-guide .guide-track {
		display: flex;
		transition: transform .28s ease;
	}

	.eclipse-guide .guide-panel {
		display: grid;
		grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
		gap: 18px;
		min-width: 100%;
		padding: 18px;
		align-items: stretch;
	}

	.eclipse-guide .guide-image-card {
		display: flex;
		align-items: center;
		justify-content: center;
		min-height: 250px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #f8e5b0 0%, #e3bd74 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
	}

	.eclipse-guide .guide-image-card img {
		display: block;
		max-width: 100%;
		max-height: 230px;
		object-fit: contain;
	}

	.eclipse-guide .guide-copy {
		padding: 16px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
	}

	.eclipse-guide .guide-kicker {
		display: inline-block;
		margin-bottom: 8px;
		color: #6d1a0e !important;
		font-size: 12px;
		text-transform: uppercase;
		letter-spacing: .06em;
	}

	.eclipse-guide h2 {
		margin: 0 0 10px;
		color: #4d1209 !important;
		font: 900 22px Georgia, "Times New Roman", serif;
	}

	.eclipse-guide .guide-copy p {
		margin: 0 0 12px;
		font-size: 14px;
		line-height: 1.55;
	}

	.eclipse-guide .guide-copy ul {
		margin: 0;
		padding-left: 18px;
	}

	.eclipse-guide .guide-copy li {
		margin: 7px 0;
		line-height: 1.45;
	}

	.eclipse-guide .guide-actions {
		display: flex;
		justify-content: space-between;
		gap: 10px;
		padding: 0 18px 18px;
	}

	.eclipse-guide .guide-nav {
		min-width: 120px;
		padding: 10px 14px;
		border: 1px solid #ffe1a0;
		border-radius: 4px;
		background: linear-gradient(180deg, #173f54 0%, #08202d 100%);
		color: #fff1bd !important;
		-webkit-text-fill-color: #fff1bd !important;
		cursor: pointer;
		font: 900 12px Arial, sans-serif;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-guide .guide-nav.next {
		background: linear-gradient(180deg, #ff9b1f 0%, #df6505 100%);
		color: #fff7d4 !important;
		-webkit-text-fill-color: #fff7d4 !important;
	}

	.eclipse-guide .guide-nav:disabled {
		opacity: .45;
		cursor: default;
	}

	#News .BoxContent .eclipse-guide .guide-hero h1,
	#News .BoxContent .eclipse-guide .guide-lead,
	#News .BoxContent .eclipse-guide .guide-copy,
	#News .BoxContent .eclipse-guide .guide-copy h2,
	#News .BoxContent .eclipse-guide .guide-copy p,
	#News .BoxContent .eclipse-guide .guide-copy li,
	#News .BoxContent .eclipse-guide .guide-kicker {
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
		text-shadow: none !important;
	}

	@media (max-width: 760px) {
		.eclipse-guide .guide-panel {
			grid-template-columns: 1fr;
		}

		.eclipse-guide .guide-image-card {
			min-height: 180px;
		}

		.eclipse-guide .guide-actions {
			flex-direction: column;
		}
	}
</style>

<div class="eclipse-guide" data-guide>
	<div class="guide-shell">
		<div class="guide-hero">
			<span class="guide-eyebrow">Guia do jogador novo</span>
			<h1>Primeiros passos no Eclipse OT</h1>
			<p class="guide-lead">
				Um caminho rapido para entender comandos, recompensas, VIP, store, NPCs e sistemas customizados sem sair desta pagina.
			</p>
		</div>

		<div class="guide-steps" aria-label="Passos do guia">
			<?php foreach ($guideSteps as $index => $step): ?>
				<button class="guide-step-button<?php echo $index === 0 ? ' active' : ''; ?>" type="button" data-guide-step="<?php echo $index; ?>" aria-label="Ir para o passo <?php echo $index + 1; ?>">
					<?php echo $index + 1; ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="guide-track-wrap">
			<div class="guide-track">
				<?php foreach ($guideSteps as $step): ?>
					<section class="guide-panel">
						<div class="guide-image-card">
							<img src="<?php echo eclipseGuideEscape($step['image']); ?>" alt="<?php echo eclipseGuideEscape($step['image_alt']); ?>">
						</div>
						<div class="guide-copy">
							<span class="guide-kicker"><?php echo eclipseGuideEscape($step['kicker']); ?></span>
							<h2><?php echo eclipseGuideEscape($step['title']); ?></h2>
							<p><?php echo eclipseGuideEscape($step['text']); ?></p>
							<ul>
								<?php foreach ($step['bullets'] as $bullet): ?>
									<li><?php echo eclipseGuideEscape($bullet); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="guide-actions">
			<button class="guide-nav prev" type="button" data-guide-prev>Anterior</button>
			<button class="guide-nav next" type="button" data-guide-next>Proximo</button>
		</div>
	</div>
</div>

<script>
	(function () {
		var guide = document.querySelector('[data-guide]');
		if (!guide) {
			return;
		}

		var track = guide.querySelector('.guide-track');
		var stepButtons = Array.prototype.slice.call(guide.querySelectorAll('[data-guide-step]'));
		var prevButton = guide.querySelector('[data-guide-prev]');
		var nextButton = guide.querySelector('[data-guide-next]');
		var activeStep = 0;

		function showStep(step) {
			activeStep = Math.max(0, Math.min(step, stepButtons.length - 1));
			track.style.transform = 'translateX(' + (-activeStep * 100) + '%)';

			stepButtons.forEach(function (button, index) {
				button.classList.toggle('active', index === activeStep);
			});

			prevButton.disabled = activeStep === 0;
			nextButton.disabled = activeStep === stepButtons.length - 1;
		}

		stepButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				showStep(parseInt(button.getAttribute('data-guide-step'), 10));
			});
		});

		prevButton.addEventListener('click', function () {
			showStep(activeStep - 1);
		});

		nextButton.addEventListener('click', function () {
			showStep(activeStep + 1);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') {
				showStep(activeStep - 1);
			} else if (event.key === 'ArrowRight') {
				showStep(activeStep + 1);
			}
		});

		showStep(0);
	})();
</script>
