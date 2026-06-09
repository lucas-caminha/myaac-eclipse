<?php
/**
 * VIP and Loyalty information page for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'VIP & Loyalty';

$vipEnabled = getBoolean(configLua('vipSystemEnabled') ?? false);
$vipBonusExp = (int) (configLua('vipBonusExp') ?? 0);
$vipBonusLoot = (int) (configLua('vipBonusLoot') ?? 0);
$vipBonusSkill = (int) (configLua('vipBonusSkill') ?? 0);
$vipAutoLootOnly = getBoolean(configLua('vipAutoLootVipOnly') ?? false);
$vipStayOnline = getBoolean(configLua('vipStayOnline') ?? false);
$vipKeepHouse = getBoolean(configLua('vipKeepHouse') ?? false);
$vipFamiliarReduction = (int) (configLua('vipFamiliarTimeCooldownReduction') ?? 0);

$loyaltyEnabled = getBoolean(configLua('loyaltyEnabled') ?? true);
$loyaltyCreationDay = (int) (configLua('loyaltyPointsPerCreationDay') ?? 1);
$loyaltyPremiumSpent = (int) (configLua('loyaltyPointsPerPremiumDaySpent') ?? 4);
$loyaltyPremiumPurchased = (int) (configLua('loyaltyPointsPerPremiumDayPurchased') ?? 4);
$loyaltyMultiplier = (float) (configLua('loyaltyBonusPercentageMultiplier') ?? 1.0);

$defaultLoyaltyTitles = [
	['Scout of Tibia', 50],
	['Sentinel of Tibia', 100],
	['Steward of Tibia', 200],
	['Warden of Tibia', 400],
	['Squire of Tibia', 1000],
	['Warrior of Tibia', 2000],
	['Keeper of Tibia', 3000],
	['Guardian of Tibia', 4000],
	['Sage of Tibia', 5000],
	['Savant of Tibia', 6000],
	['Enlightened of Tibia', 7000],
];

$defaultLoyaltyBonuses = [
	[360, 5],
	[720, 10],
	[1080, 15],
	[1440, 20],
	[1800, 25],
	[2160, 30],
	[2520, 35],
	[2880, 40],
	[3240, 45],
	[3600, 50],
];

$loyaltySourceFile = ($config['server_path'] ?? '') . 'data/libs/functions/player.lua';
$loyaltySource = eclipseLoadLoyaltyFromLua($loyaltySourceFile, $defaultLoyaltyTitles, $defaultLoyaltyBonuses);
$loyaltyTitles = $loyaltySource['titles'];
$loyaltyBonuses = $loyaltySource['bonuses'];
$loyaltyUsingLuaSource = $loyaltySource['from_lua'];

function eclipseStatusText(bool $enabled): string
{
	return $enabled ? 'Ativo' : 'Desativado';
}

function eclipseYesNo(bool $enabled): string
{
	return $enabled ? 'Sim' : 'N&atilde;o';
}

function eclipseFormatPercent(float $value): string
{
	return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
}

function eclipseLoadLoyaltyFromLua(string $file, array $fallbackTitles, array $fallbackBonuses): array
{
	if (!@is_file($file)) {
		return [
			'titles' => $fallbackTitles,
			'bonuses' => $fallbackBonuses,
			'from_lua' => false,
		];
	}

	$source = @file_get_contents($file);
	if ($source === false) {
		return [
			'titles' => $fallbackTitles,
			'bonuses' => $fallbackBonuses,
			'from_lua' => false,
		];
	}

	$titles = [];
	if (preg_match_all('/name\s*=\s*"([^"]+)"\s*,\s*points\s*=\s*(\d+)/', $source, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$titles[] = [$match[1], (int) $match[2]];
		}
	}

	$bonuses = [];
	if (preg_match_all('/minPoints\s*=\s*(\d+)\s*,\s*percentage\s*=\s*(\d+(?:\.\d+)?)/', $source, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$bonuses[] = [(int) $match[1], (float) $match[2]];
		}
	}

	return [
		'titles' => $titles ?: $fallbackTitles,
		'bonuses' => $bonuses ?: $fallbackBonuses,
		'from_lua' => !empty($titles) && !empty($bonuses),
	];
}
?>

<style>
	.eclipse-vip-page,
	.eclipse-vip-page * {
		box-sizing: border-box;
		color: #1f0804 !important;
		font-family: Arial, Helvetica, sans-serif;
		font-weight: 700;
		text-shadow: none !important;
	}

	#ContentColumn #News .eclipse-vip-page .vip-hero,
	#ContentColumn #News .eclipse-vip-page .vip-hero *,
	#ContentColumn #News .eclipse-vip-page .vip-card,
	#ContentColumn #News .eclipse-vip-page .vip-card *,
	#ContentColumn #News .eclipse-vip-page .vip-benefit,
	#ContentColumn #News .eclipse-vip-page .vip-benefit *,
	#ContentColumn #News .eclipse-vip-page .vip-note,
	#ContentColumn #News .eclipse-vip-page .vip-note *,
	#ContentColumn #News .eclipse-vip-page .vip-source-note,
	#ContentColumn #News .eclipse-vip-page .vip-source-note *,
	#ContentColumn #News .eclipse-vip-page .vip-table,
	#ContentColumn #News .eclipse-vip-page .vip-table * {
		color: #1f0804 !important;
		-webkit-text-fill-color: #1f0804 !important;
		text-shadow: none !important;
	}

	#News:has(.eclipse-vip-page) > img.Title {
		display: none !important;
	}

	#News:has(.eclipse-vip-page) > .BorderTitleText::after {
		content: "VIP & Loyalty";
		display: flex;
		align-items: center;
		height: 100%;
		padding-left: 14px;
		color: #f7e7bd !important;
		font: 900 18px Georgia, "Times New Roman", serif;
		text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255, 176, 69, .55) !important;
	}

	.eclipse-vip-page .vip-shell {
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		border: 2px solid #a66a23;
		border-radius: 5px;
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
		padding: 16px;
	}

	.eclipse-vip-page .vip-hero,
	.eclipse-vip-page .vip-card,
	.eclipse-vip-page .vip-note,
	.eclipse-vip-page .vip-source-note {
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 224, .85), 0 6px 16px rgba(71, 39, 9, .22);
	}

	.eclipse-vip-page .vip-hero {
		display: grid;
		grid-template-columns: auto minmax(0, 1fr) auto;
		gap: 16px;
		align-items: center;
		padding: 18px;
	}

	.eclipse-vip-page .vip-hero-icons {
		display: flex;
		gap: 10px;
		align-items: center;
		justify-content: center;
	}

	.eclipse-vip-page .vip-hero-icons img {
		width: 54px;
		height: 54px;
		object-fit: contain;
		filter: drop-shadow(0 3px 3px rgba(58, 24, 4, .45));
	}

	.eclipse-vip-page .vip-title {
		margin: 0 0 8px;
		color: #4d1209 !important;
		font: 900 25px Georgia, "Times New Roman", serif;
	}

	.eclipse-vip-page .vip-lead,
	.eclipse-vip-page .vip-note,
	.eclipse-vip-page .vip-source-note {
		margin: 0;
		font-size: 14px;
		line-height: 1.62;
	}

	.eclipse-vip-page .vip-status {
		display: grid;
		gap: 8px;
		min-width: 185px;
	}

	.eclipse-vip-page .vip-badge {
		display: flex;
		justify-content: space-between;
		gap: 12px;
		padding: 9px 11px;
		border: 1px solid rgba(80, 35, 12, .35);
		border-radius: 4px;
		background: linear-gradient(180deg, #f8e7b8 0%, #dba85c 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .52);
		font-size: 12px;
		text-transform: uppercase;
	}

	.eclipse-vip-page .vip-badge strong {
		color: #5a130a !important;
	}

	.eclipse-vip-page .vip-section-title {
		margin: 18px 0 10px;
		padding: 10px 14px;
		border: 1px solid #a66a23;
		border-radius: 5px 5px 0 0;
		background: linear-gradient(180deg, #0e4258 0%, #071f2d 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .13);
		color: #fff0bd !important;
		font: 900 18px Georgia, "Times New Roman", serif;
	}

	.eclipse-vip-page .vip-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 12px;
	}

	.eclipse-vip-page .vip-card {
		display: grid;
		grid-template-columns: auto minmax(0, 1fr);
		gap: 12px;
		align-items: start;
		padding: 14px;
	}

	.eclipse-vip-page .vip-card h3 {
		margin: 0 0 8px;
		color: #4d1209 !important;
		font: 900 16px Georgia, "Times New Roman", serif;
	}

	.eclipse-vip-page .vip-card-icon {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 42px;
		height: 42px;
		border: 1px solid rgba(83, 40, 14, .35);
		border-radius: 4px;
		background: rgba(255, 246, 209, .55);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .65);
	}

	.eclipse-vip-page .vip-card-icon img {
		max-width: 32px;
		max-height: 32px;
		object-fit: contain;
	}

	.eclipse-vip-page .vip-card p,
	.eclipse-vip-page .vip-card li {
		font-size: 13px;
		line-height: 1.58;
	}

	.eclipse-vip-page .vip-card p {
		margin: 0;
	}

	.eclipse-vip-page .vip-card ul {
		margin: 0;
		padding-left: 18px;
	}

	.eclipse-vip-page .vip-benefits {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 10px;
	}

	.eclipse-vip-page .vip-benefit {
		display: grid;
		grid-template-columns: auto minmax(0, 1fr);
		gap: 10px;
		align-items: center;
		min-height: 86px;
		padding: 12px;
		border: 1px solid rgba(93, 48, 17, .4);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff5ce 0%, #e5bd72 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .65);
	}

	.eclipse-vip-page .vip-benefit img {
		width: 34px;
		height: 34px;
		object-fit: contain;
		filter: drop-shadow(0 2px 2px rgba(70, 30, 7, .35));
	}

	.eclipse-vip-page .vip-benefit span {
		display: block;
		margin-bottom: 4px;
		color: #5a130a !important;
		font: 900 13px Arial, sans-serif;
		text-transform: uppercase;
	}

	.eclipse-vip-page .vip-benefit strong {
		display: block;
		font: 900 22px Georgia, "Times New Roman", serif;
		color: #1f0804 !important;
	}

	.eclipse-vip-page .vip-table-wrap {
		overflow-x: auto;
		border: 1px solid rgba(91, 49, 16, .34);
		border-radius: 5px;
		box-shadow: 0 5px 14px rgba(70, 35, 7, .2);
	}

	.eclipse-vip-page .vip-table {
		width: 100%;
		border-collapse: collapse;
		background: #f5dfaa;
	}

	.eclipse-vip-page .vip-table th,
	.eclipse-vip-page .vip-table td {
		padding: 8px 10px;
		border: 1px solid rgba(86, 48, 18, .32);
		font-size: 13px;
		text-align: left;
	}

	.eclipse-vip-page .vip-table th {
		background: #cfb98c;
		color: #2a0d06 !important;
		font-weight: 900;
	}

	.eclipse-vip-page .vip-table tbody tr:nth-child(even) td {
		background: #efd39a;
	}

	.eclipse-vip-page .vip-table td:nth-child(2),
	.eclipse-vip-page .vip-table td:nth-child(3) {
		text-align: center;
	}

	.eclipse-vip-page .vip-note {
		margin-top: 14px;
		padding: 13px 14px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
	}

	.eclipse-vip-page .vip-source-note {
		display: flex;
		gap: 10px;
		align-items: center;
		margin-top: 12px;
		padding: 10px 12px;
		background: linear-gradient(180deg, #f9e9ba 0%, #dfbc75 100%);
		font-size: 12px;
	}

	.eclipse-vip-page .vip-source-note img {
		width: 20px;
		height: 20px;
		object-fit: contain;
	}

	.eclipse-vip-page .vip-note strong {
		color: #4d1209 !important;
	}

	@media (max-width: 900px) {
		.eclipse-vip-page .vip-hero,
		.eclipse-vip-page .vip-grid,
		.eclipse-vip-page .vip-benefits {
			grid-template-columns: 1fr;
		}

		.eclipse-vip-page .vip-hero-icons {
			justify-content: flex-start;
		}

		.eclipse-vip-page .vip-status {
			min-width: 0;
		}
	}
</style>

<div class="eclipse-vip-page">
	<div class="vip-shell">
		<section class="vip-hero">
			<div class="vip-hero-icons">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-VIP.png" alt="VIP">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Loyalty.png" alt="Loyalty">
			</div>
			<div>
				<h2 class="vip-title">VIP & Loyalty</h2>
				<p class="vip-lead">
					Entenda como a conta VIP e o sistema de Loyalty melhoram sua jornada no Eclipse OT.
					O VIP usa dias premium ativos na conta, enquanto o Loyalty recompensa o tempo de conta e, se configurado, os dias premium comprados ou utilizados.
				</p>
			</div>
			<div class="vip-status">
				<div class="vip-badge"><span>VIP</span><strong><?php echo eclipseStatusText($vipEnabled); ?></strong></div>
				<div class="vip-badge"><span>Loyalty</span><strong><?php echo eclipseStatusText($loyaltyEnabled); ?></strong></div>
			</div>
		</section>

		<div class="vip-section-title">Benef&iacute;cios VIP</div>
		<div class="vip-benefits">
			<div class="vip-benefit">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Stamina.png" alt="">
				<div><span>Experi&ecirc;ncia</span><strong>+<?php echo $vipBonusExp; ?>%</strong></div>
			</div>
			<div class="vip-benefit">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-TrackLoot.png" alt="">
				<div><span>Loot</span><strong>+<?php echo $vipBonusLoot; ?>%</strong></div>
			</div>
			<div class="vip-benefit">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Trainingstatues.png" alt="">
				<div><span>Skills</span><strong>+<?php echo $vipBonusSkill; ?>%</strong></div>
			</div>
			<div class="vip-benefit">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-QuickLoot.png" alt="">
				<div><span>Auto Loot VIP</span><strong><?php echo eclipseYesNo($vipAutoLootOnly); ?></strong></div>
			</div>
			<div class="vip-benefit">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Login.png" alt="">
				<div><span>Idle protegido</span><strong><?php echo eclipseYesNo($vipStayOnline); ?></strong></div>
			</div>
			<div class="vip-benefit">
				<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-House.png" alt="">
				<div><span>Manter house</span><strong><?php echo eclipseYesNo($vipKeepHouse); ?></strong></div>
			</div>
		</div>

		<div class="vip-grid" style="margin-top: 12px;">
			<section class="vip-card">
				<div class="vip-card-icon">
					<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-VIP.png" alt="">
				</div>
				<div>
					<h3>Como ativar VIP</h3>
					<p>
						Quando o sistema VIP estiver habilitado, qualquer conta com dias premium ativos ser&aacute; reconhecida como VIP pelo servidor.
						Os b&ocirc;nus s&atilde;o aplicados automaticamente ao entrar no jogo.
					</p>
				</div>
			</section>
			<section class="vip-card">
				<div class="vip-card-icon">
					<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Summons.png" alt="">
				</div>
				<div>
					<h3>Recursos adicionais</h3>
					<ul>
						<li>Redu&ccedil;&atilde;o de cooldown de familiar: <?php echo $vipFamiliarReduction; ?> minuto(s).</li>
						<li>Recompensas online podem dar mais coins ou tokens para contas VIP quando os eventos estiverem ativos.</li>
						<li>Alguns recursos dependem da configura&ccedil;&atilde;o atual do servidor.</li>
					</ul>
				</div>
			</section>
		</div>

		<div class="vip-section-title">Informa&ccedil;&otilde;es de Loyalty</div>
		<div class="vip-grid">
			<section class="vip-card">
				<div class="vip-card-icon">
					<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Loyalty.png" alt="">
				</div>
				<div>
					<h3>Como ganhar pontos</h3>
					<ul>
						<li><?php echo $loyaltyCreationDay; ?> ponto(s) por dia desde a cria&ccedil;&atilde;o da conta.</li>
						<li><?php echo $loyaltyPremiumPurchased; ?> ponto(s) por dia premium comprado.</li>
						<li><?php echo $loyaltyPremiumSpent; ?> ponto(s) por dia premium utilizado.</li>
					</ul>
				</div>
			</section>
			<section class="vip-card">
				<div class="vip-card-icon">
					<img src="<?php echo $template_path; ?>/images/premiumfeatures/PremiumIcon-Analytics.png" alt="">
				</div>
				<div>
					<h3>O que o b&ocirc;nus afeta</h3>
					<p>
						O b&ocirc;nus de Loyalty afeta skills e magic level. Ele n&atilde;o altera diretamente experi&ecirc;ncia, loot ou dano final.
						O multiplicador atual do b&ocirc;nus &eacute; <?php echo eclipseFormatPercent($loyaltyMultiplier); ?>x.
					</p>
				</div>
			</section>
		</div>

		<div class="vip-source-note">
			<img src="<?php echo $template_path; ?>/images/content/info.gif" alt="">
			<span>
				As configura&ccedil;&otilde;es de VIP s&atilde;o lidas do config do servidor.
				Os t&iacute;tulos e b&ocirc;nus de Loyalty <?php echo $loyaltyUsingLuaSource ? 'foram carregados diretamente do script Lua do jogo.' : 'est&atilde;o usando valores padr&atilde;o porque o script Lua do jogo n&atilde;o foi encontrado pelo site.'; ?>
			</span>
		</div>

		<div class="vip-section-title">T&iacute;tulos de Loyalty</div>
		<div class="vip-table-wrap">
			<table class="vip-table">
				<thead>
				<tr>
					<th>T&iacute;tulo</th>
					<th>Pontos necess&aacute;rios</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($loyaltyTitles as [$loyaltyTitle, $points]) { ?>
					<tr>
						<td><?php echo htmlspecialchars($loyaltyTitle); ?></td>
						<td><?php echo $points; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>

		<div class="vip-section-title">B&ocirc;nus de Skills por Loyalty</div>
		<div class="vip-table-wrap">
			<table class="vip-table">
				<thead>
				<tr>
					<th>Pontos necess&aacute;rios</th>
					<th>B&ocirc;nus base</th>
					<th>B&ocirc;nus atual</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($loyaltyBonuses as [$points, $bonus]) {
					$currentBonus = $bonus * $loyaltyMultiplier;
				?>
					<tr>
						<td><?php echo $points; ?></td>
						<td>+<?php echo eclipseFormatPercent($bonus); ?>%</td>
						<td>+<?php echo eclipseFormatPercent($currentBonus); ?>%</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>

		<div class="vip-note">
			<strong>Observa&ccedil;&atilde;o:</strong>
			os valores exibidos usam a configura&ccedil;&atilde;o atual do servidor. Caso o VIP esteja desativado, dias premium continuam existindo na conta, mas os benef&iacute;cios especiais de VIP n&atilde;o s&atilde;o aplicados pelo jogo.
		</div>
	</div>
</div>
