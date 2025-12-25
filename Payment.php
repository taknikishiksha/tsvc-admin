<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id', 'client_id', 'teacher_id', 'payment_id', 'order_id',
        'payment_method', 'gateway', 'amount', 'platform_fee', 'teacher_share',
        'coordinator_share', 'tds_deducted', 'net_teacher_share', 'status',
        'paid_at', 'payout_processed_at', 'payout_status', 'payout_id',
        'payout_response', 'invoice_number', 'invoice_date', 'invoice_details',
        'refund_amount', 'refund_id', 'refund_reason', 'refunded_at',
        'gateway_request', 'gateway_response', 'gateway_error'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'teacher_share' => 'decimal:2',
        'coordinator_share' => 'decimal:2',
        'tds_deducted' => 'decimal:2',
        'net_teacher_share' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payout_processed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'invoice_date' => 'date',
        'invoice_details' => 'array',
        'gateway_request' => 'array',
        'gateway_response' => 'array',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function teacher()
    {
        return $this->belongsTo(YogaTeacher::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'captured');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeProcessedPayouts($query)
    {
        return $query->where('payout_status', 'paid');
    }

    // Methods
    public function isSuccessful()
    {
        return $this->status === 'captured';
    }

    public function isRefunded()
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }

    public function calculateCommission()
    {
        $this->platform_fee = $this->amount * 0.20; // 20%
        $this->teacher_share = $this->amount * 0.70; // 70%
        $this->coordinator_share = $this->amount * 0.10; // 10%
        
        // Calculate TDS (if applicable)
        $this->tds_deducted = $this->teacher_share * 0.05; // 5% TDS
        $this->net_teacher_share = $this->teacher_share - $this->tds_deducted;
    }

    public function generateInvoiceNumber()
    {
        if (!$this->invoice_number) {
            $this->invoice_number = 'TSVC-' . date('Ymd') . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
        }
        return $this->invoice_number;
    }

    public function markAsPaid($paymentId, $gatewayData = [])
    {
        $this->update([
            'status' => 'captured',
            'payment_id' => $paymentId,
            'paid_at' => now(),
            'gateway_response' => $gatewayData,
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_date' => now(),
        ]);

        // Calculate commission distribution
        $this->calculateCommission();
        $this->save();
    }

    public function processRefund($amount, $reason = '')
    {
        $this->update([
            'status' => $amount < $this->amount ? 'partially_refunded' : 'refunded',
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);
    }
}