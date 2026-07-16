<?php
/**
 * New player guide for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = t('server_guide.page_title');

function eclipseGuideEscape(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function eclipseGuideTranslate(string $key): string
{
	return t('server_guide.' . $key);
}

$imageBase = '/plugins/theme-canary/themes/canary/images/';

$rewardGroups = [
	[
		'name' => 'Sorcerer',
		'image' => $imageBase . 'vocations/sorcererbanner.png',
		'levels' => [
			40 => ['wand of starstorm', 'spellbook of warding'],
			50 => ['spellbook of mind control', 'batwing hat'],
			60 => ['spellbook of lost souls', 'Zaoan robe'],
			75 => ['dragon robe'],
			80 => ['yalahari mask'],
			100 => ['royal scale robe', "snake god's wristguard"],
			130 => ['spellbook of vigilance', 'furious frock'],
			150 => ['spellbook of ancient arcana', 'jungle wand'],
			180 => ['spirit guide', 'soulful legs', 'dream shroud'],
			200 => ['wand of destruction'],
			220 => ['enchanted theurgic amulet'],
			250 => ['Summoner outfit'],
			350 => ['umbral master spellbook'],
		],
	],
	[
		'name' => 'Druid',
		'image' => $imageBase . 'vocations/druidbanner.png',
		'levels' => [
			40 => ['underworld rod', 'spellbook of warding'],
			50 => ['spellbook of mind control', 'batwing hat'],
			60 => ['spellbook of lost souls', 'Zaoan robe'],
			75 => ['robe of the ice queen'],
			80 => ['yalahari mask'],
			100 => ['royal scale robe', "snake god's wristguard"],
			130 => ['spellbook of vigilance', 'furious frock'],
			150 => ['spellbook of ancient arcana', 'jungle rod'],
			180 => ['spirit guide', 'soulful legs', 'dream shroud'],
			200 => ['rod of destruction'],
			220 => ['enchanted theurgic amulet'],
			250 => ['Druid outfit'],
			350 => ['umbral master spellbook'],
		],
	],
	[
		'name' => 'Paladin',
		'image' => $imageBase . 'vocations/paladinbanner.png',
		'levels' => [
			50 => ['composite hornbow', 'Zaoan armor'],
			60 => ['chain bolter', 'amazon armor'],
			80 => ['warsinger bow', 'yalahari leg piece'],
			100 => ["master archer's armor", 'elite draken helmet'],
			120 => ['rift bow', 'rift crossbow', 'prismatic armor'],
			150 => ['depth lorica', 'prismatic legs', 'jungle bow', 'jungle quiver'],
			180 => ['dark whispers'],
			200 => ['bow of destruction'],
			220 => ['winged boots'],
			250 => ['Hunter outfit'],
			350 => ['umbral master bow', 'umbral master crossbow'],
		],
	],
	[
		'name' => 'Knight',
		'image' => $imageBase . 'vocations/knightbanner.png',
		'levels' => [
			40 => ['mercenary sword', 'titan axe'],
			50 => ['thaian sword', 'twin axe', 'war hammer', 'Zaoan armor'],
			75 => ['avenger', 'crude umbral blade', 'crude umbral axe', 'crude umbral mace'],
			80 => ['yalahari armor', 'shield of corruption'],
			100 => ['fireborn giant armor'],
			130 => ['ornate shield'],
			150 => ['prismatic helmet'],
			180 => ['ectoplasmic shield', 'ornate legs'],
			200 => ['blade of destruction', 'axe of destruction', 'mace of destruction', 'ornate chestplate'],
			225 => ['fabulous legs'],
			250 => ['Knight outfit'],
			350 => ['highest melee skill umbral master weapon'],
		],
	],
	[
		'name' => 'Monk',
		'image' => $imageBase . 'vocations/monkbanner.png',
		'levels' => [
			40 => ['nunchaku', 'legs of enlightenment'],
			50 => ['nunchaku of enlightenment', 'Zaoan monk robe'],
			70 => ['coned hat of enlightenment'],
			80 => ['legs of wisdom', 'yalahari footwraps'],
			100 => ['sai of enlightenment', 'merudri scale mail'],
			125 => ['merudri nanbando'],
			135 => ['depth claws', 'jungle survivor legs'],
			150 => ['bambus jo', 'robe of enlightenment', 'gnomish footwraps'],
			180 => ['dark vision bandana'],
			200 => ['nunchaku of destruction'],
			250 => ['iks footwraps', 'eldritch crescent moon spade', 'Monk outfit'],
			350 => ['umbral master katar'],
		],
	],
];

$guideSteps = [
	[
		'kicker' => 'step_1_kicker',
		'title' => 'step_1_title',
		'image' => $imageBase . 'vocations/knightbanner.png',
		'image_alt' => 'step_1_alt',
		'text' => 'step_1_text',
		'bullets' => ['step_1_bullet_1', 'step_1_bullet_2', 'step_1_bullet_3'],
	],
	[
		'kicker' => 'step_2_kicker',
		'title' => 'step_2_title',
		'type' => 'rewards',
		'text' => 'step_2_text',
	],
	[
		'kicker' => 'step_3_kicker',
		'title' => 'step_3_title',
		'image' => $imageBase . 'themeboxes/premium/coins_consumables.png',
		'image_alt' => 'step_3_alt',
		'text' => 'step_3_text',
		'bullets' => ['step_3_bullet_1', 'step_3_bullet_2', 'step_3_bullet_3'],
	],
	[
		'kicker' => 'step_4_kicker',
		'title' => 'step_4_title',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-VIP.png',
		'image_alt' => 'step_4_alt',
		'text' => 'step_4_text',
		'bullets' => ['step_4_bullet_1', 'step_4_bullet_2', 'step_4_bullet_3'],
	],
	[
		'kicker' => 'step_5_kicker',
		'title' => 'step_5_title',
		'image' => $imageBase . 'account/icon-tibiacoin.png',
		'image_alt' => 'step_5_alt',
		'text' => 'step_5_text',
		'bullets' => ['step_5_bullet_1', 'step_5_bullet_2', 'step_5_bullet_3'],
	],
	[
		'kicker' => 'step_6_kicker',
		'title' => 'step_6_title',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-Quests.png',
		'image_alt' => 'step_6_alt',
		'text' => 'step_6_text',
		'bullets' => ['step_6_bullet_1', 'step_6_bullet_2', 'step_6_bullet_3'],
	],
	[
		'kicker' => 'step_7_kicker',
		'title' => 'step_7_title',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-Prey.png',
		'image_alt' => 'step_7_alt',
		'text' => 'step_7_text',
		'bullets' => ['step_7_bullet_1', 'step_7_bullet_2', 'step_7_bullet_3'],
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
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
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

	.eclipse-guide .guide-panel.rewards {
		display: block;
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

	.eclipse-guide .guide-copy,
	.eclipse-guide .guide-rewards-copy {
		padding: 16px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
	}

	.eclipse-guide .guide-kicker {
		display: inline-block;
		margin-bottom: 8px;
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
		font-size: 12px;
		text-transform: uppercase;
		letter-spacing: .06em;
	}

	.eclipse-guide h2 {
		margin: 0 0 10px;
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
		font: 900 22px Georgia, "Times New Roman", serif;
	}

	.eclipse-guide .guide-copy p,
	.eclipse-guide .guide-rewards-copy p {
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

	.eclipse-guide .guide-vocation-grid {
		display: grid;
		grid-template-columns: repeat(5, minmax(0, 1fr));
		gap: 10px;
		margin-top: 14px;
	}

	.eclipse-guide .guide-vocation-card {
		overflow: hidden;
		border: 1px solid rgba(118, 70, 26, .55);
		border-radius: 5px;
		background: rgba(255, 244, 198, .55);
		box-shadow: 0 3px 8px rgba(64, 36, 9, .18);
	}

	.eclipse-guide .guide-vocation-card img {
		display: block;
		width: 100%;
		height: 86px;
		object-fit: cover;
		object-position: center top;
	}

	.eclipse-guide .guide-vocation-card strong {
		display: block;
		padding: 7px 8px;
		text-align: center;
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
	}

	.eclipse-guide .guide-reward-table-wrap {
		overflow-x: auto;
		margin-top: 14px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: rgba(255, 246, 210, .64);
	}

	.eclipse-guide .guide-reward-table {
		width: 100%;
		border-collapse: collapse;
		min-width: 680px;
	}

	.eclipse-guide .guide-reward-table th,
	.eclipse-guide .guide-reward-table td {
		padding: 8px;
		border-bottom: 1px solid rgba(118, 70, 26, .32);
		text-align: left;
		vertical-align: top;
		font-size: 12px;
		line-height: 1.35;
	}

	.eclipse-guide .guide-reward-table th {
		background: rgba(83, 29, 9, .15);
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
	}

	.eclipse-guide .guide-reward-level {
		white-space: nowrap;
		color: #5f1a0c !important;
		-webkit-text-fill-color: #5f1a0c !important;
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

	#submenu_server-guide .SubmenuitemLabel {
		color: #ffe681 !important;
		-webkit-text-fill-color: #ffe681 !important;
		font-weight: 900 !important;
		text-shadow: 0 1px 2px #210100, 0 0 6px rgba(255, 190, 42, .85) !important;
	}

	@media (max-width: 760px) {
		.eclipse-guide .guide-panel {
			grid-template-columns: 1fr;
		}

		.eclipse-guide .guide-image-card {
			min-height: 180px;
		}

		.eclipse-guide .guide-vocation-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.eclipse-guide .guide-actions {
			flex-direction: column;
		}
	}
</style>

<div class="eclipse-guide" data-guide>
	<div class="guide-shell">
		<div class="guide-hero">
			<span class="guide-eyebrow"><?php echo eclipseGuideEscape(eclipseGuideTranslate('eyebrow')); ?></span>
			<h1><?php echo eclipseGuideEscape(eclipseGuideTranslate('title')); ?></h1>
			<p class="guide-lead"><?php echo eclipseGuideEscape(eclipseGuideTranslate('lead')); ?></p>
		</div>

		<div class="guide-steps" aria-label="<?php echo eclipseGuideEscape(eclipseGuideTranslate('steps_aria')); ?>">
			<?php foreach ($guideSteps as $index => $step): ?>
				<button class="guide-step-button<?php echo $index === 0 ? ' active' : ''; ?>" type="button" data-guide-step="<?php echo $index; ?>" aria-label="<?php echo eclipseGuideEscape(eclipseGuideTranslate('go_to_step') . ' ' . ($index + 1)); ?>">
					<?php echo $index + 1; ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="guide-track-wrap">
			<div class="guide-track">
				<?php foreach ($guideSteps as $step): ?>
					<?php if (($step['type'] ?? '') === 'rewards'): ?>
						<section class="guide-panel rewards">
							<div class="guide-rewards-copy">
								<span class="guide-kicker"><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['kicker'])); ?></span>
								<h2><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['title'])); ?></h2>
								<p><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['text'])); ?></p>

								<div class="guide-vocation-grid">
									<?php foreach ($rewardGroups as $group): ?>
										<div class="guide-vocation-card">
											<img src="<?php echo eclipseGuideEscape($group['image']); ?>" alt="<?php echo eclipseGuideEscape($group['name']); ?>">
											<strong><?php echo eclipseGuideEscape($group['name']); ?></strong>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="guide-reward-table-wrap">
									<table class="guide-reward-table">
										<thead>
											<tr>
												<th><?php echo eclipseGuideEscape(eclipseGuideTranslate('reward_vocation')); ?></th>
												<th><?php echo eclipseGuideEscape(eclipseGuideTranslate('reward_level_items')); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($rewardGroups as $group): ?>
												<tr>
													<td><strong><?php echo eclipseGuideEscape($group['name']); ?></strong></td>
													<td>
														<?php foreach ($group['levels'] as $level => $items): ?>
															<div><span class="guide-reward-level">Lv. <?php echo (int) $level; ?>:</span> <?php echo eclipseGuideEscape(implode(', ', $items)); ?></div>
														<?php endforeach; ?>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</section>
					<?php else: ?>
						<section class="guide-panel">
							<div class="guide-image-card">
								<img src="<?php echo eclipseGuideEscape($step['image']); ?>" alt="<?php echo eclipseGuideEscape(eclipseGuideTranslate($step['image_alt'])); ?>">
							</div>
							<div class="guide-copy">
								<span class="guide-kicker"><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['kicker'])); ?></span>
								<h2><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['title'])); ?></h2>
								<p><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['text'])); ?></p>
								<ul>
									<?php foreach ($step['bullets'] as $bullet): ?>
										<li><?php echo eclipseGuideEscape(eclipseGuideTranslate($bullet)); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</section>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="guide-actions">
			<button class="guide-nav prev" type="button" data-guide-prev><?php echo eclipseGuideEscape(eclipseGuideTranslate('previous')); ?></button>
			<button class="guide-nav next" type="button" data-guide-next><?php echo eclipseGuideEscape(eclipseGuideTranslate('next')); ?></button>
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
