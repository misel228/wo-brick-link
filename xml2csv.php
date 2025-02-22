<?php

// Check if the file path is provided as the first command line parameter
if ($argv < 3) {
    echo "Usage: php " . $argv[0] . " <path_to_xml_file> <csv_output_file>\n";
    exit(1);
}

// Get the XML file path from the first command line argument
$xmlFilePath = $argv[1];

// Check if the file exists
if (!file_exists($xmlFilePath)) {
    echo "Error: File not found: $xmlFilePath\n";
    exit(1);
}

// Load the XML file
libxml_use_internal_errors(true);
$xml = simplexml_load_file($xmlFilePath);

// Check for errors during XML loading
if ($xml === false) {
    echo "Error: Failed to load XML file\n";
    foreach (libxml_get_errors() as $error) {
        echo $error->message . "\n";
    }
    exit(2);
}

// Successfully loaded the XML file, print it
echo "Successfully loaded XML file: $xmlFilePath\n";

$output = fopen($argv[2], 'w');
if ($output === false) {
    echo "could not open output file\n";
    exit(3);
}


// Query all <item> elements using XPath
$itemsNodes = $xml->xpath('/INVENTORY/ITEM');

$csv_rows = array_map(
    function ($item) {
        $csv = [
            'Part' => $item->ITEMID->__toString(),
            'Color' => $item->COLOR->__toString(),
            'Quantity' => $item->MINQTY->__toString(),
        ];
        return $csv;
    },
    $itemsNodes
);

$header = ['BLItemNo','BLColorId','Qty'];

fputcsv($output, $header);
foreach ($csv_rows as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit(0);
