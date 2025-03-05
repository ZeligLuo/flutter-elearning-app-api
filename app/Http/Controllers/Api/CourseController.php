<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // api course list
    public function courseList()
    {
        $result = Course::select('name', 'thumbnail', 'lesson_num', 'price', 'id')->get();

        return response()->json([
            'code' => 200,
            'msg' => 'Course list here',
            'data' => $result
        ], 200);
    }

    // api single course detail
    public function courseDetail(Request $request)
    {
        $id = $request->id;
        try {
            $result = Course::where('id', '=', $id)->select(
                'id',
                'name',
                'user_token',
                'description',
                'thumbnail',
                'lesson_num',
                'video_length',
                'price'
            )->first();

            return response()->json(
                [
                    'code' => 200,
                    'msg' => 'Course detail here',
                    'data' => $result
                ], 200
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'code' => 200,
                    'msg' => 'Server internal error',
                    'data' => $e->getMessage()
                ], 500
            );
        }
    }
}
