<?php
namespace App\Utilities;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * ExportHelper - Reusable export functionality
 */
class ExportHelper {
    /**
     * Export data to CSV
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $filename Output filename
     */
    public static function exportCSV($data, $headers = [], $filename = 'export.csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // Add headers if provided
        if (!empty($headers)) {
            fputcsv($output, $headers);
        }
        
        // Add data rows
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($output, array_values($row));
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export data to JSON
     * 
     * @param array $data Data to export
     * @param string $filename Output filename
     */
    public static function exportJSON($data, $filename = 'export.json') {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Export data to Excel (XLS)
     *
     * Note: This generates an HTML table with an `.xls` extension.
     *       Excel and similar tools can open this as a spreadsheet
     *       without requiring any additional PHP libraries.
     *
     * @param array  $data     Data to export
     * @param array  $headers  Column headers
     * @param string $filename Output filename
     */
    public static function exportExcel($data, $headers = [], $filename = 'export.xls') {
        // Ensure .xls extension
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'xls') {
            $filename = preg_replace('/\.[^.]+$/', '', $filename) . '.xls';
        }

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
    </style>
</head>
<body>
<table>
    <thead>
        <tr>';

        // Headers row
        if (!empty($headers)) {
            foreach ($headers as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
        } elseif (!empty($data)) {
            foreach (array_keys($data[0]) as $key) {
                echo '<th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th>';
            }
        }

        echo '</tr>
    </thead>
    <tbody>';

        // Data rows
        foreach ($data as $row) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td>' . htmlspecialchars((string)$value) . '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody>
</table>
</body>
</html>';

        exit;
    }

    /**
     * Export data to PDF
     * Uses Dompdf when available; falls back to downloadable HTML.
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $title Document title
     * @param string $filename Output filename
     * @param string $extraHtml Additional HTML (e.g. charts/summary) appended after the table
     */
    public static function exportPDF($data, $headers = [], $title = 'Export', $filename = 'export.pdf', $extraHtml = '') {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #4CAF50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .section-title { margin-top: 30px; font-size: 18px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .summary-table { width: 50%; margin-top: 10px; }
        .summary-table th { background-color: #f0f0f0; color: #333; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($title) . '</h1>
    <p>Generated on: ' . date('F d, Y H:i:s') . '</p>
    <table>
        <thead>
            <tr>';
        
        if (!empty($headers)) {
            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
        } else {
            // Auto-generate headers from first row
            if (!empty($data)) {
                foreach (array_keys($data[0]) as $key) {
                    $html .= '<th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th>';
                }
            }
        }
        
        $html .= '</tr>
        </thead>
        <tbody>';
        
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . htmlspecialchars((string) $value) . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
    </table>';

        // Append any custom HTML (e.g. charts/summary sections)
        if (!empty($extraHtml)) {
            $html .= $extraHtml;
        }

        $html .= '
    <div class="footer">
        <p>Church Reporting & Administrative Portal</p>
    </div>
</body>
</html>';

        // If Dompdf is available, generate a real PDF and force download
        if (class_exists(Dompdf::class)) {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            echo $dompdf->output();
            exit;
        }

        // Fallback: downloadable HTML file if Dompdf isn't installed
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . str_replace('.pdf', '.html', $filename) . '"');
        
        echo $html;
        exit;
    }
}

