<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\NotificationEntityType;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'min:1', 'max:700', 'string'],
            'task_id' => ['required', 'exists:tasks,id', function ($attribute, $value, $fail) {
                $task = Task::find($value);
                $user = Auth::user();
                if (!$task || ($user->id !== $task->creator_id && $user->id !== $task->assignee_id && !$user->hasRole('manager'))) {
                    $fail(__('You are not authorized to add a message to this task.'));
                }
            }],
            'files.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,zip,rar,pdf,doc,docx,xls,xlsx', 'max:1048576']
        ]);

        $data['user_id'] = Auth::user()->id;

        $message = TaskMessage::create($data);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('attachments', 'public');
                Attachment::create([
                    'entity_type' => 'task_message',
                    'entity_id' => $message->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->id
                ]);
            }
        }


        $users = collect([$message->task->assignee_id, $message->task->creator_id]);

        $users->each(function($user) use($data, $message) {
            if($user != Auth::user()->id)
            {
                Notification::create([
                    'user_id' => $user,
                    'title' => __('dashboard.new-message-added_to_the_task') . " (" . $message->task->title . ") " . __('dashboard.by') . " " . Auth::user()->full_name,
                    'entity_type' => NotificationEntityType::TASK->value,
                    'entity_id' => $data['task_id'],
                ]); 
            }
        });

        return response()->json([
            'message' => __('dashboard.task_message_added'),
            'id' => $message->id,
            'user_image' => $message->user->display_image,
            'user_fullname' => $message->user->full_name,
            'date' => $message->created_at->format('Y-m-d H:ia'),
            'content' => $message->message,
            'attachments' => $message->attachments,
            'ability_to_delete' => Auth::user()->hasRole('manager') || Auth::user()->id == $message->task->creator_id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskMessage $task_message)
    {
        if(Auth::user()->hasRole('manager') || auth()->user()->id == $task_message->task->creator_id)
        {
            foreach ($task_message->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
            $task_message->delete();

            return response()->json([
                'message' => __('dashboard.task_message_deleted'),
            ]);
        }

        return response(401);
    }
}
