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
    <sheet name="Chi so Nuoc" sheetId="1" r:id="rId1"/>
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
    <col min="4" max="4" width="18" customWidth="1"/> <!-- Chỉ Số Nước Cũ -->
    <col min="5" max="5" width="28" customWidth="1"/> <!-- Chỉ Số Nước Mới -->
  </cols>
  <sheetData>';

        // Row 1: Headers
        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr"><is><t>ID Căn Hộ</t></is></c>';
        $sheet1 .= '<c r="B1" t="inlineStr"><is><t>Tòa Nhà / Tầng</t></is></c>';
        $sheet1 .= '<c r="C1" t="inlineStr"><is><t>Số Căn Hộ</t></is></c>';
        $sheet1 .= '<c r="D1" t="inlineStr"><is><t>Chỉ Số Nước Cũ</t></is></c>';
        $sheet1 .= '<c r="E1" t="inlineStr"><is><t>Chỉ Số Nước Mới</t></is></c>';
        $sheet1 .= '</row>';

        $rowNum = 2;
        foreach ($apartments as $apt) {
            $blockName = $apt->floor->block->name ?? '—';
            $floorName = $apt->floor->name ?? '—';
            $location = htmlspecialchars("{$blockName} / {$floorName}", ENT_XML1, 'UTF-8');
            $aptNumber = htmlspecialchars($apt->apartment_number, ENT_XML1, 'UTF-8');

            $waterOld = \App\Models\UtilityMeter::getPreviousNewValue($apt->id, 'water', $month, $year) ?? 0;

            $sheet1 .= sprintf('<row r="%d">', $rowNum);
            // Cột A: ID
            $sheet1 .= sprintf('<c r="A%d"><v>%d</v></c>', $rowNum, $apt->id);
            // Cột B: Tòa/Tầng
            $sheet1 .= sprintf('<c r="B%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $location);
            // Cột C: Số phòng
            $sheet1 .= sprintf('<c r="C%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $aptNumber);
            // Cột D: Nước cũ
            $sheet1 .= sprintf('<c r="D%d" s="1"><v>%d</v></c>', $rowNum, $waterOld);
            // Cột E: Nước mới
            $sheet1 .= sprintf('<c r="E%d"/>', $rowNum);
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
                
                // Khởi tạo trước các cột từ A đến L để hỗ trợ nhiều cột dữ liệu
                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
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

    /**
     * Xuất file Excel mẫu để nhập Tòa/Tầng/Căn hộ
     */
    public static function exportApartmentTemplate()
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
    <sheet name="Apartment Layout" sheetId="1" r:id="rId1"/>
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
  <cellXfs count="3">
    <!-- s="0": Default (Normal) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- s="1": Number Format (cho so nguyen) -->
    <xf numFmtId="1" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="true"/>
    <!-- s="2": Decimal Format (cho dien tich) -->
    <xf numFmtId="2" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="true"/>
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
    <col min="1" max="1" width="22" customWidth="1"/>
    <col min="2" max="2" width="22" customWidth="1"/>
    <col min="3" max="3" width="25" customWidth="1"/>
    <col min="4" max="4" width="22" customWidth="1"/>
    <col min="5" max="5" width="25" customWidth="1"/>
    <col min="6" max="6" width="16" customWidth="1"/>
    <col min="7" max="7" width="22" customWidth="1"/>
    <col min="8" max="8" width="30" customWidth="1"/> <!-- Loại Tầng -->
    <col min="9" max="9" width="25" customWidth="1"/> <!-- Mô Tả Tầng -->
    <col min="10" max="10" width="18" customWidth="1"/>
    <col min="11" max="11" width="18" customWidth="1"/>
    <col min="12" max="12" width="28" customWidth="1"/>
    <col min="13" max="13" width="30" customWidth="1"/>
  </cols>
  <sheetData>';

        // Row 1: Headers
        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr"><is><t>Tên Tòa Nhà (Bắt buộc)</t></is></c>';
        $sheet1 .= '<c r="B1" t="inlineStr"><is><t>Mã Tòa Nhà (Tùy chọn)</t></is></c>';
        $sheet1 .= '<c r="C1" t="inlineStr"><is><t>Người Quản Lý Tòa (Tùy chọn)</t></is></c>';
        $sheet1 .= '<c r="D1" t="inlineStr"><is><t>Liên Hệ Quản Lý (Tùy chọn)</t></is></c>';
        $sheet1 .= '<c r="E1" t="inlineStr"><is><t>Mô Tả Tòa Nhà (Tùy chọn)</t></is></c>';
        $sheet1 .= '<c r="F1" t="inlineStr"><is><t>Số Tầng (Bắt buộc)</t></is></c>';
        $sheet1 .= '<c r="G1" t="inlineStr"><is><t>Tên Tầng (Bắt buộc)</t></is></c>';
        $sheet1 .= '<c r="H1" t="inlineStr"><is><t>Loại Tầng (Cư dân / Thương mại / Kỹ thuật / Tiện ích)</t></is></c>';
        $sheet1 .= '<c r="I1" t="inlineStr"><is><t>Mô Tả Tầng (Tùy chọn)</t></is></c>';
        $sheet1 .= '<c r="J1" t="inlineStr"><is><t>Số Căn Hộ (Bắt buộc)</t></is></c>';
        $sheet1 .= '<c r="K1" t="inlineStr"><is><t>Diện Tích m2 (Bắt buộc)</t></is></c>';
        $sheet1 .= '<c r="L1" t="inlineStr"><is><t>Trạng Thái (Trống / Đang ở / Bảo trì)</t></is></c>';
        $sheet1 .= '<c r="M1" t="inlineStr"><is><t>Mô Tả Căn Hộ (Tùy chọn)</t></is></c>';
        $sheet1 .= '</row>';

        // Row 2: Sample Data 1
        $sheet1 .= '<row r="2">';
        $sheet1 .= '<c r="A2" t="inlineStr"><is><t>Tòa A</t></is></c>';
        $sheet1 .= '<c r="B2" t="inlineStr"><is><t>BLOCK_A</t></is></c>';
        $sheet1 .= '<c r="C2" t="inlineStr"><is><t>Nguyễn Văn A</t></is></c>';
        $sheet1 .= '<c r="D2" t="inlineStr"><is><t>0912345678</t></is></c>';
        $sheet1 .= '<c r="E2" t="inlineStr"><is><t>Khu căn hộ cao cấp phía Đông</t></is></c>';
        $sheet1 .= '<c r="F2" s="1"><v>1</v></c>';
        $sheet1 .= '<c r="G2" t="inlineStr"><is><t>Tầng 1</t></is></c>';
        $sheet1 .= '<c r="H2" t="inlineStr"><is><t>Cư dân</t></is></c>';
        $sheet1 .= '<c r="I2" t="inlineStr"><is><t>Khu thương mại và căn hộ trệt</t></is></c>';
        $sheet1 .= '<c r="J2" t="inlineStr"><is><t>A101</t></is></c>';
        $sheet1 .= '<c r="K2" s="2"><v>75.50</v></c>';
        $sheet1 .= '<c r="L2" t="inlineStr"><is><t>Trống</t></is></c>';
        $sheet1 .= '<c r="M2" t="inlineStr"><is><t>Căn hộ gần sảnh chính</t></is></c>';
        $sheet1 .= '</row>';

        // Row 3: Sample Data 2
        $sheet1 .= '<row r="3">';
        $sheet1 .= '<c r="A3" t="inlineStr"><is><t>Tòa A</t></is></c>';
        $sheet1 .= '<c r="B3" t="inlineStr"><is><t>BLOCK_A</t></is></c>';
        $sheet1 .= '<c r="C3" t="inlineStr"><is><t>Nguyễn Văn A</t></is></c>';
        $sheet1 .= '<c r="D3" t="inlineStr"><is><t>0912345678</t></is></c>';
        $sheet1 .= '<c r="E3" t="inlineStr"><is><t>Khu căn hộ cao cấp phía Đông</t></is></c>';
        $sheet1 .= '<c r="F3" s="1"><v>1</v></c>';
        $sheet1 .= '<c r="G3" t="inlineStr"><is><t>Tầng 1</t></is></c>';
        $sheet1 .= '<c r="H3" t="inlineStr"><is><t>Cư dân</t></is></c>';
        $sheet1 .= '<c r="I3" t="inlineStr"><is><t>Khu thương mại và căn hộ trệt</t></is></c>';
        $sheet1 .= '<c r="J3" t="inlineStr"><is><t>A102</t></is></c>';
        $sheet1 .= '<c r="K3" s="2"><v>85.00</v></c>';
        $sheet1 .= '<c r="L3" t="inlineStr"><is><t>Đang ở</t></is></c>';
        $sheet1 .= '<c r="M3" t="inlineStr"><is><t>Căn hộ 2 phòng ngủ</t></is></c>';
        $sheet1 .= '</row>';

        // Row 4: Sample Data 3
        $sheet1 .= '<row r="4">';
        $sheet1 .= '<c r="A4" t="inlineStr"><is><t>Tòa B</t></is></c>';
        $sheet1 .= '<c r="B4" t="inlineStr"><is><t>BLOCK_B</t></is></c>';
        $sheet1 .= '<c r="C4" t="inlineStr"><is><t>Trần Thị B</t></is></c>';
        $sheet1 .= '<c r="D4" t="inlineStr"><is><t>0987654321</t></is></c>';
        $sheet1 .= '<c r="E4" t="inlineStr"><is><t>Khu chung cư xã hội phía Nam</t></is></c>';
        $sheet1 .= '<c r="F4" s="1"><v>2</v></c>';
        $sheet1 .= '<c r="G4" t="inlineStr"><is><t>Tầng 2</t></is></c>';
        $sheet1 .= '<c r="H4" t="inlineStr"><is><t>Thương mại</t></is></c>';
        $sheet1 .= '<c r="I4" t="inlineStr"><is><t>Tầng dân cư trung cấp</t></is></c>';
        $sheet1 .= '<c r="J4" t="inlineStr"><is><t>B201</t></is></c>';
        $sheet1 .= '<c r="K4" s="2"><v>68.20</v></c>';
        $sheet1 .= '<c r="L4" t="inlineStr"><is><t>Bảo trì</t></is></c>';
        $sheet1 .= '<c r="M4" t="inlineStr"><is><t>Đang sửa chữa điện nước</t></is></c>';
        $sheet1 .= '</row>';

        $sheet1 .= '  </sheetData>';
        $sheet1 .= '</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1);
        $zip->close();

        return $tempFile;
    }

    /**
     * Xuất báo cáo tài chính năm thành file Excel (.xlsx)
     */
    public static function exportFinanceReport($selectedYear, $monthlyRevenue, $totalBilled, $totalCollected, $totalUnpaid, $collectionRate, $serviceData = [])
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
    <sheet name="Bao Cao Tai Chinh" sheetId="1" r:id="rId1"/>
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
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font> <!-- Bold font -->
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
  <cellXfs count="3">
    <!-- s="0": Normal -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- s="1": Bold Headers -->
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
    <!-- s="2": Currency Format or normal number format -->
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
    <col min="1" max="1" width="20" customWidth="1"/>
    <col min="2" max="2" width="28" customWidth="1"/>
    <col min="3" max="3" width="28" customWidth="1"/>
    <col min="4" max="4" width="28" customWidth="1"/>
    <col min="5" max="5" width="20" customWidth="1"/>
    <col min="6" max="6" width="22" customWidth="1"/>
    <col min="7" max="7" width="22" customWidth="1"/>
    <col min="8" max="8" width="22" customWidth="1"/>
    <col min="9" max="9" width="22" customWidth="1"/>
  </cols>
  <sheetData>';

        // Tiêu đề báo cáo
        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr" s="1"><is><t>' . htmlspecialchars("BÁO CÁO TÀI CHÍNH NĂM " . $selectedYear, ENT_XML1, 'UTF-8') . '</t></is></c>';
        $sheet1 .= '</row>';
        $sheet1 .= '<row r="2"/>'; // Trống

        // Header cột
        $sheet1 .= '<row r="3">';
        $sheet1 .= '<c r="A3" t="inlineStr" s="1"><is><t>Tháng</t></is></c>';
        $sheet1 .= '<c r="B3" t="inlineStr" s="1"><is><t>Hóa đơn phát hành (VND)</t></is></c>';
        $sheet1 .= '<c r="C3" t="inlineStr" s="1"><is><t>Đã thu (VND)</t></is></c>';
        $sheet1 .= '<c r="D3" t="inlineStr" s="1"><is><t>Chưa thu (VND)</t></is></c>';
        $sheet1 .= '<c r="E3" t="inlineStr" s="1"><is><t>Tỷ lệ thu hồi (%)</t></is></c>';
        $sheet1 .= '<c r="F3" t="inlineStr" s="1"><is><t>Tiêu thụ Điện (VND)</t></is></c>';
        $sheet1 .= '<c r="G3" t="inlineStr" s="1"><is><t>Tiêu thụ Nước (VND)</t></is></c>';
        $sheet1 .= '<c r="H3" t="inlineStr" s="1"><is><t>Phí quản lý (VND)</t></is></c>';
        $sheet1 .= '<c r="I3" t="inlineStr" s="1"><is><t>Phí khác (VND)</t></is></c>';
        $sheet1 .= '</row>';

        $rowNum = 4;
        foreach ($monthlyRevenue as $row) {
            $monthStr = "Tháng " . str_pad($row->billing_month, 2, '0', STR_PAD_LEFT) . "/" . $row->billing_year;
            $billed = (float) $row->total_billed;
            $collected = (float) $row->total_collected;
            $unpaid = $billed - $collected;
            $rate = $billed > 0 ? round(($collected / $billed) * 100, 2) : 0;

            $m = (int)$row->billing_month;
            $elec = isset($serviceData[$m]['electricity']) ? (float)$serviceData[$m]['electricity'] : 0.0;
            $water = isset($serviceData[$m]['water']) ? (float)$serviceData[$m]['water'] : 0.0;
            $mgmt = isset($serviceData[$m]['management_fee']) ? (float)$serviceData[$m]['management_fee'] : 0.0;
            $other = isset($serviceData[$m]['other']) ? (float)$serviceData[$m]['other'] : 0.0;

            $sheet1 .= sprintf('<row r="%d">', $rowNum);
            $sheet1 .= sprintf('<c r="A%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $monthStr);
            $sheet1 .= sprintf('<c r="B%d" s="2"><v>%.2f</v></c>', $rowNum, $billed);
            $sheet1 .= sprintf('<c r="C%d" s="2"><v>%.2f</v></c>', $rowNum, $collected);
            $sheet1 .= sprintf('<c r="D%d" s="2"><v>%.2f</v></c>', $rowNum, $unpaid);
            $sheet1 .= sprintf('<c r="E%d"><v>%.2f</v></c>', $rowNum, $rate);
            $sheet1 .= sprintf('<c r="F%d" s="2"><v>%.2f</v></c>', $rowNum, $elec);
            $sheet1 .= sprintf('<c r="G%d" s="2"><v>%.2f</v></c>', $rowNum, $water);
            $sheet1 .= sprintf('<c r="H%d" s="2"><v>%.2f</v></c>', $rowNum, $mgmt);
            $sheet1 .= sprintf('<c r="I%d" s="2"><v>%.2f</v></c>', $rowNum, $other);
            $sheet1 .= '</row>';
            $rowNum++;
        }

        // Tính tổng cộng cho các loại dịch vụ
        $totalElec = 0.0;
        $totalWater = 0.0;
        $totalMgmt = 0.0;
        $totalOther = 0.0;
        if (!empty($serviceData)) {
            foreach ($serviceData as $m => $s) {
                $totalElec += $s['electricity'];
                $totalWater += $s['water'];
                $totalMgmt += $s['management_fee'];
                $totalOther += $s['other'];
            }
        }

        // Dòng tổng cộng
        $sheet1 .= sprintf('<row r="%d">', $rowNum);
        $sheet1 .= sprintf('<c r="A%d" t="inlineStr" s="1"><is><t>Tổng cộng năm %d</t></is></c>', $rowNum, $selectedYear);
        $sheet1 .= sprintf('<c r="B%d" s="1"><v>%.2f</v></c>', $rowNum, $totalBilled);
        $sheet1 .= sprintf('<c r="C%d" s="1"><v>%.2f</v></c>', $rowNum, $totalCollected);
        $sheet1 .= sprintf('<c r="D%d" s="1"><v>%.2f</v></c>', $rowNum, $totalUnpaid);
        $sheet1 .= sprintf('<c r="E%d" s="1"><v>%.2f</v></c>', $rowNum, $collectionRate);
        $sheet1 .= sprintf('<c r="F%d" s="1"><v>%.2f</v></c>', $rowNum, $totalElec);
        $sheet1 .= sprintf('<c r="G%d" s="1"><v>%.2f</v></c>', $rowNum, $totalWater);
        $sheet1 .= sprintf('<c r="H%d" s="1"><v>%.2f</v></c>', $rowNum, $totalMgmt);
        $sheet1 .= sprintf('<c r="I%d" s="1"><v>%.2f</v></c>', $rowNum, $totalOther);
        $sheet1 .= '</row>';

        $sheet1 .= '  </sheetData>';
        $sheet1 .= '</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1);
        $zip->close();

        return $tempFile;
    }

    /**
     * Xuất báo cáo Vận hành & SLA năm thành file Excel (.xlsx)
     */
    public static function exportOperationsReport($selectedYear, $tickets)
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
    <sheet name="Bao Cao Van Hanh" sheetId="1" r:id="rId1"/>
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
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font> <!-- Bold font -->
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
    <!-- s="0": Normal -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- s="1": Bold Headers -->
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
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
    <col min="1" max="1" width="10" customWidth="1"/> <!-- ID -->
    <col min="2" max="2" width="28" customWidth="1"/> <!-- Tiêu đề -->
    <col min="3" max="3" width="15" customWidth="1"/> <!-- Căn hộ -->
    <col min="4" max="4" width="15" customWidth="1"/> <!-- Tòa nhà -->
    <col min="5" max="5" width="22" customWidth="1"/> <!-- Người gửi -->
    <col min="6" max="6" width="22" customWidth="1"/> <!-- Người xử lý -->
    <col min="7" max="7" width="18" customWidth="1"/> <!-- Mức độ ưu tiên -->
    <col min="8" max="8" width="18" customWidth="1"/> <!-- Trạng thái -->
    <col min="9" max="9" width="12" customWidth="1"/> <!-- Điểm CSAT -->
    <col min="10" max="10" width="30" customWidth="1"/> <!-- Phản hồi -->
    <col min="11" max="11" width="20" customWidth="1"/> <!-- Tiếp nhận -->
    <col min="12" max="12" width="20" customWidth="1"/> <!-- Hoàn thành -->
  </cols>
  <sheetData>';

        // Tiêu đề báo cáo
        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr" s="1"><is><t>' . htmlspecialchars("BÁO CÁO VẬN HÀNH & SLA NĂM " . $selectedYear, ENT_XML1, 'UTF-8') . '</t></is></c>';
        $sheet1 .= '</row>';
        $sheet1 .= '<row r="2"/>';

        // Header cột
        $sheet1 .= '<row r="3">';
        $sheet1 .= '<c r="A3" t="inlineStr" s="1"><is><t>Mã phản ánh</t></is></c>';
        $sheet1 .= '<c r="B3" t="inlineStr" s="1"><is><t>Tiêu đề phản ánh</t></is></c>';
        $sheet1 .= '<c r="C3" t="inlineStr" s="1"><is><t>Căn hộ</t></is></c>';
        $sheet1 .= '<c r="D3" t="inlineStr" s="1"><is><t>Tòa nhà</t></is></c>';
        $sheet1 .= '<c r="E3" t="inlineStr" s="1"><is><t>Người gửi</t></is></c>';
        $sheet1 .= '<c r="F3" t="inlineStr" s="1"><is><t>Người xử lý</t></is></c>';
        $sheet1 .= '<c r="G3" t="inlineStr" s="1"><is><t>Độ ưu tiên</t></is></c>';
        $sheet1 .= '<c r="H3" t="inlineStr" s="1"><is><t>Trạng thái</t></is></c>';
        $sheet1 .= '<c r="I3" t="inlineStr" s="1"><is><t>Đánh giá CSAT</t></is></c>';
        $sheet1 .= '<c r="J3" t="inlineStr" s="1"><is><t>Ý kiến phản hồi</t></is></c>';
        $sheet1 .= '<c r="K3" t="inlineStr" s="1"><is><t>Thời gian gửi</t></is></c>';
        $sheet1 .= '<c r="L3" t="inlineStr" s="1"><is><t>Thời gian hoàn thành</t></is></c>';
        $sheet1 .= '</row>';

        $priorityMap = [
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            'urgent' => 'Khẩn cấp'
        ];
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'assigned' => 'Đã phân công',
            'in_progress' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã huỷ'
        ];

        $rowNum = 4;
        foreach ($tickets as $t) {
            $id = $t->id;
            $title = htmlspecialchars($t->title, ENT_XML1, 'UTF-8');
            $apartment = htmlspecialchars($t->apartment->apartment_number ?? 'N/A', ENT_XML1, 'UTF-8');
            $block = htmlspecialchars($t->apartment->floor->block->name ?? 'N/A', ENT_XML1, 'UTF-8');
            $sender = htmlspecialchars($t->sender->name ?? 'Cư dân', ENT_XML1, 'UTF-8');
            $handler = htmlspecialchars($t->handler->name ?? 'Chưa phân công', ENT_XML1, 'UTF-8');
            $priority = $priorityMap[$t->priority] ?? 'Trung bình';
            $status = $statusMap[$t->status] ?? 'Chờ xử lý';
            $rating = $t->rating ? $t->rating . " Sao" : 'Chưa đánh giá';
            $comment = htmlspecialchars($t->feedback_comment ?? '', ENT_XML1, 'UTF-8');
            
            $createdAt = $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '';
            $completedAt = ($t->status === 'completed' && $t->updated_at) ? \Carbon\Carbon::parse($t->updated_at)->format('d/m/Y H:i') : '';

            $sheet1 .= sprintf('<row r="%d">', $rowNum);
            $sheet1 .= sprintf('<c r="A%d"><v>%d</v></c>', $rowNum, $id);
            $sheet1 .= sprintf('<c r="B%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $title);
            $sheet1 .= sprintf('<c r="C%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $apartment);
            $sheet1 .= sprintf('<c r="D%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $block);
            $sheet1 .= sprintf('<c r="E%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $sender);
            $sheet1 .= sprintf('<c r="F%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $handler);
            $sheet1 .= sprintf('<c r="G%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $priority);
            $sheet1 .= sprintf('<c r="H%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $status);
            $sheet1 .= sprintf('<c r="I%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $rating);
            $sheet1 .= sprintf('<c r="J%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $comment);
            $sheet1 .= sprintf('<c r="K%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $createdAt);
            $sheet1 .= sprintf('<c r="L%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $completedAt);
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
     * Xuất báo cáo Cư dân & Hạ tầng thành file Excel (.xlsx)
     */
    public static function exportResidentsReport($apartments)
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
    <sheet name="Bao Cao Cu Dan" sheetId="1" r:id="rId1"/>
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
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font> <!-- Bold font -->
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
  <cellXfs count="3">
    <!-- s="0": Normal -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- s="1": Bold Headers -->
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
    <!-- s="2": Area Decimal Format -->
    <xf numFmtId="2" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="true"/>
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
    <col min="1" max="1" width="18" customWidth="1"/> <!-- Tòa nhà -->
    <col min="2" max="2" width="15" customWidth="1"/> <!-- Tầng -->
    <col min="3" max="3" width="18" customWidth="1"/> <!-- Số căn hộ -->
    <col min="4" max="4" width="15" customWidth="1"/> <!-- Diện tích -->
    <col min="5" max="5" width="22" customWidth="1"/> <!-- Trạng thái -->
    <col min="6" max="6" width="18" customWidth="1"/> <!-- Nhân khẩu -->
  </cols>
  <sheetData>';

        // Tiêu đề báo cáo
        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr" s="1"><is><t>THỐNG KÊ CHI TIẾT CĂN HỘ &amp; NHÂN KHẨU CHUNG CƯ</t></is></c>';
        $sheet1 .= '</row>';
        $sheet1 .= '<row r="2"/>';

        // Header cột
        $sheet1 .= '<row r="3">';
        $sheet1 .= '<c r="A3" t="inlineStr" s="1"><is><t>Tòa nhà</t></is></c>';
        $sheet1 .= '<c r="B3" t="inlineStr" s="1"><is><t>Tầng</t></is></c>';
        $sheet1 .= '<c r="C3" t="inlineStr" s="1"><is><t>Số căn hộ</t></is></c>';
        $sheet1 .= '<c r="D3" t="inlineStr" s="1"><is><t>Diện tích (m2)</t></is></c>';
        $sheet1 .= '<c r="E3" t="inlineStr" s="1"><is><t>Trạng thái căn hộ</t></is></c>';
        $sheet1 .= '<c r="F3" t="inlineStr" s="1"><is><t>Số nhân khẩu</t></is></c>';
        $sheet1 .= '</row>';

        $statusMap = [
            'occupied' => 'Đang có người ở',
            'vacant' => 'Còn trống',
            'maintenance' => 'Đang bảo trì'
        ];

        $rowNum = 4;
        foreach ($apartments as $apt) {
            $block = htmlspecialchars($apt->floor->block->name ?? 'N/A', ENT_XML1, 'UTF-8');
            $floor = htmlspecialchars($apt->floor->name ?? 'N/A', ENT_XML1, 'UTF-8');
            $aptNumber = htmlspecialchars($apt->apartment_number, ENT_XML1, 'UTF-8');
            $area = (float) $apt->area;
            $status = $statusMap[$apt->status] ?? 'Còn trống';
            $residentsCount = (int) $apt->resident_count;

            $sheet1 .= sprintf('<row r="%d">', $rowNum);
            $sheet1 .= sprintf('<c r="A%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $block);
            $sheet1 .= sprintf('<c r="B%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $floor);
            $sheet1 .= sprintf('<c r="C%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $aptNumber);
            $sheet1 .= sprintf('<c r="D%d" s="2"><v>%.2f</v></c>', $rowNum, $area);
            $sheet1 .= sprintf('<c r="E%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $status);
            $sheet1 .= sprintf('<c r="F%d"><v>%d</v></c>', $rowNum, $residentsCount);
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
     * Xuất báo cáo thống kê Tiện ích thành file Excel (.xlsx)
     */
    public static function exportAmenitiesReport($selectedYear, $selectedMonth, $facilities)
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
    <sheet name="Thong Ke Tien Ich" sheetId="1" r:id="rId1"/>
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
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font> <!-- Bold font -->
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
  <cellXfs count="3">
    <!-- s="0": Normal -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- s="1": Bold Headers -->
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>
    <!-- s="2": Currency Format -->
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
    <col min="1" max="1" width="25" customWidth="1"/> <!-- Tiện ích -->
    <col min="2" max="2" width="16" customWidth="1"/> <!-- Trạng thái -->
    <col min="3" max="3" width="16" customWidth="1"/> <!-- Tổng lịch đặt -->
    <col min="4" max="4" width="14" customWidth="1"/> <!-- Đã duyệt -->
    <col min="5" max="5" width="14" customWidth="1"/> <!-- Hoàn thành -->
    <col min="6" max="6" width="14" customWidth="1"/> <!-- Từ chối -->
    <col min="7" max="7" width="14" customWidth="1"/> <!-- Chờ duyệt -->
    <col min="8" max="8" width="22" customWidth="1"/> <!-- Doanh thu -->
    <col min="9" max="9" width="18" customWidth="1"/> <!-- Tỉ lệ duyệt -->
  </cols>
  <sheetData>';

        // Tiêu đề báo cáo
        $titleText = "BÁO CÁO THỐNG KÊ TIỆN ÍCH NĂM " . $selectedYear;
        if ($selectedMonth) {
            $titleText .= " THÁNG " . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
        }

        $sheet1 .= '<row r="1">';
        $sheet1 .= '<c r="A1" t="inlineStr" s="1"><is><t>' . htmlspecialchars($titleText, ENT_XML1, 'UTF-8') . '</t></is></c>';
        $sheet1 .= '</row>';
        $sheet1 .= '<row r="2"/>';

        // Header cột
        $sheet1 .= '<row r="3">';
        $sheet1 .= '<c r="A3" t="inlineStr" s="1"><is><t>Tiện ích</t></is></c>';
        $sheet1 .= '<c r="B3" t="inlineStr" s="1"><is><t>Trạng thái</t></is></c>';
        $sheet1 .= '<c r="C3" t="inlineStr" s="1"><is><t>Tổng lịch đặt</t></is></c>';
        $sheet1 .= '<c r="D3" t="inlineStr" s="1"><is><t>Đã duyệt</t></is></c>';
        $sheet1 .= '<c r="E3" t="inlineStr" s="1"><is><t>Hoàn thành</t></is></c>';
        $sheet1 .= '<c r="F3" t="inlineStr" s="1"><is><t>Từ chối</t></is></c>';
        $sheet1 .= '<c r="G3" t="inlineStr" s="1"><is><t>Chờ duyệt</t></is></c>';
        $sheet1 .= '<c r="H3" t="inlineStr" s="1"><is><t>Doanh thu (VND)</t></is></c>';
        $sheet1 .= '<c r="I3" t="inlineStr" s="1"><is><t>Tỉ lệ duyệt (%)</t></is></c>';
        $sheet1 .= '</row>';

        $statusMap = [
            'available' => 'Hoạt động',
            'maintenance' => 'Bảo trì',
            'closed' => 'Đóng cửa'
        ];

        $rowNum = 4;
        foreach ($facilities as $f) {
            $name = htmlspecialchars($f->name, ENT_XML1, 'UTF-8');
            $status = $statusMap[$f->status] ?? $f->status;
            $totalBookings = (int) $f->bookings_count;
            $approved = (int) $f->approved_count;
            $completed = (int) $f->completed_count;
            $rejected = (int) $f->rejected_count;
            $pending = (int) $f->pending_bookings_count;
            $revenue = (float) $f->revenue;
            
            // Tính tỷ lệ duyệt
            $totalProcessed = $approved + $completed + $rejected;
            $approvalRate = $totalProcessed > 0 ? round((($approved + $completed) / $totalProcessed) * 100, 1) : 0.0;

            $sheet1 .= sprintf('<row r="%d">', $rowNum);
            $sheet1 .= sprintf('<c r="A%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $name);
            $sheet1 .= sprintf('<c r="B%d" t="inlineStr"><is><t>%s</t></is></c>', $rowNum, $status);
            $sheet1 .= sprintf('<c r="C%d"><v>%d</v></c>', $rowNum, $totalBookings);
            $sheet1 .= sprintf('<c r="D%d"><v>%d</v></c>', $rowNum, $approved);
            $sheet1 .= sprintf('<c r="E%d"><v>%d</v></c>', $rowNum, $completed);
            $sheet1 .= sprintf('<c r="F%d"><v>%d</v></c>', $rowNum, $rejected);
            $sheet1 .= sprintf('<c r="G%d"><v>%d</v></c>', $rowNum, $pending);
            $sheet1 .= sprintf('<c r="H%d" s="2"><v>%.2f</v></c>', $rowNum, $revenue);
            $sheet1 .= sprintf('<c r="I%d"><v>%.1f</v></c>', $rowNum, $approvalRate);
            $sheet1 .= '</row>';
            
            $rowNum++;
        }

        // Dòng tổng cộng
        $sumBookings = 0;
        $sumApproved = 0;
        $sumCompleted = 0;
        $sumRejected = 0;
        $sumPending = 0;
        $sumRevenue = 0;
        foreach ($facilities as $f) {
            $sumBookings += $f->bookings_count;
            $sumApproved += $f->approved_count;
            $sumCompleted += $f->completed_count;
            $sumRejected += $f->rejected_count;
            $sumPending += $f->pending_bookings_count;
            $sumRevenue += $f->revenue;
        }
        $totalProcessedAll = $sumApproved + $sumCompleted + $sumRejected;
        $avgApprovalRate = $totalProcessedAll > 0 ? round((($sumApproved + $sumCompleted) / $totalProcessedAll) * 100, 1) : 0.0;

        $sheet1 .= sprintf('<row r="%d">', $rowNum);
        $sheet1 .= sprintf('<c r="A%d" t="inlineStr" s="1"><is><t>Tổng cộng</t></is></c>', $rowNum);
        $sheet1 .= sprintf('<c r="B%d"/>', $rowNum);
        $sheet1 .= sprintf('<c r="C%d" s="1"><v>%d</v></c>', $rowNum, $sumBookings);
        $sheet1 .= sprintf('<c r="D%d" s="1"><v>%d</v></c>', $rowNum, $sumApproved);
        $sheet1 .= sprintf('<c r="E%d" s="1"><v>%d</v></c>', $rowNum, $sumCompleted);
        $sheet1 .= sprintf('<c r="F%d" s="1"><v>%d</v></c>', $rowNum, $sumRejected);
        $sheet1 .= sprintf('<c r="G%d" s="1"><v>%d</v></c>', $rowNum, $sumPending);
        $sheet1 .= sprintf('<c r="H%d" s="1"><v>%.2f</v></c>', $rowNum, $sumRevenue);
        $sheet1 .= sprintf('<c r="I%d" s="1"><v>%.1f</v></c>', $rowNum, $avgApprovalRate);
        $sheet1 .= '</row>';

        $sheet1 .= '  </sheetData>';
        $sheet1 .= '</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1);
        $zip->close();

        return $tempFile;
    }
}
