<?php

namespace App\Modules\Payer\DTOs;

class BulkUploadResult
{
    public function __construct(
        public readonly int   $totalRows,
        public readonly int   $imported,
        public readonly int   $failed,
        public readonly array $errors = [],   // [ ['row' => N, 'errors' => [...]] ]
    ) {}

    public function toArray(): array
    {
        return [
            'total_rows' => $this->totalRows,
            'imported'   => $this->imported,
            'failed'     => $this->failed,
            'errors'     => $this->errors,
        ];
    }
}
