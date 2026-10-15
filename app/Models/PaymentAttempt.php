<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PaymentAttempt extends Model {
    protected $fillable = ['order_id','provider','provider_order_id','provider_payment_id','status','amount_paise','currency','verified_at','payload_json'];
    protected function casts(): array { return ['verified_at'=>'datetime','payload_json'=>'array']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
