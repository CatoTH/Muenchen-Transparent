<?php
/**
 * @var Dokument[] $dokumente
 */

echo '<ul>';
foreach ($dokumente as $dok) {
	echo '<li>';
	echo CHtml::link($dok->name, $dok->getLink()) . " (" . $dok->seiten_anzahl . " Seiten): ";
	if ($dok->antrag_id > 0) echo CHtml::link($dok->antrag->getName(true), $dok->antrag->getLink());
	echo '</li>';
}
echo '</ul>';
