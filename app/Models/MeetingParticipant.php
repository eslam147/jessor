<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class MeetingParticipant extends MorphPivot
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'meeting_participants';

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function purchaseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function joinParticipant(): self
    {
        $joinTime = $this->started_at ?? now();
        $this->fill(['started_at' => $joinTime])->save();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function leave(): self
    {
        $leaveTime = $this->ended_at ?? now();
        $this->fill(['ended_at' => $leaveTime])->save();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->delete();
    }
}
