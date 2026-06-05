<?php

namespace App\Helpers;

class SimpleXlsx
{
    /**
     * Xuất danh sách căn hộ thành file Excel (.xlsx) có Protect Sheet
     */
    public static function exportUtilityTemplate($apartments, $month, $year)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new \ZipArchive();
        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Không thể tạo file Zip tạm thời.");
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Chi so Dien Nuoc" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 4. xl/_rels/workbook.xml.rels
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);

        // 5. xl/styles.xml
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="1">
    <font><sz val="11"/><name val="Calibri"/></font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="2">
    <!-- s="0": Default (Normal) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- s="1": Number Format (cho cac cot chi so) -->
    <xf numFmtId="1" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="true"/>
  </cellXfs>
  <cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0"/>
  </cellStyles>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheet1 = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <cols>
    <col min="1" max="1" width="25" customWidth="1"/>
    <col min="2" max="2" width="22" customWidth="1"/> <!-- Tòa Nhà / Tầng -->
    <col min="3" max="3" width="15" customWidth="1"/> <!-- Số Căn Hộ -->
    <col min="4" max="4" width="18" customWidth="1"/>
    <col min="5" max="5" width="28" customWidth="1"/>
    <col min="6" max="6" width="18" customWidth="1"/>
    <col min="7" max="7" width="28" customWidth="1"/>
  </cols>
  <sheetData>';

        // Row 1: Headers
        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr"><is><t>ID Căn Hộ</t></is></c>';
        $sheet1 .= '<c r="B1" t="inlineStr"><is><t>Tòa Nhà / Tầng</t></is></c>';
        $sheet1 .= '<c r="C1" t="inlineStr"><is><t>Số Căn Hộ</t></is></c>';
        $sheet1 .= '<c r="D1" t="inlineStr"><is><t>Chỉ Số Điện Cũ</t></is></c>';
        $sheet1 .= '<c r="E1" t="inlineStr"><is><t>Chỉ Số Điện Mới</t></is></c>';
        $sheet1 .= '<c r="F1" t="inlineStr"><is><t>Chỉ Số Nước Cũ</t></is></c>';
        $sheet1 .= '<c r="G1" t="inlineStr"><is><t>Chỉ Số Nước Mới</t></is></c>';
        $sheet1 .= '</row>';

        $rowNum = 2;
        foreach ($apartments as $apt) {
            $blockName = $apt->floor->block->name ?? '—';
            $floorName = $apt->floor->name ?? '—';
            $location = htmlspecialchars("{$blockName} / {$floorName}", ENT_XML1, 'UTF-8');
            $aptNumber = htmlspecialchars($apt->apartment_number, ENT_XML1, 'UTF-8');

            $elecOld  = \App\Models\UtilityMeter::getPreviousNewValue($apt->id, 'electricity', $month, $year) ?? 0;
            $waterOld = \App\Models\UtilityMeter::getPreviousNewValue($apt->id, 'water', $month, $year) ?? 0;

            $sheet1 .= sprintf('<row r="%d">', $rowNum);
            // Cột A: ID
            $sheet1 .= sprintf('<c r="A%d"><v>%d</v></c>', $rowNum, $apt->id);
            // Cột B: Tòa/Tầng
            $sheet1 .= sprintf('<c r="B%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $location);
            // Cột C: Số phòng
            $sheet1 .= sprintf('<c r="C%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $aptNumber);
            // Cột D: Điện cũ
            $sheet1 .= sprintf('<c r="D%d" s="1"><v>%d</v></c>', $rowNum, $elecOld);
            // Cột E: Điện mới
            $sheet1 .= sprintf('<c r="E%d"/>', $rowNum);
            // Cột F: Nước cũ
            $sheet1 .= sprintf('<c r="F%d" s="1"><v>%d</v></c>', $rowNum, $waterOld);
            // Cột G: Nước mới
            $sheet1 .= sprintf('<c r="G%d"/>', $rowNum);
            $sheet1 .= '</row>';

            $rowNum++;
        }

        $sheet1 .= '  </sheetData>';
        $sheet1 .= '</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1);
        $zip->close();

        return $tempFile;
    }

    /**
     * Đọc dữ liệu từ file Excel (.xlsx) tải lên
     */
    public static function parse($filePath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception("Không thể mở file Excel tải lên. Hãy chắc chắn đó là định dạng Excel (.xlsx).");
        }

        // 1. Đọc shared strings
        $sharedStrings = [];
        $sharedStringsEntry = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsEntry) {
            $xml = simplexml_load_string($sharedStringsEntry);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } else {
                        $text = '';
                        if (isset($si->r)) {
                            foreach ($si->r as $r) {
                                $text .= (string)$r->t;
                            }
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        // 2. Đọc sheet1
        $sheetEntry = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetEntry) {
            $zip->close();
            throw new \Exception("Không tìm thấy dữ liệu Sheet trong file Excel.");
        }

        $xml = simplexml_load_string($sheetEntry);
        if (!$xml) {
            $zip->close();
            throw new \Exception("Lỗi định dạng XML trong file Excel.");
        }

        $rows = [];
        if (isset($xml->sheetData->row)) {
            foreach ($xml->sheetData->row as $rowNode) {
                $rowIndex = (int)$rowNode['r'];
                $rowData = [];
                
                // Khởi tạo trước các cột từ A đến G
                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
                    $rowData[$col] = '';
                }

                if (isset($rowNode->c)) {
                    foreach ($rowNode->c as $cell) {
                        $ref = (string)$cell['r'];
                        preg_match('/^[A-Z]+/', $ref, $matches);
                        $col = $matches[0] ?? '';
                        
                        if (!$col) continue;

                        $val = '';
                        $t = (string)$cell['t'];

                        if ($t === 's') {
                            $idx = (int)$cell->v;
                            $val = $sharedStrings[$idx] ?? '';
                        } elseif ($t === 'inlineStr') {
                            $val = (string)$cell->is->t;
                        } elseif (isset($cell->v)) {
                            $val = (string)$cell->v;
                        }

                        $rowData[$col] = trim($val);
                    }
                }

                $rows[$rowIndex] = array_values($rowData);
            }
        }

        $zip->close();
        ksort($rows);
        return array_values($rows);
    }
}
