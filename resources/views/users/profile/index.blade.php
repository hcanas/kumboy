@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-12 col-md-3">
                <h4 class="mt-3">{{ $user->name }}</h4>
                <p class="small my-0">{{ $user->email }}</p>
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
                    @can('viewAccountSettings', $user)
                        <li class="nav-item">
                            <a class="nav-link px-0" href="{{ route('user.account-settings', $user->id) }}">Account Settings</a>
                        </li>
                    @endcan
                    @can('viewAddressBook', [new \App\Models\UserAddressBook(), $user->id])
                        <li class="nav-item">
                            <a class="nav-link px-0" href="{{ route('user.address-book',  $user->id) }}">Address Book</a>
                        </li>
                    @endcan
                    @can('viewUserStores', [new \App\Models\Store(), $user->id])
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
                                    @can('viewAccountSettings', $user)
                                        <li class="nav-item">
                                            <a class="nav-link px-0" href="{{ route('user.account-settings', $user->id) }}">Account Settings</a>
                                        </li>
                                    @endcan
                                    @can('viewAddressBook', [new \App\Models\UserAddressBook(), $user->id])
                                        <li class="nav-item">
                                            <a class="nav-link px-0" href="{{ route('user.address-book', $user->id) }}">Address Book</a>
                                        </li>
                                    @endcan
                                    @can('viewUserStores', [new \App\Models\Store(), $user->id])
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

                @can('banUser', $user)
                    <div class="mt-3">
                        <a href="#" class="btn btn-danger btn-sm">Ban User</a>
                    </div>
                @endcan
            </div>
            <div class="col-12 col-md-9">
                @include($content, $contentData)
            </div>
        </div>
    </div>
@endsection