<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpInsurance extends Model
{
    protected $table = 'op_insurance';

    protected $fillable = [
        'hospcode', 'vstdate',
        'hn_total', 'visit_total', 'visit_total_op', 'visit_total_pp',
        'visit_ucs_incup', 'visit_ucs_inprov', 'visit_ucs_outprov',
        'visit_ofc', 'visit_bkk', 'visit_bmt', 'visit_sss',
        'visit_lgo', 'visit_fss', 'visit_stp', 'visit_pay',
        'visit_ppfs', 'visit_ucs_cr', 'visit_ucs_herb', 'visit_ucs_healthmed',

        'inc_total', 'inc_lab_total', 'inc_drug_total',
        'inc_ucs_incup', 'inc_lab_ucs_incup', 'inc_drug_ucs_incup',
        'inc_ucs_inprov', 'inc_lab_ucs_inprov', 'inc_drug_ucs_inprov',
        'inc_ucs_outprov', 'inc_lab_ucs_outprov', 'inc_drug_ucs_outprov',
        'inc_ofc', 'inc_lab_ofc', 'inc_drug_ofc',
        'inc_bkk', 'inc_lab_bkk', 'inc_drug_bkk',
        'inc_bmt', 'inc_lab_bmt', 'inc_drug_bmt',
        'inc_sss', 'inc_lab_sss', 'inc_drug_sss',
        'inc_lgo', 'inc_lab_lgo', 'inc_drug_lgo',
        'inc_fss', 'inc_lab_fss', 'inc_drug_fss',
        'inc_stp', 'inc_lab_stp', 'inc_drug_stp',
        'inc_pay', 'inc_lab_pay', 'inc_drug_pay',
        'inc_ppfs', 'inc_uccr', 'inc_herb',
    ];

    protected $casts = [
        'vstdate' => 'date:Y-m-d',

        // Visits -> int
        'hn_total'            => 'int',
        'visit_total'         => 'int',
        'visit_total_op'      => 'int',
        'visit_total_pp'      => 'int',
        'visit_ucs_incup'     => 'int',
        'visit_ucs_inprov'    => 'int',
        'visit_ucs_outprov'   => 'int',
        'visit_ofc'           => 'int',
        'visit_bkk'           => 'int',
        'visit_bmt'           => 'int',
        'visit_sss'           => 'int',
        'visit_lgo'           => 'int',
        'visit_fss'           => 'int',
        'visit_stp'           => 'int',
        'visit_pay'           => 'int',
        'visit_ppfs'          => 'int',
        'visit_ucs_cr'        => 'int',
        'visit_ucs_herb'      => 'int',
        'visit_ucs_healthmed' => 'int',

        // Income -> float/double
        'inc_total'            => 'float',
        'inc_lab_total'        => 'float',
        'inc_drug_total'       => 'float',
        'inc_ucs_incup'        => 'float',
        'inc_lab_ucs_incup'    => 'float',
        'inc_drug_ucs_incup'   => 'float',
        'inc_ucs_inprov'       => 'float',
        'inc_lab_ucs_inprov'   => 'float',
        'inc_drug_ucs_inprov'  => 'float',
        'inc_ucs_outprov'      => 'float',
        'inc_lab_ucs_outprov'  => 'float',
        'inc_drug_ucs_outprov' => 'float',
        'inc_ofc'              => 'float',
        'inc_lab_ofc'          => 'float',
        'inc_drug_ofc'         => 'float',
        'inc_bkk'              => 'float',
        'inc_lab_bkk'          => 'float',
        'inc_drug_bkk'         => 'float',
        'inc_bmt'              => 'float',
        'inc_lab_bmt'          => 'float',
        'inc_drug_bmt'         => 'float',
        'inc_sss'              => 'float',
        'inc_lab_sss'          => 'float',
        'inc_drug_sss'         => 'float',
        'inc_lgo'              => 'float',
        'inc_lab_lgo'          => 'float',
        'inc_drug_lgo'         => 'float',
        'inc_fss'              => 'float',
        'inc_lab_fss'          => 'float',
        'inc_drug_fss'         => 'float',
        'inc_stp'              => 'float',
        'inc_lab_stp'          => 'float',
        'inc_drug_stp'         => 'float',
        'inc_pay'              => 'float',
        'inc_lab_pay'          => 'float',
        'inc_drug_pay'         => 'float',
        'inc_ppfs'             => 'float',
        'inc_uccr'             => 'float',
        'inc_herb'             => 'float',
    ];
}
