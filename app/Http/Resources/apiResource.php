<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class apiResource extends JsonResource
{
    public $status;
    public $message;    
    public $resource;

    public function __construct($resource ,$status = true, $message = null) {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }   

    public function toArray(Request $request):array {
        return [
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,
        ];
    }
}
