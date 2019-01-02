<?php
/**
 * Author: Oliver Mohn, 814925
 * Date: 02.01.2019
 */

namespace app\src;

/**
 * Class HTMLOutput
 *
 * Klasse zum Rendern des HTML-Inhalts
 *
 * @package app\src
 */
class HTMLOutput
{

    /**
     * HTMLOutput constructor.
     */
    public function __construct()
{

}

    /**
     * Ausgabe eines einfachen HTML-Elementes
     *
     * @param $type - HTML-Tagtyp
     * @param $text - Inhalt des Tags
     * @return string - HTML-Element
     */
    public function getSimpleTag ($type, $text)
    {
        return "<{$type}>{$text}</{$type}>\n";
    }


    /**
     * Ausgabe der Kontaktansicht
     *
     * @param $contact - Array mit Adressdaten
     * @return string - HTML-Ausgabe
     */
    public function getContact ($contact) {
        $htmlOutput = "Die Tickets werden Ihnen in 2-3 Werktagen zugestellt: <br />";
        $htmlOutput .= "<br />";
        $htmlOutput .= "Ihre Adressdaten: <br />";
        $htmlOutput .= $contact["prename"] . " " . $contact["name"] . "<br />";
        $htmlOutput .= $contact["street"] . " " . $contact["strnr"] . "<br />";
        $htmlOutput .= $contact["plz"] . " " . $contact["city"] . "<br />";
        $htmlOutput .= "(Tel. " . $contact["phone"] . ")";
        return $htmlOutput;
    }
    /**
     * Umwandlung in HTML-Listenelement
     *
     * @param $listElement - String-Listenelement
     * @return string - HTML-Listenelement
     */
    public function getListElements ($listElement)
    {
        return "<li>{$listElement}</li>";
    }

    /**
     * Erstellung einer HTML-Ordered-List
     *
     * @param $list - Listenarray
     * @return string - fertige HTML-Ordered-List
     */
    public function getOrderedList ($list)
    {
        $htmlOutput = "<ol>";
        foreach ($list as $element) {
            $htmlOutput .= $this->getListElements ($element);
        }
        $htmlOutput .= "</ol>";
        return $htmlOutput;
    }

    /**
     * Ausgabe eines HTML-Textinput-Formularfelds
     *
     * @param $field - Feldname
     * @param $label - Label
     * @param $placeHolder - Platzhalter
     * @param $errorMessage - Fehlermeldung
     * @param string $value - zuvor eingebener Wert
     * @return string - fertiges Feld
     */
    public function getHTMLInputText ($field, $label, $placeHolder,
                                      $errorMessage, $value="")
    {
        $htmlOutput = <<<EOT
      <div class="form-group">
            <label class="col-form-label" for="{$field}">{$label}</label>
            <input type="text" class="form-control" id="{$field}" name="{$field}" 
                value ="{$value}" placeholder="{$placeHolder}">
            {$errorMessage}
      </div>
EOT;
        return $htmlOutput;
    }

    /**
     * Ausgabe eines Buttons
     *
     * @param $type - gewünschter Buttontyp
     * @param $text - Buttontext
     * @return string - HTML-Button
     */
    public function getHTMLButton ($type, $text)
    {
        $htmlOutput = "<button type=\"{$type}\" class=\"btn btn-primary\">{$text}</button>\n";
        return $htmlOutput;
    }


    /**
     * Methode zur Ausgabe eines Formulars
     *
     * @param $action - gewünschte Aktion
     * @param $legend - Formularlegende
     * @param $fields - Feldnamen
     * @param $placeHolders - Platzhalter
     * @param array $labels - Feldlabels
     * @param string $method - Methode (POST als Default)
     * @param array $errors - Eingabefehler
     * @param array $hiddenInput - Versteckte Felder
     * @param array $values - Formularwerte
     * @return string - HTML-Formular
     */
    public function getHTMLForm ($action, $legend, $fields, $placeHolders,
                                 $labels = [], $method="POST", $errors = [],
                                 $hiddenInput = [], $values=[])
    {

        $htmlOutput= <<<EOT

        <form action="{$action}" method="{$method}">
            <fieldset>
            <legend>{$legend}</legend>
EOT;
        foreach ($hiddenInput as $hiddenField => $hiddenValue) {
            $htmlOutput .= "<input type='hidden' name='{$hiddenField}' 
            id='{$hiddenField}' value='{$hiddenValue}'>";
        }


        $fieldAmount = count($fields);

        for ($i = 0; $i < $fieldAmount; $i++) {
            $errorMessage = "";

            if (!empty($errors[$i])) {
                $errorMessage = "<div class='error'>$errors[$i]</div>";
            }

            if (empty($values)) {
                $htmlOutput = $htmlOutput . $this->getHTMLInputText($fields[$i],
                        $labels[$i], $placeHolders[$i], $errorMessage);
            } else {
                $htmlOutput = $htmlOutput . $this->getHTMLInputText($fields[$i],
                        $labels[$i], $placeHolders[$i], $errorMessage, $values[$i]);
            }


        }



        $htmlOutput = $htmlOutput . $this->getHTMLButton("submit", "Okay");
        $htmlOutput = $htmlOutput . $this->getHTMLButton("reset", "Leeren");

        return $htmlOutput;

    }


}