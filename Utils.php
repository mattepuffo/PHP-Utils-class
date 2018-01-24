<?php

/**
 * Metodi di utilità
 * 
 * @author Mattepuffo
 * @since 2016-04-13
 * @version 2.1
 */
class Utils {

    /**
     * Ripulisce la stringa
     * 
     * @param string $name Stringa da ripulire
     * @return string La stringa ripulita
     */
    public function sanitizeName($name) {
        $cerca = array("à", "è", "é", "ì", "ò", "ù", "'", "?", " ", "__");
        $sostituisci = array("a", "e", "e", "i", "o", "u", "-", "-", "-", "-");
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
     * @deprecated since version number 2.0
     * @param int $number Numero da formattare
     * @param int $decimals Numero di decimali
     * @param string $decimalPoint Punteggiatura decimali
     * @param string $thousandPoint Punteggiatura migliaia
     * @return int Numero formattato
     */
    public function truncateNumberFormat($number, $decimals = 2, $decimalPoint = ',', $thousandPoint = '.') {
        if (($number * pow(10, $decimals + 1) % 10 ) == 5) {
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
        return (float) ((float) ($b * $c) / $a);
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
     * Restituisce l'importo più l'iva
     * 
     * @param double $amount Import
     * @param float $vatPercent Percentuale di IVA
     * @return double Importo + IVA
     */
    public function vatGetAmount($amount, $vatPercent = 22) {
        return (double) ($amount + (getTaxValue($amount, $vatPercent)));
    }

    /**
     * Restituisce il valore percentuale per l'iva
     * 
     * @param double $amount Import
     * @param float $vatPercent IVA
     * @return double Percentuale
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

}
