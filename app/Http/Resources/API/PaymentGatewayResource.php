<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $value = json_decode($this->value, true);
        $live_value = json_decode($this->live_value, true);

        if ($this->type === 'stripe') {
            if (is_array($value)) {
                unset($value['stripe_key']);
            }
            if (is_array($live_value)) {
                unset($live_value['stripe_key']);
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type'   => $this->type,
            'status'  => $this->status,
            'is_test'=> $this->is_test,
            'value'  => $value,
            'live_value'=> $live_value,
        ];
    }
}