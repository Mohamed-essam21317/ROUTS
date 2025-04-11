<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SchoolPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'),
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'days_count' => Carbon::parse($this->start_date)->diffInDays(
                $this->end_date ? Carbon::parse($this->end_date) : Carbon::parse($this->start_date)
            ) + 1,
        ];
    }
}
