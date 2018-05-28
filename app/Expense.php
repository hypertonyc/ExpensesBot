<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expense extends Model
{
  public function getCreatedAtAttribute($date)
  {
      return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y  H:i:s');
  }

  public function category()
  {
      return $this->belongsTo('App\ExpenseCategory');
  }

  public function user()
  {
      return $this->belongsTo('App\User');
  }
}
