<?php
defined('MYAAC') or die('Direct access not allowed!');

if (isset($config['boxes']))
	$config['boxes'] = explode(",", $config['boxes']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?= template_place_holder('head_start'); ?>
	<link rel="shortcut icon" href="<?= $template_path; ?>/images/favicon.ico?v=2" type="image/x-icon"/>
	<link rel="icon" href="<?= $template_path; ?>/images/favicon.ico?v=2" type="image/x-icon"/>
	<link rel="apple-touch-icon" href="<?= $template_path; ?>/images/favicon-eclipse.png?v=2"/>
	<link href="<?= $template_path; ?>/basic.css" rel="stylesheet" type="text/css"/>
	<link href="<?= $template_path; ?>/arise-overrides.css?v=72" rel="stylesheet" type="text/css"/>

	<script type="text/javascript" src="<?= $template_path; ?>/basic.js"></script>
	<script type="text/javascript" src="<?= $template_path; ?>/ticker.js"></script>
	<script id="twitter-wjs" src="<?= $template_path; ?>/js/twitter.js"></script>
	<script id="facebook-jssdk" async src="https://connect.facebook.net/en_US/all.js"></script>

	<link href="<?= $template_path; ?>/css/facebook.css" rel="stylesheet" type="text/css">

	<script src="<?= $template_path; ?>/bootstrap/popper.min.js"></script>
	<script src="<?= $template_path; ?>/bootstrap/js/bootstrap.min.js"></script>
	<link href="<?= $template_path; ?>/bootstrap/bootstrap-myaac.css" rel="stylesheet" type="text/css">

	<script>
		function CollapseTable(a_ID) {
			$('#' + a_ID).slideToggle('slow');
			if ($('#Indicator_' + a_ID).hasClass('CircleSymbolPlus')) {
				$('#Indicator_' + a_ID).attr('class', 'CircleSymbolMinus');
				$('#Indicator_' + a_ID).css('background-image', 'url(' + IMAGES + '/global/content/circle-symbol-plus.gif)');
			} else {
				$('#Indicator_' + a_ID).css('background-image', 'url(' + IMAGES + '/global/content/circle-symbol-minus.gif)');
				$('#Indicator_' + a_ID).attr('class', 'CircleSymbolPlus');
			}
		}
	</script>

	<script type="text/javascript">
		var menus = '';
		var loginStatus = "<?= ($logged ? 'true' : 'false'); ?>";
		<?php
			if(PAGE !== 'news') {
				if(isset($_REQUEST['subtopic'])) {
					$tmp = escapeHtml($_REQUEST['subtopic']);
					if($tmp === 'accountmanagement') {
						$tmp = 'accountmanage';
					}
				}
				else {
					$tmp = str_replace('/', '_', PAGE);
					$exp = explode('/', PAGE);
					if(PAGE !== 'account/create' && PAGE !== 'account/lost' && isset($exp[1])) {
						if ($exp[0] === 'account') {
							$tmp = 'account_manage';
						} else if ($exp[0] === 'news' && $exp[1] === 'archive') {
							$tmp = 'news_archive';
						}
						else if (in_array($exp[0], ['characters', 'highscores', 'guilds', 'forum'])) {
							$tmp = $exp[0];
						}
					}
				}
			}
			else {
				$tmp = 'news';
			}
		?>
		var activeSubmenuItem = "<?= $tmp; ?>";
		var IMAGES = "<?= $template_path; ?>/images";
		var LINK_ACCOUNT = "<?= BASE_URL; ?>";

		function rowOverEffect(object) {
			if (object.className == 'moduleRow') object.className = 'moduleRowOver';
		}

		function rowOutEffect(object) {
			if (object.className == 'moduleRowOver') object.className = 'moduleRow';
		}

		function InitializePage() {
			LoadLoginBox();
			LoadMenu();
		}

		// initialisation of the loginbox status by the value of the variable 'loginStatus' which is provided to the HTML-document by PHP in the file 'header.inc'
		function LoadLoginBox() {
			var buttonText = document.getElementById('ButtonText');
			var statusText = document.getElementById('LoginstatusText_2');
			if (buttonText) {
				buttonText.className = loginStatus == "false" ? 'LoginButtonText login' : 'LoginButtonText account';
			}
			if (statusText) {
				statusText.className = loginStatus == "false" ? 'LoginstatusTextAction create' : 'LoginstatusTextAction logout';
			}
		}

		function LoginButtonAction() {
			window.location = "<?= getLink('account/manage'); ?>";
		}

		function LoginstatusTextAction(source) {
			if (loginStatus === "false") {
				window.location = "<?= getLink('account/create'); ?>";
			} else {
				window.location = "<?= getLink('account/logout'); ?>";
			}
		}

		var menu = [];
		menu[0] = {};
		var unloadhelper = false;

		<?php
			$menuInitStr = '';
			foreach ($config['menu_categories'] as $item) {
				if ($item['id'] !== 'shops' || setting('core.gifts_system')) {
					$menuInitStr .= $item['id'] . '=' . ($item['id'] === 'news' ? '1' : '0') . '&';
				}
			}
		?>

		// load the menu and set the active submenu item by using the variable 'activeSubmenuItem'
		function LoadMenu()
		{
			document.getElementById("submenu_" + activeSubmenuItem).style.color = "white";
			document.getElementById("ActiveSubmenuItemIcon_" + activeSubmenuItem).style.visibility = "visible";
			menus = localStorage.getItem('menus');
			if(menus == null || menus.lastIndexOf("&") === -1) {
			  menus = "<?= $menuInitStr ?>";
		  }

			FillMenuArray();
			InitializeMenu();
		}

		function SaveMenu() {
			if (!unloadhelper) {
				SaveMenuArray();
				unloadhelper = true;
			}
		}

		// store the values of the variable 'self.name' in the array menu
		function FillMenuArray()
		{
			while (menus.length > 0) {
				var mark1 = menus.indexOf("=");
				var mark2 = menus.indexOf("&");
				var menuItemName = menus.substr(0, mark1);
				menu[0][menuItemName] = menus.substring(mark1 + 1, mark2);
				menus = menus.substr(mark2 + 1, menus.length);
			}
		}

		// hide or show the corresponding submenus
		function InitializeMenu()
		{
			for (menuItemName in menu[0]) {
				if (!document.getElementById(menuItemName + "_Submenu")) {
					continue;
				}

				if (menu[0][menuItemName] == "0") {
					document.getElementById(menuItemName + "_Submenu").style.visibility = "hidden";
					document.getElementById(menuItemName + "_Submenu").style.display = "none";
					document.getElementById(menuItemName + "_Lights").style.visibility = "visible";
					document.getElementById(menuItemName + "_Extend").style.backgroundImage = "url(" + IMAGES + "/general/plus.gif)";
				} else {
					document.getElementById(menuItemName + "_Submenu").style.visibility = "visible";
					document.getElementById(menuItemName + "_Submenu").style.display = "block";
					document.getElementById(menuItemName + "_Lights").style.visibility = "hidden";
					document.getElementById(menuItemName + "_Extend").style.backgroundImage = "url(" + IMAGES + "/general/minus.gif)";
				}
			}
		}


		function SaveMenuArray() {
			let stringSlices = "";
			let temp = "";

			for (let menuItemName in menu[0]) {
				stringSlices = menuItemName + "=" + menu[0][menuItemName] + "&";
				temp = temp + stringSlices;
			}

			localStorage.setItem('menus', temp);
		}

		// onClick open or close submenus
		function MenuItemAction(sourceId) {
			if (menu[0][sourceId] == 1) {
				CloseMenuItem(sourceId);
			} else {
				$.each(menu[0], function (index, value) {
					if (value === '1') {
						CloseMenuItem(index);
					}
				});
				OpenMenuItem(sourceId);
			}
		}

		function OpenMenuItem(sourceId) {
			menu[0][sourceId] = 1;
			var submenu = document.getElementById(sourceId + "_Submenu");
			submenu.style.visibility = "visible";
			submenu.style.display = "block";
			document.getElementById(sourceId + "_Lights").style.visibility = "hidden";
			document.getElementById(sourceId + "_Extend").style.backgroundImage = "url(" + IMAGES + "/general/minus.gif)";
		}

		function CloseMenuItem(sourceId) {
			menu[0][sourceId] = 0;
			var submenu = document.getElementById(sourceId + "_Submenu");
			submenu.style.display = "none";
			submenu.style.visibility = "hidden";
			document.getElementById(sourceId + "_Lights").style.visibility = "visible";
			document.getElementById(sourceId + "_Extend").style.backgroundImage = "url(" + IMAGES + "/general/plus.gif)";
		}

		// mouse-over effects of menubuttons and submenuitems
		function MouseOverMenuItem(source) {
			if (source.firstChild.style) {
				source.firstChild.style.visibility = "visible";
			}
		}

		function MouseOutMenuItem(source) {
			if (source.firstChild.style) {
				source.firstChild.style.visibility = "hidden";
			}
		}

		function MouseOverSubmenuItem(source) {
			if (source.style) {
				source.style.backgroundColor = "";
			}
		}

		function MouseOutSubmenuItem(source) {
			if (source.style) {
				source.style.backgroundColor = "";
			}
		}
	</script>
	<?= template_place_holder('head_end'); ?>
</head>
<body onBeforeUnLoad="SaveMenu();" onUnload="SaveMenu();" style="background-image:url(<?= $template_path ?><?= getImageMenuRandom('bgs') ?>);
		 background-size: cover;
		 background-position: center;
		 background-repeat: no-repeat;
		 background-attachment: fixed;
		 width: 100%;
			height: 100%;
		 ">
<?= template_place_holder('body_start'); ?>
<?php if (!empty($config['facebook_link'])) { ?>
	<script type="text/javascript">
		window.fbAsyncInit = function () {
			FB.init({
				appId: 497232093667125, // App ID
				status: true,              // check login status
				cookie: true,              // enable cookies to allow the server to access the session
				xfbml: true               // parse XFBML
			});
			FB.Event.subscribe('auth.login', function () {
				var URLHelper = "?";
				if (window.location.search.replace("?", "").length > 0) {
					URLHelper = "&";
				}
				if (FB_TryLogin == 1) {
					window.location = window.location + URLHelper + "step=facebooktrylogin&wasreloaded=1";
				} else if (FB_TryLogin == 2) {
					window.location = window.location + URLHelper + "page=facebooktrylogin&wasreloaded=1";
				} else {
					window.location = window.location + URLHelper + "wasreloaded=1";
				}
			});
			FB.Event.subscribe('auth.logout', function (a_Response) {
				if (a_Response.status !== 'connected') {
					window.location.href = window.location.href;
				} else {
					/* nothing to do here*/
				}
			});
			FB.Event.subscribe('auth.statusChange', function (response) {
				if (FB_ForceReload == 1 && response.status == "connected") {
					var URLHelper = "?";
					if (window.location.search.replace("?", "").length > 0) {
						URLHelper = "&";
					}
					window.location = window.location + URLHelper + "step=facebooktrylogin&wasreloaded=1";
				}
			});
		};
		(function (d) {
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement('script');
			js.id = id;
			js.async = true;
			js.src = "//connect.facebook.net/en_US/all.js";
			ref.parentNode.insertBefore(js, ref);
		}(document));
	</script>
<?php } ?>
<div id="top"></div>
<div id="ArtworkHelper">
	<div id="Bodycontainer">
		<img id="EclipseCenteredLogo"
			 src="<?= $template_path; ?>/images/header/<?= $config['logo_image']; ?>"
			 onClick="window.location = '<?= getLink('news') ?>';" alt="Eclipse OT"/>
		<div id="ContentRow">
			<div id="MenuColumn">
				<div id="LeftArtwork">
					<img id="TibiaLogoArtworkTop"
						 src="<?= $template_path; ?>/images/header/<?= $config['logo_image']; ?>"
						 onClick="window.location = '<?= getLink('news') ?>';" alt="logoartwork"/>
					<img id="LogoLink" src="<?= $template_path; ?>/images/header/tibia-logo-artwork-string.gif"
						 onClick="window.location = 'mailto:<?= $config['mail_address']; ?>';" alt="logoartwork"/>
				</div>

				<?php
					$twig->display('canary.login-box.html.twig');
					$twig->display('canary.download-box.html.twig');
				?>

				<div id='Menu'>
					<div id='MenuTop'
						 style='background-image:url(<?= $template_path; ?>/images/general/box-top.gif);'></div>

					<?php
					$menus = get_template_menus();

$countElements = 0;
foreach($config['menu_categories'] as $id => $cat) {
	if (!isset($menus[$id]) || ($id == MENU_CATEGORY_SHOP && !setting('core.gifts_system'))) {
		continue;
	}

	$countElements++;
}

$i = 0;
foreach ($config['menu_categories'] as $id => $cat) {
	if(!isset($menus[$id]) || ($id == MENU_CATEGORY_SHOP && !setting('core.gifts_system'))) {
		continue;
	}

	$i++;
	?>
	<div id='<?= $cat['id']; ?>' class='menuitem'>
	<span onClick="MenuItemAction('<?= $cat['id']; ?>')">
		<div class='MenuButton' style='background-image:url(<?= $template_path ?>/images/menu/button-background.gif);'>
			<div onMouseOver='MouseOverMenuItem(this);' onMouseOut='MouseOutMenuItem(this);'>
			<div class='Button'
																								  style='background-image:url(<?= $template_path; ?>/images/menu/button-background-over.gif);'></div>
				<span id='<?= $cat['id']; ?>_Lights' class='Lights'>
					<div class='light_lu'
						 style='background-image:url(<?= $template_path; ?>/images/menu/green-light.gif);'></div>
					<div class='light_ld'
						 style='background-image:url(<?= $template_path; ?>/images/menu/green-light.gif);'></div>
					<div class='light_ru'
						 style='background-image:url(<?= $template_path; ?>/images/menu/green-light.gif);'></div>
				</span>
				<div id='<?= $cat['id']; ?>_Icon' class='Icon'
					 style='background-image:url(<?= $template_path ?><?= getImageMenuRandom($cat['id']) ?>);'></div>
				<div id='<?= $cat['id']; ?>_Label' class='Label'
					 style='background-image:url(<?= $template_path; ?>/images/menu/label-<?= $cat['id']; ?>.gif);'></div>
				<div id='<?= $cat['id']; ?>_Extend' class='Extend'
					 style='background-image:url(<?= $template_path; ?>/images/general/plus.gif);'></div>
			</div>
		</div>
	</span>
							<div id='<?= $cat['id']; ?>_Submenu' class='Submenu'>
								<?php
								foreach ($menus[$id] as $category => $menu) {
									$menuNameTranslations = [
										'Latest News' => 'Últimas Notícias',
										'Event Schedule' => 'Agenda de Eventos',
										'Account Management' => 'Gerenciar Conta',
										'Create Account' => 'Criar Conta',
										'Lost Account?' => 'Recuperar Conta',
										'Server Rules' => 'Regras do Servidor',
										'Characters' => 'Personagens',
										'Who is Online?' => 'Quem Está Online?',
										'Last Kills' => 'Últimas Mortes',
										'Houses' => 'Casas',
										'Guilds' => 'Guildas',
										'Support List' => 'Equipe de Suporte',
										'Monsters' => 'Monstros',
										'Spells' => 'Magias',
										'Commands' => 'Comandos',
										'Server Info' => 'Informações do Servidor',
										'Exp Table' => 'Tabela de Exp',
										'Buy Points' => 'Comprar Points',
										'Shop Offer' => 'Ofertas da Loja',
										'Shop History' => 'Histórico da Loja',
									];
									$menuName = $menuNameTranslations[$menu['name']] ?? $menu['name'];
									?>
									<a href='<?php echo $menu['link_full']; ?>'<?= $menu['target_blank']?>>
										<div id='submenu_<?= str_replace('/', '_', $menu['link']); ?>'
class='Submenuitem' onMouseOver='MouseOverSubmenuItem(this)'
											 onMouseOut='MouseOutSubmenuItem(this)'>
											<div class='LeftChain'
												 style='background-image:url(<?= $template_path; ?>/images/general/chain.gif);'></div>
											<div id='ActiveSubmenuItemIcon_<?= str_replace('/', '_', $menu['link']); ?>'
												 class='ActiveSubmenuItemIcon'
												 style='background-image:url(<?= $template_path; ?>/images/menu/icon-activesubmenu.gif);'></div>
											<div class='SubmenuitemLabel' <?php echo $menu['style_color']; ?>><?php echo $menuName; ?></div>
											<div class='RightChain'
												 style='background-image:url(<?= $template_path; ?>/images/general/chain.gif);'></div>
										</div>
									</a>
									<?php
								}
								?>
							</div>
							<?php
							if ($i == $countElements) {
								?>
								<div id='MenuBottom'
									 style='background-image:url(<?= $template_path; ?>/images/general/box-bottom.gif);'></div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
					<script type="text/javascript">
						InitializePage();
					</script>
			</div>
			</div>

			<div id="ContentColumn">
				<div class="Content">

					<?php if ($config['status_bar']) { ?>
						<div class="Box">
							<div class="Corner-tl"
								 style="background-image:url(<?= $template_path; ?>/images/global/content/corner-tl.gif);"></div>
							<div class="Corner-tr"
								 style="background-image:url(<?= $template_path; ?>/images/global/content/corner-tr.gif);"></div>
							<div class="Border_1"
								 style="background-image:url(<?= $template_path; ?>/images/global/content/border-1.gif);"></div>
							<div class="BorderTitleText eclipse-status-shell"
								 style="background-image:url(<?= $template_path; ?>/images/global/content/newsheadline_background.gif); height: 28px;">
								<div class="InfoBar eclipse-onlinebar <?= $status['online'] ? 'is-online' : 'is-offline' ?>">
										<a class="InfoBarLinks eclipse-onlinebar-link" href="<?= getLink('online'); ?>">
											<span class="eclipse-status-dot"></span>
											<img class="InfoBarBigLogo" src="<?= $template_path; ?>/images/global/header/icon-players-online.png" alt="Players Online">
											<span class="InfoBarNumbers">
												<span class="InfoBarSmallElement eclipse-status-text">
													<?= $status['online'] ? $status['players'] . ' Players Online' : 'Server Offline' ?>
												</span>
											</span>
										</a>
									</div>
								</div>
								<!-- COLLAPSE STATUS BAR -->
							<?php if ($config['collapse_status']) { ?>
								<div class="collapse" id="statusbar" style="background-color: #d4c0a1;">
									<table class="Table3" cellpadding="0" cellspacing="0" style="width: 100%;">
										<tbody>
										<tr>
											<td>
												<div class="InnerTableContainer"
													 style="display: flex; flex-wrap: wrap; font-family: Verdana;">
													<?php if ($config['carousel_status']) { ?>
														<table style="width:100%;">
															<tbody>
															<tr>
																<td>
																	<div class="TableContentContainer">
																		<table class="TableContent" width="100%"
																			   style="border:1px solid #faf0d7; font-size: 12px;">
																			<tbody>
																			<tr bgcolor="#F1E0C6">
																				<td>
																					<div class="container">
																						<div
																							id="carouselExampleCaptions"
																							class="carousel slide"
																							data-bs-ride="carousel">
																							<div class="carousel-inner">
																								<?php
																								$count = 1;
																								foreach ($config['carousel'] as $carousel) {
																									if ($count == 1) {
																										?>
																										<div
																											class="carousel-item active">
																											<img
																												src="<?= $template_path ?>/images/carousel/<?= $carousel ?>"
																												style="width: 100%;">
																										</div>
																										<?php
																									} elseif ($count > 1) {
																										?>
																										<div
																											class="carousel-item">
																											<img
																												src="<?= $template_path ?>/images/carousel/<?= $carousel ?>"
																												style="width: 100%;">
																										</div>
																										<?php
																									}
																									$count++;
																								}
																								?>
																							</div>
																							<button
																								class="carousel-control-prev"
																								type="button"
																								data-bs-target="#carouselExampleCaptions"
																								data-bs-slide="prev">
																								<span
																									class="carousel-control-prev-icon"
																									aria-hidden="true"></span>
																							</button>
																							<button
																								class="carousel-control-next"
																								type="button"
																								data-bs-target="#carouselExampleCaptions"
																								data-bs-slide="next">
																								<span
																									class="carousel-control-next-icon"
																									aria-hidden="true"></span>
																							</button>
																						</div>
																					</div>
																				</td>
																			</tr>
																			</tbody>
																		</table>
																	</div>
																</td>
															</tr>
															</tbody>
														</table>
													<?php } ?>
												</div>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							<?php } ?>
							<!-- COLLAPSE STATUS BAR -->
							<div class="Border_1"
								 style="background-image:url(<?= $template_path; ?>/images/global/content/border-1.gif);"></div>
							<div class="CornerWrapper-b">
								<div class="Corner-bl"
									 style="background-image:url(<?= $template_path; ?>/images/global/content/corner-bl.gif);"></div>
							</div>
							<div class="CornerWrapper-b">
								<div class="Corner-br"
									 style="background-image:url(<?= $template_path; ?>/images/global/content/corner-br.gif);"></div>
							</div>
						</div>
					<?php } ?>

					<div id="ContentHelper">
						<?= tickers(); ?>
						<div id="News" class="Box">
							<div class="Corner-tl"
								 style="background-image:url(<?= $template_path; ?>/images/content/corner-tl.gif);"></div>
							<div class="Corner-tr"
								 style="background-image:url(<?= $template_path; ?>/images/content/corner-tr.gif);"></div>
							<div class="Border_1"
								 style="background-image:url(<?= $template_path; ?>/images/content/border-1.gif);"></div>
							<div class="BorderTitleText"
								 style="background-image:url(<?= $template_path; ?>/images/content/title-background-green.gif);"></div>
							<?php
							$headline = $template_path . '/images/header/headline-' . PAGE . '.gif';
							if (!file_exists($headline))
								$headline = $template_path . '/headline.php?t=' . ucfirst($title);
							?>
							<img class="Title" src="<?= $headline; ?>" alt="Contentbox headline"/>
							<div class="Border_2">
								<div class="Border_3">
									<?php $hooks->trigger(HOOK_TIBIACOM_BORDER_3); ?>
									<div class="BoxContent"
										 style="background-image:url(<?= $template_path; ?>/images/content/scroll.gif);">
										<?= template_place_holder('center_top') . $content; ?>
									</div>
								</div>
							</div>
							<div class="Border_1"
								 style="background-image:url(<?= $template_path; ?>/images/content/border-1.gif);"></div>

							<div class="CornerWrapper-b">
								<div class="Corner-bl"
									 style="background-image:url(<?= $template_path; ?>/images/content/corner-bl.gif);"></div>
							</div>
							<div class="CornerWrapper-b">
								<div class="Corner-br"
									 style="background-image:url(<?= $template_path; ?>/images/content/corner-br.gif);"></div>
							</div>
						</div>
					</div>
				</div>
				<div id="EclipseFooterServerSave">
					<?php include __DIR__ . "/boxes/serversave.php"; ?>
				</div>
				<div id="Footer">
					<?php
					$eclipseFooter = [];
						if (admin()) {
							$eclipseFooter[] = generateLink(ADMIN_URL, "Admin Panel", true);
						}
						$eclipseFooter[] = "Eclipse Ot";
						$eclipseFooter[] = "Copyright &copy; " . date("Y") . " Eclipse Ot. All rights reserved.";
						echo implode("<br/>", $eclipseFooter);
						?>
				</div>
			</div>

			<div id="ThemeboxesColumn">
				<?php
				if ($db->hasTable('boosted_creature') && $db->hasTable('boosted_boss')):
				$creaturequery = $db->query("SELECT `boostname`, `looktype`, `lookfeet` , `looklegs` , `lookhead` , `lookbody` , `lookaddons` , `lookmount`   FROM `boosted_creature`")->fetch();
				$creaturename = $creaturequery["boostname"];
				$creaturetype = $creaturequery["looktype"];
				$creaturefeet = $creaturequery["lookfeet"];
				$creaturelegs = $creaturequery["looklegs"];
				$creaturehead = $creaturequery["lookhead"];
				$creaturebody = $creaturequery["lookbody"];
				$creatureaddons = $creaturequery["lookaddons"];
				$creaturemount = $creaturequery["lookmount"];
				?>

				<?php
				$bossquery = $db->query("SELECT `boostname`, `looktypeEx`, `looktype`, `lookfeet` , `looklegs` , `lookhead` , `lookbody` , `lookaddons` , `lookmount`   FROM `boosted_boss`")->fetch();
				$bossname = $bossquery["boostname"];
				$bosstypeEx = $bossquery["looktypeEx"];
				$bosstype = $bossquery["looktype"];
				$bossfeet = $bossquery["lookfeet"];
				$bosslegs = $bossquery["looklegs"];
				$bosshead = $bossquery["lookhead"];
				$bossbody = $bossquery["lookbody"];
				$bossaddons = $bossquery["lookaddons"];
				$bossmount = $bossquery["lookmount"];
				?>
				<div id="RightArtwork">
					<img id="Creature"
						 src="<?= $config['outfit_images_url'] ?>?id=<?= $creaturetype; ?>&addons=<?= $creatureaddons; ?>&head=<?= $creaturehead; ?>&body=<?= $creaturebody; ?>&legs=<?= $creaturelegs; ?>&feet=<?= $creaturefeet; ?>&mount=<?= $creaturemount; ?>"
						 alt="Creature of the Day"
						 title="Today's boosted creature: <?= ucwords(strtolower(trim($creaturename))); ?>">

					<?php if ($bosstypeEx != 0): ?>
						<img id="Boss" src="<?= $config['item_images_url'] ?><?= $bosstypeEx; ?>.gif"
							 alt="Boss of the Day"
							 title="Today's boosted boss: <?= ucwords(strtolower(trim($bossname))); ?>">
					<?php else: ?>
						<img id="Boss"
							 src="<?= $config['outfit_images_url'] ?>?id=<?= $bosstype; ?>&addons=<?= $bossaddons; ?>&head=<?= $bosshead; ?>&body=<?= $bossbody; ?>&legs=<?= $bosslegs; ?>&feet=<?= $bossfeet; ?>&mount=<?= $bossmount; ?>"
							 alt="Boss of the Day"
							 title="Today's boosted boss: <?= ucwords(strtolower(trim($bossname))); ?>">
					<?php endif; ?>

					<img id="PedestalAndOnline" src="<?= $template_path; ?>/images/header/pedestal.gif"
						 alt="Monster Pedestal and Players Online Box"/>
				</div>
				<?php endif; ?>

				<div id="Themeboxes">
					<?php
					$twig_loader->prependPath(__DIR__ . '/boxes/templates');

					foreach ($config['boxes'] as $box) {
						/** @var string $template_name */
						$file = __DIR__ . '/boxes/' . $box . '.php';
						if (file_exists($file)) {
							include($file); ?>
							<?php
						}
					}

					if ($config['template_allow_change'])
						echo '<span style="color: white">Template:</span><br/>' . template_form();
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?= template_place_holder('body_end'); ?>

<style id="eclipse-right-sidebar-final">
	#ThemeboxesColumn { width: 210px !important; min-width: 210px !important; display: flex !important; flex-direction: column !important; align-items: center !important; position: absolute !important; right: 0 !important; left: auto !important; margin-right: 0 !important; margin-left: 15px !important; }
	#Themeboxes { width: 188px !important; display: flex !important; flex-direction: column !important; align-items: center !important; gap: 14px !important; margin: 0 auto !important; }
	#RightArtwork { display: none !important; }
	#Themeboxes > br, #Themeboxes .rank_bottom, #Themeboxes .discord_bottom, #Themeboxes .donate_bottom, #Themeboxes .serversave_bottom, #Themeboxes .searchchar_bottom, #Themeboxes .rashid_bottom { display: none !important; }
	#Themeboxes .donate { order: 1 !important; }
	#Themeboxes .eclipse-boosted { order: 2 !important; }
	#Themeboxes .discord { order: 4 !important; }
	
	
	
	#Themeboxes .rank { order: 3 !important; }
	#Themeboxes .eclipse-rightbox, #Themeboxes .donate, #Themeboxes .discord, #Themeboxes .serversave, #Themeboxes .searchchar, #Themeboxes .rank { width: 188px !important; min-width: 188px !important; max-width: 188px !important; height: auto !important; margin: 0 !important; box-sizing: border-box !important; text-align: center !important; position: relative !important; }
	#Themeboxes .eclipse-rightbox-title, #Themeboxes .donate_header, #Themeboxes .discord_header, #Themeboxes .serversave_header, #Themeboxes .searchchar_header, #Themeboxes .rank_header { width: 188px !important; height: 32px !important; line-height: 31px !important; box-sizing: border-box !important; background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important; border: 2px solid #a86b23 !important; border-radius: 4px 4px 0 0 !important; color: #f7e7bd !important; font-family: Georgia, 'Times New Roman', serif !important; font-size: 15px !important; font-weight: 800 !important; text-transform: uppercase !important; text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important; margin: 0 !important; padding: 0 !important; background-image: none !important; }
	#Themeboxes .eclipse-rightbox-title::before, #Themeboxes .eclipse-rightbox-title::after, #Themeboxes .donate_header::before, #Themeboxes .donate_header::after, #Themeboxes .discord_header::before, #Themeboxes .discord_header::after, #Themeboxes .serversave_header::before, #Themeboxes .serversave_header::after, #Themeboxes .searchchar_header::before, #Themeboxes .searchchar_header::after, #Themeboxes .rank_header::before, #Themeboxes .rank_header::after { content: '*'; color: #ffcb66; font-size: 12px; padding: 0 5px; }
	#Themeboxes .eclipse-rightbox-content, #Themeboxes .donate_content, #Themeboxes .discord_content, #Themeboxes .serversave_content, #Themeboxes .searchchar_content, #Themeboxes .rank_content { width: 188px !important; min-width: 188px !important; max-width: 188px !important; height: auto !important; min-height: 0 !important; padding: 12px 10px !important; box-sizing: border-box !important; background-color: #d9b36d !important; background-image: linear-gradient(180deg, rgba(239,212,158,.97), rgba(202,156,86,.97)) !important; border: 2px solid #a86b23 !important; border-top: 0 !important; border-radius: 0 0 5px 5px !important; color: #432816 !important; box-shadow: inset 0 0 0 1px rgba(255,244,198,.55), 0 4px 12px rgba(0,0,0,.55) !important;  }
	#Themeboxes .donate_content { padding: 9px !important; }
	#Themeboxes .donate_content img { width: 166px !important; height: 78px !important; object-fit: cover !important; display: block !important; margin: 0 auto 8px !important; border: 1px solid rgba(69,24,16,.85) !important; filter: saturate(1.15) contrast(1.08) brightness(.88) !important; }
	#Themeboxes .donate_content > div { position: relative !important; }
	#Themeboxes .donate_content > div::after { content: 'CONTEUDO EXCLUSIVO'; position: absolute; left: 0; right: 0; bottom: 13px; color: #ffe16c; font-family: Georgia, 'Times New Roman', serif; font-weight: 800; font-style: italic; font-size: 14px; text-shadow: 0 2px 2px #160706, 0 0 5px #000; }
	#Themeboxes .donate_button, #Themeboxes .discord_button, #Themeboxes .searchchar_button, #Themeboxes .rank_button, #Themeboxes .eclipse-action-button { width: 154px !important; height: 34px !important; line-height: 32px !important; display: inline-block !important; border: 1px solid #ffe5a2 !important; border-radius: 4px !important; background: linear-gradient(180deg, #ff9d26 0%, #c45608 100%) !important; color: #fff8dc !important; font-family: Verdana, Arial, sans-serif !important; font-size: 11px !important; font-weight: 800 !important; text-transform: uppercase !important; text-decoration: none !important; text-shadow: 0 1px 1px #4c1200 !important; box-shadow: inset 0 1px 0 rgba(255,255,255,.32), 0 2px 7px rgba(0,0,0,.45) !important; cursor: pointer !important; padding: 0 !important; }
	#Themeboxes .donate_button:hover, #Themeboxes .discord_button:hover, #Themeboxes .searchchar_button:hover, #Themeboxes .rank_button:hover, #Themeboxes .eclipse-action-button:hover { background: linear-gradient(180deg, #ffc04f 0%, #d86108 100%) !important; color: #fff !important; }
	#Themeboxes .eclipse-boosted-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 8px !important; padding: 14px 9px !important; }
	#Themeboxes .eclipse-boosted-item { display: flex; flex-direction: column; align-items: center; color: #274052; font-family: Verdana, Arial, sans-serif; font-size: 9px; line-height: 1.2; }
	#Themeboxes .eclipse-boosted-frame { width: 50px; height: 50px; display: grid; place-items: center; background: rgba(13,37,53,.08); border: 2px solid #28516d; border-radius: 4px; margin-bottom: 5px; overflow: hidden; }
	#Themeboxes .eclipse-boosted-frame img { max-width: 64px; max-height: 64px; object-fit: contain; }
	#Themeboxes .eclipse-boosted-item strong { color: #18384f; font-size: 10px; }
	#Themeboxes .eclipse-boosted-item span { max-width: 72px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
	#Themeboxes .discord_content, #Themeboxes .eclipse-actionbox { display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; gap: 9px !important; min-height: 116px !important; }
	#Themeboxes .discord_content::before { content: ''; width: 54px; height: 54px; border-radius: 50%; background: radial-gradient(circle at 50% 45%, #7d86ff 0 28%, #5865f2 29% 58%, #3540b8 59% 100%); border: 4px solid rgba(255,255,255,.42); box-shadow: 0 0 18px rgba(88,101,242,.65); }
	#Themeboxes .discord_content::after { content: 'Entre na nossa comunidade!'; color: #4d3520; font-size: 10px; margin-top: -3px; }
	#Themeboxes .discord_button { background: linear-gradient(180deg, #6777ff 0%, #3344cb 100%) !important; border-color: #cfd5ff !important; }
	#Themeboxes .eclipse-action-icon { width: 54px; height: 54px; border-radius: 50%; display: grid; place-items: center; background: radial-gradient(circle at 50% 45%, #f4d06a 0 28%, #c8972e 29% 70%, #8d5b14 71% 100%); color: #fff5cd; font-family: Georgia, 'Times New Roman', serif; font-size: 30px; font-weight: 900; box-shadow: 0 0 18px rgba(255,180,59,.5); }
	#Themeboxes .eclipse-actionbox p { margin: 0; color: #4d3520; font-size: 10px; line-height: 1.35; }
	#Themeboxes .serversave_content { display: grid !important; justify-content: stretch !important; align-items: center !important; gap: 7px !important; }
	#Themeboxes .serversave_text { color: #4d3520 !important; font-weight: 700 !important; }
	#Themeboxes .serversave_countdown { color: #fff3d5 !important; background: rgba(40,18,12,.72) !important; border-color: #ffe2a1 !important; border-radius: 4px !important; font-size: 22px !important; letter-spacing: 0 !important; }
	#Themeboxes .searchchar_input { width: 154px !important; height: 32px !important; margin: 0 auto 7px !important; border: 1px solid #8a6230 !important; border-radius: 4px !important; box-sizing: border-box !important; }
	#Themeboxes .rank_content { padding: 8px 8px 11px !important; }
	#Themeboxes .rank_player { color: #4d3520 !important; min-height: 42px !important; padding: 6px 2px 6px 4px !important; border-bottom: 1px solid rgba(112,73,33,.35) !important; }
	#Themeboxes .rank_text { margin-left: 46px !important; color: #4d3520 !important; }
	#Themeboxes .rank_text a { color: #44140d !important; }




		#Themeboxes .eclipse-boosted-grid { gap: 10px !important; padding: 12px 8px 13px !important; overflow: visible !important; }
		#Themeboxes .eclipse-boosted-item { min-width: 0 !important; overflow: visible !important; }
		#Themeboxes .eclipse-boosted-frame { width: 58px !important; height: 58px !important; overflow: visible !important; margin-bottom: 6px !important; }
		#Themeboxes .eclipse-boosted-frame img { max-width: 76px !important; max-height: 76px !important; object-fit: contain !important; }
		#Themeboxes .eclipse-boosted-item span { max-width: 78px !important; white-space: normal !important; overflow: hidden !important; text-overflow: clip !important; display: -webkit-box !important; -webkit-line-clamp: 2 !important; -webkit-box-orient: vertical !important; line-height: 1.15 !important; min-height: 21px !important; }
		#EclipseFooterServerSave { width: 210px !important; margin: 22px auto 10px !important; }
		#EclipseFooterServerSave .serversave { width: 210px !important; filter: none !important; }
		#EclipseFooterServerSave .serversave_header { width: 210px !important; height: 26px !important; line-height: 25px !important; background: linear-gradient(180deg, rgba(47,9,6,.96), rgba(8,3,3,.98)) !important; border: 1px solid rgba(255,122,24,.75) !important; border-bottom: 0 !important; border-radius: 4px 4px 0 0 !important; color: #ffd494 !important; font-size: 12px !important; letter-spacing: 0 !important; text-shadow: 0 0 8px rgba(255,93,32,.85), 0 1px 2px #000 !important; }
		#EclipseFooterServerSave .serversave_header::before, #EclipseFooterServerSave .serversave_header::after { content: '*'; color: #ffb653; padding: 0 5px; }
		#EclipseFooterServerSave .serversave_content { width: 210px !important; padding: 8px 10px 10px !important; background: linear-gradient(180deg, rgba(20,5,4,.94), rgba(3,1,1,.96)) !important; border: 1px solid rgba(255,122,24,.75) !important; border-top: 0 !important; border-radius: 0 0 4px 4px !important; box-shadow: 0 0 18px rgba(0,0,0,.72), inset 0 0 14px rgba(255,70,24,.10) !important; }
		#EclipseFooterServerSave .serversave_text { color: #d9a66c !important; font-size: 10px !important; text-transform: uppercase !important; }
		#EclipseFooterServerSave .serversave_countdown { color: #ffe5bc !important; background: rgba(0,0,0,.55) !important; border: 1px solid rgba(255,195,104,.50) !important; border-radius: 3px !important; font-size: 18px !important; text-shadow: 0 0 8px rgba(255,94,32,.55), 0 1px 2px #000 !important; }

		/* Final polish: boosted sprite centering and dark footer server save box. */
		#Themeboxes .eclipse-boosted-grid {
		  grid-template-columns: 1fr 1fr !important;
		  gap: 8px !important;
		  padding: 12px 8px 14px !important;
		}
		
		#Themeboxes .eclipse-boosted-item {
		  align-items: center !important;
		  justify-content: flex-start !important;
		  text-align: center !important;
		}
		
		#Themeboxes .eclipse-boosted-frame {
		  width: 58px !important;
		  height: 58px !important;
		  display: flex !important;
		  align-items: center !important;
		  justify-content: center !important;
		  overflow: visible !important;
		  box-sizing: border-box !important;
		}
		
		#Themeboxes .eclipse-boosted-frame img {
		  display: block !important;
		  margin: auto !important;
		  position: static !important;
		  transform: none !important;
		}
		
		#Themeboxes .eclipse-boosted-boss .eclipse-boosted-frame img {
		  max-width: 76px !important;
		  max-height: 76px !important;
		}
		
		#Themeboxes .eclipse-boosted-creature .eclipse-boosted-frame img {
		  max-width: 54px !important;
		  max-height: 54px !important;
		}
		
		#Themeboxes .eclipse-boosted-item span {
		  max-width: 74px !important;
		  margin: 0 auto !important;
		  white-space: normal !important;
		  overflow: hidden !important;
		  display: -webkit-box !important;
		  -webkit-line-clamp: 2 !important;
		  -webkit-box-orient: vertical !important;
		}
		
		#EclipseFooterServerSave,
		#EclipseFooterServerSave .serversave {
		  width: 210px !important;
		  height: auto !important;
		  min-height: 0 !important;
		  box-sizing: border-box !important;
		}
		
		#EclipseFooterServerSave {
		  margin: 20px auto 8px !important;
		}
		
		#EclipseFooterServerSave .serversave_header {
		  width: 210px !important;
		  height: 28px !important;
		  line-height: 28px !important;
		  box-sizing: border-box !important;
		  margin: 0 !important;
		}
		
		#EclipseFooterServerSave .serversave_content {
		  width: 210px !important;
		  height: auto !important;
		  min-height: 66px !important;
		  padding: 9px 12px 10px !important;
		  display: flex !important;
		  flex-direction: column !important;
		  align-items: center !important;
		  justify-content: center !important;
		  gap: 7px !important;
		  box-sizing: border-box !important;
		}
		
		#EclipseFooterServerSave .serversave_text {
		  width: 100% !important;
		  line-height: 1.1 !important;
		}
		
		#EclipseFooterServerSave .serversave_countdown {
		  width: 164px !important;
		  height: 32px !important;
		  line-height: 31px !important;
		  padding: 0 !important;
		  box-sizing: border-box !important;
		}
		
		/* Simple footer server save. */
		#EclipseFooterServerSave {
		  width: 190px !important;
		  margin: 18px auto 8px !important;
		  text-align: center !important;
		}
		
		#EclipseFooterServerSave .serversave {
		  width: 190px !important;
		  height: auto !important;
		  margin: 0 auto !important;
		  filter: none !important;
		}
		
		#EclipseFooterServerSave .serversave_header,
		#EclipseFooterServerSave .serversave_text,
		#EclipseFooterServerSave .serversave_bottom {
		  display: none !important;
		}
		
		#EclipseFooterServerSave .serversave_content {
		  width: 190px !important;
		  height: auto !important;
		  min-height: 0 !important;
		  padding: 0 !important;
		  margin: 0 !important;
		  display: block !important;
		  background: transparent !important;
		  border: 0 !important;
		  box-shadow: none !important;
		}
		
		#EclipseFooterServerSave .serversave_countdown {
		  width: auto !important;
		  height: auto !important;
		  line-height: 1 !important;
		  padding: 0 !important;
		  margin: 0 auto !important;
		  display: inline-block !important;
		  background: transparent !important;
		  border: 0 !important;
		  color: #ffd494 !important;
		  font-family: Georgia, 'Times New Roman', serif !important;
		  font-size: 20px !important;
		  font-weight: 800 !important;
		  text-shadow: 0 0 10px rgba(255, 87, 28, .55), 0 1px 2px #000 !important;
		}
		
		/* Server save as a single footer line. */
		#EclipseFooterServerSave .serversave_content {
		  display: flex !important;
		  align-items: baseline !important;
		  justify-content: center !important;
		  gap: 7px !important;
		  width: auto !important;
		}
		
		#EclipseFooterServerSave .serversave_content::before {
		  content: 'Server Save:';
		  color: #ffd494 !important;
		  font-family: Georgia, 'Times New Roman', serif !important;
		  font-size: 14px !important;
		  font-weight: 800 !important;
		  text-shadow: 0 0 8px rgba(255, 87, 28, .45), 0 1px 2px #000 !important;
		}
		
		#EclipseFooterServerSave .serversave_countdown {
		  font-size: 18px !important;
		}
		
		/* Server save stacked footer text. */
		#EclipseFooterServerSave .serversave_content {
		  display: flex !important;
		  flex-direction: column !important;
		  align-items: center !important;
		  justify-content: center !important;
		  gap: 4px !important;
		}
		
		#EclipseFooterServerSave .serversave_content::before {
		  content: 'Server Save:';
		  color: #ffd494 !important;
		  font-family: Georgia, 'Times New Roman', serif !important;
		  font-size: 14px !important;
		  font-weight: 800 !important;
		  line-height: 1 !important;
		  text-shadow: 0 0 8px rgba(255, 87, 28, .45), 0 1px 2px #000 !important;
		}
		
		#EclipseFooterServerSave .serversave_countdown {
		  font-size: 18px !important;
		  line-height: 1 !important;
		}
		
		/* Center Eclipse logo above the online players bar. */
		#LeftArtwork {
		  position: fixed !important;
		  top: 0 !important;
		  left: 0 !important;
		  right: 0 !important;
		  width: 100vw !important;
		  height: 210px !important;
		  display: flex !important;
		  justify-content: center !important;
		  align-items: flex-start !important;
		  pointer-events: none !important;
		  z-index: 50000 !important;
		}
		
		#LeftArtwork #TibiaLogoArtworkTop {
		  position: relative !important;
		  top: 4px !important;
		  left: auto !important;
		  width: 286px !important;
		  height: 208px !important;
		  display: block !important;
		  margin-left: 0 !important;
		  transform: none !important;
		  object-fit: contain !important;
		  pointer-events: auto !important;
		  z-index: 50000 !important;
		}

		#LeftArtwork {
		  display: none !important;
		}

		#EclipseCenteredLogo {
		  position: absolute !important;
		  top: 4px !important;
		  left: 50% !important;
		  width: 286px !important;
		  height: 208px !important;
		  display: block !important;
		  transform: translateX(-50%) !important;
		  object-fit: contain !important;
		  cursor: pointer !important;
		  z-index: 999999 !important;
		  filter: drop-shadow(0 16px 22px rgba(0,0,0,.96)) drop-shadow(0 0 20px rgba(255,54,24,.58));
		}

		/* Final central content skin. */
		.Content .Box {
		  margin-bottom: 14px !important;
		  background: rgba(6, 2, 2, .88) !important;
		  border: 1px solid rgba(255, 112, 36, .42) !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  box-shadow: 0 16px 32px rgba(0,0,0,.72), 0 0 0 1px rgba(255,203,115,.08), inset 0 0 28px rgba(255,54,24,.07) !important;
		}

		.Content .Corner-tl,
		.Content .Corner-tr,
		.Content .Corner-bl,
		.Content .Corner-br,
		.Content .Border_1,
		.Content .Border_2,
		.Content .Border_3,
		.Content .Border_4,
		.Content .Border_5,
		.Content .Border_6,
		.Content .Border_7,
		.Content .Border_8,
		.Content .Border_9,
		.Content .BottomCornersHelper,
		.Content .CornerWrapper-b {
		  display: none !important;
		}

		.Content .BoxContent {
		  min-height: 76px !important;
		  padding: 12px 14px !important;
		  background: radial-gradient(circle at 50% 0, rgba(160,26,17,.20), transparent 42%), linear-gradient(180deg, rgba(31,8,8,.94), rgba(13,4,4,.98)) !important;
		  color: #f2d9be !important;
		  font-family: Verdana, Arial, sans-serif !important;
		  font-size: 12px !important;
		  line-height: 1.55 !important;
		  box-shadow: inset 0 1px 0 rgba(255,219,150,.10), inset 0 0 32px rgba(0,0,0,.46) !important;
		}

		.Content .Border_2,
		.Content .Border_3 {
		  display: block !important;
		  margin: 0 !important;
		  padding: 0 !important;
		  background: transparent !important;
		  border: 0 !important;
		}

		.Content .BoxContent table,
		.Content .BoxContent tbody,
		.Content .BoxContent tr,
		.Content .BoxContent td {
		  background: transparent !important;
		  color: inherit !important;
		}

		.Content .BoxContent td {
		  padding: 8px 10px !important;
		}

		.Content .BoxContent a {
		  color: #ffb768 !important;
		  text-decoration: none !important;
		}

		.Content .BoxContent a:hover {
		  color: #ffe1a8 !important;
		  text-shadow: 0 0 8px rgba(255,112,36,.55) !important;
		}

		.Content .Title {
		  position: relative !important;
		  top: auto !important;
		  left: auto !important;
		  display: block !important;
		  padding: 6px 12px 7px !important;
		  color: #ffe2a8 !important;
		  font-family: Georgia, 'Times New Roman', serif !important;
		  font-size: 20px !important;
		  font-weight: 800 !important;
		  line-height: 1 !important;
		  letter-spacing: 0 !important;
		  text-shadow: 0 2px 2px #000, 0 0 10px rgba(255,95,35,.62) !important;
		}

		.Content .Title::first-letter {
		  color: #5e170d !important;
		}

		.Content .BorderTitleText:not(.eclipse-status-shell) {
		  height: 34px !important;
		  display: flex !important;
		  align-items: center !important;
		  background: linear-gradient(90deg, rgba(10,3,3,.98), rgba(61,12,8,.96) 48%, rgba(10,3,3,.98)) !important;
		  border-bottom: 1px solid rgba(255,105,42,.45) !important;
		  box-shadow: inset 0 1px 0 rgba(255,219,150,.12), inset 0 -1px 0 rgba(0,0,0,.72), 0 5px 14px rgba(0,0,0,.35) !important;
		}

		.Content #NewsTicker .BoxContent,
		.Content #News .BoxContent,
		.Content #FeaturedArticle .BoxContent {
		  min-height: 88px !important;
		}

		.Content #NewsTicker .BoxContent .Row {
		  min-height: 28px !important;
		  padding: 7px 9px 7px 34px !important;
		  box-sizing: border-box !important;
		  border: 1px solid rgba(255,126,45,.22) !important;
		  border-radius: 4px !important;
		  background: linear-gradient(180deg, rgba(0,0,0,.38), rgba(0,0,0,.22)) !important;
		}

		.Content #NewsTicker .NewsTickerIcon {
		  top: 8px !important;
		  left: 9px !important;
		  filter: drop-shadow(0 0 6px rgba(255,95,35,.55)) !important;
		}

		.Content #NewsTicker .NewsTickerText {
		  margin-left: 0 !important;
		  color: #ead0b8 !important;
		}

		.Content #NewsTicker .NewsTickerDate {
		  position: static !important;
		  display: inline-block !important;
		  width: auto !important;
		  margin-right: 16px !important;
		  color: #c69a72 !important;
		  font-size: 10px !important;
		}

		.Content #NewsTicker .NewsTickerShortText,
		.Content #NewsTicker .NewsTickerFullText {
		  margin-left: 0 !important;
		  color: #f1d9bf !important;
		}

		.Content #News .NewsHeadlineBackground,
		.Content #FeaturedArticle .NewsHeadlineBackground,
		.Content #NewsArchive .NewsHeadlineBackground {
		  height: 32px !important;
		  margin: 0 0 10px !important;
		  border: 1px solid rgba(255,126,45,.28) !important;
		  border-radius: 4px !important;
		  background: linear-gradient(90deg, rgba(0,0,0,.78), rgba(50,9,8,.84), rgba(0,0,0,.78)) !important;
		  box-shadow: inset 0 1px 0 rgba(255,218,145,.10) !important;
		}

		.Content #News .NewsHeadlineIcon,
		.Content #FeaturedArticle .NewsHeadlineIcon,
		.Content #NewsArchive .NewsHeadlineIcon {
		  top: 5px !important;
		  left: 9px !important;
		  margin: 0 !important;
		  filter: drop-shadow(0 0 6px rgba(255,95,35,.50)) !important;
		}

		.Content #News .NewsHeadlineDate,
		.Content #FeaturedArticle .NewsHeadlineDate,
		.Content #NewsArchive .NewsHeadlineDate {
		  top: 9px !important;
		  left: 48px !important;
		  width: 96px !important;
		  color: #c69a72 !important;
		  font-size: 10px !important;
		}

		.Content #News .NewsHeadlineText,
		.Content #FeaturedArticle .NewsHeadlineText,
		.Content #NewsArchive .NewsHeadlineText {
		  top: 8px !important;
		  left: 142px !important;
		  color: #d8c1ad !important;
		  font-size: 13px !important;
		  text-shadow: 0 1px 2px #000 !important;
		}

		/* Final contrast pass for ticker and news text. */
		.Content .BoxContent {
		  background: radial-gradient(circle at 50% 0, rgba(189,45,22,.24), transparent 48%), linear-gradient(180deg, rgba(48,16,13,.97), rgba(26,9,8,.98)) !important;
		  color: #ffe6ca !important;
		}

		.Content .BoxContent div,
		.Content .BoxContent span,
		.Content .BoxContent td,
		.Content .BoxContent p,
		.Content .BoxContent b,
		.Content .BoxContent i {
		  color: #ffe6ca !important;
		  opacity: 1 !important;
		}

		.Content #NewsTicker .BoxContent {
		  background: linear-gradient(180deg, rgba(52,17,13,.98), rgba(24,7,7,.98)) !important;
		}

		.Content #NewsTicker .BoxContent .Row {
		  min-height: 38px !important;
		  padding: 10px 38px 10px 38px !important;
		  border-color: rgba(255,160,72,.40) !important;
		  background: linear-gradient(180deg, rgba(92,33,24,.88), rgba(47,15,12,.92)) !important;
		  box-shadow: inset 0 1px 0 rgba(255,221,158,.12), 0 6px 16px rgba(0,0,0,.30) !important;
		}

		.Content #NewsTicker .NewsTickerText,
		.Content #NewsTicker .NewsTickerText div,
		.Content #NewsTicker .NewsTickerText span,
		.Content #NewsTicker .NewsTickerShortText,
		.Content #NewsTicker .NewsTickerFullText {
		  color: #ffe3c1 !important;
		  opacity: 1 !important;
		  text-shadow: 0 1px 2px #000 !important;
		}

		.Content #NewsTicker .NewsTickerDate {
		  color: #ffbf7a !important;
		  font-weight: 700 !important;
		}

		.Content #NewsTicker .NewsTickerIcon {
		  top: 11px !important;
		}

		.Content #NewsTicker .NewsTickerExtend {
		  position: absolute !important;
		  top: 12px !important;
		  right: 11px !important;
		}

		.Content #News .NewsHeadlineBackground,
		.Content #FeaturedArticle .NewsHeadlineBackground,
		.Content #NewsArchive .NewsHeadlineBackground {
		  background: linear-gradient(90deg, rgba(15,4,4,.96), rgba(93,25,15,.92), rgba(15,4,4,.96)) !important;
		  border-color: rgba(255,160,72,.42) !important;
		}

		.Content #News .NewsHeadlineDate,
		.Content #FeaturedArticle .NewsHeadlineDate,
		.Content #NewsArchive .NewsHeadlineDate {
		  color: #ffbf7a !important;
		  opacity: 1 !important;
		}

		.Content #News .NewsHeadlineText,
		.Content #FeaturedArticle .NewsHeadlineText,
		.Content #NewsArchive .NewsHeadlineText {
		  color: #ffe3c1 !important;
		  opacity: 1 !important;
		}

		.Content #News .BoxContent > table td {
		  color: #ffe6ca !important;
		  font-size: 13px !important;
		  line-height: 1.65 !important;
		  text-shadow: 0 1px 2px rgba(0,0,0,.72) !important;
		}

		.Content #News .BoxContent > table td img[align="bottom"] {
		  filter: sepia(.45) saturate(1.8) brightness(1.9) drop-shadow(0 0 7px rgba(255,125,45,.45)) !important;
		}

		/* Keep all central content text readable over dark panels. */
		#ContentColumn .Content,
		#ContentColumn .Content *,
		#ContentColumn .Content .BoxContent,
		#ContentColumn .Content .BoxContent *,
		#ContentColumn .Content table,
		#ContentColumn .Content tr,
		#ContentColumn .Content td,
		#ContentColumn .Content th,
		#ContentColumn .Content div,
		#ContentColumn .Content span,
		#ContentColumn .Content p,
		#ContentColumn .Content li,
		#ContentColumn .Content label,
		#ContentColumn .Content small,
		#ContentColumn .Content strong,
		#ContentColumn .Content b,
		#ContentColumn .Content i {
		  color: #ffe6ca !important;
		}

		#ContentColumn .Content a,
		#ContentColumn .Content a * {
		  color: #ffbf7a !important;
		}

		#ContentColumn .Content a:hover,
		#ContentColumn .Content a:hover * {
		  color: #fff1cf !important;
		}

		#ContentColumn .Content input,
		#ContentColumn .Content select,
		#ContentColumn .Content textarea {
		  color: #24100a !important;
		}

		/* Ticker uses lowercase id in MyAAC output. */
		#ContentColumn #newsticker .BoxContent {
		  min-height: 78px !important;
		  padding: 12px 14px !important;
		  background: linear-gradient(180deg, rgba(68,22,15,.98), rgba(31,9,8,.98)) !important;
		}

		#ContentColumn #newsticker .Row,
		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even {
		  min-height: 38px !important;
		  display: block !important;
		  box-sizing: border-box !important;
		  background: linear-gradient(180deg, rgba(138,71,55,.92), rgba(86,37,29,.94)) !important;
		  border: 1px solid rgba(255,196,115,.55) !important;
		  border-radius: 4px !important;
		  color: #fff0d6 !important;
		  opacity: 1 !important;
		  box-shadow: inset 0 1px 0 rgba(255,240,190,.22), 0 6px 16px rgba(0,0,0,.35) !important;
		}

		#ContentColumn #newsticker .Row {
		  position: relative !important;
		  padding: 0 !important;
		}

		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even {
		  padding: 10px 38px 10px 38px !important;
		}

		#ContentColumn #newsticker .NewsTickerText,
		#ContentColumn #newsticker .NewsTickerText *,
		#ContentColumn #newsticker .NewsTickerShortText,
		#ContentColumn #newsticker .NewsTickerFullText {
		  color: #fff0d6 !important;
		  opacity: 1 !important;
		  text-shadow: 0 1px 2px #000 !important;
		}

		#ContentColumn #newsticker .NewsTickerDate {
		  color: #ffd08a !important;
		  font-weight: 800 !important;
		}

		#ContentColumn #newsticker .NewsTickerIcon {
		  top: 11px !important;
		  left: 10px !important;
		  filter: brightness(1.7) saturate(1.6) drop-shadow(0 0 7px rgba(255,120,45,.65)) !important;
		}

		#ContentColumn #newsticker .NewsTickerExtend {
		  position: absolute !important;
		  top: 12px !important;
		  right: 11px !important;
		  filter: brightness(1.75) saturate(1.5) !important;
		}

		#ContentColumn #newsticker .NewsTickerText,
		#ContentColumn #newsticker .NewsTickerText *,
		#ContentColumn #newsticker .NewsTickerShortText,
		#ContentColumn #newsticker .NewsTickerFullText {
		  color: #fff8e8 !important;
		  font-weight: 700 !important;
		  text-shadow: 0 1px 2px #000, 0 0 8px rgba(255, 218, 150, .28) !important;
		}

		#ContentColumn #newsticker .NewsTickerDate {
		  color: #ffe0a6 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even {
		  background: linear-gradient(180deg, rgba(116,50,37,.96), rgba(64,25,20,.98)) !important;
		}

		#ContentColumn #News .BoxContent {
		  background: linear-gradient(180deg, rgba(52,17,13,.98), rgba(24,7,7,.98)) !important;
		}

		#ContentColumn #News .NewsHeadlineBackground {
		  background: linear-gradient(180deg, rgba(116,50,37,.96), rgba(64,25,20,.98)) !important;
		  border: 1px solid rgba(255,196,115,.55) !important;
		  border-radius: 4px !important;
		  box-shadow: inset 0 1px 0 rgba(255,240,190,.22), 0 6px 16px rgba(0,0,0,.35) !important;
		}

		#ContentColumn #News .NewsHeadlineDate {
		  color: #ffe0a6 !important;
		  font-weight: 900 !important;
		  opacity: 1 !important;
		  text-shadow: 0 1px 2px #000 !important;
		}

		#ContentColumn #News .NewsHeadlineText {
		  color: #fff8e8 !important;
		  font-weight: 800 !important;
		  opacity: 1 !important;
		  text-shadow: 0 1px 2px #000, 0 0 8px rgba(255,218,150,.28) !important;
		}

		#ContentColumn #News .BoxContent table,
		#ContentColumn #News .BoxContent tr,
		#ContentColumn #News .BoxContent td,
		#ContentColumn #News .BoxContent td * {
		  color: #fff8e8 !important;
		  font-weight: 500 !important;
		  opacity: 1 !important;
		  text-shadow: 0 1px 2px rgba(0,0,0,.72) !important;
		}

		#ContentColumn #News .BoxContent > table {
		  margin-top: 4px !important;
		  background: linear-gradient(180deg, rgba(116,50,37,.42), rgba(64,25,20,.34)) !important;
		  border: 1px solid rgba(255,196,115,.20) !important;
		  border-radius: 4px !important;
		}

		#ContentColumn #News .BoxContent td img[align="bottom"] {
		  filter: sepia(.45) saturate(1.8) brightness(2.1) drop-shadow(0 0 7px rgba(255,125,45,.55)) !important;
		}

		/* News card: light parchment style for readability. */
		#ContentColumn #News .BoxContent {
		  background: linear-gradient(180deg, #f1d391, #c9964d) !important;
		  border: 1px solid #8f5525 !important;
		  border-radius: 0 0 5px 5px !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.7), 0 8px 18px rgba(0,0,0,.35) !important;
		}

		#ContentColumn #News .NewsHeadlineBackground {
		  background: linear-gradient(180deg, #2d0b08, #090202) !important;
		  border: 1px solid rgba(255,178,79,.58) !important;
		  border-radius: 4px !important;
		  box-shadow: inset 0 1px 0 rgba(255,220,140,.2) !important;
		}

		#ContentColumn #News .NewsHeadlineDate {
		  color: #ffd08a !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 2px #000 !important;
		}

		#ContentColumn #News .NewsHeadlineText {
		  color: #ffe7b8 !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 2px #000, 0 0 8px rgba(255,178,79,.35) !important;
		}

		#ContentColumn #News .BoxContent > table {
		  margin-top: 6px !important;
		  background: linear-gradient(180deg, #f5ddb0, #d6ad70) !important;
		  border: 1px solid rgba(87,48,22,.25) !important;
		  border-radius: 4px !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.55) !important;
		}

		#ContentColumn #News .BoxContent > table td,
		#ContentColumn #News .BoxContent > table td * {
		  color: #3c1d12 !important;
		  font-weight: 600 !important;
		  text-shadow: none !important;
		  opacity: 1 !important;
		}

		#ContentColumn #News .BoxContent > table td {
		  padding: 12px 14px !important;
		  font-size: 13px !important;
		  line-height: 1.55 !important;
		}

		#ContentColumn #News .BoxContent td img[align="bottom"] {
		  filter: sepia(.45) saturate(1.7) brightness(.75) contrast(1.25) !important;
		}

		/* Match central news/ticker with right sidebar boxes. */
		#ContentColumn #newsticker,
		#ContentColumn #News {
		  background: transparent !important;
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.30), 0 8px 20px rgba(0,0,0,.55) !important;
		}

		#ContentColumn #newsticker .BorderTitleText,
		#ContentColumn #News .BorderTitleText {
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		  border-bottom: 2px solid #a86b23 !important;
		  height: 36px !important;
		}

		#ContentColumn #newsticker .BoxContent,
		#ContentColumn #News .BoxContent {
		  background-color: #d9b36d !important;
		  background-image: linear-gradient(180deg, rgba(239,212,158,.97), rgba(202,156,86,.97)) !important;
		  border: 0 !important;
		  border-radius: 0 0 3px 3px !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.55) !important;
		}

		#ContentColumn #newsticker .Title,
		#ContentColumn #News .Title {
		  color: #f7e7bd !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even,
		#ContentColumn #News .BoxContent > table {
		  background: linear-gradient(180deg, rgba(246,222,174,.95), rgba(211,164,91,.95)) !important;
		  border: 1px solid rgba(137,83,33,.42) !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.58) !important;
		}

		#ContentColumn #newsticker .NewsTickerText,
		#ContentColumn #newsticker .NewsTickerText *,
		#ContentColumn #newsticker .NewsTickerShortText,
		#ContentColumn #newsticker .NewsTickerFullText,
		#ContentColumn #News .BoxContent > table td,
		#ContentColumn #News .BoxContent > table td * {
		  color: #432816 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #newsticker .NewsTickerDate,
		#ContentColumn #News .NewsHeadlineDate {
		  color: #5e170d !important;
		  font-weight: 900 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #News .NewsHeadlineBackground {
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 100%) !important;
		  border: 1px solid #a86b23 !important;
		  box-shadow: inset 0 1px 0 rgba(255,220,140,.22) !important;
		}

		#ContentColumn #News .NewsHeadlineText {
		  color: #f7e7bd !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		/* Central content body: same yellow/beige tone as Boosted and Highscores. */
		#ContentColumn #newsticker .BoxContent,
		#ContentColumn #News .BoxContent {
		  background-color: #d9b36d !important;
		  background-image: linear-gradient(180deg, #efd49e 0%, #d9b36d 48%, #ca9c56 100%) !important;
		}

		#ContentColumn #newsticker .Row,
		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even,
		#ContentColumn #News .BoxContent > table,
		#ContentColumn #News .BoxContent > table tr,
		#ContentColumn #News .BoxContent > table td {
		  background-color: #e2bd74 !important;
		  background-image: linear-gradient(180deg, #f1d9a4 0%, #dfb76e 100%) !important;
		  border-color: rgba(137,83,33,.48) !important;
		}

		#ContentColumn #newsticker .NewsTickerText,
		#ContentColumn #newsticker .NewsTickerText *,
		#ContentColumn #News .BoxContent > table td,
		#ContentColumn #News .BoxContent > table td * {
		  color: #1f1008 !important;
		  text-shadow: none !important;
		  opacity: 1 !important;
		}

		#ContentColumn #newsticker .NewsTickerDate,
		#ContentColumn #News .NewsHeadlineDate {
		  color: #4f130b !important;
		}

		/* Cancel legacy red hue filter on central content wrappers. */
		#ContentColumn #newsticker,
		#ContentColumn #News,
		#ContentColumn #newsticker .Border_2,
		#ContentColumn #newsticker .Border_3,
		#ContentColumn #News .Border_2,
		#ContentColumn #News .Border_3,
		#ContentColumn #newsticker .BoxContent,
		#ContentColumn #News .BoxContent,
		#ContentColumn #newsticker .Row,
		#ContentColumn #newsticker .Odd,
		#ContentColumn #newsticker .Even,
		#ContentColumn #News .BoxContent > table,
		#ContentColumn #News .BoxContent > table tr,
		#ContentColumn #News .BoxContent > table td {
		  filter: none !important;
		}

		#ContentColumn #newsticker .BoxContent,
		#ContentColumn #News .BoxContent {
		  background: linear-gradient(180deg, #efd49e 0%, #d9b36d 48%, #ca9c56 100%) !important;
		}

		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even,
		#ContentColumn #News .BoxContent > table,
		#ContentColumn #News .BoxContent > table tr,
		#ContentColumn #News .BoxContent > table td {
		  background: linear-gradient(180deg, #f1d9a4 0%, #dfb76e 100%) !important;
		}

		/* Final central content color lock: neutralize inherited red filters. */
		#ContentColumn #newsticker,
		#ContentColumn #News,
		#ContentColumn #newsticker *,
		#ContentColumn #News * {
		  filter: none !important;
		}

		#ContentColumn #newsticker .BoxContent,
		#ContentColumn #News .BoxContent,
		#ContentColumn #newsticker .Row,
		#ContentColumn #newsticker .Row > .Odd,
		#ContentColumn #newsticker .Row > .Even,
		#ContentColumn #News .BoxContent > table,
		#ContentColumn #News .BoxContent > table tbody,
		#ContentColumn #News .BoxContent > table tr,
		#ContentColumn #News .BoxContent > table td {
		  background-color: #d9b36d !important;
		  background-image: linear-gradient(180deg, #f0d79f 0%, #d9b36d 52%, #c99c57 100%) !important;
		  color: #1f1008 !important;
		  text-shadow: none !important;
		  opacity: 1 !important;
		}

		/* Account login page polish. */
		#ContentColumn .eclipse-login-page,
		#ContentColumn .eclipse-login-page * {
		  filter: none !important;
		}

		#ContentColumn .eclipse-login-page {
		  color: #24100a !important;
		}

		#ContentColumn .eclipse-login-card {
		  margin: 0 0 16px !important;
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #efd49e 0%, #d9b36d 54%, #ca9c56 100%) !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.45), 0 8px 20px rgba(0,0,0,.55) !important;
		}

		#ContentColumn .eclipse-login-card .CaptionContainer,
		#ContentColumn .eclipse-login-card .CaptionInnerContainer {
		  height: 36px !important;
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		  border: 0 !important;
		}

		#ContentColumn .eclipse-login-card .CaptionContainer span,
		#ContentColumn .eclipse-login-card .CaptionInnerContainer span {
		  display: none !important;
		}

		#ContentColumn .eclipse-login-card .CaptionContainer .Text {
		  position: static !important;
		  width: auto !important;
		  height: 36px !important;
		  padding: 0 14px !important;
		  display: flex !important;
		  align-items: center !important;
		  color: #f7e7bd !important;
		  font: 800 15px Georgia, 'Times New Roman', serif !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .eclipse-login-card .Table4,
		#ContentColumn .eclipse-login-card .Table3,
		#ContentColumn .eclipse-login-card .InnerTableContainer,
		#ContentColumn .eclipse-login-card .TableContentAndRightShadow,
		#ContentColumn .eclipse-login-card .TableContentContainer,
		#ContentColumn .eclipse-login-card .TableContent,
		#ContentColumn .eclipse-login-card .TableContent tbody,
		#ContentColumn .eclipse-login-card .TableContent tr,
		#ContentColumn .eclipse-login-card .TableContent td {
		  background: transparent !important;
		  border-color: transparent !important;
		  box-shadow: none !important;
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-login-card .TableContent td * {
		  color: #2a1208 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-login-card .TableContent {
		  margin: 12px !important;
		  width: calc(100% - 24px) !important;
		  background: linear-gradient(180deg, #f1d9a4 0%, #dfb76e 100%) !important;
		  border: 1px solid rgba(137,83,33,.48) !important;
		  border-radius: 4px !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.58), 0 4px 12px rgba(0,0,0,.24) !important;
		}

		#ContentColumn .eclipse-login-card .TableShadowContainer,
		#ContentColumn .eclipse-login-card .TableShadowContainerRightTop {
		  display: none !important;
		}

		#ContentColumn .eclipse-login-card-account .LabelV {
		  width: 170px !important;
		  padding: 10px 18px !important;
		  color: #3a0905 !important;
		  font-weight: 900 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-login-card-account .LabelV span,
		#ContentColumn .eclipse-login-card-account label {
		  color: #3a0905 !important;
		  font-weight: 900 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-login-card-account input[type="text"],
		#ContentColumn .eclipse-login-card-account input[type="password"] {
		  height: 34px !important;
		  padding: 0 12px !important;
		  box-sizing: border-box !important;
		  background: #fff3d3 !important;
		  border: 1px solid #8f5a24 !important;
		  border-radius: 3px !important;
		  color: #1f1008 !important;
		  font: 700 13px Verdana, Arial, sans-serif !important;
		  box-shadow: inset 0 1px 4px rgba(64,32,12,.28), 0 1px 0 rgba(255,255,255,.55) !important;
		}

		#ContentColumn .eclipse-login-card-account input[type="text"]:focus,
		#ContentColumn .eclipse-login-card-account input[type="password"]:focus {
		  outline: none !important;
		  border-color: #5e170d !important;
		  box-shadow: 0 0 0 2px rgba(94,23,13,.22), inset 0 1px 4px rgba(64,32,12,.22) !important;
		}

		#ContentColumn .eclipse-login-card-account label {
		  color: #3c1d12 !important;
		  font-weight: 700 !important;
		}

		#ContentColumn .eclipse-login-card .BigButton,
		#ContentColumn .eclipse-login-card .MediumButtonBackground {
		  width: 156px !important;
		  height: 34px !important;
		  border: 1px solid #ffe5a2 !important;
		  border-radius: 4px !important;
		  background: linear-gradient(180deg, #ff9d26 0%, #c45608 100%) !important;
		  box-shadow: inset 0 1px 0 rgba(255,255,255,.32), 0 2px 7px rgba(0,0,0,.45) !important;
		  overflow: hidden !important;
		}

		#ContentColumn .eclipse-login-card .BigButtonOver,
		#ContentColumn .eclipse-login-card .MediumButtonOver {
		  display: none !important;
		}

		#ContentColumn .eclipse-login-card .BigButtonText,
		#ContentColumn .eclipse-login-card .MediumButtonText {
		  width: 156px !important;
		  height: 34px !important;
		  color: #fff8dc !important;
		  font: 900 12px Verdana, Arial, sans-serif !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		  text-transform: uppercase !important;
		}

		#ContentColumn .eclipse-login-divider {
		  margin: 18px 0 16px !important;
		  text-align: center !important;
		}

		#ContentColumn .eclipse-login-divider h1 {
		  margin: 0 !important;
		  color: #3a0905 !important;
		  font: 900 28px Verdana, Arial, sans-serif !important;
		  text-shadow: 0 1px 0 rgba(255,235,183,.72), 0 2px 8px rgba(58,9,5,.22) !important;
		}

		#ContentColumn #LoginCreateAccountBox {
		  padding: 4px 4px 4px 10px !important;
		  color: #3c1d12 !important;
		}

		#ContentColumn #LoginCreateAccountBox,
		#ContentColumn #LoginCreateAccountBox * {
		  color: #2a1208 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #LoginCreateAccountBox p {
		  margin: 8px 0 !important;
		  color: #2a1208 !important;
		  font-size: 15px !important;
		  line-height: 1.35 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #LoginCreateAccountBox b {
		  color: #5e170d !important;
		}

		/* Final login page typography. */
		#ContentColumn .Content .eclipse-login-page,
		#ContentColumn .Content .eclipse-login-page *:not(.CaptionContainer):not(.CaptionContainer *):not(.CaptionInnerContainer):not(.CaptionInnerContainer *):not(.BigButton):not(.BigButton *):not(.BigButtonText):not(.MediumButtonText):not(button):not(button *):not(input):not(input *) {
		  color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-login-page .CaptionContainer .Text,
		#ContentColumn .Content .eclipse-login-page .CaptionContainer .Text *,
		#ContentColumn .Content .eclipse-login-page .CaptionInnerContainer .Text,
		#ContentColumn .Content .eclipse-login-page .CaptionInnerContainer .Text * {
		  color: #fff !important;
		  font-weight: 900 !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .Content .eclipse-login-page .eclipse-login-divider h1 {
		  color: #3a0905 !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 0 rgba(255,235,183,.72), 0 2px 8px rgba(58,9,5,.22) !important;
		}

		#ContentColumn .Content .eclipse-login-page input[type="text"],
		#ContentColumn .Content .eclipse-login-page input[type="password"] {
		  color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-login-page .BigButtonText,
		#ContentColumn .Content .eclipse-login-page .MediumButtonText,
		#ContentColumn .Content .eclipse-login-page button,
		#ContentColumn .Content .eclipse-login-page button * {
		  color: #fff8dc !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}

		#ContentColumn .Content .eclipse-login-page .eclipse-login-create-button {
		  border: 0 !important;
		  background: transparent !important;
		  cursor: pointer !important;
		}

		/* Vocation cards on account and character creation. */
		#ContentColumn .eclipse-vocation-grid {
		  display: grid !important;
		  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
		  gap: 10px !important;
		  margin: 8px 0 10px !important;
		}

		#ContentColumn .eclipse-vocation-card {
		  position: relative !important;
		  display: flex !important;
		  flex-direction: column !important;
		  min-height: 230px !important;
		  padding: 8px !important;
		  box-sizing: border-box !important;
		  cursor: pointer !important;
		  border: 2px solid rgba(108, 56, 21, .58) !important;
		  border-radius: 5px !important;
		  background: linear-gradient(180deg, #f0d79f 0%, #d6ad70 100%) !important;
		  color: #24100a !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.58), 0 3px 9px rgba(0,0,0,.28) !important;
		  overflow: hidden !important;
		  transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease !important;
		}

		#ContentColumn .eclipse-vocation-card:hover,
		#ContentColumn .eclipse-vocation-card:has(input:checked) {
		  border-color: #5e170d !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.70), 0 0 0 2px rgba(94,23,13,.24), 0 5px 14px rgba(0,0,0,.34) !important;
		  transform: translateY(-1px) !important;
		}

		#ContentColumn .eclipse-vocation-card input[type="radio"] {
		  position: absolute !important;
		  top: 9px !important;
		  left: 9px !important;
		  z-index: 2 !important;
		}

		#ContentColumn .eclipse-vocation-card img {
		  width: 100% !important;
		  height: 118px !important;
		  object-fit: contain !important;
		  display: block !important;
		  margin: 2px 0 8px !important;
		  padding: 4px !important;
		  box-sizing: border-box !important;
		  background: radial-gradient(circle at 50% 40%, rgba(35,77,99,.86), rgba(7,18,27,.95)) !important;
		  border: 1px solid #a86b23 !important;
		  border-radius: 4px !important;
		}

		#ContentColumn .eclipse-vocation-name {
		  display: block !important;
		  margin: 0 0 3px !important;
		  color: #3a0905 !important;
		  font: 900 16px Georgia, 'Times New Roman', serif !important;
		  text-shadow: 0 1px 0 rgba(255,235,183,.70) !important;
		}

		#ContentColumn .eclipse-vocation-role {
		  display: block !important;
		  margin-bottom: 7px !important;
		  color: #5e170d !important;
		  font: 900 10px Verdana, Arial, sans-serif !important;
		  text-transform: uppercase !important;
		  letter-spacing: 0 !important;
		}

		#ContentColumn .eclipse-vocation-description {
		  display: block !important;
		  color: #2a1208 !important;
		  font: 700 11px/1.35 Verdana, Arial, sans-serif !important;
		  text-shadow: none !important;
		}

		/* Creation forms readable text lock. Overrides the legacy light global content color. */
		#ContentColumn .Content .eclipse-create-account-page,
		#ContentColumn .Content .eclipse-create-character-page,
		#ContentColumn .Content .eclipse-create-account-page *,
		#ContentColumn .Content .eclipse-create-character-page * {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page .LabelV,
		#ContentColumn .Content .eclipse-create-account-page .LabelV span,
		#ContentColumn .Content .eclipse-create-character-page .LabelV,
		#ContentColumn .Content .eclipse-create-character-page .LabelV span,
		#ContentColumn .Content .eclipse-create-account-page .OptionContainer label,
		#ContentColumn .Content .eclipse-create-character-page .OptionContainer label,
		#ContentColumn .Content .eclipse-create-character-page b {
		  color: #3a0905 !important;
		  font-weight: 900 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page a,
		#ContentColumn .Content .eclipse-create-character-page a {
		  color: #5e170d !important;
		  font-weight: 800 !important;
		}

		#ContentColumn .Content .eclipse-create-account-page .FormFieldError,
		#ContentColumn .Content .eclipse-create-character-page .FormFieldError,
		#ContentColumn .Content .eclipse-create-account-page .red,
		#ContentColumn .Content .eclipse-create-character-page .red {
		  color: #8b1208 !important;
		}

		#ContentColumn .Content .eclipse-create-account-page .eclipse-vocation-card,
		#ContentColumn .Content .eclipse-create-character-page .eclipse-vocation-card,
		#ContentColumn .Content .eclipse-create-account-page .eclipse-vocation-card *,
		#ContentColumn .Content .eclipse-create-character-page .eclipse-vocation-card * {
		  color: #2a1208 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page .eclipse-vocation-card .eclipse-vocation-name,
		#ContentColumn .Content .eclipse-create-character-page .eclipse-vocation-card .eclipse-vocation-name {
		  color: #3a0905 !important;
		  text-shadow: 0 1px 0 rgba(255,235,183,.70) !important;
		}

		#ContentColumn .Content .eclipse-create-account-page .eclipse-vocation-card .eclipse-vocation-role,
		#ContentColumn .Content .eclipse-create-character-page .eclipse-vocation-card .eclipse-vocation-role {
		  color: #5e170d !important;
		}

		#ContentColumn .Content .eclipse-create-account-page .eclipse-vocation-card .eclipse-vocation-description,
		#ContentColumn .Content .eclipse-create-character-page .eclipse-vocation-card .eclipse-vocation-description {
		  color: #2a1208 !important;
		  font-weight: 800 !important;
		}

		/* Create account contrast lock for the legacy Arise table markup. */
		#ContentColumn .Content .eclipse-create-account-page form#createaccount,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .InnerTableContainer,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .TableContent,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .TableContent tr,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .TableContent td {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .LabelV,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .LabelV *,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .OptionContainer,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .OptionContainer *,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount td > span,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount td > span *,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount td > div,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount td > div * {
		  color: #2a1208 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .LabelV,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .LabelV span {
		  color: #3a0905 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .FormFieldError,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #account_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #email_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #password_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #password_confirm_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #character_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #sex_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #vocation_error,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount #town_error {
		  color: #5e170d !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .eclipse-vocation-card,
		#ContentColumn .Content .eclipse-create-account-page form#createaccount .eclipse-vocation-card * {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .eclipse-vocation-card .eclipse-vocation-name {
		  color: #3a0905 !important;
		  text-shadow: 0 1px 0 rgba(255, 235, 183, .70) !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .eclipse-vocation-card .eclipse-vocation-role {
		  color: #5e170d !important;
		}

		#ContentColumn .Content .eclipse-create-account-page form#createaccount .eclipse-vocation-card .eclipse-vocation-description {
		  color: #2a1208 !important;
		  font-weight: 800 !important;
		}

		/* Global central content contrast for beige/yellow Arise tables. */
		#ContentColumn .Content .TableContentContainer,
		#ContentColumn .Content .InnerTableContainer,
		#ContentColumn .Content .TableContent,
		#ContentColumn .Content .TableContent tr,
		#ContentColumn .Content .TableContent td,
		#ContentColumn .Content .TableContent div,
		#ContentColumn .Content .TableContent p,
		#ContentColumn .Content .TableContent span,
		#ContentColumn .Content .TableContent label,
		#ContentColumn .Content .TableContent small,
		#ContentColumn .Content .TableContent strong,
		#ContentColumn .Content .TableContent b,
		#ContentColumn .Content .Odd,
		#ContentColumn .Content .Even,
		#ContentColumn .Content .LabelV,
		#ContentColumn .Content .LabelV *,
		#ContentColumn .Content .LabelH,
		#ContentColumn .Content .LabelH * {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .LabelV,
		#ContentColumn .Content .LabelV span,
		#ContentColumn .Content .LabelH,
		#ContentColumn .Content .LabelH span {
		  color: #3a0905 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .TableContent a,
		#ContentColumn .Content .InnerTableContainer a {
		  color: #5e170d !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .FormFieldError,
		#ContentColumn .Content .red,
		#ContentColumn .Content span.red,
		#ContentColumn .Content .TableContent .red {
		  color: #8b1208 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content input,
		#ContentColumn .Content select,
		#ContentColumn .Content textarea {
		  color: #1f1008 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .CaptionContainer,
		#ContentColumn .Content .CaptionContainer *,
		#ContentColumn .Content .CaptionInnerContainer,
		#ContentColumn .Content .CaptionInnerContainer *,
		#ContentColumn .Content .BorderTitleText,
		#ContentColumn .Content .BorderTitleText *,
		#ContentColumn .Content .Title,
		#ContentColumn .Content .Title * {
		  color: #f7e7bd !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .Content .BigButtonText,
		#ContentColumn .Content .MediumButtonText,
		#ContentColumn .Content button,
		#ContentColumn .Content button *,
		#ContentColumn .Content input[type="submit"],
		#ContentColumn .Content input[type="button"] {
		  color: #fff8dc !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}

		/* Create account page polish. */
		#ContentColumn .eclipse-create-account-page {
		  color: #24100a !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount > .TableContainer {
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #efd49e 0%, #d9b36d 58%, #c99a51 100%) !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.48), 0 10px 26px rgba(0,0,0,.55) !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount > .TableContainer > table {
		  background: transparent !important;
		}

		#ContentColumn .eclipse-create-account-page .CaptionContainer,
		#ContentColumn .eclipse-create-account-page .CaptionInnerContainer {
		  height: 38px !important;
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		  border: 0 !important;
		}

		#ContentColumn .eclipse-create-account-page .CaptionContainer span,
		#ContentColumn .eclipse-create-account-page .CaptionInnerContainer span {
		  display: none !important;
		}

		#ContentColumn .eclipse-create-account-page .CaptionContainer .Text {
		  position: static !important;
		  width: auto !important;
		  height: 38px !important;
		  padding: 0 16px !important;
		  display: flex !important;
		  align-items: center !important;
		  color: #f7e7bd !important;
		  font: 900 17px Georgia, 'Times New Roman', serif !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .InnerTableContainer {
		  padding: 12px !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .TableContentContainer {
		  padding: 0 !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .TableContentAndRightShadow,
		#ContentColumn .eclipse-create-account-page form#createaccount .TableContentContainer,
		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent,
		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent tbody,
		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent tr,
		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent td {
		  background-image: none !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent {
		  width: 100% !important;
		  margin: 0 0 14px !important;
		  border: 1px solid rgba(137,83,33,.54) !important;
		  border-radius: 5px !important;
		  background: linear-gradient(180deg, #f2dca9 0%, #dfbd7b 100%) !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.64), 0 5px 14px rgba(0,0,0,.24) !important;
		  overflow: hidden !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent td {
		  padding: 6px 12px !important;
		  vertical-align: middle !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .TableContent tr:has(.FormFieldError:empty) td {
		  height: 0 !important;
		  padding-top: 0 !important;
		  padding-bottom: 0 !important;
		  line-height: 0 !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .LabelV {
		  width: 185px !important;
		  color: #3a0905 !important;
		  font: 900 13px Verdana, Arial, sans-serif !important;
		  white-space: normal !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount input[type="text"],
		#ContentColumn .eclipse-create-account-page form#createaccount input:not([type]),
		#ContentColumn .eclipse-create-account-page form#createaccount #character_name,
		#ContentColumn .eclipse-create-account-page form#createaccount input[type="password"],
		#ContentColumn .eclipse-create-account-page form#createaccount select {
		  width: min(100%, 380px) !important;
		  height: 34px !important;
		  padding: 0 10px !important;
		  box-sizing: border-box !important;
		  border: 1px solid #8f5a24 !important;
		  border-radius: 3px !important;
		  background: #fff3d3 !important;
		  color: #1f1008 !important;
		  font: 700 13px Verdana, Arial, sans-serif !important;
		  box-shadow: inset 0 1px 4px rgba(64,32,12,.28), 0 1px 0 rgba(255,255,255,.55) !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount input[type="text"]:focus,
		#ContentColumn .eclipse-create-account-page form#createaccount input:not([type]):focus,
		#ContentColumn .eclipse-create-account-page form#createaccount #character_name:focus,
		#ContentColumn .eclipse-create-account-page form#createaccount input[type="password"]:focus,
		#ContentColumn .eclipse-create-account-page form#createaccount select:focus {
		  outline: none !important;
		  border-color: #5e170d !important;
		  box-shadow: 0 0 0 2px rgba(94,23,13,.22), inset 0 1px 4px rgba(64,32,12,.22) !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .OptionContainer {
		  display: inline-flex !important;
		  align-items: center !important;
		  gap: 6px !important;
		  min-height: 30px !important;
		  margin: 0 18px 4px 0 !important;
		  color: #2a1208 !important;
		  font-weight: 800 !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount input[type="radio"],
		#ContentColumn .eclipse-create-account-page form#createaccount input[type="checkbox"] {
		  accent-color: #6f1f10 !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .FormFieldError {
		  display: inline-block !important;
		  min-height: 14px !important;
		  color: #6f1f10 !important;
		  font-size: 11px !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .FormFieldError:empty {
		  display: none !important;
		  min-height: 0 !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .eclipse-vocation-grid {
		  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
		  gap: 12px !important;
		  padding: 2px 0 !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .eclipse-vocation-card {
		  min-height: 245px !important;
		  padding: 9px !important;
		  border-color: rgba(108,56,21,.65) !important;
		  background: linear-gradient(180deg, #f4dda8 0%, #dbb46f 100%) !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .eclipse-vocation-card img {
		  height: 116px !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .BigButton {
		  width: 168px !important;
		  height: 36px !important;
		  border: 1px solid #ffe5a2 !important;
		  border-radius: 4px !important;
		  background: linear-gradient(180deg, #ff9d26 0%, #c45608 100%) !important;
		  box-shadow: inset 0 1px 0 rgba(255,255,255,.32), 0 3px 10px rgba(0,0,0,.45) !important;
		  overflow: hidden !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .BigButtonOver {
		  display: none !important;
		}

		#ContentColumn .eclipse-create-account-page form#createaccount .BigButtonText {
		  width: 168px !important;
		  height: 36px !important;
		  color: #fff8dc !important;
		  font: 900 12px Verdana, Arial, sans-serif !important;
		  text-transform: uppercase !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}

		/* Account management page polish. */
		#ContentColumn .eclipse-account-manage-page,
		#ContentColumn .eclipse-account-manage-page .TableContent,
		#ContentColumn .eclipse-account-manage-page .TableContent td,
		#ContentColumn .eclipse-account-manage-page .TableContent div,
		#ContentColumn .eclipse-account-manage-page .TableContent span,
		#ContentColumn .eclipse-account-manage-page .TableContent small,
		#ContentColumn .eclipse-account-manage-page .TableContent b,
		#ContentColumn .eclipse-account-manage-page .TableContent strong,
		#ContentColumn .eclipse-account-manage-page .TableContent label {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-account-manage-page .TableContainer {
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #efd49e 0%, #d9b36d 58%, #c99a51 100%) !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.48), 0 8px 22px rgba(0,0,0,.50) !important;
		}

		#ContentColumn .eclipse-account-manage-page .TableContainer > table {
		  background: transparent !important;
		}

		#ContentColumn .eclipse-account-manage-page .CaptionContainer,
		#ContentColumn .eclipse-account-manage-page .CaptionInnerContainer {
		  height: 36px !important;
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		  border: 0 !important;
		}

		#ContentColumn .eclipse-account-manage-page .CaptionContainer span,
		#ContentColumn .eclipse-account-manage-page .CaptionInnerContainer span {
		  display: none !important;
		}

		#ContentColumn .eclipse-account-manage-page .CaptionContainer .Text {
		  position: static !important;
		  width: auto !important;
		  height: 36px !important;
		  padding: 0 14px !important;
		  display: flex !important;
		  align-items: center !important;
		  color: #f7e7bd !important;
		  font: 900 16px Georgia, 'Times New Roman', serif !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .eclipse-account-manage-page .InnerTableContainer {
		  padding: 10px 12px !important;
		}

		#ContentColumn .eclipse-account-manage-page .TableContentAndRightShadow,
		#ContentColumn .eclipse-account-manage-page .TableContentContainer,
		#ContentColumn .eclipse-account-manage-page .TableContent,
		#ContentColumn .eclipse-account-manage-page .TableContent tbody,
		#ContentColumn .eclipse-account-manage-page .TableContent tr,
		#ContentColumn .eclipse-account-manage-page .TableContent td {
		  background-image: none !important;
		}

		#ContentColumn .eclipse-account-manage-page .TableContent {
		  width: 100% !important;
		  border: 1px solid rgba(137,83,33,.54) !important;
		  border-radius: 5px !important;
		  background: linear-gradient(180deg, #f2dca9 0%, #dfbd7b 100%) !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.64), 0 4px 12px rgba(0,0,0,.22) !important;
		  overflow: hidden !important;
		}

		#ContentColumn .eclipse-account-manage-page .TableContent td {
		  padding: 7px 10px !important;
		  vertical-align: middle !important;
		}

		#ContentColumn .eclipse-account-manage-page .LabelV,
		#ContentColumn .eclipse-account-manage-page .LabelH,
		#ContentColumn .eclipse-account-manage-page .LabelH td {
		  color: #3a0905 !important;
		  font: 900 12px Verdana, Arial, sans-serif !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-account-manage-page a {
		  color: #5e170d !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-account-manage-page .BigBoldText,
		#ContentColumn .eclipse-account-manage-page .BigBoldText * {
		  color: #3a0905 !important;
		  text-shadow: 0 1px 0 rgba(255,235,183,.70) !important;
		}

		#ContentColumn .eclipse-account-manage-page .premiumbenefits {
		  gap: 10px !important;
		  color: #2a1208 !important;
		  font-weight: 800 !important;
		}

		#ContentColumn .eclipse-account-manage-page .premiumbenefits > div {
		  min-height: 58px !important;
		  padding: 8px !important;
		  border: 1px solid rgba(137,83,33,.36) !important;
		  border-radius: 4px !important;
		  background: rgba(255,243,211,.34) !important;
		}

		#ContentColumn .eclipse-account-manage-page .BigButtonText,
		#ContentColumn .eclipse-account-manage-page .MediumButtonText,
		#ContentColumn .eclipse-account-manage-page input[type="submit"],
		#ContentColumn .eclipse-account-manage-page input[type="button"] {
		  color: #fff8dc !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}

		/* Account management contrast fixes. */
		#ContentColumn .Content .eclipse-account-manage-page,
		#ContentColumn .Content .eclipse-account-manage-page *:not(.BigButton):not(.BigButton *):not(.BigButtonText):not(.MediumButtonText):not(button):not(button *):not(input[type="submit"]):not(input[type="button"]) {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-account-manage-page .CaptionContainer,
		#ContentColumn .Content .eclipse-account-manage-page .CaptionContainer *,
		#ContentColumn .Content .eclipse-account-manage-page .CaptionInnerContainer,
		#ContentColumn .Content .eclipse-account-manage-page .CaptionInnerContainer *,
		#ContentColumn .Content .eclipse-account-manage-page .BorderTitleText,
		#ContentColumn .Content .eclipse-account-manage-page .BorderTitleText *,
		#ContentColumn .Content .eclipse-account-manage-page .Title,
		#ContentColumn .Content .eclipse-account-manage-page .Title * {
		  color: #f7e7bd !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .Content .eclipse-account-manage-page .BigButtonText,
		#ContentColumn .Content .eclipse-account-manage-page .MediumButtonText,
		#ContentColumn .Content .eclipse-account-manage-page button,
		#ContentColumn .Content .eclipse-account-manage-page button *,
		#ContentColumn .Content .eclipse-account-manage-page input[type="submit"],
		#ContentColumn .Content .eclipse-account-manage-page input[type="button"] {
		  color: #fff8dc !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}

		#ContentColumn .Content .eclipse-account-manage-page .TableContent,
		#ContentColumn .Content .eclipse-account-manage-page .InnerTableContainer,
		#ContentColumn .Content .eclipse-account-manage-page .TableContentContainer {
		  background-color: #e8c57f !important;
		}

		/* Generic success/confirmation page. */
		#ContentColumn .eclipse-success-page,
		#ContentColumn .eclipse-success-page *:not(.BigButton):not(.BigButton *):not(.BigButtonText):not(.MediumButtonText) {
		  color: #24100a !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-success-panel {
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #f3dc9f 0%, #dfba73 68%, #c99547 100%) !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.55), 0 8px 22px rgba(0,0,0,.46) !important;
		}

		#ContentColumn .eclipse-success-title {
		  padding: 11px 14px !important;
		  color: #f7e7bd !important;
		  font: 900 16px Georgia, 'Times New Roman', serif !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		}

		#ContentColumn .eclipse-success-body {
		  min-height: 92px !important;
		  padding: 22px 24px !important;
		  border-top: 1px solid rgba(255,244,198,.50) !important;
		  font: 700 13px/1.65 Verdana, Arial, sans-serif !important;
		  background: rgba(255,242,205,.34) !important;
		}

		#ContentColumn .eclipse-success-body b,
		#ContentColumn .eclipse-success-body strong {
		  color: #5e170d !important;
		}

		#ContentColumn .eclipse-success-actions {
		  display: flex !important;
		  justify-content: center !important;
		  margin-top: 16px !important;
		}

		/* Final account management typography. */
		#ContentColumn .Content .eclipse-account-manage-page,
		#ContentColumn .Content .eclipse-account-manage-page *:not(.CaptionContainer):not(.CaptionContainer *):not(.CaptionInnerContainer):not(.CaptionInnerContainer *):not(.BorderTitleText):not(.BorderTitleText *):not(.Title):not(.Title *):not(.BigButton):not(.BigButton *):not(.BigButtonText):not(.MediumButtonText):not(button):not(button *):not(input[type="submit"]):not(input[type="button"]) {
		  color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-account-manage-page .CaptionContainer .Text,
		#ContentColumn .Content .eclipse-account-manage-page .CaptionContainer .Text *,
		#ContentColumn .Content .eclipse-account-manage-page .CaptionInnerContainer .Text,
		#ContentColumn .Content .eclipse-account-manage-page .CaptionInnerContainer .Text *,
		#ContentColumn .Content .eclipse-account-manage-page .BorderTitleText,
		#ContentColumn .Content .eclipse-account-manage-page .BorderTitleText *,
		#ContentColumn .Content .eclipse-account-manage-page .Title,
		#ContentColumn .Content .eclipse-account-manage-page .Title * {
		  color: #fff !important;
		  font-weight: 900 !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		/* Final login contrast pass, after generic content overrides. */
		#ContentColumn .eclipse-login-page .eclipse-login-card-account .LabelV,
		#ContentColumn .eclipse-login-page .eclipse-login-card-account .LabelV span,
		#ContentColumn .eclipse-login-page .eclipse-login-card-account label,
		#ContentColumn .eclipse-login-page #LoginCreateAccountBox,
		#ContentColumn .eclipse-login-page #LoginCreateAccountBox *,
		#ContentColumn .Content .eclipse-login-page .eclipse-login-card-account .LabelV,
		#ContentColumn .Content .eclipse-login-page .eclipse-login-card-account .LabelV span,
		#ContentColumn .Content .eclipse-login-page .eclipse-login-card-account label,
		#ContentColumn .Content .eclipse-login-page #LoginCreateAccountBox,
		#ContentColumn .Content .eclipse-login-page #LoginCreateAccountBox * {
		  color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-login-page .eclipse-login-card .CaptionContainer .Text,
		#ContentColumn .eclipse-login-page .eclipse-login-card .CaptionContainer .Text *,
		#ContentColumn .Content .eclipse-login-page .eclipse-login-card .CaptionContainer .Text,
		#ContentColumn .Content .eclipse-login-page .eclipse-login-card .CaptionContainer .Text * {
		  color: #fff !important;
		  font-weight: 900 !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		/* Login inputs and password visibility. */
		#ContentColumn .eclipse-login-page .eclipse-login-input,
		#ContentColumn .eclipse-login-page .eclipse-login-input:focus,
		#ContentColumn .eclipse-login-page .eclipse-login-input:-webkit-autofill,
		#ContentColumn .eclipse-login-page .eclipse-login-input:-webkit-autofill:hover,
		#ContentColumn .eclipse-login-page .eclipse-login-input:-webkit-autofill:focus {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  caret-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-login-page .eclipse-password-field {
		  position: relative !important;
		  display: flex !important;
		  align-items: center !important;
		  width: 100% !important;
		}

		#ContentColumn .eclipse-login-page .eclipse-password-field .eclipse-password-input {
		  padding-right: 42px !important;
		}

		#ContentColumn .eclipse-login-page .eclipse-password-toggle {
		  position: absolute !important;
		  right: 5px !important;
		  top: 50% !important;
		  transform: translateY(-50%) !important;
		  width: 30px !important;
		  height: 26px !important;
		  border: 1px solid #8f5a24 !important;
		  border-radius: 3px !important;
		  background: linear-gradient(180deg, #f6d98f 0%, #c99547 100%) !important;
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font-size: 15px !important;
		  font-weight: 900 !important;
		  line-height: 22px !important;
		  text-align: center !important;
		  text-shadow: none !important;
		  box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 1px 3px rgba(0,0,0,.25) !important;
		  cursor: pointer !important;
		}

		#ContentColumn .eclipse-login-page .eclipse-password-toggle:hover {
		  background: linear-gradient(180deg, #ffe4a6 0%, #d6a253 100%) !important;
		}

		/* Account manage beige panels must use dark text. */
		#ContentColumn .eclipse-account-manage-page .eclipse-account-welcome,
		#ContentColumn .eclipse-account-manage-page .eclipse-account-welcome *,
		#ContentColumn .eclipse-account-manage-page .TableContent,
		#ContentColumn .eclipse-account-manage-page .TableContent *:not(.BigButtonText):not(.MediumButtonText):not(button):not(button *):not(input[type="submit"]):not(input[type="button"]),
		#ContentColumn .eclipse-account-manage-page .InnerTableContainer,
		#ContentColumn .eclipse-account-manage-page .InnerTableContainer *:not(.CaptionContainer):not(.CaptionContainer *):not(.CaptionInnerContainer):not(.CaptionInnerContainer *):not(.BigButtonText):not(.MediumButtonText):not(button):not(button *):not(input[type="submit"]):not(input[type="button"]) {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .eclipse-account-manage-page .CaptionContainer .Text,
		#ContentColumn .eclipse-account-manage-page .CaptionContainer .Text *,
		#ContentColumn .eclipse-account-manage-page .CaptionInnerContainer .Text,
		#ContentColumn .eclipse-account-manage-page .CaptionInnerContainer .Text * {
		  color: #fff !important;
		  -webkit-text-fill-color: #fff !important;
		  font-weight: 900 !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		/* Downloads page: keep the beige panels readable despite generic dark-content rules. */
		#ContentColumn .Content .eclipse-download-page,
		#ContentColumn .Content .eclipse-download-page *:not(.download-button):not(.download-button *):not(.download-step) {
		  color: #1f0804 !important;
		  -webkit-text-fill-color: #1f0804 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-download-page .download-title,
		#ContentColumn .Content .eclipse-download-page .download-title *,
		#ContentColumn .Content .eclipse-download-page .download-card strong,
		#ContentColumn .Content .eclipse-download-page .download-note strong {
		  color: #4d1209 !important;
		  -webkit-text-fill-color: #4d1209 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-download-page .download-step {
		  color: #fff0b8 !important;
		  -webkit-text-fill-color: #fff0b8 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-download-page .download-button,
		#ContentColumn .Content .eclipse-download-page .download-button * {
		  color: #fff7d4 !important;
		  -webkit-text-fill-color: #fff7d4 !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 1px #4c1600 !important;
		}

		#ContentColumn .Content .eclipse-download-page .download-button.secondary,
		#ContentColumn .Content .eclipse-download-page .download-button.secondary * {
		  color: #fff1bd !important;
		  -webkit-text-fill-color: #fff1bd !important;
		}

		/* Rules page: dark text on beige panels, light only in section counters. */
		#ContentColumn .Content .eclipse-rules-page,
		#ContentColumn .Content .eclipse-rules-page *:not(.rules-index) {
		  color: #1f0804 !important;
		  -webkit-text-fill-color: #1f0804 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-rules-page .rules-title,
		#ContentColumn .Content .eclipse-rules-page .rules-title *,
		#ContentColumn .Content .eclipse-rules-page .rules-card h3,
		#ContentColumn .Content .eclipse-rules-page .rules-card h3 *:not(.rules-index),
		#ContentColumn .Content .eclipse-rules-page .rules-note strong {
		  color: #4d1209 !important;
		  -webkit-text-fill-color: #4d1209 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-rules-page .rules-index {
		  color: #fff0b8 !important;
		  -webkit-text-fill-color: #fff0b8 !important;
		  text-shadow: none !important;
		}

		/* Highscores: cleaner ranking table without outfit thumbnails. */
		#ContentColumn .Content .eclipse-highscores-page,
		#ContentColumn .Content .eclipse-highscores-page *:not(.CaptionContainer):not(.CaptionContainer *):not(.CaptionInnerContainer):not(.CaptionInnerContainer *) {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .TableContainer {
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #efd49e 0%, #d9b36d 58%, #c99a51 100%) !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.48), 0 8px 22px rgba(0,0,0,.50) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .CaptionContainer,
		#ContentColumn .Content .eclipse-highscores-page .CaptionInnerContainer {
		  height: 36px !important;
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		  border: 0 !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .CaptionContainer span,
		#ContentColumn .Content .eclipse-highscores-page .CaptionInnerContainer span {
		  display: none !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .CaptionContainer .Text,
		#ContentColumn .Content .eclipse-highscores-page .CaptionContainer .Text *,
		#ContentColumn .Content .eclipse-highscores-page .CaptionInnerContainer .Text,
		#ContentColumn .Content .eclipse-highscores-page .CaptionInnerContainer .Text * {
		  color: #fff !important;
		  -webkit-text-fill-color: #fff !important;
		  font: 900 16px Georgia, "Times New Roman", serif !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .RightArea {
		  float: right !important;
		  font: 800 11px Verdana, Arial, sans-serif !important;
		  padding-top: 2px !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .InnerTableContainer,
		#ContentColumn .Content .eclipse-highscores-page .TableContentContainer,
		#ContentColumn .Content .eclipse-highscores-page .TableContent,
		#ContentColumn .Content .eclipse-highscores-page .TableContent tbody,
		#ContentColumn .Content .eclipse-highscores-page .TableContent tr,
		#ContentColumn .Content .eclipse-highscores-page .TableContent td {
		  background-image: none !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .TableContent {
		  width: 100% !important;
		  border: 1px solid rgba(137,83,33,.54) !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #f4dfad 0%, #dfbd7b 100%) !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.64), 0 4px 12px rgba(0,0,0,.22) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .TableContent td {
		  padding: 8px 10px !important;
		  vertical-align: middle !important;
		  border-color: rgba(125,73,26,.28) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .TableContent tr:not(.LabelH):nth-child(odd) td {
		  background: rgba(255,245,216,.42) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .TableContent tr:not(.LabelH):nth-child(even) td {
		  background: rgba(224,184,113,.34) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .LabelH,
		#ContentColumn .Content .eclipse-highscores-page .LabelH td {
		  background: linear-gradient(180deg, #4b1008 0%, #1a0503 100%) !important;
		  color: #fff8dc !important;
		  -webkit-text-fill-color: #fff8dc !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 1px #000 !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .PageNavigation,
		#ContentColumn .Content .eclipse-highscores-page .PageNavigation * {
		  color: #2a0905 !important;
		  -webkit-text-fill-color: #2a0905 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .CurrentPageLink {
		  display: inline-block !important;
		  min-width: 18px !important;
		  padding: 2px 5px !important;
		  border-radius: 3px !important;
		  background: #4b1008 !important;
		  color: #fff8dc !important;
		  -webkit-text-fill-color: #fff8dc !important;
		  text-align: center !important;
		}

		#ContentColumn .Content .eclipse-highscores-page a,
		#ContentColumn .Content .eclipse-highscores-page a * {
		  color: #4d1209 !important;
		  -webkit-text-fill-color: #4d1209 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-highscores-page select {
		  min-width: 220px !important;
		  height: 32px !important;
		  padding: 0 10px !important;
		  border: 1px solid #8f5a24 !important;
		  border-radius: 3px !important;
		  background: #fff3d3 !important;
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font: 800 13px Verdana, Arial, sans-serif !important;
		  box-shadow: inset 0 1px 4px rgba(64,32,12,.24), 0 1px 0 rgba(255,255,255,.55) !important;
		}

		#ContentColumn .Content .eclipse-highscores-page .highscores-note {
		  margin: 12px 0 !important;
		  padding: 10px 12px !important;
		  border: 1px solid rgba(137,83,33,.45) !important;
		  border-radius: 4px !important;
		  background: linear-gradient(180deg, #f5dfad 0%, #dfbd7b 100%) !important;
		  color: #2a0905 !important;
		  -webkit-text-fill-color: #2a0905 !important;
		  font-weight: 800 !important;
		}

		#ContentColumn #News .BoxContent .eclipse-highscores-page,
		#ContentColumn #News .BoxContent .eclipse-highscores-page td,
		#ContentColumn #News .BoxContent .eclipse-highscores-page div,
		#ContentColumn #News .BoxContent .eclipse-highscores-page span,
		#ContentColumn #News .BoxContent .eclipse-highscores-page b,
		#ContentColumn #News .BoxContent .eclipse-highscores-page strong,
		#ContentColumn #News .BoxContent .eclipse-highscores-page label,
		#ContentColumn #News .BoxContent .eclipse-highscores-page small,
		#ContentColumn #News .BoxContent .eclipse-highscores-page i,
		#ContentColumn #News .BoxContent .eclipse-highscores-page select,
		#ContentColumn #News .BoxContent .eclipse-highscores-page option {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #News .BoxContent .eclipse-highscores-page .CaptionContainer .Text,
		#ContentColumn #News .BoxContent .eclipse-highscores-page .CaptionContainer .Text *,
		#ContentColumn #News .BoxContent .eclipse-highscores-page .CaptionInnerContainer .Text,
		#ContentColumn #News .BoxContent .eclipse-highscores-page .CaptionInnerContainer .Text *,
		#ContentColumn #News .BoxContent .eclipse-highscores-page .LabelH,
		#ContentColumn #News .BoxContent .eclipse-highscores-page .LabelH td,
		#ContentColumn #News .BoxContent .eclipse-highscores-page .LabelH * {
		  color: #fff8dc !important;
		  -webkit-text-fill-color: #fff8dc !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 1px #000 !important;
		}

		/* Characters search/results: beige panels with dark readable text. */
		#ContentColumn .Content .eclipse-characters-page,
		#ContentColumn .Content .eclipse-characters-page *:not(input[type="submit"]):not(input[type="submit"] *):not(.BigButtonText):not(.MediumButtonText) {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn .Content .eclipse-characters-page .TableContainer {
		  border: 2px solid #a86b23 !important;
		  border-radius: 5px !important;
		  overflow: hidden !important;
		  background: linear-gradient(180deg, #efd49e 0%, #d9b36d 58%, #c99a51 100%) !important;
		  box-shadow: inset 0 0 0 1px rgba(255,244,198,.48), 0 8px 22px rgba(0,0,0,.50) !important;
		}

		#ContentColumn .Content .eclipse-characters-page .CaptionContainer,
		#ContentColumn .Content .eclipse-characters-page .CaptionInnerContainer {
		  height: 36px !important;
		  background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
		  border: 0 !important;
		}

		#ContentColumn .Content .eclipse-characters-page .CaptionContainer span,
		#ContentColumn .Content .eclipse-characters-page .CaptionInnerContainer span {
		  display: none !important;
		}

		#ContentColumn .Content .eclipse-characters-page .CaptionContainer .Text,
		#ContentColumn .Content .eclipse-characters-page .CaptionContainer .Text *,
		#ContentColumn .Content .eclipse-characters-page .CaptionInnerContainer .Text,
		#ContentColumn .Content .eclipse-characters-page .CaptionInnerContainer .Text * {
		  color: #fff !important;
		  -webkit-text-fill-color: #fff !important;
		  font: 900 16px Georgia, "Times New Roman", serif !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn .Content .eclipse-characters-page .InnerTableContainer,
		#ContentColumn .Content .eclipse-characters-page .TableContentContainer,
		#ContentColumn .Content .eclipse-characters-page .TableContent,
		#ContentColumn .Content .eclipse-characters-page .TableContent tbody,
		#ContentColumn .Content .eclipse-characters-page .TableContent tr,
		#ContentColumn .Content .eclipse-characters-page .TableContent td {
		  background-image: none !important;
		}

		#ContentColumn .Content .eclipse-characters-page .TableContent {
		  width: 100% !important;
		  border: 1px solid rgba(137,83,33,.54) !important;
		  border-radius: 5px !important;
		  background: linear-gradient(180deg, #f2dca9 0%, #dfbd7b 100%) !important;
		  box-shadow: inset 0 1px 0 rgba(255,246,204,.64), 0 4px 12px rgba(0,0,0,.22) !important;
		}

		#ContentColumn .Content .eclipse-characters-page .TableContent td {
		  padding: 7px 10px !important;
		  vertical-align: middle !important;
		}

		#ContentColumn .Content .eclipse-characters-page tr[bgcolor] td {
		  background: rgba(255,243,211,.34) !important;
		}

		#ContentColumn .Content .eclipse-characters-page tr[bgcolor] + tr[bgcolor] td {
		  border-top: 1px solid rgba(137,83,33,.22) !important;
		}

		#ContentColumn .Content .eclipse-characters-page .LabelV,
		#ContentColumn .Content .eclipse-characters-page .LabelV150,
		#ContentColumn .Content .eclipse-characters-page .LabelV175 {
		  color: #3a0905 !important;
		  -webkit-text-fill-color: #3a0905 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-characters-page input:not([type]),
		#ContentColumn .Content .eclipse-characters-page input[type="text"] {
		  height: 32px !important;
		  padding: 0 10px !important;
		  border: 1px solid #8f5a24 !important;
		  border-radius: 3px !important;
		  background: #fff3d3 !important;
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font: 800 13px Verdana, Arial, sans-serif !important;
		  box-shadow: inset 0 1px 4px rgba(64,32,12,.28), 0 1px 0 rgba(255,255,255,.55) !important;
		}

		#ContentColumn .Content .eclipse-characters-page a,
		#ContentColumn .Content .eclipse-characters-page a * {
		  color: #4d1209 !important;
		  -webkit-text-fill-color: #4d1209 !important;
		  font-weight: 900 !important;
		}

		#ContentColumn .Content .eclipse-characters-page input[type="submit"],
		#ContentColumn .Content .eclipse-characters-page .BigButtonText,
		#ContentColumn .Content .eclipse-characters-page .MediumButtonText {
		  color: #fff8dc !important;
		  -webkit-text-fill-color: #fff8dc !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}

		#ContentColumn #News .BoxContent .eclipse-characters-page,
		#ContentColumn #News .BoxContent .eclipse-characters-page td,
		#ContentColumn #News .BoxContent .eclipse-characters-page div,
		#ContentColumn #News .BoxContent .eclipse-characters-page span,
		#ContentColumn #News .BoxContent .eclipse-characters-page b,
		#ContentColumn #News .BoxContent .eclipse-characters-page strong,
		#ContentColumn #News .BoxContent .eclipse-characters-page label,
		#ContentColumn #News .BoxContent .eclipse-characters-page nobr {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #News .BoxContent .eclipse-characters-page .CaptionContainer .Text,
		#ContentColumn #News .BoxContent .eclipse-characters-page .CaptionContainer .Text *,
		#ContentColumn #News .BoxContent .eclipse-characters-page .CaptionInnerContainer .Text,
		#ContentColumn #News .BoxContent .eclipse-characters-page .CaptionInnerContainer .Text * {
		  color: #fff !important;
		  -webkit-text-fill-color: #fff !important;
		  font-weight: 900 !important;
		  text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
		}

		#ContentColumn #News .BoxContent .eclipse-characters-page input:not(.BigButtonText):not([type="submit"]):not([type="button"]):not([type="hidden"]) {
		  color: #000 !important;
		  -webkit-text-fill-color: #000 !important;
		  caret-color: #000 !important;
		  font-weight: 800 !important;
		  text-shadow: none !important;
		}

		#ContentColumn #News .BoxContent .eclipse-characters-page input[type="submit"],
		#ContentColumn #News .BoxContent .eclipse-characters-page input[type="button"],
		#ContentColumn #News .BoxContent .eclipse-characters-page .BigButtonText,
		#ContentColumn #News .BoxContent .eclipse-characters-page .MediumButtonText {
		  color: #fff8dc !important;
		  -webkit-text-fill-color: #fff8dc !important;
		  font-weight: 900 !important;
		  text-shadow: 0 1px 1px #4c1200 !important;
		}
		</style>
<style>
	.scrollToTop {
		padding: 10px;
		text-align: center;
		font-weight: bold;
		color: #444;
		text-decoration: none;
		position: fixed;
		bottom: 10px;
		right: 12px;
		display: none;
		z-index: 50000;
		cursor: pointer;
	}

	.scrollToTop img {
		width: 42px;
		height: auto;
	}
</style>
<script>
	$(document).ready(function () {
		//Check to see if the window is top if not then display button
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('.scrollToTop').fadeIn();
			} else {
				$('.scrollToTop').fadeOut();
			}
		});
		//Click event to scroll to top
		$('.scrollToTop').click(function () {
			$('html, body').animate({scrollTop: 0}, 800);
			return false;
		});
	});
</script>
<script>
	$(document).ready(function () {
		//Check to see if the window is top if not then display button
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('.TopButton').fadeIn();
			}
		});
		//Click event to scroll to top
		$('.TopButton').click(function () {
			$('html, body').animate({scrollTop: 0}, 800);
			return false;
		});
	});
</script>
<div class="scrollToTop" title="To the top">
	<img alt style="border:0;" src="<?= $template_path . '/images/global/content/back-to-top.gif' ?>">
</div>
<script src="<?= $template_path; ?>/js/generic.js"></script>
<div id="HelperDivContainer" style="background-image: url(<?= $template_path; ?>/images/global/content/scroll.gif);">
	<div class="HelperDivArrow"
		 style="background-image: url(<?= $template_path; ?>/images/global/content/helper-div-arrow.png);"></div>
	<div id="HelperDivHeadline"></div>
	<div id="HelperDivText"></div>
	<center><img class="Ornament" src="<?= $template_path; ?>/images/global/content/ornament.gif"></center>
	<br>
</div>

</body>
</html>
<?php
/**
 * @param $menu
 * @return string
 */
function getImageMenuRandom($menu): string
{
	global $config;
	if (!$config['allow_menu_animated']) {
		return $menu === 'bgs' ? "/images/header/{$config['background_image']}" : "/images/menu/icon-{$menu}.gif";
	}

	$images = [
		'bgs'            => ['00.jpg', '01.jpg', '02.jpg', '03.jpg', '04.jpg', '05.jpg', '06.jpg', '07.jpg', '08.jpg', '09.jpg', '10.jpg', '11.jpg', '12.jpg'],
		'news'           => ['icon-news01.gif', 'icon-news02.gif', 'icon-news03.gif', 'icon-news04.gif', 'icon-news05.gif', 'icon-news06.gif'],
		'community'      => ['icon-community01.gif', 'icon-community02.gif', 'icon-community03.gif', 'icon-community04.gif', 'icon-community05.gif', 'icon-community06.gif', 'icon-community07.gif', 'icon-community08.gif'],
		'forum'          => ['icon-forum01.gif', 'icon-forum02.gif', 'icon-forum03.gif', 'icon-forum04.gif', 'icon-forum05.gif', 'icon-forum06.gif', 'icon-forum07.gif', 'icon-forum08.gif', 'icon-forum09.gif', 'icon-forum10.gif'],
		'account'        => ['icon-account01.gif', 'icon-account02.gif', 'icon-account03.gif', 'icon-account04.gif', 'icon-account05.gif'],
		'library'        => ['icon-library01.gif', 'icon-library02.gif', 'icon-library03.gif', 'icon-library04.gif', 'icon-library05.gif'],
		'wars'           => ['icon-wars01.gif', 'icon-wars02.gif', 'icon-wars03.gif', 'icon-wars04.gif', 'icon-wars05.gif', 'icon-wars06.gif', 'icon-wars07.gif', 'icon-wars08.gif', 'icon-wars09.gif', 'icon-wars10.gif', 'icon-wars11.gif', 'icon-wars12.gif', 'icon-wars13.gif', 'icon-wars14.gif'],
		'events'         => ['icon-events01.gif', 'icon-events02.gif', 'icon-events03.gif', 'icon-events04.gif', 'icon-events05.gif', 'icon-events06.gif', 'icon-events07.gif', 'icon-events08.gif', 'icon-events09.gif', 'icon-events10.gif', 'icon-events11.gif', 'icon-events12.gif', 'icon-events13.gif'],
		'support'        => ['icon-support01.gif', 'icon-support02.gif', 'icon-support03.gif', 'icon-support04.gif', 'icon-support05.gif', 'icon-support06.gif', 'icon-support07.gif', 'icon-support08.gif', 'icon-support09.gif', 'icon-support10.gif', 'icon-support11.gif'],
		'shops'          => ['icon-shops01.gif', 'icon-shops02.gif', 'icon-shops03.gif', 'icon-shops04.gif'],
		'charactertrade' => ['icon-bazaar01.gif', 'icon-bazaar02.gif'],
	];
	if (!$images[$menu]) {
		return "/images/menu/icon-{$menu}.gif";
	}

	// generate random number size of the array
	$img = $images[$menu][rand(0, count($images[$menu]) - 1)];
	return $menu !== 'bgs' ? "/images/menu/anim/{$img}" : "/images/header/bgs/{$img}";
}
