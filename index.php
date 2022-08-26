<?php
$url = '';

function extractGoogleFontsUrl($url){
    $html = file_get_contents($url);

    //Instantiate the DOMDocument class.
    $htmlDom = new DOMDocument;

    //Parse the HTML of the page using DOMDocument::loadHTML
    @$htmlDom->loadHTML($html);

    //Extract the links from the HTML.
    $links = $htmlDom->getElementsByTagName('link');

    //Array that will contain our extracted links.
    $extractedLinks = array();

    //Loop through the DOMNodeList.
    //We can do this because the DOMNodeList object is traversable.
    foreach($links as $link){

        //Get the link in the href attribute.
        $linkHref = $link->getAttribute('href');

        if (strpos($linkHref, 'fonts.googleapis') == false) {
            continue;
        }

        //Add the link to our $extractedLinks array.
        $extractedLinks = $linkHref;
    }
    return $extractedLinks;
}

function getFontName($fontString){
    $partLink = explode('=', $fontString);
    $fontName = explode(':', $partLink[1]);
    $clearFontName = strtolower(str_replace("+","-",$fontName[0]));
    return $clearFontName;
}

function getFontVariants($fontString){
    $fontNameQuery = parse_url($fontString, PHP_URL_QUERY);
    if(strpos($fontNameQuery, ':') !== false){
        $justVariants = explode(':',$fontNameQuery);
        $justVariantsToLower = strtolower(str_replace("400","regular",$justVariants[1]));
        $justVariantsRefactored = implode(",", array_unique(explode(',',$justVariantsToLower)));
    } else{
        $justVariantsRefactored = 'regular';
    }

    return $justVariantsRefactored;
}

function getFontDownloadUrl($fontName,$fontVariants){
    // create & initialize a curl session
    $fontFormats = "eot,woff,woff2,ttf,svg";
    $downloadUrl = "https://google-webfonts-helper.herokuapp.com/api/fonts/".$fontName."?download=zip&subsets=latin&variants=".$fontVariants."&formats=".$fontFormats;
    
    return $downloadUrl;
}

$extractedGoogleFontUrl = extractGoogleFontsUrl($url);
echo $extractedGoogleFontUrl."<br>";

$formatedFontName = getFontName($extractedGoogleFontUrl);
echo $formatedFontName."<br>";

// echo $extractedGoogleFontUrl."<br><br><br>";
$formatedFontVariants = getFontVariants($extractedGoogleFontUrl);
echo $formatedFontVariants."<br>";

echo getFontDownloadUrl($formatedFontName, $formatedFontVariants);