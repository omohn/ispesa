<?php
/**
 * Author: Oliver Mohn, 814925
 * Date: 02.01.2019
 */

include "./views/layout/header.php";
include "./src/HTMLOutput.php";

use app\src\HTMLOutput;

// Muster für Validierungen werden definiert
$wishPattern            = "/^[\wöäüÖÄÜßéèáàâêî \-]*$/";
$namePattern            = "/^[a-zA-ZöäüÖÄÜßéèáàâêî \-]*$/";
$streetPattern          = "/^[\wöäüÖÄÜßéèáàâêî\. \-]*$/";
$streetNumberPattern    = "/^[0-9][0-9\/ \-]*[a-zA-Z]*$/";
$plzPattern             = "/^\d{5}$/";
$phonePattern           = "/^\+?([\d\/ \-]+)$/";

// assoziatives Array mit den Zuweisungen der gesuchten Muster
$patterns       = [
    "wish"      => $wishPattern,
    "prename"   => $namePattern,
    "name"      => $namePattern,
    "street"    => $streetPattern,
    "strnr"     => $streetNumberPattern,
    "plz"       => $plzPattern,
    "city"      => $namePattern,
    "phone"     => $phonePattern
];

// Feldnamen für Kontaktformular
$addressFields  = [
    "prename",
    "name",
    "street",
    "strnr",
    "plz",
    "city",
    "phone"
];

// Array für POST-Daten
$formData       = [];

// "Wunschliste"
$wishes         = [];

// Fehlerbehandlung
$errors         = [];
$error          = false;
$errorMessages  = [
    "wish"      => "Es sind leider keine Sonderzeichen erlaubt.",
    "empty"     => "Wert fehlt.",
    "prename"   => "Bitte gebe einen gültigen Vornamen ein.",
    "name"      => "Bitte gebe einen gültigen Nachnamen ein.",
    "street"    => "Bitte gebe einen gültigen Straßennamen ein.",
    "strnr"     => "Bitte gebe eine gültige Hausnummer ein.",
    "plz"       => "Bitte gib eine gültige Postleitzahl ein.",
    "city"      => "Bitte gebe einen gültigen Ort ein.",
    "phone"     => "Bitte gebe eine gültige Telefonnummer ein."
];

/**
 * escape-Funktion zum Entschärfen von Formular-Daten um Cross-Site-Scripting
 * zu unterbinden
 * @param $string - übergebener Text
 * @return string - Text ohne HTML-Entities
 */
function escape($string)
{
    return htmlentities($string, ENT_QUOTES, "UTF-8");
}

/**
 * Funktion wird benutzt um Formulardaten zu bearbeiten
 * 1. trim entfernt Leerzeichen links und rechts
 * 2. stripslashes entfernt Backslashes
 * 3. escape-Funktion wandelt für HTML reservierte Zeichen um
 *
 * @param $input - Eingabe
 * @return string - bearbeiteter Text
 */
function prepInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = escape($input);
    return $input;
}

/**
 * Darstellung Wunsch-Formular
 *
 * @param array $errors -  optional, Array mit Formularfehlermeldungen
 *                         empty, wenn keine Fehler übergeben werden
 */
function showWishForm($errors = [])
{
    $htmlOutput = new HTMLOutput();

    $action = escape($_SERVER["PHP_SELF"]);
    $legend = "Reisewünsche";
    $method = "POST";

    $fields = []; // Feldnamen
    $labels = []; // Label
    $placeHolders = []; // Platzhalter

    // Felddaten werden initialisiert
    for ($x = 0; $x <= 2; $x++) {

        $fields[$x] = "wish" . ($x + 1) ;
        $labels[$x] = "Wunsch-Reiseziel " . ($x + 1);
        $placeHolders[$x] = "Bitte gib Dein Wunschreiseziel ein!";

    }

    echo $htmlOutput->getSimpleTag("h1", "Urlaubs-Wunschzettel");
    echo $htmlOutput->getHTMLForm($action, $legend, $fields, $placeHolders,
        $labels, $method, $errors);
}

/**
 * Kontakt-Formular wird dargestellt
 *
 * @param $fieldNames - Array mit zu generierenden Formularfeldern
 * @param $hiddenFields - optional, versteckte Formularfelder zur Übergabe der Wünsche
 * @param $errors - optional, Array mit Fehlermeldungen
 * @param $content - optional, eingegebene Werte
 */
function showContactForm ($fieldNames, $hiddenFields = [], $errors = [], $content = []) {

    $htmlOutput = new HTMLOutput();

    $action = escape($_SERVER["PHP_SELF"]);
    $method = "POST";

    $labels = [
        "Vorname",
        "Nachname",
        "Straße",
        "Nr.",
        "PLZ",
        "Ort",
        "Telefonnummer"
    ];

    $placeHolders = [
        "Vorname",
        "Nachname",
        "Straße",
        "Nr.",
        "PLZ",
        "Wohnort",
        "Telefonnummer"
    ];

    $legend = "Kontaktinformation";

    echo $htmlOutput->getSimpleTag("h1", "Urlaubs-Wunschzettel");
    echo $htmlOutput->getOrderedList($hiddenFields);
    echo $htmlOutput->getHTMLForm($action, $legend, $fieldNames, $placeHolders,
        $labels, $method, $errors, $hiddenFields, $content);

}

/**
 * Gibt Abschlußdialog aus
 *
 * @param $contact - Kontaktdaten
 * @param $wishList - Urlaubswünsche
 */
function showGoodbye($contact, $wishList) {

    $htmlOutput = new HTMLOutput();

    echo $htmlOutput->getSimpleTag("h1", "Bestätigung");
    echo $htmlOutput->getOrderedList($wishList);
    echo $htmlOutput->getContact($contact);

}


// Wunsch-Formular wurde ausgefüllt, aber noch nicht das Adress-Formular!
// ($_POST['phone'] ist noch nicht deklariert. "phone" ist willkürlich gewählt.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND !isset($_POST['phone'])) {

    // POST-Daten werden in $formData-Array übertragen und eine Manipulation der
    // POST-Array-Keys ausgeschlossen, indem die Keys aus dem POST-Array mit
    // dem Muster abgeglichen werden.
    // Bei einem Fehler wird das Skript mit einer Fehlermeldung abgebrochen.
    foreach ($_POST as $key => $value) {

        try {
            if (preg_match("/^wish1|2|3$/", $key)) {
                $formData[$key] = $value;
            } else {
                throw new Exception("<h2>Fehler! Es gibt ein Problem mit den
                 Post-Daten.</h2>");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    // alle Felder sind leer, d.h. das Wunsch-Formular muss mit einem Hinweis
    // angezeigt werden
    if (empty($formData["wish1"]) && empty($formData["wish2"])
        && empty($formData["wish3"])) {

        $errors[] = $errorMessages["empty"];

        showWishForm($errors);

        // mindestens ein Wunsch-Feld wurde ausgefüllt
        // die Daten werden validiert
    } else {
        // Zählvariable, um Fehlerposition in $errors[] korrekt zu setzen
        $x = 0;
        foreach ($formData as $key => $value) {

            // ungültige Eingabe -> Wert wird gelöscht und Fehlermeldung für
            // das Feld gesetzt.
            if (!preg_match($patterns['wish'], $formData[$key])) {
                $errors[$x] = $errorMessages["wish"];
                $formData[$key] = "";

            // gültige Eingabe
            } elseif (!empty($formData[$key])){
                $errors[$x] = "";
                prepInput($formData[$key]);
            }
            $x++;
        }

        // mindestens ein Wunsch-Feld wurde ohne Sonderzeichen eingegeben
        // Aufruf vom Kontakt-Forum
        if (!empty($formData["wish1"]) || !empty($formData["wish2"])
            || !empty($formData["wish3"])) {

            showContactForm($addressFields, $formData);

        // keine gültige Eingabe, das Wunsch-Formular wird wieder aufgerufen
        } else {

            showWishForm($errors);

        }
    }

    // tritt ein, wenn die Wunscheingabe gültig war
} elseif (isset($_POST['phone'])) {

    $x = 0;
    // eventuelle Probleme mit manipulierten Post-Daten werden abgefangen
    try {
        foreach ($_POST as $key => $value) {
            // Wünsche kommen in $wishes-Array und werden
            if ($key == "wish1" || $key == "wish2" || $key == "wish3") {
                $wishes[$key] = prepInput($value);
                continue;
            } elseif (in_array($key, $addressFields)) {

                $formData[$key] = $value;
                // leeres Feld
                if (empty($formData[$key])){
                    $errors[$x] = $errorMessages["empty"];
                    $error = true;
                // ungültige Eingabe
                } elseif (!preg_match($patterns[$key], $formData[$key])){
                    $formData[$key] = "";
                    $errors[$x] = $errorMessages[$key];
                    $error = true;
                // gültige Eingabe
                } else {
                    $formData[$key] = prepInput($formData[$key]);
                    $errors[$x] = "";
                }
            } else {
                throw new Exception("<h2>Fehler! Es gibt ein Problem mit 
                    den Post-Daten.</h2>");
            }
            $x++;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }

    if ($error) {
        showContactForm($addressFields, $wishes, $errors, array_values($formData));
    } else {
        showGoodbye($formData, $wishes);
    }

} else {
    showWishForm();
}

include "./views/layout/footer.php";