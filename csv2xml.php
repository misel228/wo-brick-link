<?php

// Check if the file path is provided as the first command line parameter
if ($argv < 3) {
    echo "Usage: php " . $argv[0] . " <path_to_csv_file> <xml_output_file>\n";
    exit(1);
}

// Get the XML file path from the first command line argument
$csvFilePath = $argv[1];

// Check if the file exists
if (!file_exists($csvFilePath)) {
    echo "Error: File not found: $csvFilePath\n";
    exit(1);
}

// Load the CSV file
$csv = fopen($csvFilePath, 'r');
$csv_data = [];
$header = fgetcsv($csv);

while (($row = fgetcsv($csv)) != false) {
    $csv_data[] = array_combine($header, $row);
    echo '.';
}
echo "\n";


// Successfully loaded the XML file, print it
echo "Successfully loaded CSV file: $csvFilePath\n";

$output = fopen($argv[2], 'w');

if ($output === false) {
    echo "could not open output file\n";
    exit(3);
}

function getItemType($item_no)
{
    if (substr($item_no, 0, 2) == 'sp') {
        return 'M';
    }
    return 'P';
}

$xml_data = array_map(
    function ($item) {
        $item_type = getItemType($item['BLItemNo']);
        $xml_row =
            '<ITEM>'
            . '<ITEMTYPE>' . $item_type . '</ITEMTYPE>'
            . '<ITEMID>' . $item['BLItemNo'] . '</ITEMID>'
            . '<COLOR>' . $item['BLColorId'] . '</COLOR>'
            . '<MINQTY>' . $item['Qty'] . '</MINQTY>'
            . '</ITEM>';

            return $xml_row;
    },
    $csv_data
);

$xml = '<INVENTORY>' . implode("\n", $xml_data) . '</INVENTORY>';

fputs($output, $xml);
