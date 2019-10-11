<?php

include 'vendor/autoload.php';

function extractText($obj, $nested = 0){
    $txt = "";
    if(method_exists($obj, 'getSections')) {
        foreach ($obj->getSections() as $section) {
            $txt .= " " . extractText($section, $nested+1);
        }
    }else if (method_exists($obj, 'getElements')) {
        foreach ($obj->getElements() as $element) {
            $txt .= " " . extractText($element, $nested+1);
        }
    }else if (method_exists($obj, 'getText')) {
        $txt .= $obj->getText();
    }else if(method_exists($obj, 'getRows')) {
        foreach ($obj->getRows() as $row) {
            $txt .= " " . extractText($row, $nested+1);
        }
    }else if(method_exists($obj, 'getCells')) {
        foreach ($obj->getCells() as $cell) {
            $txt .= " " . extractText($cell, $nested+1);
        }
    }else if (get_class($obj) != "PhpOffice\PhpWord\Element\TextBreak"){
        $txt .= "(".get_class($obj).")"; # unknown object
    }

    return $txt;
}

$fileType = strtolower(pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION));

if(isset($_POST["submit"])) {

    if($fileType == 'docx') {

        $phpWord= \PhpOffice\PhpWord\IOFactory::createReader('Word2007')
            ->load($_FILES['file']['tmp_name']);

        echo str_word_count(extractText($phpWord));
    }

    if($fileType == 'pdf') {

        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($_FILES['file']['tmp_name']);
        $text = $pdf->getText();

        echo str_word_count($text);
    }
}
