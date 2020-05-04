# PDFlib Wrapper

I use this PDFlib wrapper in my own projects. It is feature incomplete and provided as-is. Updates are irregular and they may break the API. Great sales pitch, eh?

## Example

This example creates a new PDF, places a page from an existing PDF, uses PDFlib PPS to fill a text block, and draws a red circle.

```php
use Pdf\Color\CmykColor;
use Pdf\Drawing;
use Pdf\PdfBuilder;

$pdf = new PdfBuilder;

$document = $pdf->import(file_get_contents('test.pdf'));
$page = $document->page(1, ['cloneBoxes']);

$pdf->addPage();
$pdf->placePage($page, 0, 0, ['cloneBoxes']);

$page->block('address')->fill('123 Fake St., Toronto, ON  M1A 1A1');

$pdf->draw(function (Drawing $drawing) {
    $drawing->stroke(new CmykColor(0, 100, 100, 0), 0.25)->circle(100, 100, 50)->paintStroke();
});

echo $pdf->render();
```
