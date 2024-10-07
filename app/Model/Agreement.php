<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'transaction_id',
        'amount',
        'percentage',
        'currency',
    ];

    /**
     * Relação com o modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com o modelo Transaction.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
