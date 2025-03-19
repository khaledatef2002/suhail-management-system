<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function display(Notification $notification)
    {
        $notification->is_read = true;
        $notification->save();

        return redirect($notification->display_href);
    }
}
