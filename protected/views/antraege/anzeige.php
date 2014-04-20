<?php
/**
 * @var Antrag $antrag
 * @var AntraegeController $this
 */

$this->pageTitle = $antrag->getName();

$assets_base = $this->getAssetsBase();


$personen = array(
	AntragPerson::$TYP_GESTELLT_VON => array(),
	AntragPerson::$TYP_INITIATORIN  => array(),
);
foreach ($antrag->antraegePersonen as $ap) $personen[$ap->typ][] = $ap->person;


$historie = $antrag->getHistoryDiffs();

?>
<h1><?= $antrag->getName() ?></h1>

<table class="table table-bordered">
	<tbody>
	<tr>
		<th>Originallink:</th>
		<td><?= CHtml::link($antrag->getSourceLink(), $antrag->getSourceLink()) ?></td>
	</tr>
	<? if (count($personen[AntragPerson::$TYP_INITIATORIN])) { ?>
		<tr>
			<th>Initiiert von:</th>
			<td>
				<ul>
					<?
					foreach ($personen[AntragPerson::$TYP_INITIATORIN] as $person) {
						echo "<li>";
						/** @var Person $person */
						if ($person->stadtraetIn) {
							echo CHtml::link($person->stadtraetIn->name, "#");
							echo " (" . CHtml::encode($person->ratePartei($antrag->gestellt_am)) . ")";
						} else {
							echo CHtml::encode($person->name);
						}
						echo "</li>\n";
					}
					?>
				</ul>
			</td>
		</tr>
	<?
	}
	if (count($personen[AntragPerson::$TYP_GESTELLT_VON])) {
		?>
		<tr>
			<th>Gestellt von:</th>
			<td>
				<ul>
					<?
					foreach ($personen[AntragPerson::$TYP_GESTELLT_VON] as $person) {
						echo "<li>";
						/** @var Person $person */
						if ($person->stadtraetIn) {
							echo CHtml::link($person->stadtraetIn->name, "#");
							echo " (" . CHtml::encode($person->ratePartei($antrag->gestellt_am)) . ")";
						} else {
							echo CHtml::encode($person->name);
						}
						echo "</li>\n";
					}
					?>
				</ul>
			</td>
		</tr>
	<?
	}
	?>
	<tr>
		<th>Gremium:</th>
		<td><?
			if ($antrag->ba_nr > 0) {
				echo CHtml::link("Bezirksausschuss " . $antrag->ba_nr, $this->createUrl("index/ba", array("ba_nr" => $antrag->ba_nr))) . " (" . CHtml::encode($antrag->ba->name). ")<br>";
			} else {
				echo "Sadtrat<br>";
			}
			echo CHtml::encode(strip_tags($antrag->referat));
			?></td>
	</tr>
	<tr>
		<th>Daten:</th>
		<td>
			<table class="daten"><?
				echo "<tr><th>Antragsnummer:</th><td>" . CHtml::encode($antrag->antrags_nr) . "</td></tr>";
				if ($antrag->gestellt_am > 0) echo "<tr><th>Gestellt am:</th><td>" . CHtml::encode(RISTools::datumstring($antrag->gestellt_am)) . "</td></tr>\n";
				if ($antrag->registriert_am > 0) echo "<tr><th>Registriert am:</th><td>" . CHtml::encode(RISTools::datumstring($antrag->registriert_am)) . "</td></tr>\n";
				if ($antrag->bearbeitungsfrist > 0) echo "<tr><th>Bearbeitungsfrist:</th><td>" . CHtml::encode(RISTools::datumstring($antrag->bearbeitungsfrist)) . "</td></tr>\n";
				if ($antrag->fristverlaengerung > 0) echo "<tr><th>Fristverlängerung:</th><td>" . CHtml::encode(RISTools::datumstring($antrag->fristverlaengerung)) . "</td></tr>\n";

				echo "<tr><th>Status:</th><td>";
				echo CHtml::encode($antrag->status);
				if ($antrag->bearbeitung != "") echo " / ";
				echo CHtml::encode($antrag->bearbeitung);
				echo "</td></tr>\n";
				/** @TODO: erledigt am: http://www.ris-muenchen.de/RII2/RII/ris_antrag_detail.jsp?risid=3234251 */
				?></table>
		</td>
	</tr>
	<tr>
		<th>Dokumente:</th>
		<td>
			<ul>
				<?
				$doks = $antrag->dokumente;
				usort($doks, function ($dok1, $dok2) {
					/**
					 * @var AntragDokument $dok1
					 * @var AntragDokument $dok2
					 */
					$ts1 = RISTools::date_iso2timestamp($dok1->datum);
					$ts2 = RISTools::date_iso2timestamp($dok2->datum);
					if ($ts1 > $ts2) return -1;
					if ($ts1 < $ts2) return 1;
					return 0;
				});
				foreach ($doks as $dok) {
					echo " <li>" . date("d . m . Y", RISTools::date_iso2timestamp($dok->datum)) . ": " . CHtml::link($dok->name, $dok->getOriginalLink()) . " </li > ";
				} ?>
			</ul>
		</td>
	</tr>
	<? if (count($antrag->antrag2vorlagen) > 0) { ?>
		<tr>
			<th>Verbundene Stadtratsvorlagen:</th>
			<td>
				<ul>
					<? foreach ($antrag->antrag2vorlagen as $vorlage) {
						echo "<li> " . CHtml::link($vorlage->getName(), $this->createUrl("antraege / anzeigen", array("id" => $vorlage->id))) . " </li> ";
					} ?>
				</ul>
			</td>
		</tr>
	<?
	}
	if (count($antrag->vorlage2antraege) > 0) {
		?>
		<tr>
			<th>Verbundene Stadtratsanträge:</th>
			<td>
				<ul>
					<? foreach ($antrag->vorlage2antraege as $antrag2) {
						echo "<li> " . CHtml::link($antrag2->getName(), $this->createUrl("antraege / anzeigen", array("id" => $antrag2->id))) . " </li> ";
					} ?>
				</ul>
			</td>
		</tr>
	<?
	}
	if (count($historie) > 0) {
		?>
		<tr>
			<th>Historie: <span class="icon - info - circled" title="Seit dem 1. April 2014" style="font - size: 12px; color: gray;"></span></th>
			<td>
				<ol>
					<? foreach ($historie as $hist) {
						echo " <li>" . $hist->getDatum() . ": <ul> ";
						$diff = $hist->getFormattedDiff();
						foreach ($diff as $d) {
							echo "<li><strong> " . $d->getFeld() . ":</strong> ";
							echo "<del> " . $d->getAlt() . "</del> => <ins> " . $d->getNeu() . "</ins></li> ";
						}
						echo "</ul></li>\n";
					} ?>
				</ol>
			</td>
		</tr>
	<?
	}
	?>
	</tbody>
</table>
