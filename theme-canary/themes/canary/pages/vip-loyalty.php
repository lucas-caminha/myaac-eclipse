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
$loyaltyPremiumSpent = (int) (configLua('loyaltyPointsPerPremiumDaySpent') ?? 0);
$loyaltyPremiumPurchased = (int) (configLua('loyaltyPointsPerPremiumDayPurchased') ?? 0);
$loyaltyMultiplier = (float) (configLua('loyaltyBonusPercentageMultiplier') ?? 1.0);

$loyaltyTitles = [
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

$loyaltyBonuses = [
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

function eclipseStatusText(bool $enabled): string
{
	return $enabled ? 'Ativo' : 'Desativado';
}

function eclipseYesNo(bool $enabled): string
{
	return $enabled ? 'Sim' : 'N&atilde;o';
}
?>

<style>
	.eclipse-vip-page,
	.eclipse-vip-page * {
		box-sizing: border-box;
		color: #1f0804 !important;
		font-weight: 800;
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
	.eclipse-vip-page .vip-note {
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 224, .85), 0 6px 16px rgba(71, 39, 9, .22);
	}

	.eclipse-vip-page .vip-hero {
		display: grid;
		grid-template-columns: minmax(0, 1fr) auto;
		gap: 16px;
		align-items: center;
		padding: 18px;
	}

	.eclipse-vip-page .vip-title {
		margin: 0 0 8px;
		color: #4d1209 !important;
		font: 900 25px Georgia, "Times New Roman", serif;
	}

	.eclipse-vip-page .vip-lead,
	.eclipse-vip-page .vip-note {
		margin: 0;
		font-size: 14px;
		line-height: 1.55;
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
		padding: 14px;
	}

	.eclipse-vip-page .vip-card h3 {
		margin: 0 0 8px;
		color: #4d1209 !important;
		font: 900 16px Georgia, "Times New Roman", serif;
	}

	.eclipse-vip-page .vip-card p,
	.eclipse-vip-page .vip-card li {
		font-size: 13px;
		line-height: 1.48;
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
		min-height: 78px;
		padding: 12px;
		border: 1px solid rgba(93, 48, 17, .4);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff5ce 0%, #e5bd72 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .65);
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
		font-size: 21px;
		color: #1f0804 !important;
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

	.eclipse-vip-page .vip-table td:nth-child(2),
	.eclipse-vip-page .vip-table td:nth-child(3) {
		text-align: center;
	}

	.eclipse-vip-page .vip-note {
		margin-top: 14px;
		padding: 13px 14px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
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

		.eclipse-vip-page .vip-status {
			min-width: 0;
		}
	}
</style>

<div class="eclipse-vip-page">
	<div class="vip-shell">
		<section class="vip-hero">
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
			<div class="vip-benefit"><span>Experi&ecirc;ncia</span><strong>+<?php echo $vipBonusExp; ?>%</strong></div>
			<div class="vip-benefit"><span>Loot</span><strong>+<?php echo $vipBonusLoot; ?>%</strong></div>
			<div class="vip-benefit"><span>Skills</span><strong>+<?php echo $vipBonusSkill; ?>%</strong></div>
			<div class="vip-benefit"><span>Auto Loot VIP</span><strong><?php echo eclipseYesNo($vipAutoLootOnly); ?></strong></div>
			<div class="vip-benefit"><span>Idle protegido</span><strong><?php echo eclipseYesNo($vipStayOnline); ?></strong></div>
			<div class="vip-benefit"><span>Manter house</span><strong><?php echo eclipseYesNo($vipKeepHouse); ?></strong></div>
		</div>

		<div class="vip-grid" style="margin-top: 12px;">
			<section class="vip-card">
				<h3>Como ativar VIP</h3>
				<p>
					Quando o sistema VIP estiver habilitado, qualquer conta com dias premium ativos ser&aacute; reconhecida como VIP pelo servidor.
					Os b&ocirc;nus s&atilde;o aplicados automaticamente ao entrar no jogo.
				</p>
			</section>
			<section class="vip-card">
				<h3>Recursos adicionais</h3>
				<ul>
					<li>Redu&ccedil;&atilde;o de cooldown de familiar: <?php echo $vipFamiliarReduction; ?> minuto(s).</li>
					<li>Recompensas online podem dar mais coins ou tokens para contas VIP quando os eventos estiverem ativos.</li>
					<li>Alguns recursos dependem da configura&ccedil;&atilde;o atual do servidor.</li>
				</ul>
			</section>
		</div>

		<div class="vip-section-title">Informa&ccedil;&otilde;es de Loyalty</div>
		<div class="vip-grid">
			<section class="vip-card">
				<h3>Como ganhar pontos</h3>
				<ul>
					<li><?php echo $loyaltyCreationDay; ?> ponto(s) por dia desde a cria&ccedil;&atilde;o da conta.</li>
					<li><?php echo $loyaltyPremiumPurchased; ?> ponto(s) por dia premium comprado.</li>
					<li><?php echo $loyaltyPremiumSpent; ?> ponto(s) por dia premium utilizado.</li>
				</ul>
			</section>
			<section class="vip-card">
				<h3>O que o b&ocirc;nus afeta</h3>
				<p>
					O b&ocirc;nus de Loyalty afeta skills e magic level. Ele n&atilde;o altera diretamente experi&ecirc;ncia, loot ou dano final.
					O multiplicador atual do b&ocirc;nus &eacute; <?php echo rtrim(rtrim(number_format($loyaltyMultiplier, 2, '.', ''), '0'), '.'); ?>x.
				</p>
			</section>
		</div>

		<div class="vip-section-title">T&iacute;tulos de Loyalty</div>
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

		<div class="vip-section-title">B&ocirc;nus de Skills por Loyalty</div>
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
					<td>+<?php echo $bonus; ?>%</td>
					<td>+<?php echo rtrim(rtrim(number_format($currentBonus, 2, '.', ''), '0'), '.'); ?>%</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<div class="vip-note">
			<strong>Observa&ccedil;&atilde;o:</strong>
			os valores exibidos usam a configura&ccedil;&atilde;o atual do servidor. Caso o VIP esteja desativado, dias premium continuam existindo na conta, mas os benef&iacute;cios especiais de VIP n&atilde;o s&atilde;o aplicados pelo jogo.
		</div>
	</div>
</div>
