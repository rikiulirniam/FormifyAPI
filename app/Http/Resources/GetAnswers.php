<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAnswers extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Create an associative array for the answers
        $answers = $this->answers->reduce(function ($carry, $ans) {
            $carry[$ans->question->name] = $ans->value;
            return $carry;
        }, []);

        return [
            'date' => $this->date,
            'user' => $this->user,
            'answers' => $answers
        ];
    }
}
