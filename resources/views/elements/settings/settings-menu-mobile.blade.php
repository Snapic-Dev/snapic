<div class="mt-3 inline-border-tabs text-bold">
    <nav class="nav nav-pills nav-justified">
        @foreach($availableSettings as $route => $setting)
        @if(Auth::user()->role_id !== 1 && (Auth::user()->role_id === 2 || (Auth::user()->role_id === 3 && !Auth::user()->identity_verified_at)) && ($route==="rates" || $route==="payments"))
        <div></div>
        @elseif( Auth::user()->role_id === 3 && Auth::user()->identity_verified_at && ($route==="producer"))
        <div></div>
        @else
        <a class="nav-item nav-link {{$activeSettingsTab == $route ? 'active' : ''}}" href="{{route('my.settings',['type'=>$route])}}">
            <div class="d-flex justify-content-center">
                @include('elements.icon',['icon'=>$setting['icon'].'-outline','centered'=>'false','variant'=>'medium'])
            </div>
        </a>
        @endif
        @endforeach
    </nav>
</div>