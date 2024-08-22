<li class="dropdown notifications-menu">
    <a href="#" class="waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown" title="Notifications">
        <i class="icon-Notifications"><span class="path1"></span><span class="path2"></span></i>
    </a>
    <ul class="dropdown-menu animated bounceIn">

        <li class="header">
            <div class="p-20">
                <div class="flexbox">
                    <div>
                        <h4 class="mb-0 mt-0">Notifications</h4>
                    </div>
                    <div>
                        @if ($unreadNotificationCount)
                            <a href="#" class="text-danger">Mark All As Read</a>
                        @endif
                    </div>
                </div>
            </div>
        </li>

        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu sm-scrol">
                @forelse ($notifications as $notify)
                    <li>
                        <a href="#">
                            <i class="fa fa-users text-info"></i>
                            {{ $notify->title }}
                        </a>
                    </li>
                @empty
                    <li class="text-center">
                        <small>No Notifications</small>
                    </li>
                @endforelse
            </ul>
        </li>
    </ul>
</li>
