@extends('layouts.app')
@section('page-title', 'Users')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1w pb-2">
                    <h4 class="my-0">Users</h4>
                    <form action="{{ route('user.search') }}" METHOD="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" name="keyword" class="form-control form-control-sm" placeholder="Search keyword...">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </div>
                    </form>
                </div>
                @if ($users->isEmpty())
                    <div class="alert alert-danger mt-3">No records found.</div>
                @else
                    @if ($users->isNotEmpty())
                        @foreach ($users AS $user)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <div>
                                    <a href="{{ route('user.activity-log', $user->id) }}" class="h6">{{ $user->name }}</a> <br>
                                    <span class="small text-secondary">{{ ucwords($user->role) }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="small">{{ $user->email }}</span> <br>
                                    <span class="small text-{{ $user->banned_until === null ? 'success' : 'danger' }}">
                                        @if ($user->banned_until === null)
                                            Active
                                        @else
                                            Banned
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @include('shared.pagination', [
                        'item_start' => $item_start,
                        'item_end' => $item_end,
                        'total_count' => $total_count,
                        'current_page' => $current_page,
                        'total_pages' => $total_pages,
                        'items_per_page' => $items_per_page,
                        'keyword' => $keyword,
                        'url' => route('user.view-all'),
                    ])
                @endif
            </div>
        </div>
    </div>
@endsection