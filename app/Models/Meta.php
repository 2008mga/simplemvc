<?php namespace Simple\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
        'name',
        'value',
        'created_at',
        'updated_at'
    ];

    public static function between($start_date, $end_date, $get = false)
    {
        $query = self::query()
            ->where('created_at', '<=', $end_date)
            ->where('created_at', '>=', $start_date);
        return ($get) ? $query->get() : $query;
    }
}