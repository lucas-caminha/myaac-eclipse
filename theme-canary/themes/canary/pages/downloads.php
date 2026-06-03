<?php
/**
 * Custom downloads page for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Baixar Cliente';

$clientVersion = config('client');
if (is_numeric($clientVersion)) {
	$clientVersion = number_format(((int)$clientVersion) / 100, 2, '.', '');
} else {
	$clientVersion = '13.x';
}

$clientUrl = 'https://drive.google.com/drive/folders/0B2-sMQkWYzhGSFhGVlY2WGk5czQ';
$ipChangerUrl = 'https://static.otland.net/ipchanger.exe';
$serverIp = str_replace(['http://', 'https://', '/'], '', configLua('url') ?? '');
if ($serverIp === '') {
	$serverIp = 'eclipseot.com';
}
?>

<style>
	.eclipse-download-page,
	.eclipse-download-page * {
		color: #1f0804 !important;
		font-weight: 800;
		text-shadow: none !important;
		box-sizing: border-box;
	}

	#News:has(.eclipse-download-page) > img.Title[src*="headline-downloads"] {
		display: none !important;
	}

	#News:has(.eclipse-download-page) > .BorderTitleText::after {
		content: "Baixar Cliente";
		display: flex;
		align-items: center;
		height: 100%;
		padding-left: 14px;
		color: #f7e7bd !important;
		font: 900 18px Georgia, "Times New Roman", serif;
		text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255, 176, 69, .55) !important;
	}

	.eclipse-download-page .download-shell {
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		border: 2px solid #a66a23;
		border-radius: 5px;
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
		padding: 16px;
	}

	.eclipse-download-page .download-hero {
		display: grid;
		grid-template-columns: minmax(0, 1fr) auto;
		gap: 16px;
		align-items: center;
		padding: 18px;
		border: 1px solid rgba(119, 72, 28, .48);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 224, .85), 0 6px 16px rgba(71, 39, 9, .25);
	}

	.eclipse-download-page .download-title {
		margin: 0 0 8px;
		font: 900 24px Georgia, "Times New Roman", serif;
		color: #4d1209 !important;
	}

	.eclipse-download-page .download-lead {
		margin: 0;
		line-height: 1.5;
		font-size: 14px;
	}

	.eclipse-download-page .download-badge-row {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		margin-top: 14px;
	}

	.eclipse-download-page .download-badge {
		padding: 6px 10px;
		border: 1px solid rgba(99, 51, 15, .36);
		border-radius: 4px;
		background: rgba(255, 249, 218, .62);
		color: #3f1509 !important;
		font-size: 12px;
	}

	.eclipse-download-page .download-actions {
		display: grid;
		gap: 10px;
		min-width: 210px;
	}

	.eclipse-download-page .download-button {
		display: block;
		padding: 11px 15px;
		border: 1px solid #ffe1a0;
		border-radius: 4px;
		background: linear-gradient(180deg, #ff9b1f 0%, #df6505 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .48), 0 3px 9px rgba(73, 31, 2, .34);
		color: #fff7d4 !important;
		font: 900 13px Arial, sans-serif;
		text-align: center;
		text-transform: uppercase;
		text-decoration: none;
		text-shadow: 0 1px 1px #4c1600 !important;
	}

	.eclipse-download-page .download-button.secondary {
		background: linear-gradient(180deg, #173f54 0%, #08202d 100%);
		border-color: #d69a3d;
		color: #fff1bd !important;
	}

	.eclipse-download-page .download-grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 12px;
		margin-top: 14px;
	}

	.eclipse-download-page .download-card,
	.eclipse-download-page .download-note {
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #f8e5b0 0%, #e3bd74 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
		padding: 13px;
	}

	.eclipse-download-page .download-card strong {
		display: block;
		margin-bottom: 6px;
		color: #4d1209 !important;
		font-size: 14px;
	}

	.eclipse-download-page .download-card span,
	.eclipse-download-page .download-note span {
		display: block;
		font-size: 13px;
		line-height: 1.45;
	}

	.eclipse-download-page .download-step {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 24px;
		height: 24px;
		margin-right: 7px;
		border-radius: 50%;
		background: #5a130a;
		color: #fff0b8 !important;
		font: 900 13px Arial, sans-serif;
	}

	.eclipse-download-page .download-note {
		margin-top: 14px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
	}

	.eclipse-download-page .download-note strong {
		color: #4d1209 !important;
	}

	@media (max-width: 860px) {
		.eclipse-download-page .download-hero,
		.eclipse-download-page .download-grid {
			grid-template-columns: 1fr;
		}

		.eclipse-download-page .download-actions {
			min-width: 0;
		}
	}
</style>

<div class="eclipse-download-page">
	<div class="download-shell">
		<div class="download-hero">
			<div>
				<h2 class="download-title">Baixar Cliente</h2>
				<p class="download-lead">
					Use o cliente oficial Tibia <?php echo htmlspecialchars($clientVersion); ?> para jogar no Eclipse OT.
					Depois de instalar, abra o IP Changer e conecte no servidor.
				</p>
				<div class="download-badge-row">
					<span class="download-badge">Cliente <?php echo htmlspecialchars($clientVersion); ?></span>
					<span class="download-badge">Windows</span>
					<span class="download-badge">Servidor: <?php echo htmlspecialchars($serverIp); ?></span>
				</div>
			</div>
			<div class="download-actions">
				<a class="download-button" href="<?php echo $clientUrl; ?>" target="_blank" rel="noopener">Baixar Cliente</a>
				<a class="download-button secondary" href="<?php echo $ipChangerUrl; ?>" target="_blank" rel="noopener">Baixar IP Changer</a>
			</div>
		</div>

		<div class="download-grid">
			<div class="download-card">
				<strong><span class="download-step">1</span>Baixe o cliente</strong>
				<span>Abra o pacote do cliente e instale normalmente no seu computador.</span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">2</span>Configure o acesso</strong>
				<span>Use o IP Changer para apontar o cliente para o endereço do Eclipse OT.</span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">3</span>Entre no jogo</strong>
				<span>Crie sua conta, escolha sua vocação e comece sua jornada.</span>
			</div>
		</div>

		<div class="download-note">
			<strong>Observação:</strong>
			<span>Se o navegador bloquear o download, confirme que deseja manter o arquivo. Baixe sempre pelos links oficiais desta página.</span>
		</div>
	</div>
</div>
