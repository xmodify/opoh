<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpInsurance extends Model
{
      protected $table = 'op_insurance';

    protected $fillable = [
        'hospcode', 'vstdate',
        'total_visit', 'endpoint',
        'ofc_visit', 'ofc_edc',
        'non_authen', 'non_hmain',
        'uc_anywhere', 'uc_anywhere_endpoint',
        'uc_cr', 'uc_cr_endpoint',
        'uc_herb', 'uc_herb_endpoint',
        'ppfs', 'ppfs_endpoint',
        'uc_healthmed', 'uc_healthmed_endpoint',
    ];

    protected $casts = [
        'vstdate' => 'date:Y-m-d',
        'total_visit' => 'int',
        'endpoint' => 'int',
        'ofc_visit' => 'int',
        'ofc_edc' => 'int',
        'non_authen' => 'int',
        'non_hmain' => 'int',
        'uc_anywhere' => 'int',
        'uc_anywhere_endpoint' => 'int',
        'uc_cr' => 'int',
        'uc_cr_endpoint' => 'int',
        'uc_herb' => 'int',
        'uc_herb_endpoint' => 'int',
        'ppfs' => 'int',
        'ppfs_endpoint' => 'int',
        'uc_healthmed' => 'int',
        'uc_healthmed_endpoint' => 'int',
    ];
}
