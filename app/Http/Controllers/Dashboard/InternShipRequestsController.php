<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\InternshipRequestStatus;
use App\Http\Controllers\Controller;
use App\Mail\SendNewAccountInfoMail;
use App\Models\InternshipRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class InternShipRequestsController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('role:manager|admin')
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $intern_requests = InternshipRequest::query();
            return DataTables::of($intern_requests)
            ->addColumn('action', function(InternshipRequest $intern_request){
                $is_already_user = User::where('email', $intern_request->email)->count() > 0;
                return 
                "
                <div class='dropdown'>
                    <button class='btn btn-soft-secondary btn-sm dropdown' type='button' data-bs-toggle='dropdown' aria-expanded='true'>
                        <i class='ri-more-fill'></i>
                    </button>
                    <ul class='dropdown-menu dropdown-menu-end' data-popper-placement='bottom-end' style='position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(-34px, 29px);'>
                "
                .
                ($intern_request->status == InternshipRequestStatus::PENDING->value ?
                ("
                        <li>
                            <a class='dropdown-item accept' data-id='". $intern_request->id ."' role='button'>
                                <i class='ri-eye-fill align-bottom me-2 text-muted'></i>
                                Accept
                            </a>
                        </li>
                "
                    .
                    (!$is_already_user ?
                    "
                            <li>
                                <a class='dropdown-item accept_and_create' data-id='". $intern_request->id ."' role='button'>
                                    <i class='ri-eye-fill align-bottom me-2 text-muted'></i>
                                    Accept & Create Account
                                </a>
                            </li>
                            <li>
                                <a class='dropdown-item reject' data-id='". $intern_request->id ."' role='button'>
                                    <i class='ri-pencil-fill align-bottom me-2 text-muted'></i>
                                    Reject
                                </a>
                            </li>
                            <li class='dropdown-divider'></li>
                    ":"")
                ) : "")
                .
                "
                        <li>
                            <form data-id='".$intern_request->id."' onsubmit='remove_user(event)'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='_token' value='" . csrf_token() . "'>
                                <button class='remove_button dropdown-item edit-list' type='button'>
                                    <i class='ri-delete-bin-fill align-bottom me-2 text-muted'></i>
                                    Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                ";
            })
            ->addColumn('full_name', function(InternshipRequest $intern_request){
                $is_already_user = User::where('email', $intern_request->email)->count() > 0;
                return $intern_request->first_name . ' ' . $intern_request->last_name . ($is_already_user ? " <span class='badge text-bg-success'>". __('dashboard.already_a_user') ."</span>" : "");
            })
            ->editColumn('cv', function(InternshipRequest $intern_request){
                return "<a href='". asset('storage/' . $intern_request->cv) ."' target='_blank'> <i class='ri-file-transfer-line fs-3'></i> </a>";
            })
            ->editColumn('status', function(InternshipRequest $intern_request){
                return match($intern_request->status){
                    InternshipRequestStatus::ACCEPTED->value => '<span class="badge text-bg-success">'. __('dashboard.accepted') .'</span>',
                    InternshipRequestStatus::PENDING->value => '<span class="badge text-bg-warning">'. __('dashboard.pending') .'</span>',
                    InternshipRequestStatus::REJECTED->value => '<span class="badge text-bg-danger">'. __('dashboard.rejected') .'</span>'
                };
            })
            ->rawColumns(['full_name', 'cv', 'status', 'action'])
            ->make(true);
        }
        return view('dashboard.internship-requests.index');
    }

    public function accept(Request $request, InternshipRequest $internship_request)
    {
        $internship_request->status = InternshipRequestStatus::ACCEPTED->value;
        $internship_request->save();

        return response()->json(['message' => __('dashboard.internship_request_accepted')]);       
    }
    public function accept_and_create(Request $request, InternshipRequest $internship_request)
    {
        $is_email_exists = User::where('email', $internship_request->email)->count() > 0;
        $is_phone_exists = User::where('phone_number', $internship_request->phone_number)->count() > 0;
        if($is_email_exists)
        {
            return response()->json(['errors' => [ 'email' => [__('dashboard.email_is_already_created')]]]);
        }
        else if($is_phone_exists)
        {
            return response()->json(['errors' => [ 'email' => [__('dashboard.phone_is_already_created')]]]);
        }

        $internship_request->status = InternshipRequestStatus::ACCEPTED->value;
        $internship_request->save();

        $randomPassword = Str::random(12);

        $user = User::create([
            'first_name' => $internship_request->first_name,
            'last_name' => $internship_request->last_name,
            'email' => $internship_request->email,
            'password' => $randomPassword,
            'date_of_birth' => $internship_request->date_of_birth,
            'phone_number' => $internship_request->phone_number,
        ]);

        $user->assignRole('student');

        Mail::to($user->email)->send(new SendNewAccountInfoMail($user, $randomPassword));

        return response()->json(['message' => __('dashboard.internship_request_accepted_and_email_sent')]);
    }
    public function reject(InternshipRequest $internship_request)
    {
        $internship_request->status = InternshipRequestStatus::REJECTED->value;
        $internship_request->save();

        return response()->json(['message' => __('dashboard.internship_request_rejected')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternshipRequest $internship_request)
    {
        if($internship_request->cv && Storage::disk('public')->exists($internship_request->cv))
        {
            Storage::disk('public')->delete($internship_request->cv);
        }

        $internship_request->delete();

        return response()->json(['message' => __('dashboard.internship_request_deleted')]);
    }
}
