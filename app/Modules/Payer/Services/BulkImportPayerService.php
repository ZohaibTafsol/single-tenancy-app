<?php

namespace App\Modules\Payer\Services;

use App\Modules\Payer\DTOs\BulkUploadResult;
use App\Modules\Payer\DTOs\PayerDTO;
use App\Modules\Payer\Models\Payer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BulkImportPayerService
{
    // How many rows to insert in a single DB query
    private const CHUNK_SIZE = 100;

    // Required CSV columns (all others are treated as optional)
    private const REQUIRED_COLUMNS = [
        'file_type', 'last_name', 'id_type', 'id_number',
        'address_one', 'city', 'country',
    ];

    public function __construct(
        private readonly PayerCsvRowValidator $rowValidator,
    ) {}

    public function import(UploadedFile $file, int $userId): BulkUploadResult
    {
        $path = $file->getRealPath();

        // ── 1. Open & parse CSV ────────────────────────────────────────────────
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return new BulkUploadResult(0, 0, 0, [
                ['row' => 0, 'errors' => ['Unable to open uploaded file.']],
            ]);
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            return new BulkUploadResult(0, 0, 0, [
                ['row' => 0, 'errors' => ['CSV file is empty or has no header row.']],
            ]);
        }

        $headers = array_map('trim', $headers);

        // Check required columns exist
        $missing = array_diff(self::REQUIRED_COLUMNS, $headers);
        if (! empty($missing)) {
            fclose($handle);
            return new BulkUploadResult(0, 0, 0, [[
                'row'    => 0,
                'errors' => ['Missing required columns: ' . implode(', ', $missing)],
            ]]);
        }

        // ── 2. Process rows ────────────────────────────────────────────────────
        $totalRows = 0;
        $errors    = [];
        $validRows = [];

        $rowNumber = 1; // header = 0, first data row = 1

        while (($rawRow = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely blank rows
            if (empty(array_filter($rawRow))) {
                continue;
            }

            $totalRows++;
            $row = array_combine($headers, array_pad($rawRow, count($headers), null));

            $result = $this->rowValidator->validate($row, $rowNumber);

            if (! $result['valid']) {
                $errors[] = ['row' => $rowNumber, 'errors' => $result['errors']];
                continue;
            }

            $validRows[] = $this->buildInsertData($row, $userId);
        }

        fclose($handle);
        Log::info(json_encode($validRows));

        // ── 3. Bulk insert in chunks ───────────────────────────────────────────
        $imported = 0;
        if (! empty($validRows)) {
            DB::transaction(function () use ($validRows, &$imported) {
                foreach (array_chunk($validRows, self::CHUNK_SIZE) as $chunk) {
                    Payer::insert($chunk);
                    $imported += count($chunk);
                }
            });
        }

        return new BulkUploadResult(
            totalRows: $totalRows,
            imported:  $imported,
            failed:    $totalRows - $imported,
            errors:    $errors,
        );
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function buildInsertData(array $row, int $userId): array
    {
        $isIndividual = ($row['file_type'] ?? '') === 'Individual';
        $now          = now()->toDateTimeString();

        // Compute composite "name" field (same logic as PayerDTO::toArray)
        if ($isIndividual) {
            $parts = array_filter([
                $row['first_name']  ?? null,
                $row['middle_name'] ?? null,
                $row['last_name']   ?? null,
            ]);
            $name = implode(' ', $parts);
        } else {
            $name = $row['last_name']; // Business name stored in last_name column
        }

        return [
            'user_id'                  => $userId,
            'uuid'                     => (string) Str::uuid(),
            'file_type'                => $row['file_type'],
            'name'                     => $name,
            'first_name'               => $row['first_name']    ?? null,
            'middle_name'              => $row['middle_name']   ?? null,
            'last_name'                => $row['last_name'],
            // 'suffix'                   => $row['suffix']        ?? null,
            'suffix'                   => empty($row['suffix']) ? null : $row['suffix'],
            'id_type'                  => $row['id_type'],
            'id_number'                => $row['id_number'],
            'email'                    => $row['email']         ?? null,
            'phone_number'             => $row['phone_number']  ?? null,
            'disregarded_entity'       => $row['disregarded_entity']       ?? null,
            'address_one'              => $row['address_one'],
            'address_two'              => $row['address_two']   ?? null,
            'city'                     => $row['city'],
            'state'                    => $row['state']         ?? null,
            'zip_code'                 => $row['zip_code']      ?? null,
            'country'                  => strtoupper($row['country'] ?? 'US'),
            'is_foreign_address'       => $this->toBool($row['is_foreign_address']  ?? false),
            'withholding_tax_state_id' => $row['withholding_tax_state_id'] ?? null,
            'client_payer_id'          => $row['client_payer_id']          ?? null,
            'group_id'                 => $row['group_id']                 ?? null,
            'is_last_filing'           => $this->toBool($row['is_last_filing']      ?? false),
            'tin_status'               => $row['tin_status']               ?? null,
            'is_tin_check'             => $this->toBool($row['is_tin_check']        ?? false),
            'un_mask_recipient_tin'    => $this->toBool($row['un_mask_recipient_tin'] ?? false),
            'trade_name'               => $row['trade_name']               ?? null,
            'is_active'                => true,
            'created_at'               => $now,
            'updated_at'               => $now,
        ];
    }

    private function toBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
