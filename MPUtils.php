<?php

/**
 * Metodi di utilità
 *
 * @author Matteo Ferrone
 * @since 2020-10-26
 * @version 2.7.4
 */
class MPUtils {

    /**
     * Ripulisce la stringa
     *
     * @param string $name Stringa da ripulire
     * @return string La stringa ripulita
     */
    public function sanitizeName($name) {
        $cerca = array("à", "è", "é", "ì", "ò", "ù", "'", "?", " ", "__", "&", "%", "#", "(", ")");
        $sostituisci = array("a", "e", "e", "i", "o", "u", "-", "-", "-", "-", "e", "-per-cento-", "-", "-", "-");
        return str_replace($cerca, $sostituisci, trim(strtolower($name)));
    }

    /**
     * Riporta la stringa in versione "human"
     *
     * @param string $name Stringa da "ricostruire"
     * @return string Stringa "ricostruita"
     */
    public function unSanitizeName($name) {
        $cerca = array("-", "_");
        $sostituisci = array(' ', ' ');
        return str_replace($cerca, $sostituisci, trim($name));
    }

    /**
     * Leva alcune parole dal testo
     *
     * @param string $testo Testo da ripulire
     * @return string Testo ripulito
     */
    public function levaParolacce($testo) {
        $cerca = array("stronz", "merd", "cacca", "porc", "vaffanculo", "cul", "cazz", "figa", "fottere", "scopare",
            "idiot", "scem", "cretin", "deficent", "deficient", "imbecill", "pisci", "pisciare", "smerdare", "fottiti",
            "fottut", "trombare", "porko", "cornut", "troi", "puttan", "zoccol", "fregn", "suca", "minchi");
        return str_replace($cerca, "***", $testo);
    }

    /**
     * Tronca il testo
     *
     * @param string $testo Testo da ripulire
     * @param int $caratteri Caratteri a cui troncare il testo
     * @return string Testo troncato
     */
    public function troncaTesto($testo, $caratteri = 300) {
        if (strlen($testo) <= $caratteri) {
            return $testo;
        }
        $nuovoTesto = substr($testo, 0, $caratteri);
        $condizione1 = preg_match("/^([^<]|<[^a]|<a.*>.*<\/a>)*$/", $nuovoTesto);
        if ($condizione1 == 0) {
            $caratteri = strrpos($nuovoTesto, "<a");
            $nuovoTesto = substr($testo, 0, $caratteri); // Taglia prima del link
        }
        return $nuovoTesto;
    }

    /**
     * Riempie un file
     *
     * @param string $file File da scrivere
     * @param string $message Messaggio da scrivere
     */
    public function scriviTesto($file, $message) {
        $f = fopen($file, 'a+');
        fwrite($f, $message);
        fclose($f);
    }

    /**
     * Controlla validità struttura email
     *
     * @param string $email Email da controllare
     * @return boolean TRUE o FALSE a seconda che l'email sia valida o meno
     */
    public function chkEmail($email) {
        $email = trim($email);
        if (!$email) {
            return false;
        }
        $numAt = count(explode('@', $email)) - 1;
        if ($numAt != 1) {
            return false;
        }
        if (strpos($email, ';') || strpos($email, ',') || strpos($email, ' ')) {
            return false;
        }
        if (!preg_match('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email)) {
            return false;
        }
        return true;
    }

    /**
     * Elabora il number format
     *
     * @param int $number Numero da formattare
     * @param int $decimals Numero di decimali
     * @param string $decimalPoint Punteggiatura decimali
     * @param string $thousandPoint Punteggiatura migliaia
     * @return int Numero formattato
     * @deprecated since version number 2.0
     */
    public function truncateNumberFormat($number, $decimals = 2, $decimalPoint = ',', $thousandPoint = '.') {
        if (($number * pow(10, $decimals + 1) % 10) == 5) {
            $number -= pow(10, -($decimals + 1));
        }
        return number_format($number, $decimals, $decimalPoint, $thousandPoint);
    }

    /**
     * Lista di anni
     *
     * @param int $annoStart Anno di inizio
     * @param int $annoEnd Anno di fine
     * @return array Lista di anni
     */
    public function listaAnni($annoStart, $annoEnd) {
        $arrAnni = array();
        for ($i = $annoStart; $i <= $annoEnd; $i++) {
            $arrAnni[] = $i;
        }
        rsort($arrAnni);
        return $arrAnni;
    }

    /**
     * Parsing di XML in formato JSON
     *
     * @param stirng $file File da parsare
     * @return string Stringa JSON
     */
    public function parseXml($file) {
        $fileContents = file_get_contents($file);
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents);
        $json = json_encode($simpleXml);
        return $json;
    }

    /**
     * Compara le classi passate come parametri
     *
     * @param class $objA
     * @param class $objB
     * @return mixed
     */
    public function sortObject($objA, $objB) {
        return strcmp($objA->__toString(), $objB->__toString());
    }

    /**
     * Converte le new line nel tag br
     *
     * @param string $text Testo da modificare
     * @return string Testo modificato
     */
    public function ln2br($text) {
        return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />'));
    }

    /**
     * Ritorna il valore di usando la classica operazione
     * $a : $b = $c : X
     *
     * @param float $a
     * @param float $b
     * @param float $c
     * @return float
     */
    public function getProportionValue($a, $b, $c) {
        return (float)((float)($b * $c) / $a);
    }

    /**
     * Converte una pagina HTML in puro testo
     *
     * @param string $string Testo HTML
     * @return string Testo convertito
     */
    public function htmlToText($string) {
        $search = array(
            "'<script[^>]*?>.*?</script>'si",
            "'<[\/\!]*?[^<>]*?>'si",
            "'([\r\n])[\s]+'",
            "'&(quot|#34);'i",
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&(reg|#174);'i",
            "'&#8482;'i",
            "'&#149;'i",
            "'&#151;'i",
            "'&#(\d+);'e"
        );
        $replace = array(
            " ",
            " ",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            "&iexcl;",
            "&cent;",
            "&pound;",
            "&copy;",
            "&reg;",
            "<sup><small>TM</small></sup>",
            "&bull;",
            "-",
            "uchr(\\1)"
        );
        $text = preg_replace($search, $replace, $string);
        return $text;
    }

    /**
     * Controlla se l'encoding è UTF-8
     *
     * @param string $text Testo da controllare
     * @return string Il tipo di encoding
     */
    public function isUTF8($text) {
        $res = mb_detect_encoding($text);
        return $res == "UTF-8" || $res == "ASCII";
    }

    /**
     * Tenta di chiudere tutti i tag HTML non chiusi
     *
     * @param string $unclosedString Testo HTML da controllare
     * @return string Testo modificato
     */
    public function closeUnclosedTags($unclosedString) {
        preg_match_all("/<([^\/]\w*)>/", $closedString = $unclosedString, $tags);
        for ($i = count($tags[1]) - 1; $i >= 0; $i--) {
            $tag = $tags[1][$i];
            if (substr_count($closedString, "</$tag>") < substr_count($closedString, "<$tag>")) {
                $closedString .= "</$tag>";
            }
        }
        return $closedString;
    }

    /**
     * @param $amount
     * @param int $vatPercent
     * @return float|int
     */
    public function vatGetValue($amount, $vatPercent = 22) {
        return ($amount * $vatPercent) / 100;
    }

    /**
     * Usando Javascript, chiude la finestra corrente, ed eventualmente la riapre
     *
     * @param boolean $reloadOpener Se deve riaprire la finestra
     */
    public function windowClose($reloadOpener = false) {
        if (!$reloadOpener) {
            echo "<script  type=\"text/javascript\" >\nwindow.self.close()\n</script>\n";
        } else {
            echo "<script type=\"text/javascript\" >\nwindow.opener.location.reload(true);window.self.close()\n</script>\n";
        }
    }

    /**
     * Converte un array in XML
     *
     * ESEMPIO:
     * $xml = new SimpleXMLElement('<root/>');
     * arrayToXml($array, $xml);
     * $xml->asXML($nomeFile);
     *
     * @param array $array
     * @param xml object $xml
     * @since 2.2
     */
    public function arrayToXml($array, &$xml) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    arrayToXml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("item$key");
                    arrayToXml($value, $subnode);
                }
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    /**
     * Converte un array in CSV
     *
     * @param array $array
     * @param string $csvFile
     * @since 2.2
     */
    public function arrayToCsv($array, $csvFile) {
        $f = fopen($csvFile, 'w');
        foreach ($array as $row) {
            fputcsv($f, $row, ';');
        }
        fclose($f);
    }

    /**
     * Crea un file csv e lo metto in download
     *
     * @param $array
     * @param string $filename
     * @param string $delimiter
     */
    function downloadCsv($array, $filename = "export.csv", $delimiter = ";") {
        $f = fopen('php://memory', 'w');
        foreach ($array as $line) {
            fputcsv($f, $line, $delimiter);
        }
        fseek($f, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        fpassthru($f);
    }

    /**
     * Genera un colore random
     *
     * @return string
     * @since 2.3
     */
    public function generateColor() {
        mt_srand((double)microtime() * 1000000);
        $colorCode = '';
        while (strlen($colorCode) < 6) {
            $colorCode .= sprintf("%02X", mt_rand(0, 255));
        }
        return '#' . $colorCode;
    }

    /**
     * Calcola il numero di giorni in un anno
     *
     * @param $year
     * @return int
     */
    public function daysInYear($year) {
        $days = 0;
        for ($month = 1; $month <= 12; $month++) {
            $days = $days + cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        return $days;
    }

    /**
     * @param $monthNumber
     * @return string
     * @since 2.7.2
     */
    public function getMonthName($monthNumber) {
        $arrayMonths = array(
            1 => 'Gennaio',
            2 => 'Febbraio',
            3 => 'Marzo',
            4 => 'Aprile',
            5 => 'Maggio',
            6 => 'Giugno',
            7 => 'Luglio',
            8 => 'Agosto',
            9 => 'Settembre',
            10 => 'Ottobre',
            11 => 'Novembre',
            12 => 'Dicembre'
        );
        return $arrayMonths[$monthNumber];
    }

    /**
     * Calcola la differenza in giorni tra due date
     *
     * Esempio:
     * echo $mpUtils->diffDateInDays("2019-01-01", date('Y-m-d'));
     * echo $mpUtils->diffDateInDays('first day of january', date('Y-m-d'));
     *
     * @param $date1
     * @param $date2
     * @return bool|DateInterval
     * @throws Exception
     */
    public function diffDateInDays($date1, $date2) {
        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);
        $diff = $d1->diff($d2);
        return $diff->format('%r%a');
    }

    /**
     * Visualizza tutti i giorni di un mese
     *
     * @param $year
     * @param $month
     * @param string $day
     * @return array
     * @throws Exception
     * @since 2.5
     */
    function getDaysInMonth($year, $month, $day = 'Monday') {

        $strDate = 'first ' . $day . ' of ' . $year . '-' . $month;

        $startDay = new \DateTime($strDate);

        $days = array();

        while ($startDay->format('Y-m') <= $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT)) {
            $days[] = clone($startDay);
            $startDay->modify('+ 7 days');
        }

        return $days;
    }

    /**
     * @param $imgToCopy
     * @param $dir
     * @param $nomeFile
     * @param $text
     * @param string $color
     * @param int $fontSize
     * @param string $imageFormat
     * @throws ImagickException
     */
    function duplicaConWatermark($imgToCopy, $dir, $nomeFile, $text, $color = 'black', $fontSize = 20, $imageFormat = 'png') {
        $image = new Imagick($imgToCopy);
        $draw = new ImagickDraw();
        $draw->setFontSize($fontSize);
        $draw->setFillColor($color);
        $draw->setGravity(Imagick::GRAVITY_CENTER);
        $image->annotateImage($draw, 100, 12, 0, $text);
        $image->setImageFormat($imageFormat);
        $image->writeImage($dir . $nomeFile . '.' . $imageFormat);
    }
}
