<?php

namespace App\View\Components\StudentDashboard\Notification;

use App\Models\Notification as NotificationModel;
use Illuminate\View\Component;

class NotificationComponent extends Component
{
    public $notifications;
    public $unreadNotificationCount;

    public function __construct($count = 10)
    {
        $user = auth()->user();
        $notificationQuery = NotificationModel::query()->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        $this->notifications = $notificationQuery->latest()->paginate($count);
        $this->unreadNotificationCount = $notificationQuery->count();
    }

    public function render()
    {
        return view('components.student-dashboard.notification.notification-component');
    }
}
