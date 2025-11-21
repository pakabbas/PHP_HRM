<?php

class SimplePdf
{
    public static function downloadTable(string $title, array $headers, array $rows, string $filename = 'report.pdf'): void
    {
        $lines = [];
        $y = 780;
        $lines[] = self::textLine($title, 50, $y, 18);
        $y -= 30;

        $headerLine = implode(' | ', $headers);
        $lines[] = self::textLine($headerLine, 50, $y, 12);
        $y -= 18;
        $lines[] = self::textLine(str_repeat('-', strlen($headerLine)), 50, $y, 10);
        $y -= 18;

        foreach ($rows as $row) {
            $lineText = implode(' | ', array_map(fn($value) => (string) $value, $row));
            if ($y < 60) {
                // New page not supported in this minimal builder
                break;
            }
            $lines[] = self::textLine($lineText, 50, $y, 11);
            $y -= 16;
        }

        $pdf = self::buildPdf($lines);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    protected static function textLine(string $text, int $x, int $y, int $fontSize): string
    {
        $safeText = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        return "BT /F1 {$fontSize} Tf {$x} {$y} Td ({$safeText}) Tj ET";
    }

    protected static function buildPdf(array $contentLines): string
    {
        $content = implode("\n", $contentLines);
        $stream = "q\n1 0 0 1 0 0 cm\n{$content}\nQ";
        $length = strlen($stream);

        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>"; // 1
        $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>"; // 2
        $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>"; //3
        $objects[] = "<< /Length {$length} >>\nstream\n{$stream}\nendstream"; //4
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>"; //5

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n{$object}\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefPosition}\n%%EOF";
        return $pdf;
    }
}

