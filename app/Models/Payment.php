<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'subscription_id',
        'bundle_id',
        'amount',
        'gateway',
        'payment_method',
        'wallet_provider',
        'transaction_id',
        'gateway_transaction_id',
        'user_transaction_id',
        'payment_note',
        'sender_name',
        'sender_mobile',
        'status',
        'gateway_response',
        'approved_at',
        'approved_by',
        'admin_note',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
        'subscription_id' => 'integer',
        'bundle_id' => 'integer',
        'approved_by' => 'integer',
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Generate unique transaction ID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = 'TXN_' . strtoupper(Str::random(10)) . '_' . time();
            }
        });
    }

    /**
     * Get the user that owns the payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the payment
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the subscription that owns the payment
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the bundle that owns the payment
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    /**
     * Get the admin who approved the payment
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['success', 'approved']);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'rejected']);
    }

    /**
     * Check if payment is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if payment is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if payment is manual
     */
    public function isManual(): bool
    {
        return in_array($this->payment_method, ['bank_transfer', 'mobile_wallet']);
    }

    /**
     * Mark payment as successful
     */
    public function markAsSuccessful(string $gatewayTransactionId = null, array $response = []): void
    {
        $this->update([
            'status' => 'success',
            'gateway_transaction_id' => $gatewayTransactionId,
            'gateway_response' => $response,
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(array $response = []): void
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => $response,
        ]);
    }

    /**
     * Approve manual payment
     */
    public function approve(User $admin, string $note = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'admin_note' => $note,
        ]);
    }

    /**
     * Reject manual payment
     */
    public function reject(User $admin, string $note = null): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'admin_note' => $note,
        ]);
    }

    /**
     * Get payment type (course, bundle, or subscription)
     */
    public function getTypeAttribute(): string
    {
        if ($this->course_id) {
            return 'course';
        } elseif ($this->bundle_id) {
            return 'bundle';
        } elseif ($this->subscription_id) {
            return 'subscription';
        }
        
        return 'unknown';
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for course payments
     */
    public function scopeCoursePayments($query)
    {
        return $query->whereNotNull('course_id');
    }

    /**
     * Scope for subscription payments
     */
    public function scopeSubscriptionPayments($query)
    {
        return $query->whereNotNull('subscription_id');
    }

    /**
     * Scope for bundle payments
     */
    public function scopeBundlePayments($query)
    {
        return $query->whereNotNull('bundle_id');
    }

    /**
     * Scope for manual payments
     */
    public function scopeManualPayments($query)
    {
        return $query->whereIn('payment_method', ['bank_transfer', 'mobile_wallet']);
    }

    /**
     * Scope for approved payments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected payments
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending')->whereIn('payment_method', ['bank_transfer', 'mobile_wallet']);
    }
}
