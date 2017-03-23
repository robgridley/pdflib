# PDFlib Wrapper

I have to work with PDFlib on a daily basis and the standard API is painful. I have written this wrapper to make it suck less.

This package is provided as-is. It will receive regular updates, but these updates may break the API.

## Example

This example creates a new PDF, places a page from an existing PDF, and uses PDFlib PPS to fill a text block.

```php
use Pdf\PdfBuilder;

$pdf = new PdfBuilder;

$document = $pdf->import(file_get_contents('test.pdf'));
$page = $document->page(1, ['cloneBoxes']);

$pdf->addPage();
$pdf->placePage($page, 0, 0, ['cloneBoxes']);

$page->block('address')->fill('123 Fake St., Toronto, ON  M1A 1A1');

echo $pdf->render();
```

If you have ever had to use PDFlib, you can see how this approach is much easier to read and write.