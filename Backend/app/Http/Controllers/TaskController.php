<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::select('id', 'title', 'description', 'image')->get();
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'title' => 'required',
        //     'description' => 'required',
        //     'image' => 'required'|'image'
        // ]);
        $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('task/image',$request->image,$imageName);
        Task::create($request->post()+ ['image' => $imageName]);
        return response()->json([
            'message'=>'Item added successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json([
            'task' => $task
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable'
        ]);

            if($request->image){
                $exist = Storage::disk('public')->exists("task/image/{$task->image}");
                if($exist){
                    Storage::disk('public')->delete("task/image/{$task->image}");
                    $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('task/image',$request->image,$imageName);
                    $task->image = $imageName;
                }
            }
        $task->fill($request->post())->update();
        $task->save;
        return response()->json([
            'message'=>'Item updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if($task->image){
            $exist = Storage::disk('public')->exists("task/image/{$task->image}");
            if($exist){
                Storage::disk('public')->delete("task/image/{$task->image}");
            }
        }
        $task->delete();
        return response()->json([
            'message'=>'Item deleted successfully'
        ]);
    }
}
