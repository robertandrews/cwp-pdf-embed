<?php
/**
 * Plugin Name: PDF Embed
 * Description: Renders PDF links as embeds on the frontend.
 * Version: 1.0.0
 * Author: Your Name
 */

// Filter the content and replace PDF links with embeds
function pdf_embed_filter_content($content)
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

    $xpath = new DOMXPath($dom);

    $pdfLinks = $xpath->query('//a[contains(@href, ".pdf")]');

    foreach ($pdfLinks as $pdfLink) {
        $parentNode = $pdfLink->parentNode;

        // Check if the link is the sole content of the parent node
        if ($parentNode->childNodes->count() === 1 && $parentNode->firstChild === $pdfLink) {
            $embedCode = $dom->createElement('embed');
            $embedCode->setAttribute('src', $pdfLink->getAttribute('href'));
            $embedCode->setAttribute('type', 'application/pdf');
            $embedCode->setAttribute('width', '100%');
            $embedCode->setAttribute('height', '950px');

            $parentNode->parentNode->replaceChild($embedCode, $parentNode);
        }
    }

    $newContent = $dom->saveHTML();

    return $newContent;
}
add_filter('the_content', 'pdf_embed_filter_content');