<?php

/**
 * @param $text
 *
 * Converts XML format to simplified format delimited by "|"
 */

function xmlToCSV($text) {

    printOutputHeader();

    // use SimpleXML to parse the XML docs: http://php.net/manual/en/simplexml.examples-basic.php
    $structuredXml = new SimpleXMLElement($text);

    // go through every tour presented in xml file
    foreach ($structuredXml as $tour) {

        $title = $tour->Title;
        $code = $tour->Code;
        $duration = $tour->Duration;
        $inclusions = $tour->Inclusions;

        $minPrice = PHP_INT_MAX;

        // go through all listed prices
        foreach ($tour->DEP as $departure) {
            $eurPrice = $departure['EUR'];

            // apply discount if it is present in the data
            if(!empty($departure['DISCOUNT'])) {

                $floatDiscount = convertPercentageToFloat($departure['DISCOUNT']);
                assertDiscountPercentage($floatDiscount); // just to make sure we don't do too big discount
                $eurPrice *= (1 - $floatDiscount); // deduct discount from the price

                if($eurPrice < $minPrice) {
                    $minPrice = $eurPrice;
                }
            }
        }

        printCustomOutput($title, $code, $duration, $inclusions, $minPrice);
    }
}

/**
 * @param $percentageString
 * @return float|int
 *
 * convert percentage to float e.g. (25% to 0.25)
 */

function convertPercentageToFloat($percentageString) {
    $percentage = str_replace("%","", $percentageString);
    return $percentage/100;
}

/**
 * @param $floatDiscount
 * @throws Exception
 *
 * sanity check discount price
 *
 */

function assertDiscountPercentage($floatDiscount) {
    if($floatDiscount > 1) {
        throw new Exception("discount greater then 100% found");
    }
}

/**
 * @param $title
 * @param $code
 * @param $duration
 * @param $inclusions
 * @param $minPrice
 * @param string $delimiter
 *
 * prints out converted xml in desired format
 */

function printCustomOutput($title, $code, $duration, $inclusions, $minPrice, $delimiter = "|")
{
    echo decodeHtmlEntities($title) . $delimiter; // "Title" is a string, with html entities (like '&amp;') converted back to symbols
    echo (string)$code . $delimiter; // "Code" is a string
    echo (int)$duration . $delimiter; // "Duration" is an integer
    echo processInclusionText($inclusions) . $delimiter; // "Inclusions" is a string (just simple text: without html tags, double or triple spaces; with html entities converted back to symbols)
    printf("%0.2f", $minPrice); //"MinPrice" is a float with 2 digits after the decimal point; it's the minimal EUR value among all tour departures, taking into account the discount, if presented (for example, if the price is 1724 and the discount is "15%", then the departure price evaluates to 1465.40)
    echo "\n";
}

/**
 * @param $htmlEncodedText
 * @return mixed|string
 *
 * reverse html entities back to characters, &nbsp; is converted to regular space
 */

function decodeHtmlEntities($htmlEncodedText) {
    $out = str_replace("&nbsp;", " ", $htmlEncodedText); // replace &nbsp; for regular space as html_entity_decode produces no-breaking space
    $out = html_entity_decode($out); // revert all others html encoded entities
    return $out;
}

/**
 * @param $inclusions
 * @return mixed|string
 *
 * Formats inclusions string according to specification:
 * "Inclusions" is a string (just simple text: without html tags, double or triple spaces; with html entities converted back to symbols)
 */

function processInclusionText($inclusions) {
    $inclusions = strip_tags($inclusions); // removes html markup tags
    $inclusions = decodeHtmlEntities($inclusions);
    $inclusions = preg_replace('/\s+/', ' ', $inclusions); // \s+ finds a white space (1 or more in a row), It is replaced with one space
    $inclusions = trim($inclusions); // remove white space from beginning and end, they might be there because of formatting of xml input

    return $inclusions;
}


/**
 * prints the first row of output with format definition
 */
function printOutputHeader() {
    echo "Title|Code|Duration|Inclusions|MinPrice\n";
}
