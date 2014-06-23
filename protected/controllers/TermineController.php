<?php

class TermineController extends RISBaseController
{

	/**
	 * @param int $termin_id
	 */
	public function actionAnzeigen($termin_id) {
		/** @var Termin $antrag */
		$termin = Termin::model()->findByPk($termin_id);
		if (!$termin) {
			$this->render('/index/error', array("code" => 404, "message" => "Der Termin wurde nicht gefunden"));
			return;
		}

		$this->load_leaflet_css      = true;

		$this->render("anzeige", array(
			"termin" => $termin
		));
	}

	/**
	 * @param int $termin_id
	 */
	public function actionTopGeoExport($termin_id) {
		$termin_id = IntVal($termin_id);

		$this->load_leaflet_css      = true;

		/** @var Termin $sitzung */
		$termin = Termin::model()->findByPk($termin_id);

		$this->renderPartial("top_geo_export", array(
			"termin" => $termin
		));
	}


}