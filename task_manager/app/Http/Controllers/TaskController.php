<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

date_default_timezone_set('Asia/Kolkata');

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = Auth::guard('admin')->user();
        $user_token = $user->token;
        $task =  Task::select('token', 'title', 'status', 'favorite')
            ->selectRaw('date_format(created_at, "%d %b,%Y") as date')
            ->selectRaw('date_format(created_at, "%l:%i %p") as time')
            ->whereIn('status', [0, 1])->where('user_token', $user_token)->get();

        $task_count_completed =  Task::where('status', [1])->count();
        $favourite =  Task::where('favorite', [1])->count();

        $task_count = count($task);
        return response()->json([
            'user_image' => $user->image,
            'user_name' => $user->name,
            'total_count' => $task_count,
            'total_task_completed' => $task_count_completed,
            'total_favourite' => $favourite,
            'list' => $task
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $user_token = $user->token;
        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->messages()
            ]);
        } else {
            $task = Task::create([
                'title' => $request->title,
                'token' => rand(10000, 99999),
                'user_token' => $user_token
            ]);

            if ($task) {
                return response()->json([
                    'status_code' => 200,
                    'task' => $task->title
                ], 200);
            }
        }
    }


    public function show(Request $request)
    {
        $task =  Task::select('token', 'title', 'status', 'favorite')->selectRaw('date_format(created_at, "%Y-%m-%d")as date')->selectRaw('date_format(created_at,"%H-%i-%s")as time')->where([['token', $request->token]])->first();
        if ($task) {
            return response()->json([
                'list' => $task
            ]);
        } else {
            return response()->json([
                'message' => 'No record found in this token'
            ]);
        }
    }

    public function update_task(Request $request)
    {
        //
        $task = Task::where('token', $request->token)->update(['title' => $request->title]);
        if ($task) {
            return response()->json([
                'message' => 'Record updated successfully',
                'update' => $request->title
            ], 200);
        }
    }

    public function update_status(Request $request)
    {

        $status = Task::where('token', $request->token)->value('status');
        if ($status == 0) {
            $status1 = 1;
            Task::where('token', $request->token)->update(['status' => $status1]);
            return response()->json([
                'message' => 'Task Completed'
            ]);
        } else {
            return response()->json([
                'message' => 'Task already Completed'
            ]);
        }
    }

    public function favourite_update(Request $request)
    {
        $fav = Task::where('token', $request->token)->value('favorite');

        if ($fav == 0) {
            $fav1 = 1;
            Task::where('token', $request->token)->update(['favorite' => $fav1]);
            return response()->json([
                'message' => 'This Task is marked as Favourite'
            ]);
        } else {
            $fav1 = 0;
            Task::where('token', $request->token)->update(['favorite' => $fav1]);
            return response()->json([
                'message' => 'This Task is removed from Favourite'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $task_del_count = Task::select('token')->where([['token', $request->token]])->count();

        if ($task_del_count == 0) {

            return response()->json([
                'message' => 'No Record founded'
            ]);
        } else {
            $task_del = Task::select('token')->where([['token', $request->token]])->delete();

            return response()->json([
                'message' => "deleted successfully"
                // 'echo'=> $task
            ]);
        }
    }


    public function delete_mul(Request $request)
    {
        $token = $request->token;

        Task::whereIn('token', $token)->delete();

        return response()->json([
            'message' => "Selected Record deleted successfully"

        ]);
    }


    public function fav_filter(Request $request)
    {

        if ($request->favorite == 1) {
            $filter_fav = Task::select('token', 'title', 'status', 'favorite')->where('favorite', $request->favorite)->get();
            $filter_count = count($filter_fav);
            if ($filter_fav) {
                return response()->json([
                    'total_filter_count' => $filter_count,
                    'favourite' => $filter_fav
                ]);
            }
        } else if ($request->favorite == 0) {
            $filter_fav = Task::select('token', 'title', 'status', 'favorite')->where('favorite', $request->favorite)->get();
            $filter_count = count($filter_fav);
            if ($filter_fav) {
                return response()->json([
                    'total_filter_count' => $filter_count,
                    'not_favourite' => $filter_fav
                ]);
            }
        }
    }


    public function fav_status(Request $request)
    {

        if ($request->status == 1) {
            $filter_status = Task::select('token', 'title', 'status', 'favorite')->where('status', $request->status)->get();
            if ($filter_status) {

                return response()->json([
                    'Completed' => $filter_status
                ]);
            }
        } else  if ($request->status == 0) {
            $filter_status = Task::select('token', 'title', 'status', 'favorite')->where('status', $request->status)->get();
            if ($filter_status) {
                return response()->json([
                    'not_completed' => $filter_status
                ]);
            }
        }
    }
}
