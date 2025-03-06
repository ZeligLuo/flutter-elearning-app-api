<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    protected $casts = [
        'video'=>'json'
    ];

    public function setVideoAttribute($value) {
        $this->attributes['video'] = json_encode(array_values($value));
    }

    public function getVideoAttribute($value) {
        $resultVideo = json_decode($value, true)?:[];

        if(!empty($resultVideo)) {
            foreach($resultVideo as $k=>$v) {
                $resultVideo[$k]['url']=$v['url'];
                $resultVideo[$k]['thumbnail']=$v['thumbnail'];

            }
        }

        return $resultVideo;
        // $this->attributes['video'] = array_values(json_decode($value, true)?:[]);
    }
}
