@extends('layouts.app')

@section('content')
    <div class="container h-100">
        <div class="row d-flex align-items-stretch mt-3 profile">
            <div class="col-12 col-lg-3 pb-3 text-center profile-sidebar">
                <h4 class="mt-3">{{ $user->name }}</h4>
                <p class="small my-0">
                    @php $pos = strpos($user->email, '@'); @endphp
                    {{ substr_replace($user->email, str_repeat('*', $pos - 4), 4, $pos - 4) }}
                </p>
                <p class="small text-{{ $user->banned_until === null ? 'success' : 'danger' }}">
                    @if ($user->banned_until === null)
                        Active
                    @else
                        Banned
                    @endif
                </p>

                {{-- Web View --}}
                <ul class="nav flex-column d-none d-md-block">
                    <li class="nav-item">
                        <a class="nav-link px-0" href="{{ route('user.activity-log', $user->id) }}">Activity Log</a>
                    </li>
                    @can('update', $user)
                        <li class="nav-item">
                            <a class="nav-link px-0" href="{{ route('user.account-settings', $user->id) }}">Account Settings</a>
                        </li>
                    @endcan
                    @can('manage', [new \App\Models\UserAddressBook(), $user->id])
                        <li class="nav-item">
                            <a class="nav-link px-0" href="{{ route('user.address-book',  $user->id) }}">Address Book</a>
                        </li>
                    @endcan
                    @can('listOwn', [new \App\Models\Store(), $user->id])
                        <li class="nav-item">
                            <a class="nav-link px-0" href="{{ route('user.stores',  $user->id) }}">Stores</a>
                        </li>
                    @endcan
                    @if (Auth::user()->id === $user->id)
                        <li class="nav-item">
                            <a class="nav-link px-0" href="{{ route('user.notifications', $user->id) }}">Notifications</a>
                        </li>
                    @endif
                </ul>

                {{-- Mobile View --}}
                <div class="accordion d-md-none" id="accordionFlush">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Profile Menu
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlush">
                            <div class="accordion-body">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link px-0" href="{{ route('user.activity-log', $user->id) }}">Activity Log</a>
                                    </li>
                                    @can('update', $user)
                                        <li class="nav-item">
                                            <a class="nav-link px-0" href="{{ route('user.account-settings', $user->id) }}">Account Settings</a>
                                        </li>
                                    @endcan
                                    @can('manage', [new \App\Models\UserAddressBook(), $user->id])
                                        <li class="nav-item">
                                            <a class="nav-link px-0" href="{{ route('user.address-book', $user->id) }}">Address Book</a>
                                        </li>
                                    @endcan
                                    @can('listOwn', [new \App\Models\Store(), $user->id])
                                        <li class="nav-item">
                                            <a class="nav-link px-0" href="{{ route('user.stores',  $user->id) }}">Stores</a>
                                        </li>
                                    @endcan
                                    @if (Auth::user()->id === $user->id)
                                        <li class="nav-item">
                                            <a class="nav-link px-0" href="{{ route('user.notifications', $user->id) }}">Notifications</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @can('restrict', $user)
                    <div class="mt-3">
                        <a href="#" class="btn btn-danger btn-sm">Ban User</a>
                    </div>
                @endcan
            </div>
            <div class="col-12 col-lg-9">
                @yield('profile-content')
            </div>
        </div>
    </div>
@endsection