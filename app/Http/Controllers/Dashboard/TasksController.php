<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\NotificationEntityType;
use App\Enum\TaskStatus;
use App\Http\Controllers\Controller;
use App\Mail\TaskCreatedMail;
use App\Mail\TaskDeletedMail;
use App\Models\Attachment;
use App\Models\Notification;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Yajra\DataTables\Facades\DataTables;

class TasksController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('role:manager|admin', except: ['show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            if(Auth::user()->hasRole('manager'))
            {
                $tasks = Task::query();
            }
            else
            {
                $tasks = Task::where(function($q){
                    return $q->where('creator_id', Auth::user()->id)->orWhere('assignee_id', Auth::user()->id);
                });
            }
    
            $status = [];
            if($request->new_status == "true")
            {
                $status[] = TaskStatus::NEW->value;
            }
            if($request->review_status == "true")
            {
                $status[] = TaskStatus::REVIEW->value;
            }
            if($request->working_status == "true")
            {
                $status[] = TaskStatus::WORKING->value;
            }
            if($request->feedback_status == "true")
            {
                $status[] = TaskStatus::FEEDBACK->value;
            }
            if($request->done_status == "true")
            {
                $status[] = TaskStatus::DONE->value;
            }

            $tasks = $tasks->whereIn('status', $status);

            return DataTables::of($tasks)
            ->addColumn('action', function($row){
                return 
                "<div class='d-flex align-items-center justify-content-center gap-2'>
                    <a href='" . route('dashboard.tasks.show', $row) . "'><i class='ri-eye-line fs-4' type='submit'></i></a>
                "
                .
                (Auth::user()->hasRole('manager') || Auth::user()->id == $row['creator_id'] ?
                "
                    <a href='" . route('dashboard.tasks.edit', $row) . "'><i class='ri-settings-5-line fs-4' type='submit'></i></a>
                ":"")
                .
                "
                    <form data-id='".$row['id']."' onsubmit='remove_user(event)'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <button class='remove_button' type='button'><i class='ri-delete-bin-5-line text-danger fs-4'></i></button>
                    </form>
                </div>";
            })
            ->editColumn('task', function(Task $task){
                return truncatePost($task->title);
            })
            ->editColumn('asignee', function(Task $task){
                return "
                    <div class='d-flex align-items-center gap-2'>
                        <div class='rounded-4 overflow-hidden d-flex justify-content-center align-items-cneter' style='width: 30px; height: 30px;'>
                            <img src='". $task->assignee->display_image ."' style='min-width: 100%; min-height:100%'>  
                        </div>
                        <span>{$task->assignee->full_name} ". (Auth::id() == $task->assignee->id ? '('. __('dashboard.you') .')' : '') ."</span>
                    </div>
                ";
            })
            ->editColumn('creator', function(Task $task){
                return "
                    <div class='d-flex align-items-center gap-2'>
                        <div class='rounded-4 overflow-hidden d-flex justify-content-center align-items-cneter' style='width: 30px; height: 30px;'>
                            <img src='". $task->creator->display_image ."' style='min-width: 100%; min-height:100%'>  
                        </div>
                        <span>{$task->creator->full_name} ". (Auth::id() == $task->creator->id ? '('. __('dashboard.you') .')' : '') ."</span>
                    </div>
                ";
            })
            ->editColumn('status', function(Task $task){
                return match($task->status)
                {
                    TaskStatus::NEW->value => '<span class="badge text-bg-dark">'. __('dashboard.new') .'</span>',
                    TaskStatus::REVIEW->value => '<span class="badge text-bg-warning">'. __('dashboard.review') .'</span>',
                    TaskStatus::WORKING->value => '<span class="badge text-bg-primary">'. __('dashboard.working') .'</span>',
                    TaskStatus::FEEDBACK->value => '<span class="badge text-bg-warning">'. __('dashboard.feedback') .'</span>',
                    TaskStatus::DONE->value => '<span class="badge text-bg-success">'. __('dashboard.done') .'</span>',
                };
            })
            ->rawColumns(['asignee', 'creator', 'status', 'action'])
            ->make(true);
        }
        
        return view('dashboard.tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:300'],
            'due_date' => 'required|date',
            'assignee_id' => ['required', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'files.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,zip,rar,pdf,doc,docx,xls,xlsx', 'max:1048576']
        ]);

        $data['creator_id'] = Auth::user()->id;

        $task = Task::create($data);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('attachments', 'public');
                Attachment::create([
                    'entity_type' => 'task',
                    'entity_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->id
                ]);
            }
        }

        Notification::create([
            'user_id' => $data['assignee_id'],
            'title' => __('dashboard.new-task-created') . " (" . $task->title . ") " . __('dashboard.by') . " " . $task->creator->full_name . " " . __('dashboard.due_to') . " " . $task->due_date,
            'entity_type' => NotificationEntityType::TASK->value,
            'entity_id' => $task->id,
        ]);

        Mail::to($task->assignee->email)->send(new TaskCreatedMail($task));

        return response()->json(['redirectUrl' => route('dashboard.tasks.edit', $task)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if(Auth::user()->hasRole('manager') || Auth::user()->id == $task->creator->id || Auth::user()->id == $task->assignee->id)
        {
            return view('dashboard.tasks.show', compact('task'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if(Auth::user()->hasRole('manager') || (Auth::user()->hasRole('admin') && Auth::user()->id == $task->creator->id))
        {
            return view('dashboard.tasks.edit', compact('task'));
        }
        return response(401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if(Auth::user()->hasRole('manager') || (Auth::user()->hasRole('admin') && Auth::user()->id == $task->creator->id))
        {
            $data = $request->validate([
                'title' => ['required', 'string', 'max:300'],
                'status' => ['required', 'in:' . TaskStatus::NEW->value . "," . TaskStatus::WORKING->value . "," . TaskStatus::REVIEW->value . "," . TaskStatus::FEEDBACK->value . "," . TaskStatus::DONE->value . ","],
                'due_date' => 'required|date',
                'assignee_id' => ['required', 'exists:users,id'],
                'description' => ['nullable', 'string', 'max:5000'],
                'files.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,zip,rar,pdf,doc,docx,xls,xlsx', 'max:1048576'],
                'existingFiles' => ['nullable', 'array'],
                'existingFiles.*' => ['nullable', function ($attribute, $value, $fail) use ($task) {
                    if (!Attachment::where('id', $value)
                        ->where('entity_id', $task->id)
                        ->where('entity_type', 'task')
                        ->exists()) {
                        $fail(__('The selected file is invalid.'));
                    }
                },],
            ]);

            if($request->existingFiles)
            {
                $deletedAttachments = Attachment::where('entity_type', 'task')
                ->where('entity_id', $task->id)
                ->whereNotIn('id', $data['existingFiles'])->get();

                foreach ($deletedAttachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                    }
                    $attachment->delete();
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('attachments', 'public');
                    Attachment::create([
                        'entity_type' => 'task',
                        'entity_id' => $task->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::user()->id
                    ]);
                }
            }

            if($data['assignee_id'] != $task->assignee_id)
            {
                Notification::create([
                    'user_id' => $data['assignee_id'],
                    'title' => Auth::user()->full_name . __('dashboard.assigned_the_task') . " (" . $task->title . ") " . " " . __('dashboard.for_you'),
                    'entity_type' => NotificationEntityType::TASK->value,
                    'entity_id' => $task->id,
                ]); 
            }
            else if($data['status'] != $task->status)
            {
                Notification::create([
                    'user_id' => $data['assignee_id'],
                    'title' => Auth::user()->full_name . __('dashboard.changed_the_status_of_task_from') . " (" . $task->title . ") " . __('dashboard.to') . " " . __('dashboard.' . $data['status']),
                    'entity_type' => NotificationEntityType::TASK->value,
                    'entity_id' => $task->id,
                ]); 
            }
            else
            {
                Notification::create([
                    'user_id' => $data['assignee_id'],
                    'title' => Auth::user()->full_name . __('dashboard.updated_the_Task') . " (" . $task->title . ") ",
                    'entity_type' => NotificationEntityType::TASK->value,
                    'entity_id' => $task->id,
                ]); 
            }

            $task->update($data);

            return response()->json(
                [
                    'message' => __('dashboard.task_updated'),
                    'attachments_data' => $task->attachments
                ]
            );
        }
        return response(401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if(Auth::user()->hasRole('manager') || (Auth::user()->hasRole('admin') && Auth::user()->id == $task->creator->id))
        {
            foreach ($task->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
    
            foreach ($task->messages as $message) {
                foreach ($message->attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                    }
                    $attachment->delete();
                }
                $message->delete();
            }
    
            Notification::create([
                'user_id' => $task->assignee->id,
                'title' => __('dashboard.the_task') . " (" . $task->title . ") " . __('dashboard.has_been_deleted'),
                'entity_type' => NotificationEntityType::TASK->value,
                'entity_id' => $task->id,
            ]);
        
            Mail::to($task->assignee->email)->send(new TaskDeletedMail($task));
    
            $task->delete();
        
            return response()->json(['message' => __('dashboard.task_deleted')]);
        }
        return response(401);    
    }

    public function set_status(Request $request, Task $task)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . TaskStatus::NEW->value . "," . TaskStatus::WORKING->value . "," . TaskStatus::REVIEW->value . "," . TaskStatus::FEEDBACK->value . "," . TaskStatus::DONE->value]
        ]);

        if(in_array($data['status'], [TaskStatus::FEEDBACK->value, TaskStatus::DONE->value]) && !(Auth::user()->hasRole('manager') || Auth::user()->id == $task->creator->id))
        {
            return response(401);
        }

        $task->status = $data['status'];
        $task->save();

        Notification::create([
            'user_id' => $task->assignee->id,
            'title' => Auth::user()->full_name . __('dashboard.changed_the_status_of_task_from') . " (" . $task->title . ") " . __('dashboard.to') . " " . __('dashboard.' . $data['status']),
            'entity_type' => NotificationEntityType::TASK->value,
            'entity_id' => $task->id,
        ]); 

        return response()->json(['message' => __('dashboard.task_status_updated')]);
    }
}
