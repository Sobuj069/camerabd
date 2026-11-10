<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class CheckCourierStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courier:check {consignment_ids* : One or more consignment IDs to check and sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check live status from Steadfast Courier API for specified consignment IDs and update database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cids = $this->argument('consignment_ids');

        if (empty($cids)) {
            $this->error('Please provide at least one consignment ID.');
            return 1;
        }

        $this->info("Checking " . count($cids) . " consignment(s) with Steadfast API...\n");

        $rows = [];

        foreach ($cids as $cid) {
            $cid = trim((string)$cid);
            if (empty($cid)) continue;

            $this->line("Fetching Steadfast live status for Consignment #{$cid}...");

            $apiResponse = function_exists('status') ? status($cid) : [];
            $rawStatus = $apiResponse['delivery_status'] ?? ($apiResponse['status'] ?? 'unknown');
            if (is_numeric($rawStatus) && isset($apiResponse['delivery_status'])) {
                $rawStatus = $apiResponse['delivery_status'];
            }

            $mappedStatus = function_exists('mapCourierStatus') ? mapCourierStatus((string)$rawStatus) : ucfirst($rawStatus);

            $invoice = Invoice::where('consignment_id', $cid)->first();

            $oldDbStatus = $invoice ? $invoice->order_status : 'Not found in DB';
            $invNo = $invoice ? $invoice->invoice_no : 'N/A';
            $customerName = $invoice && $invoice->customer ? $invoice->customer->name : 'N/A';

            $actionTaken = 'None';

            if ($invoice) {
                if ($invoice->order_status !== $mappedStatus && !empty($mappedStatus) && $mappedStatus !== 'Unknown') {
                    $oldStatus = $invoice->order_status;

                    if (function_exists('syncInvoiceStockForStatusChange')) {
                        syncInvoiceStockForStatusChange($invoice, $mappedStatus, $oldStatus);
                    }

                    $invoice->order_status = $mappedStatus;
                    $invoice->save();

                    if (in_array(strtolower(trim($mappedStatus)), ['delivered'])) {
                        if ($invoice->total_due > 0 && $invoice->status != 2 && function_exists('autoPayInvoiceDue')) {
                            autoPayInvoiceDue($invoice);
                        }
                    }

                    $actionTaken = "Updated to {$mappedStatus}";
                } else {
                    $actionTaken = "Already {$invoice->order_status}";
                }
            }

            $rows[] = [
                $cid,
                $invNo,
                $customerName,
                is_array($apiResponse) ? json_encode($apiResponse, JSON_UNESCAPED_UNICODE) : (string)$apiResponse,
                $rawStatus,
                $mappedStatus,
                $oldDbStatus,
                $actionTaken
            ];
        }

        $this->table(
            ['Consignment ID', 'Invoice No', 'Customer', 'Raw API Response', 'Steadfast Status', 'Mapped Status', 'Old DB Status', 'Action Taken'],
            $rows
        );

        $this->info("\nSteadfast Courier Check & Sync completed.");
        return 0;
    }
}
