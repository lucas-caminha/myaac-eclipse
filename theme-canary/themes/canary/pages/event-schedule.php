<style>
  .eclipse-event-schedule {
	color: #000 !important;
	font-weight: 800;
	text-shadow: none !important;
  }

  .eclipse-event-schedule .BoxContent {
	background: linear-gradient(180deg, #f3dc9f 0%, #dfba73 68%, #c99547 100%) !important;
	border: 2px solid #a86b23;
	border-radius: 5px;
	box-shadow: inset 0 0 0 1px rgba(255,244,198,.55), 0 8px 22px rgba(0,0,0,.46);
	padding-bottom: 14px;
  }

  .eclipse-event-schedule .TableContainer {
	background: transparent !important;
	box-shadow: none !important;
  }

  .eclipse-event-schedule .CaptionContainer,
  .eclipse-event-schedule .CaptionInnerContainer {
	height: 40px !important;
	background: linear-gradient(180deg, #234d63 0%, #0d2535 55%, #07121b 100%) !important;
	border: 0 !important;
  }

  .eclipse-event-schedule .CaptionContainer > span,
  .eclipse-event-schedule .CaptionInnerContainer > span {
	display: none !important;
  }

  .eclipse-event-schedule .CaptionContainer .Text {
	position: static !important;
	width: 100% !important;
	height: 40px !important;
	padding: 0 14px !important;
	box-sizing: border-box !important;
	color: #fff !important;
	font: 900 15px Georgia, "Times New Roman", serif !important;
	text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255,176,69,.55) !important;
  }

  #eventscheduletable {
	width: 100%;
	border-collapse: separate;
	table-layout: fixed;
	border-spacing: 4px;
	padding: 8px;
	border: 1px solid rgba(137,83,33,.54);
	border-radius: 5px;
	background: linear-gradient(180deg, #efd49e 0%, #d9b36d 58%, #c99a51 100%);
	box-shadow: inset 0 1px 0 rgba(255,246,204,.64), 0 4px 12px rgba(0,0,0,.22);
  }

  #eventscheduletable td {
	border: 1px solid rgba(137,83,33,.44);
	border-radius: 4px;
	height: 26px;
	overflow: hidden;
	font-weight: 900;
	color: #000 !important;
	text-shadow: none !important;
  }

  .eventscheduleheadertop {
	margin: auto;
	width: 100%;
	display: flex;
	min-width: 400px;
	align-items: center;
  }

  .eventscheduleheaderblockleft {
	margin-left: auto;
	margin-right: auto;
	text-align: center;
	position: relative;
  }

  .eventscheduleheaderdateblock {
	position: absolute;
	left: 50%;
	transform: translateX(-50%);
	width: 220px;
	text-align: center;
	font-size: 15px;
  }

  .eventscheduleheaderleft {
	float: left;
  }

  .eventscheduleheaderright {
	float: right;
  }

  .eventscheduleheaderblockright {
	text-align: right;
	white-space: nowrap;
	margin-right: 5px;
  }

  .eventschedule-weekdays td {
	color: #fff !important;
	background: #0d2535 !important;
	border-color: rgba(255,214,145,.34) !important;
	text-shadow: 0 1px 1px #000 !important;
  }

  td#default {
	color: #000 !important;
	background: linear-gradient(180deg, #f6dfaa 0%, #e4bf78 100%);
  }

  td#today {
	color: #000 !important;
	background: linear-gradient(180deg, #fff1bf 0%, #f0c967 100%);
	border-color: #5e170d;
	box-shadow: inset 0 0 0 2px rgba(94,23,13,.22);
  }

  td#other_day {
	color: #000 !important;
	background: rgba(158,108,55,.32);
	border: none;
  }

  .day {
	font-weight: 900;
	margin: 4px 0 6px 5px;
	color: #000 !important;
	font-size: 13px;
	text-shadow: none !important;
  }

  .activated {
	font-size: 12pt;
	font-weight: bold;
	color: #000 !important;
	text-shadow: none !important;
	word-break: break-word;
  }

  .event_name {
	color: #fff !important;
	width: 100%;
	font-weight: bold;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	padding: 2px 4px;
	margin-bottom: 2px;
	border: 1px solid rgba(255,245,202,.55);
	border-radius: 3px;
	box-sizing: border-box;
	text-shadow: 0 1px 1px rgba(0,0,0,.75);
  }

  .eclipse-event-schedule-note {
	margin: 10px 14px 0;
	color: #000 !important;
	font-weight: 800;
	text-shadow: none !important;
  }
</style>
<?php
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Agenda de Eventos';

$currentYear = date('Y');
$currentMonth = date('n');

$getYear	= $_GET['year'] ?? $currentYear;
$getMonth	= $_GET['month'] ?? $currentMonth;

$dateObj	= DateTime::createFromFormat('!m', $getMonth);
$monthNames = [
	1 => 'Janeiro',
	2 => 'Fevereiro',
	3 => 'Março',
	4 => 'Abril',
	5 => 'Maio',
	6 => 'Junho',
	7 => 'Julho',
	8 => 'Agosto',
	9 => 'Setembro',
	10 => 'Outubro',
	11 => 'Novembro',
	12 => 'Dezembro',
];
$monthName = $monthNames[(int)$getMonth] ?? $dateObj->format('F');

function showWeeks(): string
{
	$out = "";
	$weeks = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
	for ($i = 0; $i < 7; $i++) $out .= "<td>$weeks[$i]</td>";
	return $out;
}

function generateIndicator($event, $currentDay): string
{
	$isStartOrEnd = '';

	$explodeStartDate = explode('/', $event['startdate']);
	$explodeEndDate = explode('/', $event['enddate']);

	if ($currentDay == $explodeStartDate[1] ||
		$currentDay == $explodeEndDate[1]
	) {
		$isStartOrEnd = '*';
	}

	$out = "<span style='width: 120px;' class='HelperDivIndicator'";
	$div = "<div class='activated'>{$event['name']}:</div><div style='margin-bottom: 20px'>&amp;bull; {$event['description']}</div>";
	$out .= 'onmouseover="ActivateHelperDiv($(this), &quot;&quot;, &quot;' . $div . '&quot;, &quot;&quot;);"';
	$out .= 'onmouseout="$(&quot;#HelperDivContainer&quot;).hide();">';
	$out .= "<div class='event_name' style='background: {$event['colordark']};'>{$isStartOrEnd}{$event['name']}</div></span>";

	return $out;
}

function showCalendar($month, $year): string
{
	$amountDays = date('t', mktime(0, 0, 0, $month, 1, $year));
	$currentDay = 0;

	$firstDayOfWeek = jddayofweek(cal_to_jd(CAL_GREGORIAN, $month, "01", $year), 0) - 1;

	$outDays = "<tr class='eventschedule-weekdays' style='text-align:center; width:120px; background-color:#0d2535; color:#fff !important;'>" . showWeeks() . "</tr>";

	$events_xml = config('data_path') . 'XML/events.xml';

	$events_json = config('data_path') . 'json/eventscheduler/events.json';

	if (file_exists($events_json)) {
		$eventsDecode = json_decode(file_get_contents($events_json), true);
		$events = $eventsDecode['events'];
		foreach ($events as &$event) {
			$event['colordark'] = $event['colors']['colordark'];
		}

		function compareEvent(array $first, array $second): int {
			return ((int)$first['details']['displaypriority'] <=> (int)$second['details']['displaypriority']);
		}

		usort($events, 'compareEvent');
	} elseif (file_exists($events_xml)) {
		$xml = simplexml_load_file($events_xml);

		$events = [];
		foreach ($xml->event as $event) {
			$event['description'] = $event->description['description'];
			$event['colordark'] = $event->colors['colordark'];

			$events[] = $event;
		}

		function compareEvent($obj1, $obj2): int {
			return ((int)$obj1->details['displaypriority'] <=> (int)$obj2->details['displaypriority']);
		}

		usort($events, 'compareEvent');
	}

	for ($row = 0; $row < 6; $row++) {
		$outDays .= "<tr>";
		for ($column = 0; $column < 7; $column++) {
			$outDays .= "<td style='height:82px; background-clip: padding-box; overflow: hidden; vertical-align:top; color:#000 !important; -webkit-text-fill-color:#000 !important; font-weight:900 !important; text-shadow:none !important;' ";
			$color = "other_day";
			if ($currentDay == (date('d') - 1) && date('m') == $month) {
				$color = "today";
			} else {
				if (($currentDay + 1) <= $amountDays) {
					$color = ($column < $firstDayOfWeek && $row == 0) ? "other_day" : "default";
				}
			}

			$outDays .= "id='$color'>";

			if ($currentDay + 1 <= $amountDays) {
				if ($column < $firstDayOfWeek && $row == 0) {
					$outDays .= " ";
				} else {
					$outDays .= "<div class='day' style='color:#000 !important; -webkit-text-fill-color:#000 !important; font-weight:900 !important; text-shadow:none !important;'><span style='vertical-align: text-bottom; color:#000 !important; -webkit-text-fill-color:#000 !important; font-weight:900 !important; text-shadow:none !important;'>" . ++$currentDay . " </span></div>";

					if (isset($events)) {
						$current_date = "$month/$currentDay/$year";

						foreach ($events as $event) {
							$start_date = strtotime($event['startdate']);
							$end_date = strtotime($event['enddate']);
							$current_date_time = strtotime($current_date);

							if ($current_date_time >= $start_date && $current_date_time <= $end_date) {
								$outDays .= generateIndicator($event, $currentDay);
							}
						}
					}
				}
			} else {
				break;
			}
			$outDays .= "</td>";
		}
		$outDays .= "</tr>";
	}

	return $outDays;
}
?>

<div class="eclipse-event-schedule">
<div class="BoxContent">
	<div id="eventscheduletablecontainer">
		<div class="TableContainer">
			<div class="CaptionContainer">
				<div class="CaptionInnerContainer">
					<span class="CaptionEdgeLeftTop" style="background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);"></span>
					<span class="CaptionEdgeRightTop" style="background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);"></span>
					<span class="CaptionBorderTop" style="background-image:url(https://static.tibia.com/images/global/content/table-headline-border.gif);"></span>
					<span class="CaptionVerticalLeft" style="background-image:url(https://static.tibia.com/images/global/content/box-frame-vertical.gif);"></span>

					<div class="Text">
						<div class="eventscheduleheadertop">
							<div class="eventscheduleheaderblockleft">
								<div class="eventscheduleheaderdateblock">
									<span class="eventscheduleheaderleft">
										<?php

										$year = $getYear;
										$month = $getMonth - 1;

										if ( $getMonth == 1 ) {
											$year = $getYear - 1;
											$month = 12;
										}

										if ($getMonth > $currentMonth || $getYear > $currentYear) {
											echo '<a href="' . getLink('event-schedule') . '?year=' . $year .
										'&month=' . $month . '" style="color:white;" > &laquo;</a>';
										}

										?>
									</span>
									<?= $monthName . ' ' . $getYear; ?>
									<span class="eventscheduleheaderright">

										<?php

										$year = $getYear;
										$month = $getMonth + 1;

										if ( $getMonth == 12 ) {
											$year = $getYear + 1;
											$month = 1;
										}

										echo '<a href="' . getLink('event-schedule') . '?year=' . $year . '&month=' . $month . '" style="color:white;" > &raquo;</a>';

										?>
									</span>
								</div>
							</div>
							<div class="eventscheduleheaderblockright"><?= date('Y-m-d H:i') ?></div>
						</div>
					</div>

					<span class="CaptionVerticalRight" style="background-image:url(https://static.tibia.com/images/global/content/box-frame-vertical.gif);"></span>
					<span class="CaptionBorderBottom" style="background-image:url(https://static.tibia.com/images/global/content/table-headline-border.gif);"></span>
					<span class="CaptionEdgeLeftBottom" style="background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);"></span>
					<span class="CaptionEdgeRightBottom" style="background-image:url(https://static.tibia.com/images/global/content/box-frame-edge.gif);"></span>
				</div>
			</div>
			<table class="Table1" cellpadding="0" cellspacing="0" style="background-color: transparent;">
				<tbody>
				<tr>
					<td>
						<div class="InnerTableContainer" style="padding: 10px;">
							<table style="width:100%;" id="eventscheduletable">
								<tbody>
								<?= showCalendar($getMonth, $getYear) ?>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	<br>
	<div class="eclipse-event-schedule-note">* O evento começa/termina no server save deste dia.</div>
</div>
</div>
