<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'deadline',
        'category',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'deadline' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the goal
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the progress percentage (0-100)
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount == 0) {
            return 0;
        }

        $percentage = ($this->current_amount / $this->target_amount) * 100;
        return min(100, round($percentage, 2));
    }

    /**
     * Get the remaining amount needed
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    /**
     * Get days remaining until deadline
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Check if goal is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->deadline || $this->is_completed) {
            return false;
        }

        return now()->isAfter($this->deadline);
    }

    /**
     * Calculate projected completion date based on current savings rate
     * Requires average monthly contribution
     */
    public function getProjectedCompletionDate($monthlyContribution)
    {
        if ($monthlyContribution <= 0 || $this->remaining_amount <= 0) {
            return null;
        }

        $monthsNeeded = ceil($this->remaining_amount / $monthlyContribution);
        return now()->addMonths($monthsNeeded);
    }

    /**
     * Scope for active (not completed) goals
     */
    public function scopeActive($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope for completed goals
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Mark goal as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'current_amount' => $this->target_amount,
        ]);
    }
}
