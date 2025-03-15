<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\InternshipRequestStatus;
use App\Enum\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\InternshipRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if(Auth::user()->hasAnyRole(['student', 'employee']))
        {   
            $statistics = $this->get_student_employee_statistics();
        }
        else if(Auth::user()->hasAnyRole(['manager', 'admin']))
        {
            $statistics = $this->get_manager_admin_statistics();
        }
        return view('dashboard.index', compact('statistics'));
    }
    private function get_student_employee_statistics()
    {
        $assigned_new_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::NEW->value)->count();
        $assigned_working_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::WORKING->value)->count();
        $assigned_feedback_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::FEEDBACK->value)->count();
        $assigned_review_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::REVIEW->value)->count();

        return [
            'assigned_new_tasks' => $assigned_new_tasks,
            'assigned_working_tasks' => $assigned_working_tasks,
            'assigned_feedback_tasks' => $assigned_feedback_tasks,
            'assigned_review_tasks' => $assigned_review_tasks
        ];
    }

    private function get_manager_admin_statistics()
    {
        $assigned_new_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::NEW->value)->count();
        $assigned_working_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::WORKING->value)->count();
        $assigned_feedback_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::FEEDBACK->value)->count();
        $assigned_review_tasks = Auth::user()->assigned_tasks()->where('status', TaskStatus::REVIEW->value)->count();

        $created_new_tasks = Auth::user()->created_tasks()->where('status', TaskStatus::NEW->value)->count();
        $created_working_tasks = Auth::user()->created_tasks()->where('status', TaskStatus::WORKING->value)->count();
        $created_feedback_tasks = Auth::user()->created_tasks()->where('status', TaskStatus::FEEDBACK->value)->count();
        $created_review_tasks = Auth::user()->created_tasks()->where('status', TaskStatus::REVIEW->value)->count();

        $pending_internships = InternshipRequest::where('status', InternshipRequestStatus::PENDING->value)->count();
        $accepted_internships = InternshipRequest::where('status', InternshipRequestStatus::ACCEPTED->value)->count();
        $rejected_internships = InternshipRequest::where('status', InternshipRequestStatus::REJECTED->value)->count();

        return [
            'assigned_new_tasks' => $assigned_new_tasks,
            'assigned_working_tasks' => $assigned_working_tasks,
            'assigned_feedback_tasks' => $assigned_feedback_tasks,
            'assigned_review_tasks' => $assigned_review_tasks,
            'created_new_tasks' => $created_new_tasks,
            'created_working_tasks' => $created_working_tasks,
            'created_feedback_tasks' => $created_feedback_tasks,
            'created_review_tasks' => $created_review_tasks,
            'pending_internships' => $pending_internships,
            'accepted_internships' => $accepted_internships,
            'rejected_internships' => $rejected_internships,
        ];
    }
}
