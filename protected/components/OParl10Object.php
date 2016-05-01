<?php

class OParl10Object {
    const TYPE_AGENDAITEM      = 'https://oparl.org/schema/1.0/AgendaItem';
    const TYPE_BODY            = 'https://oparl.org/schema/1.0/Body';
    const TYPE_CONSULTATION    = 'https://oparl.org/schema/1.0/Consultation';
    const TYPE_FILE            = 'https://oparl.org/schema/1.0/File';
    const TYPE_LEGISLATIVETERM = 'https://oparl.org/schema/1.0/LegislativeTerm';
    const TYPE_LOCATION        = 'https://oparl.org/schema/1.0/Location';
    const TYPE_MEETING         = 'https://oparl.org/schema/1.0/Meeting';
    const TYPE_MEMBERSHIP      = 'https://oparl.org/schema/1.0/Membership';
    const TYPE_ORGANIZATION    = 'https://oparl.org/schema/1.0/Organization';
    const TYPE_PAPER           = 'https://oparl.org/schema/1.0/Paper';
    const TYPE_PERSON          = 'https://oparl.org/schema/1.0/Person';
    const TYPE_SYSTEM          = 'https://oparl.org/schema/1.0/System';

    /*
     * Gibt ein beliebiges Objekt als OParl-Objekt im Form eines arrays zurück
     */
    public static function object($typ, $id, $subtype = null) {
        if ($subtype != null)
            $typ = $typ . '/' . $subtype;
        if      ($typ == 'file'                  ) return self::file($id);
        else if ($typ == 'meeting'               ) return self::meeting($id);
        else if ($typ == 'membership/fraktion'   ) return self::membership($id, 'fraktion');
        else if ($typ == 'membership/gremium'    ) return self::membership($id, 'gremium');
        else if ($typ == 'membership/referat'    ) return self::membership($id, 'referat');
        else if ($typ == 'organization/fraktion' ) return self::organization($id, 'fraktion');
        else if ($typ == 'organization/gremium'  ) return self::organization($id, 'gremium');
        else if ($typ == 'organization/referat'  ) return self::organization($id, 'referat');
        else if ($typ == 'person'                ) return self::person($id);
        else if ($typ == 'system'                ) return self::system($id);
        else if ($typ == 'term'                  ) return self::terms($id);
        else if ($typ == 'paper'                 ) return self::paper($id);
        else if ($typ == 'agendaitem'            ) return ["note:" => "not implemented yet"];
        else if ($typ == 'location'              ) return ["note:" => "not implemented yet"];
        else if ($typ == 'consultation'          ) return ["note:" => "not implemented yet"];
        else if ($typ == 'body'                  ) {
            // FIXME: https://github.com/codeformunich/Muenchen-Transparent/issues/135
            if ($id == 0) {
                $body = 0;
                $name = 'Stadrat der Landeshauptstadt München';
                $shortName = 'Stadtrat';
                $website = 'http://www.muenchen.de/';
            } else {
                $ba = Bezirksausschuss::model()->findByPk($id);
                $body = $ba->ba_nr;
                $name = 'Bezirksausschuss ' . $ba->ba_nr . ': ' . $ba->name;
                $shortName = 'BA ' . $ba->ba_nr;
                $website = Yii::app()->createAbsoluteUrl($ba->getLink());
            }
            return OParl10Object::body($body, $name, $shortName, $website);
        } else {
            header("HTTP/1.0 404 Not Found");
            return ['error' => 'No such type "' . $typ. '"'];
        }
    }

    /**
     * Erzeugt das 'oparl:System'-Objekt, also den API-Einstiegspunkt
     */
    public static function system($id) {
        return [
            'id'                 => OParl10Controller::getOparlObjectUrl('system', null),
            'type'               => self::TYPE_SYSTEM,
            'oparlVersion'       => OParl10Controller::VERSION,
            'otherOparlVersions' => [],
            'body'               => OParl10Controller::getOparlListUrl('body'),
            'name'               => Yii::app()->params['projectTitle'],
            'contactEmail'       => Yii::app()->params['adminEmail'],
            'contactName'        => Yii::app()->params['adminEmailName'],
            'website'            => SITE_BASE_URL,
            'vendor'             => 'https://github.com/codeformunich/Muenchen-Transparent',
            'product'            => 'https://github.com/codeformunich/Muenchen-Transparent',
        ];
    }

    /**
     * Erzeugt ein 'oparl:Body'-Objekt, also den Stadtrat oder die Bezirksauschüsse
     */
    public static function body($body, $name, $shortName, $website) {
        return [
            'id'              => OParl10Controller::getOparlObjectUrl('body', $body),
            'type'            => self::TYPE_BODY,
            'system'          => OParl10Controller::getOparlObjectUrl('system', null),
            'contactEmail'    => Yii::app()->params['adminEmail'],
            'contactName'     => Yii::app()->params['adminEmailName'],
            'name'            => $name,
            'shortName'       => $shortName,
            'website'         => $website,
            'organization'    => OParl10Controller::getOparlListUrl('organization', $body),
            'person'          => OParl10Controller::getOparlListUrl('person',       $body),
            'meeting'         => OParl10Controller::getOparlListUrl('meeting',      $body),
            'paper'           => OParl10Controller::getOparlListUrl('paper',        $body),
            'terms'           => OParl10Controller::getOparlListUrl('term',         $body),
        ];
    }

    /**
     * Erzeugt ein 'oparl:LegislativeTerm'-Objekten, also eine Legislaturperiode
     *
     * Wenn als id -1 übergeben wird, dann wird die gesammte Liste zurückgegeben
     */
    public static function terms($id) {
        $data = [
            [
                'type'      => self::TYPE_LEGISLATIVETERM,
                'name'      => 'Unbekannt',
                'startDate' => '0000-00-00',
                'endDate'   => '0000-00-00',
            ],
            [
                'type'      => self::TYPE_LEGISLATIVETERM,
                'name'      => '1996-2002',
                'startDate' => '1996-12-03',
                'endDate'   => '2002-12-03',
            ],
            [
                'type'      => self::TYPE_LEGISLATIVETERM,
                'name'      => '2002-2008',
                'startDate' => '2002-12-03',
                'endDate'   => '2008-12-03',
            ],
            [
                'type'      => self::TYPE_LEGISLATIVETERM,
                'name'      => '2008-2014',
                'startDate' => '2008-12-03',
                'endDate'   => '2014-12-03',
            ],
            [
                'type'      => self::TYPE_LEGISLATIVETERM,
                'name'      => '2014-2020',
                'startDate' => '2014-12-03',
                'endDate'   => '2020-12-03',
            ],
        ];

        if ($id == -1)
            return $data;
        else
            return $data[$id];
    }

    /**
     * Erzeugt ein 'oparl:Organization'-Objekt, das ein Germium, eine Fraktion oder ein Referat abbildet
     */
    public static function organization($id, $subtype) {
        if ($subtype == "fraktion") {
            $object = Fraktion::model()->findByPk($id);
        } else if ($subtype == "gremium") {
            $object = Gremium::model()->findByPk($id);
        } else if ($subtype == "referat") {
            $object = Referat::model()->findByPk($id);
        } else {
            header("HTTP/1.0 404 Not Found");
            return ['error' => 'No such subtype "' . $subtype . '"'];
        }

        $meetings    = [];
        $memberships = [];

        return [
            'id'             => OParl10Controller::getOparlObjectUrl('organization/' . $subtype, $object->id),
            'type'           => self::TYPE_ORGANIZATION,
            'body'           => OParl10Controller::getOparlObjectUrl('body', $object->getBaNr()),
            'name'           => $object->getName(false),
            'shortName'      => $object->getName(true),
            'meeting'        => $meetings,
            'membership'     => $memberships,
            'classification' => $object->getTypName(),
        ];
    }

    /**
     * Erzeugt ein 'oparl:Membership'-Objekt, das die Mitgliedschaften eines Stadrats in einer Fraktion, einem Gremium, Referat abbildet
     */
    public static function membership($id, $subtype) {
        if ($subtype == "fraktion") {
            $object = StadtraetInFraktion::model()->findByPk($id);
            $organization = $object->fraktion;
        } else if ($subtype == "gremium") {
            $object = StadtraetInGremium::model()->findByPk($id);
            $organization = $object->gremium;
        } else if ($subtype == "referat") {
            $object = StadtraetInReferat::model()->findByPk($id);
            $organization = $object->referat;
        } else {
            header("HTTP/1.0 404 Not Found");
            return ['error' => 'No such subtype "' . $subtype . '"'];
        }

        $data = [
            'id'           => OParl10Controller::getOparlObjectUrl('membership/' . $subtype, $object->id),
            'type'         => self::TYPE_MEMBERSHIP,
            'organization' => OParl10Controller::getOparlObjectUrl('organization/' . $subtype, $organization->id),
            'person'       => OParl10Controller::getOparlObjectUrl('person',  $object->stadtraetIn->id),
            'role'         => $object->getFunktion(),
        ];

        if ($object->datum_von !== null)
            $data['startDate'] = $object->datum_von;

        if ($object->datum_bis !== null)
            $data['endDate'] = $object->datum_bis;

        return $data;
    }

    /**
     * Erzeugt ein 'oparl:Person'-Objekt, das StadträtInnen abbildet
     */
    public static function person($id) {
        $stadtraetin = StadtraetIn::model()->findByPk($id);

        $body = 0; // fallback

        if (count($stadtraetin->getFraktionsMitgliedschaften()) > 0) {
            $body = $stadtraetin->getFraktionsMitgliedschaften()[0]->fraktion->ba_nr;
            if ($body == null)
                $body = 0;
        }

        // Zwingende Attribute
        $data = [
            'id'         => OParl10Controller::getOparlObjectUrl('person', $stadtraetin->id),
            'type'       => self::TYPE_PERSON,
            'body'       => OParl10Controller::getOparlObjectUrl('body', $body),
            'name'       => $stadtraetin->name,
            'familyName' => $stadtraetin->errateNachname(),
            'givenName'  => $stadtraetin->errateVorname(),
        ];

        // Das Geschlecht übersetzen
        if ($stadtraetin->geschlecht) {
            if ($stadtraetin->geschlecht == 'weiblich')
                $data['gender'] = 'female';
            else if ($stadtraetin->geschlecht == 'maennlich')
                $data['gender'] = 'male';
            else
                $data['gender'] = 'other';
        }

        if ($stadtraetin->referentIn)
            $data['status'] = "Berufsmäßiger Stadtrat";
        else
            $data['status'] = "Ehrenamtlicher Stadtrat";

        // optionale Attribute
        $optional_properties = [
            'life'                                  => $stadtraetin->beschreibung,
            'lifeSource'                            => $stadtraetin->quellen,
            'email'                                 => $stadtraetin->email,
            'muenchenTransparent:elected'           => $stadtraetin->gewaehlt_am,
            'muenchenTransparent:dateOfBirth'       => $stadtraetin->geburtstag,
            'muenchenTransparent:beruf'             => $stadtraetin->beruf,
            'muenchenTransparent:bio'               => $stadtraetin->bio,
            'muenchenTransparent:website'           => $stadtraetin->web,
            'muenchenTransparent:twitter'           => $stadtraetin->twitter,
            'muenchenTransparent:facebook'          => $stadtraetin->facebook,
            'muenchenTransparent:abgeordnetenwatch' => $stadtraetin->abgeordnetenwatch,
        ];

        foreach ($optional_properties as $key => $value) {
            if ($value && $value != "")
                $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Erzeugt ein 'oparl:File'-Objekt, das Dokumente abbildet
     */
    public static function file($id) {
        $dokument = Dokument::model()->findByPk($id);

        $data = [
            'id'   => OParl10Controller::getOparlObjectUrl('file', $dokument->id),
            'type' => self::TYPE_FILE,
            'name' => $dokument->getName(),
            'muenchenTransparent:orignalAccessUrl' => $dokument->getLink(),
        ];

        if (substr($dokument->url, -strlen('.pdf')) === '.pdf') {
            $data['fileName' ] = $dokument->getName(true) . '.pdf';
            $data['mimeType' ] = 'application/pdf';
            $data['accessUrl'] = SITE_BASE_URL . $dokument->getLinkZumDokument() . '.pdf';
        } else if (substr($dokument->url, -strlen('.tiff')) === '.tiff') {
            $data['fileName' ] = $dokument->getName(true) . '.tiff';
            $data['mimeType' ] = 'image/tiff';
            $data['accessUrl'] =  SITE_BASE_URL . $dokument->getLinkZumDokument() . '.tiff'; // FIXME: https://github.com/codeformunich/Muenchen-Transparent/issues/137
        } else {
            $data['fileName' ] = $dokument->getName(true);
            $data['accessUrl'] = $dokument->getLink(); // FIXME: Da der Dateityp unbekannt ist gibt es auch keinen proxy
        }

        /*
        if ($dokument->antrag)
            $data['paper'] = [OParl10Controller::getOparlObjectUrl('paper', $dokument->antrag->id)];

        if ($dokument->termin)
            $data['meeting'] = [OParl10Controller::getOparlObjectUrl('meeting', $dokument->termin->id)];

        if ($dokument->tagesordnungspunkt)
            $data['agendaItem'] = [OParl10Controller::getOparlObjectUrl('agendaItem', $dokument->tagesordnungspunkt->id)];
        */

        if ($dokument->ocr_von)
            $data['muenchenTransparent:ocrCreator'] = $dokument->ocr_von;

        if ($dokument->deleted)
            $data['deleted'] = true;

        return $data;
    }

    /**
     * Erzeugt ein 'oparl:Meeting'-Objekt, das einen Termin abbildet
     */
    public static function meeting($id) {
        $termin = Termin::model()->findByPk($id);

        $data = [
            'id'           => OParl10Controller::getOparlObjectUrl('meeting', $termin->id),
            'type'         => self::TYPE_MEETING,
            'name'         => $termin->gremium->name,
            'meetingState' => $termin->sitzungsstand,
            'start'        => OParl10Controller::toOparlDateTime($termin->termin),
            'organization' => OParl10Controller::getOparlObjectUrl('organization/gremium', $termin->gremium->id),
            'modified'     => OParl10Controller::toOparlDateTime($termin->datum_letzte_aenderung),
        ];

        $data['auxiliaryFile'] = [];
        foreach ($termin->antraegeDokumente as $dokument)
            $data['auxiliaryFile'][] = OParl10Controller::getOparlObjectUrl('file', $dokument->id);

        if ($termin->abgesetzt)
            $data['cancelled'] = true;
        return $data;
    }

    public static function paper($id) {
        $antrag = Antrag::model()->findByPk($id);

        $data = [
            'id'               => OParl10Controller::getOparlObjectUrl('paper', $antrag->id),
            'type'             => self::TYPE_PAPER,
            'body'             => OParl10Controller::getOparlObjectUrl('body', ($antrag->ba_nr != null ? $antrag->ba_nr : 0)),
            'name'             => $antrag->getName(),
            'reference'        => $antrag->antrags_nr,
            'paperType'        => $antrag->getTypName(),
            'auxiliaryFile'    => [],
            'underDirectionof' => [OParl10Controller::getOparlObjectUrl('organization/referat', $antrag->referat_id)],
            'keyword'          => [],
        ];

        foreach ($antrag->dokumente as $dokument)
            $data['auxiliaryFile'][] = OParl10Controller::getOparlObjectUrl('file', $dokument->id);

        foreach ($antrag->tags as $tags)
            $data['auxiliaryFile'][] = $tags->name;

        if ($antrag->vorgang != null) {
            $data['relatedPaper'] = [];
            foreach ($antrag->vorgang->antraege as $verwandt)
                $data['relatedPaper'][] = OParl10Controller::getOparlObjectUrl('paper', $verwandt->id);
        }

        return $data;
    }
}
