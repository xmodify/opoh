<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // สำคัญ: ให้โมเดลนี้เป็น authenticatable
use Laravel\Sanctum\HasApiTokens;

class Hospital extends Model
{
   use HasApiTokens;

    protected $fillable = ['hospcode','name','token_api','contact','ip_whitelist'];

    // ถ้าต้องการกำหนด guard แยก สามารถตั้งค่าใน config/auth.php
}
