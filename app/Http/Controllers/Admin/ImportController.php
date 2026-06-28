<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\TryoutPackage;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class ImportController extends Controller
{
    public function showForm(TryoutPackage $tryout)
    {
        return view('admin.tryouts.import', compact('tryout'));
    }

    public function wordPreview(Request $request, TryoutPackage $tryout)
    {
        if ($tryout->jenis_ujian === 'drill' && is_null($tryout->category_id)) {
            return back()->with('error', 'Kategori pada Paket belum dipilih.');
        }

        $request->validate([
            'file' => 'required|file|extensions:docx,zip',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filePath = $file->getRealPath();

        \Log::info("DOCX Import per Paket: File diterima. Nama: {$originalName}, Paket ID: {$tryout->id}");

        $warnings = [];
        $questions = [];
        $stats = [
            'tables_count' => 0,
            'rows_count' => 0,
            'soal_count' => 0,
            'jawaban_count' => 0,
            'pembahasan_count' => 0,
            'kunci_count' => 0,
            'images_count' => 0,
            'jenis_found' => [],
        ];

        $tempImportDir = public_path('storage/temp_import');
        if (!file_exists($tempImportDir)) {
            mkdir($tempImportDir, 0777, true);
        }

        // Gunakan parseDocxXml sebagai parser utama karena jauh lebih handal dalam mengekstrak semua jenis gambar (VML, Drawing, dll.) secara rekursif
        try {
            $questions = $this->parseDocxXml($filePath, $warnings, $stats, $tempImportDir);
        } catch (\Throwable $e) {
            \Log::error("DOCX Import per Paket: XML parser gagal. Pesan: " . $e->getMessage() . ". Mencoba fallback ke PHPWord.");
            
            if (class_exists('ZipArchive')) {
                try {
                    \PhpOffice\PhpWord\Settings::setTempDir($tempImportDir);
                    $phpWord = IOFactory::load($filePath);
                    $questions = $this->parseWordTables($phpWord, $warnings, $stats, $tempImportDir);
                } catch (\Throwable $e2) {
                    return back()->with('error', 'Gagal memproses file Word: ' . $e2->getMessage());
                }
            } else {
                return back()->with('error', 'Gagal memproses file Word: ' . $e->getMessage());
            }
        }

        if (empty($questions)) {
            return back()->with('error', "Format Word tidak sesuai dengan Template Import Smart CBT. Silakan unduh Template Word terlebih dahulu dan sesuaikan struktur dokumen sebelum melakukan import.");
        }

        session([
            'import_questions_' . $tryout->id => $questions,
            'import_warnings_' . $tryout->id => $warnings,
        ]);

        return view('admin.tryouts.import_preview', compact('questions', 'tryout', 'warnings'));
    }

    public function pdfPreview(Request $request, TryoutPackage $tryout)
    {
        if ($tryout->jenis_ujian === 'drill' && is_null($tryout->category_id)) {
            return back()->with('error', 'Kategori pada Paket belum dipilih.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf',
        ]);

        $file = $request->file('file');
        $parser = new \Smalot\PdfParser\Parser();
        try {
            $pdf = $parser->parseFile($file->getRealPath());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file PDF: ' . $e->getMessage());
        }

        $warnings = [];
        $tempDestDir = public_path('storage/temp_import');
        if (!file_exists($tempDestDir)) {
            mkdir($tempDestDir, 0777, true);
        }

        $extractedImages = [];
        try {
            $objects = $pdf->getObjects();
            foreach ($objects as $key => $object) {
                if ($object instanceof \Smalot\PdfParser\PDFObject && $object->getHeader()->get('Subtype') == 'Image') {
                    $data = $object->getContent();
                    if (!empty($data)) {
                        $ext = 'jpg';
                        $filter = $object->getHeader()->get('Filter');
                        if ($filter == 'FlateDecode') {
                            $ext = 'png';
                        }
                        $filename = time() . '_' . uniqid() . '_pdf_img.' . $ext;
                        file_put_contents($tempDestDir . '/' . $filename, $data);
                        $extractedImages[] = 'storage/temp_import/' . $filename;
                    }
                }
            }
        } catch (\Exception $e) {
            $warnings[] = "Gagal mengekstrak gambar dari PDF: " . $e->getMessage();
        }

        $fullText = '';
        try {
            $pages = $pdf->getPages();
            foreach ($pages as $page) {
                $fullText .= $page->getText() . "\n";
            }
        } catch (\Exception $e) {
            $fullText = $pdf->getText();
        }

        $lines = explode("\n", $fullText);
        $questions = [];
        $rawQuestions = [];
        $currentQuestion = null;
        $options = [];
        $lastState = null;

        $cleanText = function($str) {
            return trim(preg_replace('/[\x{00a0}\x{200b}\s]+/u', ' ', $str));
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            $parts = [];
            if (strpos($trimmed, '|') !== false) {
                $rawParts = explode('|', $trimmed);
                foreach ($rawParts as $p) {
                    $parts[] = $cleanText($p);
                }
            } else {
                $pattern = '/^(?:(\d+)\s+)?(SOAL|JAWABAN|PEMBAHASAN|PERTANYAAN|PILIHAN|OPSI|KUNCI\s+JAWABAN|KUNCI|PENJELASAN|SOLUSI|KETERANGAN)\s+(.*)$/i';
                if (preg_match($pattern, $trimmed, $matches)) {
                    $no = $matches[1] !== '' ? $matches[1] : '';
                    $jenis = strtoupper($matches[2]);
                    $rest = trim($matches[3]);
                    $isi = $rest;
                    $jawaban = '';

                    if (strpos($jenis, 'JAWABAN') !== false || strpos($jenis, 'PILIHAN') !== false || strpos($jenis, 'OPSI') !== false) {
                        if (preg_match('/^(.*)\s+(\d+)$/', $rest, $subMatches)) {
                            $isi = trim($subMatches[1]);
                            $jawaban = $subMatches[2];
                        }
                    }
                    $parts = [$no, $jenis, $isi, $jawaban];
                } else {
                    if ($currentQuestion && $lastState) {
                        if ($lastState === 'soal') {
                            $currentQuestion['soal'] .= "\n" . $line;
                        } elseif ($lastState === 'opsi' && !empty($options)) {
                            $options[count($options) - 1]['text'] .= "\n" . $line;
                        } elseif ($lastState === 'pembahasan') {
                            $currentQuestion['pembahasan'] .= "\n" . $line;
                        }
                    }
                    continue;
                }
            }

            if (count($parts) < 2) continue;

            $no = $parts[0] ?? '';
            $jenis = strtoupper($parts[1] ?? '');
            $isi = $parts[2] ?? '';
            $jawaban = $parts[3] ?? '';

            $isSoal = (strpos($jenis, 'SOAL') !== false || strpos($jenis, 'PERTANYAAN') !== false);
            $isJawaban = (strpos($jenis, 'JAWABAN') !== false || strpos($jenis, 'PILIHAN') !== false || strpos($jenis, 'OPSI') !== false);
            $isPembahasan = (strpos($jenis, 'PEMBAHASAN') !== false || strpos($jenis, 'PENJELASAN') !== false || strpos($jenis, 'SOLUSI') !== false);
            $isKunci = (strpos($jenis, 'KUNCI') !== false || strpos($jenis, 'JWB') !== false);

            if ($isSoal) {
                if ($currentQuestion) {
                    $rawQuestions[] = [
                        'q' => $currentQuestion,
                        'options' => $options
                    ];
                }
                $currentQuestion = [
                    'soal' => $isi,
                    'question_image' => null,
                    'tingkat_kesulitan' => 'sedang',
                    'pembahasan' => '',
                    'explanation_image' => null,
                    'has_explanation' => false,
                ];
                $options = [];
                $lastState = 'soal';
            } elseif ($isJawaban && $currentQuestion) {
                $score = ($jawaban !== '') ? (int)$jawaban : 0;
                $options[] = [
                    'text' => $isi,
                    'image' => null,
                    'score' => $score
                ];
                $lastState = 'opsi';
            } elseif ($isPembahasan && $currentQuestion) {
                $currentQuestion['pembahasan'] = $isi;
                $currentQuestion['has_explanation'] = true;
                $lastState = 'pembahasan';
            } elseif ($isKunci && $currentQuestion) {
                $kunciVal = strtoupper($cleanText($isi));
                if (preg_match('/([A-E])/i', $kunciVal, $matches)) {
                    $currentQuestion['jawaban_benar'] = $matches[1];
                }
                $lastState = 'kunci';
            }
        }

        if ($currentQuestion) {
            $rawQuestions[] = [
                'q' => $currentQuestion,
                'options' => $options
            ];
        }

        if (!empty($extractedImages) && !empty($rawQuestions)) {
            $slots = [];
            foreach ($rawQuestions as $qIdx => $raw) {
                $q = $raw['q'];
                $slots[] = [
                    'q_idx' => $qIdx,
                    'type' => 'question_image',
                    'text' => $q['soal'] ?? '',
                    'priority' => (empty($q['soal']) ? 1 : 2),
                    'opt_idx' => null,
                    'seq' => count($slots)
                ];
                foreach ($raw['options'] as $optIdx => $opt) {
                    $slots[] = [
                        'q_idx' => $qIdx,
                        'type' => 'option_image',
                        'text' => $opt['text'] ?? '',
                        'priority' => (empty($opt['text']) ? 1 : 3),
                        'opt_idx' => $optIdx,
                        'seq' => count($slots)
                    ];
                }
                if ($q['has_explanation']) {
                    $slots[] = [
                        'q_idx' => $qIdx,
                        'type' => 'explanation_image',
                        'text' => $q['pembahasan'] ?? '',
                        'priority' => (empty($q['pembahasan']) ? 1 : 2),
                        'opt_idx' => null,
                        'seq' => count($slots)
                    ];
                }
            }

            usort($slots, function($a, $b) {
                if ($a['priority'] !== $b['priority']) {
                    return $a['priority'] - $b['priority'];
                }
                return $a['seq'] - $b['seq'];
            });

            $imgCount = count($extractedImages);
            for ($i = 0; $i < $imgCount && $i < count($slots); $i++) {
                $slot = $slots[$i];
                if ($slot['type'] === 'question_image') {
                    $rawQuestions[$slot['q_idx']]['q']['question_image'] = $extractedImages[$i];
                } elseif ($slot['type'] === 'option_image') {
                    $rawQuestions[$slot['q_idx']]['options'][$slot['opt_idx']]['image'] = $extractedImages[$i];
                } elseif ($slot['type'] === 'explanation_image') {
                    $rawQuestions[$slot['q_idx']]['q']['explanation_image'] = $extractedImages[$i];
                }
            }
        }

        foreach ($rawQuestions as $raw) {
            $questions[] = $this->finalizeTableQuestion($raw['q'], $raw['options']);
        }

        if (empty($questions)) {
            return back()->with('error', 'Tidak ada soal valid yang berhasil diproses dari file PDF.');
        }

        session([
            'import_questions_' . $tryout->id => $questions,
            'import_warnings_' . $tryout->id => $warnings,
        ]);

        return view('admin.tryouts.import_preview', compact('questions', 'tryout', 'warnings'));
    }

    public function confirm(Request $request, TryoutPackage $tryout)
    {
        if ($tryout->jenis_ujian === 'drill' && is_null($tryout->category_id)) {
            return redirect()->route('admin.tryouts.show', $tryout)->with('error', 'Kategori pada Paket belum dipilih.');
        }

        $questions = session('import_questions_' . $tryout->id);
        if (!$questions) {
            return redirect()->route('admin.tryouts.show', $tryout)->with('error', 'Sesi impor telah kedaluwarsa.');
        }

        $destDir = public_path('storage/questions');
        if (!file_exists($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $moveFile = function($tempPath) use ($destDir) {
            if (!$tempPath || !file_exists(public_path($tempPath))) {
                return null;
            }
            $filename = time() . '_' . uniqid() . '_' . basename($tempPath);
            rename(public_path($tempPath), $destDir . '/' . $filename);
            return 'storage/questions/' . $filename;
        };

        try {
            \DB::beginTransaction();

            $importedCount = 0;
            $maxUrutan = $tryout->questions()->max('urutan') ?? 0;

            // Dukung format JSON (dari UI modern) maupun input array 'q' (dari unit test)
            if ($request->filled('questions_json')) {
                $overrides = json_decode($request->input('questions_json'), true) ?: [];
            } else {
                $overrides = $request->input('q', []);
            }

            $qCodeId = $tryout->question_code_id;
            $qCatId = $tryout->category_id;

            if ($tryout->jenis_ujian === 'tryout') {
                if (is_null($qCodeId)) {
                    $fallbackCode = \App\Models\QuestionCode::where('group_id', $tryout->group_id)->first();
                    if ($fallbackCode) {
                        $qCodeId = $fallbackCode->id;
                    }
                }
                if (is_null($qCatId)) {
                    if ($qCodeId) {
                        $fallbackCat = \App\Models\Category::where('question_code_id', $qCodeId)->first();
                        if ($fallbackCat) {
                            $qCatId = $fallbackCat->id;
                        }
                    } else {
                        $fallbackCat = \App\Models\Category::first();
                        if ($fallbackCat) {
                            $qCatId = $fallbackCat->id;
                            $qCodeId = $fallbackCat->question_code_id;
                        }
                    }
                }
            }

            foreach ($questions as $index => $qData) {
                $override = $overrides[$index] ?? [];
                $jawabanBenar = $override['jawaban_benar'] ?? $qData['jawaban_benar'] ?? 'A';
                $tingkatKesulitan = $override['tingkat_kesulitan'] ?? $qData['tingkat_kesulitan'] ?? 'sedang';
                $soalText = html_entity_decode(($override['soal'] ?? $qData['soal']) ?? '', ENT_QUOTES, 'UTF-8');
                $opsiA = html_entity_decode(($override['opsi_a'] ?? $qData['opsi_a']) ?? '', ENT_QUOTES, 'UTF-8');
                $opsiB = html_entity_decode(($override['opsi_b'] ?? $qData['opsi_b']) ?? '', ENT_QUOTES, 'UTF-8');
                $opsiC = html_entity_decode(($override['opsi_c'] ?? $qData['opsi_c']) ?? '', ENT_QUOTES, 'UTF-8');
                $opsiD = html_entity_decode(($override['opsi_d'] ?? $qData['opsi_d']) ?? '', ENT_QUOTES, 'UTF-8');
                $opsiE = html_entity_decode(($override['opsi_e'] ?? $qData['opsi_e']) ?? '', ENT_QUOTES, 'UTF-8');
                $pembahasan = html_entity_decode(($override['pembahasan'] ?? $qData['pembahasan']) ?? '', ENT_QUOTES, 'UTF-8');

                $scoreA = (int)($override['score_a'] ?? $qData['score_a'] ?? 0);
                $scoreB = (int)($override['score_b'] ?? $qData['score_b'] ?? 0);
                $scoreC = (int)($override['score_c'] ?? $qData['score_c'] ?? 0);
                $scoreD = (int)($override['score_d'] ?? $qData['score_d'] ?? 0);
                $scoreE = (int)($override['score_e'] ?? $qData['score_e'] ?? 0);

                // Validasi data penting sebelum insert
                if (is_null($tryout->group_id)) {
                    throw new \Exception("Group ID pada Paket tidak boleh kosong.");
                }
                if ($tryout->jenis_ujian === 'drill') {
                    if (is_null($tryout->question_code_id)) {
                        throw new \Exception("Kode Soal pada Paket tidak boleh kosong.");
                    }
                    if (is_null($tryout->category_id)) {
                        throw new \Exception("Kategori pada Paket tidak boleh kosong.");
                    }
                }

                $qImg = $moveFile($qData['question_image']);
                $optAImg = $moveFile($qData['option_a_image']);
                $optBImg = $moveFile($qData['option_b_image']);
                $optCImg = $moveFile($qData['option_c_image']);
                $optDImg = $moveFile($qData['option_d_image']);
                $optEImg = $moveFile($qData['option_e_image']);
                $expImg = $moveFile($qData['explanation_image']);

                Question::create([
                    'tryout_package_id' => $tryout->id,
                    'urutan'            => ++$maxUrutan,
                    'group_id'          => $tryout->group_id,
                    'question_code_id'  => $tryout->question_code_id ?? $qCodeId,
                    'category_id'       => $tryout->category_id ?? $qCatId,
                    'soal'              => $soalText,
                    'image'             => $qImg,
                    'question_image'    => $qImg,
                    'opsi_a'            => $opsiA,
                    'option_a_image'    => $optAImg,
                    'opsi_b'            => $opsiB,
                    'option_b_image'    => $optBImg,
                    'opsi_c'            => $opsiC,
                    'option_c_image'    => $optCImg,
                    'opsi_d'            => $opsiD,
                    'option_d_image'    => $optDImg,
                    'opsi_e'            => $opsiE !== '' ? $opsiE : null,
                    'option_e_image'    => $optEImg,
                    'score_a'           => $scoreA,
                    'score_b'           => $scoreB,
                    'score_c'           => $scoreC,
                    'score_d'           => $scoreD,
                    'score_e'           => $scoreE,
                    'jawaban_benar'     => $jawabanBenar,
                    'pembahasan'        => $pembahasan,
                    'explanation_image' => $expImg,
                    'tingkat_kesulitan' => $tingkatKesulitan,
                ]);

                $importedCount++;
            }

            \DB::commit();

            // Bersihkan file temporary dan session hanya setelah transaksi sukses commit
            $tempImportPath = public_path('storage/temp_import');
            if (file_exists($tempImportPath)) {
                $files = glob($tempImportPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }

            session()->forget([
                'import_questions_' . $tryout->id,
                'import_warnings_' . $tryout->id,
            ]);

            return redirect()->route('admin.tryouts.show', $tryout)->with('success', "$importedCount soal berhasil diimpor ke paket {$tryout->nama}.");

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error("Gagal melakukan impor soal. Error: " . $e->getMessage());
            return redirect()->route('admin.tryouts.show', $tryout)->with('error', 'Gagal menyimpan soal ke database: ' . $e->getMessage());
        }
    }

    private function extractImagesFromCellXml(\SimpleXMLElement $cell, string $docxPath, array $rels, string $tempImportDir): array
    {
        $images = [];
        $cell->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $cell->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $cell->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
        $cell->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $cell->registerXPathNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');

        // Scan seluruh atribut secara rekursif untuk mencari value berformat 'rId...'
        $rIds = [];
        $allAttributes = $cell->xpath('.//@*');
        if (!empty($allAttributes)) {
            foreach ($allAttributes as $attr) {
                $val = (string)$attr;
                if (strpos($val, 'rId') === 0) {
                    $rIds[] = $val;
                }
            }
        }

        $rIds = array_unique($rIds);

        foreach ($rIds as $rId) {
            if (isset($rels[$rId])) {
                $target = $rels[$rId];
                $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
                $imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'wmf', 'emf', 'svg', 'webp'];
                if (in_array($ext, $imageExtensions)) {
                    $zipPath = 'word/' . ltrim($target, '/');
                    $imageData = $this->readFileFromZip($docxPath, $zipPath);
                    if (!empty($imageData)) {
                        $filename = 'extracted_xml_img_' . uniqid() . '.' . $ext;
                        $fullPath = $tempImportDir . '/' . $filename;
                        if (file_put_contents($fullPath, $imageData) !== false) {
                            $images[] = 'storage/temp_import/' . $filename;
                            \Log::info("DOCX XML Parser: Berhasil mengekstrak gambar dari cell: {$zipPath}");
                        }
                    }
                }
            }
        }

        return $images;
    }

    private function parseDocxXml(string $docxPath, array &$warnings, array &$stats, string $tempImportDir): array
    {
        \Log::info("DOCX XML Parser: Mulai parsing | ZipArchive: " . (class_exists('ZipArchive') ? 'Ya' : 'Tidak'));
        $xmlContent = $this->readFileFromZip($docxPath, 'word/document.xml');

        if (empty($xmlContent)) {
            throw new \Exception("Tidak dapat membaca word/document.xml dari file DOCX.");
        }

        $relsContent = $this->readFileFromZip($docxPath, 'word/_rels/document.xml.rels');
        $rels = [];
        if (!empty($relsContent)) {
            try {
                $relsXml = new \SimpleXMLElement($relsContent, LIBXML_NOERROR | LIBXML_NOWARNING);
                foreach ($relsXml->Relationship as $rel) {
                    $id = (string)$rel['Id'];
                    $target = (string)$rel['Target'];
                    $rels[$id] = $target;
                }
            } catch (\Exception $e) {
                \Log::warning("DOCX XML Parser: Gagal membaca document.xml.rels: " . $e->getMessage());
            }
        }

        try {
            $xml = new \SimpleXMLElement($xmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        } catch (\Exception $e) {
            throw new \Exception("Gagal parse XML dokumen Word: " . $e->getMessage());
        }

        $namespaces = $xml->getNamespaces(true);
        $wNs = $namespaces['w'] ?? 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $xml->registerXPathNamespace('w', $wNs);

        $tables = $xml->xpath('//w:tbl');
        if (empty($tables)) $tables = $xml->xpath('w:body/w:tbl');

        $stats['tables_count'] = count($tables ?? []);
        if (empty($tables)) return [];

        $cleanText = function($str) {
            return trim(preg_replace('/[\x{00a0}\x{200b}\s]+/u', ' ', $str));
        };

        $extractCellText = function(\SimpleXMLElement $cell): string {
            $paragraphs = $cell->xpath('.//w:p');
            if (empty($paragraphs)) {
                $result = '';
                foreach ($cell->xpath('.//w:t') as $t) $result .= (string)$t;
                return $result;
            }
            $paraTexts = [];
            foreach ($paragraphs as $p) {
                $pt = '';
                foreach ($p->xpath('.//w:t') as $t) $pt .= (string)$t;
                if ($pt !== '') $paraTexts[] = $pt;
            }
            return implode("\n", $paraTexts);
        };

        $questions = [];

        foreach ($tables as $tableIndex => $table) {
            $rows = $table->xpath('.//w:tr');
            $stats['rows_count'] += count($rows);

            $jenisColIndex = null; $isiColIndex = null; $jawabanColIndex = null; $headerMapped = false;
            $currentQuestion = null; $options = []; $qNum = count($questions) + 1;

            foreach ($rows as $row) {
                $cells = $row->xpath('.//w:tc');
                if (count($cells) < 3) continue;

                if (!$headerMapped) {
                    $cellTexts = [];
                    foreach ($cells as $ci => $cell) {
                        $cellTexts[$ci] = strtolower($cleanText($extractCellText($cell)));
                    }
                    if (in_array('jenis', $cellTexts) && in_array('isi', $cellTexts)) {
                        $jenisColIndex   = array_search('jenis', $cellTexts);
                        $isiColIndex     = array_search('isi', $cellTexts);
                        $jawabanColIndex = array_search('jawaban', $cellTexts);
                        if ($jawabanColIndex === false) {
                            foreach ($cellTexts as $idx => $txt) {
                                if (strpos($txt, 'jawab') !== false) { $jawabanColIndex = $idx; break; }
                            }
                        }
                        $headerMapped = true;
                        continue;
                    }
                }

                $n = count($cells);
                $jIdx  = $jenisColIndex  ?? ($n >= 4 ? 1 : 0);
                $iIdx  = $isiColIndex    ?? ($n >= 4 ? 2 : 1);
                $jwIdx = $jawabanColIndex ?? ($n >= 4 ? 3 : 2);

                if (!isset($cells[$jIdx]) || !isset($cells[$iIdx])) continue;

                $jenisUpper  = strtoupper($cleanText($extractCellText($cells[$jIdx])));
                $isiText     = trim($extractCellText($cells[$iIdx]));
                $jawabanText = isset($cells[$jwIdx]) ? $cleanText($extractCellText($cells[$jwIdx])) : '';

                $isiImages = $this->extractImagesFromCellXml($cells[$iIdx], $docxPath, $rels, $tempImportDir);
                $stats['images_count'] += count($isiImages);

                if (!empty($jenisUpper)) $stats['jenis_found'][] = $jenisUpper;

                $isSoal      = strpos($jenisUpper, 'SOAL') !== false || strpos($jenisUpper, 'PERTANYAAN') !== false;
                $isJawaban   = strpos($jenisUpper, 'JAWABAN') !== false || strpos($jenisUpper, 'PILIHAN') !== false || strpos($jenisUpper, 'OPSI') !== false;
                $isPembahasan = strpos($jenisUpper, 'PEMBAHASAN') !== false || strpos($jenisUpper, 'PENJELASAN') !== false;
                $isKunci     = strpos($jenisUpper, 'KUNCI') !== false || strpos($jenisUpper, 'JWB') !== false;

                if ($isSoal) {
                    $stats['soal_count']++;
                    if ($currentQuestion) {
                        $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                        $qNum++;
                    }
                    $currentQuestion = [
                        'soal' => $isiText,
                        'question_image' => $this->mergeImagesVertically($isiImages, $tempImportDir),
                        'tingkat_kesulitan' => 'sedang',
                        'pembahasan' => '',
                        'explanation_image' => null,
                    ];
                    $options = [];
                } elseif ($isJawaban && $currentQuestion) {
                    $stats['jawaban_count']++;
                    $options[] = [
                        'text' => $isiText,
                        'image' => $this->mergeImagesVertically($isiImages, $tempImportDir),
                        'score' => (int)$jawabanText
                    ];
                } elseif ($isPembahasan && $currentQuestion) {
                    $stats['pembahasan_count']++;
                    $currentQuestion['pembahasan'] = $isiText;
                    $currentQuestion['explanation_image'] = $this->mergeImagesVertically($isiImages, $tempImportDir);
                } elseif ($isKunci && $currentQuestion) {
                    $stats['kunci_count']++;
                    if (preg_match('/([A-E])/i', $isiText . $jawabanText, $m)) {
                        $currentQuestion['jawaban_benar'] = strtoupper($m[1]);
                    }
                }
            }

            if ($currentQuestion) {
                $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
            }
        }

        return $questions;
    }

    private function readFileFromZip(string $zipPath, string $entryName): ?string
    {
        if (in_array('zip', stream_get_wrappers())) {
            $content = @file_get_contents("zip://{$zipPath}#{$entryName}");
            if (!empty($content)) {
                return $content;
            }
        }

        if (class_exists('ZipArchive')) {
            try {
                $zip = new \ZipArchive();
                if ($zip->open($zipPath) === true) {
                    $content = $zip->getFromName($entryName);
                    $zip->close();
                    if (!empty($content)) {
                        return $content;
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning("readFileFromZip: ZipArchive gagal: " . $e->getMessage());
            }
        }

        if (class_exists('PharData')) {
            $tmpZip = null;
            try {
                $tmpZip = sys_get_temp_dir() . '/docx_' . uniqid() . '.zip';
                if (copy($zipPath, $tmpZip)) {
                    $phar = new \PharData($tmpZip);
                    if (isset($phar[$entryName])) {
                        $content = file_get_contents($phar[$entryName]->getPathname());
                        @unlink($tmpZip);
                        if (!empty($content)) {
                            return $content;
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning("readFileFromZip: PharData gagal: " . $e->getMessage());
            } finally {
                if ($tmpZip && file_exists($tmpZip)) @unlink($tmpZip);
            }
        }

        return $this->readFileFromZipBinary($zipPath, $entryName);
    }

    private function readFileFromZipBinary(string $zipPath, string $entryName): ?string
    {
        $fp = @fopen($zipPath, 'rb');
        if (!$fp) return null;

        try {
            while (!feof($fp)) {
                $sig = fread($fp, 4);
                if ($sig === false || strlen($sig) < 4) break;
                if ($sig !== "PK\x03\x04") break;

                $header = fread($fp, 26);
                if ($header === false || strlen($header) < 26) break;

                $data = unpack('vversion/vflags/vcompression/vlastmod_time/vlastmod_date/Vcrc32/Vcompressed_size/Vuncompressed_size/vfilename_len/vextra_len', $header);
                $filename = $data['filename_len'] > 0 ? fread($fp, $data['filename_len']) : '';
                if ($data['extra_len'] > 0) {
                    fread($fp, $data['extra_len']);
                }

                if ($filename === false || $filename === '') break;

                if ($filename === $entryName) {
                    $compressedData = $data['compressed_size'] > 0 ? fread($fp, $data['compressed_size']) : '';
                    fclose($fp);

                    if ($data['compression'] === 0) {
                        return $compressedData;
                    } elseif ($data['compression'] === 8) {
                        $result = @gzinflate($compressedData);
                        if ($result !== false) {
                            return $result;
                        }
                        return null;
                    } else {
                        return null;
                    }
                } else {
                    if ($data['compressed_size'] > 0) fseek($fp, $data['compressed_size'], SEEK_CUR);
                }
            }
        } finally {
            if (is_resource($fp)) fclose($fp);
        }

        return null;
    }

    private function parseWordTables($phpWord, &$warnings, &$stats = null, $tempImportDir = null)
    {
        if (!$tempImportDir) {
            $tempImportDir = public_path('storage/temp_import');
        }

        if ($stats === null) {
            $stats = [
                'tables_count' => 0,
                'rows_count' => 0,
                'soal_count' => 0,
                'jawaban_count' => 0,
                'pembahasan_count' => 0,
                'kunci_count' => 0,
                'images_count' => 0,
                'jenis_found' => [],
            ];
        }

        $questions = [];
        $tableIndex = 0;

        $cleanText = function($str) {
            $str = preg_replace('/[\x{00a0}\x{200b}\s]+/u', ' ', $str);
            return trim($str);
        };

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $tableIndex++;
                    $stats['tables_count'] = $tableIndex;
                    
                    $currentQuestion = null;
                    $options = [];
                    $qNum = count($questions) + 1;

                    $rows = $element->getRows();
                    $stats['rows_count'] += count($rows);

                    $jenisColIndex = null;
                    $isiColIndex = null;
                    $jawabanColIndex = null;
                    $headerMapped = false;

                    foreach ($rows as $rowIndex => $row) {
                        $cells = $row->getCells();
                        if (count($cells) < 3) {
                            continue;
                        }

                        if (!$headerMapped) {
                            $cellTexts = [];
                            foreach ($cells as $cellIndex => $cell) {
                                $txt = '';
                                $imgs = [];
                                $this->extractElementContent($cell, $txt, $imgs);
                                $cellTexts[$cellIndex] = strtolower($cleanText($txt));
                            }

                            $hasJenis = in_array('jenis', $cellTexts);
                            $hasIsi = in_array('isi', $cellTexts);
                            
                            if ($hasJenis && $hasIsi) {
                                $jenisColIndex = array_search('jenis', $cellTexts);
                                $isiColIndex = array_search('isi', $cellTexts);
                                $jawabanColIndex = array_search('jawaban', $cellTexts);
                                if ($jawabanColIndex === false) {
                                    foreach ($cellTexts as $idx => $txt) {
                                        if (strpos($txt, 'jawab') !== false) {
                                            $jawabanColIndex = $idx;
                                            break;
                                        }
                                    }
                                }
                                $headerMapped = true;
                                continue;
                            }
                        }

                        $jIdx = $jenisColIndex;
                        $iIdx = $isiColIndex;
                        $jwIdx = $jawabanColIndex;

                        if ($jIdx === null || $iIdx === null) {
                            if (count($cells) >= 4) {
                                $jIdx = 1;
                                $iIdx = 2;
                                $jwIdx = 3;
                            } else {
                                $jIdx = 0;
                                $iIdx = 1;
                                $jwIdx = 2;
                            }
                        }

                        if (!isset($cells[$jIdx]) || !isset($cells[$iIdx])) {
                            continue;
                        }

                        $jenisCell = $cells[$jIdx];
                        $isiCell = $cells[$iIdx];
                        $jawabanCell = ($jwIdx !== null && isset($cells[$jwIdx])) ? $cells[$jwIdx] : null;

                        $jenisText = '';
                        $jenisImages = [];
                        $this->extractElementContent($jenisCell, $jenisText, $jenisImages);
                        
                        $jenisUpper = strtoupper($cleanText($jenisText));
                        if (!empty($jenisUpper)) {
                            $stats['jenis_found'][] = $jenisUpper;
                        }

                        $isSoal = (strpos($jenisUpper, 'SOAL') !== false || strpos($jenisUpper, 'PERTANYAAN') !== false);
                        $isJawaban = (strpos($jenisUpper, 'JAWABAN') !== false || strpos($jenisUpper, 'PILIHAN') !== false || strpos($jenisUpper, 'OPSI') !== false);
                        $isPembahasan = (strpos($jenisUpper, 'PEMBAHASAN') !== false || strpos($jenisUpper, 'PENJELASAN') !== false || strpos($jenisUpper, 'SOLUSI') !== false || strpos($jenisUpper, 'KETERANGAN') !== false);
                        $isKunci = (strpos($jenisUpper, 'KUNCI') !== false || strpos($jenisUpper, 'JWB') !== false);

                        if ($isSoal) {
                            $stats['soal_count']++;
                            if ($currentQuestion) {
                                $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                                $qNum++;
                            }

                            $hasImg = $this->hasImageElement($isiCell);
                            $isiText = '';
                            $isiImages = [];
                            $this->extractElementContent($isiCell, $isiText, $isiImages);
                            $stats['images_count'] += count($isiImages);

                            $currentQuestion = [
                                'soal' => trim($isiText),
                                'question_image' => $this->mergeImagesVertically($isiImages, $tempImportDir),
                                'tingkat_kesulitan' => 'sedang',
                                'pembahasan' => '',
                                'explanation_image' => null,
                            ];

                            if ($hasImg && empty($isiImages)) {
                                $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar soal pada Soal #{$qNum}.";
                                $currentQuestion['review_required'] = true;
                            }

                            $options = [];
                        } elseif ($isJawaban) {
                            $stats['jawaban_count']++;
                            if ($currentQuestion) {
                                $hasImg = $this->hasImageElement($isiCell);
                                $isiText = '';
                                $isiImages = [];
                                $this->extractElementContent($isiCell, $isiText, $isiImages);
                                $stats['images_count'] += count($isiImages);

                                $jawabanText = '';
                                $jawabanImages = [];
                                if ($jawabanCell) {
                                    $this->extractElementContent($jawabanCell, $jawabanText, $jawabanImages);
                                }
                                $score = (int)$cleanText($jawabanText);

                                $options[] = [
                                    'text' => trim($isiText),
                                    'image' => $this->mergeImagesVertically($isiImages, $tempImportDir),
                                    'score' => $score
                                ];

                                if ($hasImg && empty($isiImages)) {
                                    $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar opsi pada Soal #{$qNum}, Opsi " . chr(65 + count($options) - 1) . ".";
                                    $currentQuestion['review_required'] = true;
                                }
                            }
                        } elseif ($isPembahasan) {
                            $stats['pembahasan_count']++;
                            if ($currentQuestion) {
                                $hasImg = $this->hasImageElement($isiCell);
                                $isiText = '';
                                $isiImages = [];
                                $this->extractElementContent($isiCell, $isiText, $isiImages);
                                $stats['images_count'] += count($isiImages);

                                $currentQuestion['pembahasan'] = trim($isiText);
                                $currentQuestion['explanation_image'] = $this->mergeImagesVertically($isiImages, $tempImportDir);

                                if ($hasImg && empty($isiImages)) {
                                    $warnings[] = "REVIEW REQUIRED: Gagal mengekstrak gambar pembahasan pada Soal #{$qNum}.";
                                    $currentQuestion['review_required'] = true;
                                }
                            }
                        } elseif ($isKunci) {
                            $stats['kunci_count']++;
                            if ($currentQuestion) {
                                $kunciText = '';
                                $kunciImages = [];
                                $this->extractElementContent($isiCell, $kunciText, $kunciImages);

                                $kunciVal = strtoupper($cleanText($kunciText));
                                if (preg_match('/([A-E])/i', $kunciVal, $matches)) {
                                    $currentQuestion['jawaban_benar'] = $matches[1];
                                }
                            }
                        }
                    }

                    if ($currentQuestion) {
                        $questions[] = $this->finalizeTableQuestion($currentQuestion, $options);
                    }
                }
            }
        }

        return $questions;
    }

    private function extractElementContent($element, &$text, &$images)
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            $text .= "\n";
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $child) {
                $this->extractElementContent($child, $text, $images);
            }
            $text .= "\n";
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Title) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText();
            }
            if (method_exists($element, 'getElements')) {
                foreach ($element->getElements() as $child) {
                    $this->extractElementContent($child, $text, $images);
                }
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            $imgPath = $this->savePhpWordImage($element);
            if ($imgPath) {
                $images[] = $imgPath;
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Shape || $element instanceof \PhpOffice\PhpWord\Element\Object) {
            $imgPath = $this->savePhpWordImage($element);
            if ($imgPath) {
                $images[] = $imgPath;
            }
        } elseif (method_exists($element, 'getSource') || method_exists($element, 'getImageString') || method_exists($element, 'getImageBinary')) {
            if (!($element instanceof \PhpOffice\PhpWord\Element\Text) && !($element instanceof \PhpOffice\PhpWord\Element\TextRun)) {
                $imgPath = $this->savePhpWordImage($element);
                if ($imgPath) {
                    $images[] = $imgPath;
                }
            }
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $this->extractElementContent($child, $text, $images);
            }
        }
    }

    private function hasImageElement($element)
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\Image || 
            $element instanceof \PhpOffice\PhpWord\Element\Shape ||
            $element instanceof \PhpOffice\PhpWord\Element\Object) {
            return true;
        }
        if (method_exists($element, 'getSource') || method_exists($element, 'getImageString') || method_exists($element, 'getImageBinary')) {
            if (!($element instanceof \PhpOffice\PhpWord\Element\Text) && !($element instanceof \PhpOffice\PhpWord\Element\TextRun)) {
                return true;
            }
        }
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                if ($this->hasImageElement($child)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function savePhpWordImage($imageElement)
    {
        $source = null;
        if (method_exists($imageElement, 'getSource')) {
            $source = $imageElement->getSource();
        }
        
        $imageData = null;
        $ext = 'png';

        if ($source && strpos($source, 'zip://') === 0) {
            $archivePath = substr($source, 6);
            $hashPos = strrpos($archivePath, '#');
            if ($hashPos !== false) {
                $zipFile = substr($archivePath, 0, $hashPos);
                $entryName = substr($archivePath, $hashPos + 1);
                
                $imageData = $this->readFileFromZip($zipFile, $entryName);
                if (!empty($imageData)) {
                    $ext = pathinfo($entryName, PATHINFO_EXTENSION) ?: 'png';
                }
            }
        }

        if (empty($imageData)) {
            if (empty($source)) {
                if (method_exists($imageElement, 'getImageString')) {
                    $imageData = $imageElement->getImageString();
                } elseif (method_exists($imageElement, 'getImageBinary')) {
                    $imageData = $imageElement->getImageBinary();
                }
            } else {
                if (strpos($source, 'data:') === 0) {
                    $parts = explode(',', $source);
                    if (isset($parts[1])) {
                        $imageData = base64_decode($parts[1]);
                        if (preg_match('/data:image\/(\w+);base64/', $parts[0], $matches)) {
                            $ext = $matches[1];
                        }
                    }
                } elseif (file_exists($source)) {
                    $imageData = file_get_contents($source);
                    $ext = pathinfo($source, PATHINFO_EXTENSION) ?: 'png';
                } else {
                    if (method_exists($imageElement, 'getImageString')) {
                        $imageData = $imageElement->getImageString();
                    } elseif (method_exists($imageElement, 'getImageBinary')) {
                        $imageData = $imageElement->getImageBinary();
                    }
                    $ext = pathinfo($source, PATHINFO_EXTENSION) ?: 'png';
                }
            }
        }

        if ($imageData) {
            $tempDestDir = public_path('storage/temp_import');
            if (!file_exists($tempDestDir)) {
                mkdir($tempDestDir, 0777, true);
            }
            $filename = 'extracted_img_' . uniqid() . '.' . $ext;
            $fullPath = $tempDestDir . '/' . $filename;
            if (file_put_contents($fullPath, $imageData) === false) {
                return null;
            }
            return 'storage/temp_import/' . $filename;
        }

        return null;
    }

    private function finalizeTableQuestion($q, $options)
    {
        $q['opsi_a'] = '';
        $q['option_a_image'] = null;
        $q['opsi_b'] = '';
        $q['option_b_image'] = null;
        $q['opsi_c'] = '';
        $q['option_c_image'] = null;
        $q['opsi_d'] = '';
        $q['option_d_image'] = null;
        $q['opsi_e'] = '';
        $q['option_e_image'] = null;

        $q['score_a'] = 0;
        $q['score_b'] = 0;
        $q['score_c'] = 0;
        $q['score_d'] = 0;
        $q['score_e'] = 0;

        if (!isset($q['jawaban_benar'])) {
            $q['jawaban_benar'] = 'A';
        }

        $labels = ['a', 'b', 'c', 'd', 'e'];
        $highestScore = -1;
        $bestOptionIndex = 0;
        $hasExplicitScores = false;

        foreach ($options as $index => $opt) {
            if ($index >= 5) {
                break;
            }

            $label = $labels[$index];
            $q["opsi_" . $label] = $opt['text'];
            if ($opt['image']) {
                $q["option_" . $label . "_image"] = $opt['image'];
            }

            $q["score_" . $label] = $opt['score'];
            if ($opt['score'] > 0) {
                $hasExplicitScores = true;
            }

            if ($opt['score'] > $highestScore) {
                $highestScore = $opt['score'];
                $bestOptionIndex = $index;
            }
        }

        if ($hasExplicitScores) {
            $nonZeroScoresCount = 0;
            foreach ($options as $opt) {
                if ($opt['score'] > 0) {
                    $nonZeroScoresCount++;
                }
            }
            if ($highestScore === 1 && $nonZeroScoresCount === 1) {
                foreach ($labels as $lbl) {
                    if ($q["score_" . $lbl] === 1) {
                        $q["score_" . $lbl] = 5;
                    }
                }
                $highestScore = 5;
            }
        }

        if (!empty($options)) {
            if ($hasExplicitScores) {
                $q['jawaban_benar'] = strtoupper($labels[$bestOptionIndex]);
            } else {
                $correctLetter = $q['jawaban_benar'] ?? 'A';
                $correctIndex = array_search(strtolower($correctLetter), $labels);
                if ($correctIndex !== false) {
                    $q["score_" . strtolower($correctLetter)] = 5;
                }
            }
        }

        return $q;
    }

    private function mergeImagesVertically(array $imagePaths, string $tempImportDir): ?string
    {
        if (empty($imagePaths)) {
            return null;
        }
        if (count($imagePaths) === 1) {
            return $imagePaths[0];
        }

        $validPaths = [];
        foreach ($imagePaths as $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                $validPaths[] = $fullPath;
            }
        }

        if (empty($validPaths)) {
            return null;
        }
        if (count($validPaths) === 1) {
            return str_replace(public_path() . DIRECTORY_SEPARATOR, '', $validPaths[0]);
        }

        $totalHeight = 0;
        $maxWidth = 0;
        $imagesInfo = [];

        foreach ($validPaths as $path) {
            $info = @getimagesize($path);
            if ($info === false) continue;
            
            $width = $info[0];
            $height = $info[1];
            $type = $info[2];

            $imagesInfo[] = [
                'path' => $path,
                'width' => $width,
                'height' => $height,
                'type' => $type
            ];

            if ($width > $maxWidth) {
                $maxWidth = $width;
            }
            $totalHeight += $height + 10;
        }

        if (empty($imagesInfo)) {
            return null;
        }

        $totalHeight -= 10;

        $destImg = @imagecreatetruecolor($maxWidth, $totalHeight);
        if (!$destImg) {
            return str_replace(public_path() . DIRECTORY_SEPARATOR, '', $validPaths[0]);
        }

        $white = imagecolorallocate($destImg, 255, 255, 255);
        imagefill($destImg, 0, 0, $white);

        $currentY = 0;
        foreach ($imagesInfo as $img) {
            $srcImg = null;
            switch ($img['type']) {
                case IMAGETYPE_JPEG:
                    $srcImg = @imagecreatefromjpeg($img['path']);
                    break;
                case IMAGETYPE_PNG:
                    $srcImg = @imagecreatefrompng($img['path']);
                    break;
                case IMAGETYPE_GIF:
                    $srcImg = @imagecreatefromgif($img['path']);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagecreatefromwebp')) {
                        $srcImg = @imagecreatefromwebp($img['path']);
                    }
                    break;
            }

            if ($srcImg) {
                $offsetX = ($maxWidth - $img['width']) / 2;
                imagecopy($destImg, $srcImg, $offsetX, $currentY, 0, 0, $img['width'], $img['height']);
                imagedestroy($srcImg);
                $currentY += $img['height'] + 10;
            }
        }

        $filename = 'merged_img_' . uniqid() . '.png';
        $outputPath = $tempImportDir . '/' . $filename;
        
        if (@imagepng($destImg, $outputPath)) {
            imagedestroy($destImg);
            return 'storage/temp_import/' . $filename;
        }

        imagedestroy($destImg);
        return str_replace(public_path() . DIRECTORY_SEPARATOR, '', $validPaths[0]);
    }

    public function downloadTemplateWord()
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $section->addText("TEMPLATE IMPORT SOAL - SMART CBT", ['name' => 'Arial', 'size' => 16, 'bold' => true]);
        $section->addText("Petunjuk: Isi tabel di bawah ini untuk mengimpor soal. Kolom 'Jawaban' digunakan untuk bobot nilai pilihan jawaban.", ['name' => 'Arial', 'size' => 10, 'italic' => true]);
        $section->addTextBreak(1);

        $styleTable = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
        $styleFirstRowHeader = ['bgColor' => 'EAEAEA', 'bold' => true];
        $phpWord->addTableStyle('TemplateTable', $styleTable, $styleFirstRowHeader);
        $table = $section->addTable('TemplateTable');

        $table->addRow();
        $table->addCell(1000)->addText("No", ['bold' => true]);
        $table->addCell(2000)->addText("Jenis", ['bold' => true]);
        $table->addCell(5000)->addText("Isi", ['bold' => true]);
        $table->addCell(1500)->addText("Jawaban", ['bold' => true]);

        // Contoh Soal 1
        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("SOAL");
        $table->addCell(5000)->addText("Manakah yang merupakan lambang sila pertama Pancasila?");
        $table->addCell(1500)->addText("");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("JAWABAN A");
        $table->addCell(5000)->addText("Bintang");
        $table->addCell(1500)->addText("5");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("JAWABAN B");
        $table->addCell(5000)->addText("Rantai");
        $table->addCell(1500)->addText("0");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("JAWABAN C");
        $table->addCell(5000)->addText("Pohon Beringin");
        $table->addCell(1500)->addText("0");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("JAWABAN D");
        $table->addCell(5000)->addText("Kepala Banteng");
        $table->addCell(1500)->addText("0");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("JAWABAN E");
        $table->addCell(5000)->addText("Padi dan Kapas");
        $table->addCell(1500)->addText("0");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("KUNCI");
        $table->addCell(5000)->addText("A");
        $table->addCell(1500)->addText("");

        $table->addRow();
        $table->addCell(1000)->addText("1");
        $table->addCell(2000)->addText("PEMBAHASAN");
        $table->addCell(5000)->addText("Sila pertama berlambangkan bintang emas dengan latar belakang hitam.");
        $table->addCell(1500)->addText("");

        $filename = "Template_Import_Soal_SmartCBT.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
