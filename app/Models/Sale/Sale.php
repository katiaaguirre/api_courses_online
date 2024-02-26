<?php

namespace App\Models\Sale;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "user_id",
        "payment_method",
        "currency_total",
        "currency_payment",
        "total",
        "price_dolar",
        "n_transaccion"
    ];

    public function setCreatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["created_at"] = Carbon::now();
       }
    
    public function setUpdatedAtAttribute($value){
        date_default_timezone_set("America/Mexico_city");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function sale_details(){
        return $this->hasMany(SalesDetails::class);
    }
}
