<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSignatory extends Model
{
    //

    protected $table = 'document_signatories';


    protected $fillable = [
        'control_no',
        //performance standard
        'performance_standard_discussed_by_control_no',
        'performance_standard_approved_by_control_no',
        //ipcr
        'ipcr_reviewed_by_control_no',
        'ipcr_approved_by_control_no',
        'ipcr_assessed_by_control_no',
        'ipcr_final_rating_by_control_no',
        //por
        'por_confirmed_control_no',
        'por_approved_final_rating_control_no'
    ];


}
