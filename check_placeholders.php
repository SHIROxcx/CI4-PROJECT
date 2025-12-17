<?php

$zip = new ZipArchive();
if ($zip->open('public/assets/templates/moa_template.docx') === true) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    if ($xml !== false) {
        // Find all ${...} placeholders
        preg_match_all('/\$\{([^}]+)\}/', $xml, $matches);
        
        if (!empty($matches[1])) {
            echo "Found placeholders in template:\n";
            foreach (array_unique($matches[1]) as $placeholder) {
                echo "  - \${" . $placeholder . "}\n";
            }
        } else {
            echo "No \${...} placeholders found\n";
        }
        
        // Also check for ###...### markers
        preg_match_all('/###([^#]+)###/', $xml, $markers);
        if (!empty($markers[1])) {
            echo "\nFound markers in template:\n";
            foreach (array_unique($markers[1]) as $marker) {
                echo "  - ###" . $marker . "###\n";
            }
        } else {
            echo "\nNo ###...### markers found\n";
        }
    }
} else {
    echo "Failed to open template\n";
}
