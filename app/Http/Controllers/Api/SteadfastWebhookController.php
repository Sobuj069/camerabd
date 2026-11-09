<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SteadfastWebhookController extends Controller
{
    /**
     * Handle incoming real-time status update webhook from Steadfast Courier.
     * Webhook URL: https://camerabd.com.bd/api/steadfast/webhook
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Steadfast Webhook Received:', $request->all());

        // Supports both GET ping (for webhook verification) and POST payload
        if ($request->isMethod('get') || $request->isMethod('head')) {
            return response()->json([
                'status'  => 200,
                'message' => 'Steadfast webhook endpoint is active and listening.'
            ]);
        }

        $consignmentId = $request->input('consignment_id') ?? $request->input('cid');
        $invoiceNo     = $request->input('invoice') ?? $request->input('invoice_no');
        $rawStatus     = $request->input('status') ?? $request->input('delivery_status');

        if (empty($consignmentId) && empty($invoiceNo)) {
            return response()->json([
                'status'  => 400,
                'message' => 'Missing consignment_id or invoice identifier in webhook payload.'
            ], 400);
        }

        // Find matching Invoice
        $invoice = null;
        if (!empty($consignmentId)) {
            $invoice = Invoice::where('consignment_id', $consignmentId)->first();
        }
        if (!$invoice && !empty($invoiceNo)) {
            $cleanNo = trim($invoiceNo);
            $numericId = (int)preg_replace('/[^0-9]/', '', $cleanNo);
            $invoice = Invoice::where('invoice_no', $cleanNo)
                ->orWhere('id', $numericId)
                ->first();
        }

        if (!$invoice) {
            Log::warning("Steadfast Webhook: No local invoice found for CID: {$consignmentId}, Invoice: {$invoiceNo}");
            return response()->json([
                'status'  => 404,
                'message' => 'Invoice not found on store.'
            ], 404);
        }

        if (!empty($rawStatus)) {
            $mappedStatus = function_exists('mapCourierStatus') ? mapCourierStatus($rawStatus) : ucfirst($rawStatus);
            $oldStatus    = $invoice->order_status;

            if ($mappedStatus !== $oldStatus) {
                // Stock sync for cancellations or returns
                if (function_exists('syncInvoiceStockForStatusChange')) {
                    syncInvoiceStockForStatusChange($invoice, $mappedStatus, $oldStatus);
                }

                $invoice->order_status = $mappedStatus;

                if (!empty($consignmentId) && empty($invoice->consignment_id)) {
                    $invoice->consignment_id = $consignmentId;
                }

                if ($request->filled('delivery_charge')) {
                    $invoice->delivery_charge = (float)$request->input('delivery_charge');
                }

                if (in_array(strtolower($rawStatus), ['delivered', 'partial_delivered']) && $request->filled('cod_amount')) {
                    $invoice->paid_amount = (float)$request->input('cod_amount');
                    $invoice->total_due = max(0, (float)($invoice->total_amount ?? 0) - (float)$invoice->paid_amount);
                }

                $invoice->save();
                Log::info("Steadfast Webhook: Invoice #{$invoice->invoice_no} updated from '{$oldStatus}' to '{$mappedStatus}'");
            }
        }

        return response()->json([
            'status'     => 200,
            'message'    => 'Steadfast status successfully synced.',
            'invoice_id' => $invoice->id,
            'status_set' => $invoice->order_status
        ]);
    }
}
