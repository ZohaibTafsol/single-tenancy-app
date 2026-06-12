<?php

namespace App\Modules\Payer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payer\DTOs\PayerDTO;
use App\Modules\Payer\Exceptions\PayerNotFoundException;
use App\Modules\Payer\Http\Requests\{CreatePayerRequest, UpdatePayerRequest, UpdatePayerStatusRequest};
use App\Modules\Payer\Services\PayerService;
use App\Modules\Payer\Services\BulkImportPayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Modules\Payer\Http\Requests\BulkUploadPayerRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;


class PayerController extends Controller
{
    public function __construct(
        private readonly PayerService $payerService,
        private readonly BulkImportPayerService $bulkImportPayerService,
    ) {}

    // filter and pagination 
    public function index(Request $request): JsonResponse
    {
        $filter_params = $request->only(['name', 'email']);
        $payer = $this->payerService->getPayers($filter_params);
        return $this->success($payer, "Payer found");
    }

    public function store(CreatePayerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data["user_id"] = auth()->id();
        $dto = PayerDTO::fromRequest($data);
        $result = $this->payerService->createPayer($dto);
        return $this->success($result->toArray(), 'Payer Created Successful..');
    }
    public function show(string $uuid): JsonResponse
    {
        try {
            $payer = $this->payerService->getPayerByUuid($uuid);
            return $this->success($payer, "Payer found");
        } catch (PayerNotFoundException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }
    public function update(UpdatePayerRequest $request, string $uuid): JsonResponse
    {
        try {
            $data = $request->validated();
            $data["user_id"] = auth()->id();
            $dto = PayerDTO::fromRequest($data);
            $result = $this->payerService->update($uuid, $dto);
            return $this->success($result->toArray(), 'Payer Updated Successful..');
        } catch (PayerNotFoundException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }
    public function status(UpdatePayerStatusRequest $request, string $uuid): JsonResponse
    {
        try {
            $result = $this->payerService->updateStatus($uuid, $request->boolean('is_active'));
            return $this->success($result->toArray(), 'Payer status updated.');
        } catch (PayerNotFoundException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    public function destroy(string $uuid): JsonResponse
    {
        try {
            $this->payerService->delete($uuid);
            return $this->success('Payer Deleted Successful..');
        } catch (PayerNotFoundException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payers_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            // Required
            'file_type',
            'last_name',
            'id_type',
            'id_number',
            'address_one',
            'city',
            'country',
            // Individual
            'first_name',
            'middle_name',
            'suffix',
            // Contact
            'email',
            'phone_number',
            // Address extras
            'address_two',
            'state',
            'zip_code',
            'is_foreign_address',
            // Optional
            'disregarded_entity',
            'withholding_tax_state_id',
            'client_payer_id',
            'group_id',
            'is_last_filing',
            // Tax1099 (optional)
            'payer_detail_id',
            'tin_status',
            'is_tin_check',
            'un_mask_recipient_tin',
            'trade_name',
        ];

        // Two sample rows – one Individual, one Business
        $samples = [
            [
                // file_type, last_name, id_type, id_number, address_one, city, country
                'Individual',
                'Doe',
                'SSN',
                '123-45-6789',
                '123 Main St',
                'Austin',
                'US',
                // first_name, middle_name, suffix
                'John',
                'A',
                'Jr',
                // email, phone_number
                'john.doe@example.com',
                '512-555-0100',
                // address_two, state, zip_code, is_foreign_address
                'Apt 4B',
                'TX',
                '73301',
                '0',
                // disregarded_entity, withholding_tax_state_id, client_payer_id, group_id, is_last_filing
                '',
                '',
                'CP-001',
                'GRP-A',
                '0',
                // payer_detail_id, tin_status, is_tin_check, un_mask_recipient_tin, trade_name
                '',
                '',
                '0',
                '0',
                '',
            ],
            [
                // file_type, last_name (= Business Name), id_type, id_number, address_one, city, country
                'Business',
                'Acme Corporation',
                'EIN',
                '12-3456789',
                '456 Commerce Blvd',
                'Dallas',
                'US',
                // first_name, middle_name, suffix
                '',
                '',
                '',
                // email, phone_number
                'billing@acme.com',
                '214-555-0200',
                // address_two, state, zip_code, is_foreign_address
                'Suite 200',
                'TX',
                '75201',
                '0',
                // disregarded_entity, withholding_tax_state_id, client_payer_id, group_id, is_last_filing
                '',
                '',
                'CP-002',
                'GRP-B',
                '0',
                // payer_detail_id, tin_status, is_tin_check, un_mask_recipient_tin, trade_name
                '',
                '',
                '0',
                '0',
                'Acme Corp DBA',
            ],
        ];

        $callback = function () use ($columns, $samples) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($samples as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }


    // ── 2. Handle bulk upload ──────────────────────────────────────────────────
    /**
     * POST /payers/bulk-upload
     *
     * Accepts a CSV file, validates every row, imports valid ones,
     * and returns a detailed result summary.
     *
     * Response (200):
     * {
     *   "success": true,
     *   "message": "Import complete. 45 imported, 3 failed.",
     *   "data": {
     *     "total_rows": 48,
     *     "imported":   45,
     *     "failed":      3,
     *     "errors": [
     *       { "row": 7,  "errors": ["SSN must be in the format 123-45-6789."] },
     *       { "row": 23, "errors": ["City is required.", "State is required."] }
     *     ]
     *   }
     * }
     */
    public function bulkUpload(BulkUploadPayerRequest $request): JsonResponse
    {
        $result = $this->bulkImportPayerService->import(
            $request->file('file'),
            auth()->id(),
        );

        $message = "Import complete. {$result->imported} imported, {$result->failed} failed.";

        // Return 422 only when NOTHING was imported and there were rows to process
        if ($result->imported === 0 && $result->totalRows > 0) {
            return $this->error($message, 422, $result->toArray());
        }

        return $this->success($result->toArray(), $message);
    }
}
