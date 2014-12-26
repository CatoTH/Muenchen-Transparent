<?php

/**
 * @property integer $id
 * @property integer $referentIn
 * @property string $gewaehlt_am
 * @property string $bio
 * @property string $web
 * @property string $name
 * @property string $twitter
 * @property string $facebook
 * @property string $abgeordnetenwatch
 *
 * The followings are the available model relations:
 * @property Antrag[] $antraege
 * @property Person[] $personen
 * @property StadtraetInFraktion[] $stadtraetInnenFraktionen
 * @property StadtraetInGremium[] $mitgliedschaften
 * @property StadtraetInReferat[] $stadtraetInnenReferate
 */
class StadtraetIn extends CActiveRecord implements IRISItem
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return StadtraetIn the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'stadtraetInnen';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, name, referentIn', 'required'),
            array('id, referentIn', 'numerical', 'integerOnly' => true),
            array('web', 'length', 'max' => 250),
            array('name', 'length', 'max' => 100),
            array('twitter', 'length', 'max' => 45),
            array('facebook, abgeordnetenwatch', 'length', 'max' => 200),
            array('gewaehlt_am', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'antraege'                 => array(self::MANY_MANY, 'Antrag', 'antraege_stadtraetInnen(stadtraetIn_id, antrag_id)', 'order' => 'gestellt_am DESC'),
            'personen'                 => array(self::HAS_MANY, 'Person', 'ris_stadtraetIn'),
            'stadtraetInnenFraktionen' => array(self::HAS_MANY, 'StadtraetInFraktion', 'stadtraetIn_id', 'order' => 'wahlperiode DESC'),
            'mitgliedschaften'         => array(self::HAS_MANY, 'StadtraetInGremium', 'stadtraetIn_id'),
            'stadtraetInnenReferate'   => array(self::HAS_MANY, 'StadtraetInReferat', 'stadtraetIn_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'                => 'ID',
            'gewaehlt_am'       => 'Gewaehlt Am',
            'bio'               => 'Bio',
            'web'               => 'Web',
            'name'              => 'Name',
            'twitter'           => 'Twitter',
            'facebook'          => 'Facebook',
            'abgeordnetenwatch' => 'Abgeordnetenwatch'
        );
    }

    /**
     * @param array $add_params
     * @return string
     */
    public function getLink($add_params = array())
    {
        $name = $this->getName();
        return Yii::app()->createUrl("index/stadtraetIn", array_merge(array("id" => $this->id, "name" => $name), $add_params));
    }


    /** @return string */
    public function getTypName()
    {
        return "Stadtratsmitglied";
    }

    /**
     * @return string
     */
    public function errateVorname()
    {
        if (mb_strpos($this->name, ",") > 0) {
            preg_match("/^(?<titel>([a-z]+\. )*)(?<name>.*)$/siu", $this->name, $matches);
            $titel = trim($matches["titel"]);
            if (strlen($titel) > 0) $titel .= " ";

            $x = explode(",", $matches["name"]);
            if (count($x) == 2) {
                return trim($x[1]);
            } else {
                return $this->name;
            }
        } else return $this->name;
    }

    /**
     * @return string
     */
    public function errateNachname()
    {
        if (mb_strpos($this->name, ",") > 0) {
            preg_match("/^(?<titel>([a-z]+\. )*)(?<name>.*)$/siu", $this->name, $matches);
            $titel = trim($matches["titel"]);
            if (strlen($titel) > 0) $titel .= " ";

            $x = explode(",", $matches["name"]);
            if (count($x) == 2) {
                return trim($x[0]);
            } else {
                return $this->name;
            }
        } else return $this->name;
    }

    /**
     * @param bool $kurzfassung
     * @return string
     */
    public function getName($kurzfassung = false)
    {
        if (mb_strpos($this->name, ",") > 0) {
            preg_match("/^(?<titel>([a-z]+\. )*)(?<name>.*)$/siu", $this->name, $matches);
            $titel = trim($matches["titel"]);
            if (strlen($titel) > 0) $titel .= " ";

            $x = explode(",", $matches["name"]);
            if (count($x) == 2) {
                $name = $x[1] . " " . $x[0];
            } else {
                $name = $this->name;
            }
            return $titel . trim($name);
        } else {
            return $this->name;
        }
    }

    /**
     * @param StadtraetIn[] $personen
     * @return StadtraetIn[];
     */
    public static function sortByName($personen)
    {
        usort($personen, function ($str1, $str2) {
            /** @var StadtraetIn $str1 */
            /** @var StadtraetIn $str2 */
            $name1 = preg_replace("/^([a-z]+\. )*/siu", "", $str1->getName());
            $name2 = preg_replace("/^([a-z]+\. )*/siu", "", $str2->getName());
            return strnatcasecmp($name1, $name2);
        });
        return $personen;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return "0000-00-00 00:00:00";
    }


    /**
     * @return string
     */
    public function getSourceLink()
    {
        return "http://www.ris-muenchen.de/RII/RII/ris_mitglieder_detail.jsp?risid=" . $this->id;
    }

    /**
     * @param string $datum
     * @param int|null $ba_nr
     * @return StadtraetIn[]
     */
    public static function getByFraktion($datum, $ba_nr)
    {
        if ($ba_nr === null) $ba_where = "c.ba_nr IS NULL";
        else $ba_where = "c.ba_nr = " . IntVal($ba_nr);

        $strs_in = StadtraetIn::model()->findAll(array(
            'alias' => 'a',
            'order' => 'a.name ASC',
            'with'  => array(
                'stadtraetInnenFraktionen'          => array(
                    'alias'     => 'b',
                    'condition' => 'b.datum_von <= "' . addslashes($datum) . '" AND (b.datum_bis IS NULL OR b.datum_bis >= "' . addslashes($datum) . '")',
                ),
                'stadtraetInnenFraktionen.fraktion' => array(
                    'alias'     => 'c',
                    'condition' => $ba_where,
                )
            )));
        $strs_out = array();
        foreach ($strs_in as $strs) {
            if ($strs->id == 3425214) continue; // Seltsamer RIS-Testuser http://www.ris-muenchen.de/RII/RII/ris_mitglieder_detail_fraktion.jsp?risid=3425214&periodeid=null o_O
            $strs_out[] = $strs;
        }
        return $strs_out;
    }

    /**
     * @param string $datum
     * @param int|null $ba_nr
     * @return array[]
     */
    public static function getGroupedByFraktion($datum, $ba_nr)
    {
        $strs       = static::getByFraktion($datum, $ba_nr);
        $fraktionen = array();
        foreach ($strs as $str) {
            if (!isset($fraktionen[$str->stadtraetInnenFraktionen[0]->fraktion_id])) $fraktionen[$str->stadtraetInnenFraktionen[0]->fraktion_id] = array();
            $fraktionen[$str->stadtraetInnenFraktionen[0]->fraktion_id][] = $str;
        }
        return $fraktionen;
    }
}