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
        'transaction_id',
        'gateway_transaction_id',
        'status',
        'gateway_response',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
        'subscription_id' => 'integer',
        'bundle_id' => 'integer',
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
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
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
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
        return $this->status === 'failed';
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
}
