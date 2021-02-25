@extends('pages.user.profile')
@section('page-title', $user->name.' - Activity Log')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center my-3">
                <h4 class="text-black-50 my-0">Activity Log</h4>
                <form action="{{ route('user.search-activity', $user->id) }}" METHOD="POST">
                    @csrf
                    <div class="input-group">
                        <input type="search" name="keyword" class="form-control form-control-sm" value="{{ $keyword ?? '' }}" placeholder="Search keyword...">
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <i class="material-icons d-md-none">search</i>
                            <span class="d-none d-sm-inline">Search</span>
                        </button>
                    </div>
                </form>
            </div>

            @if ($activities->isEmpty())
                <div class="alert alert-danger mt-2">No records found.</div>
            @else
                @php $odd = true; @endphp
                @foreach ($activities AS $activity)
                    <p class="p-2 mb-1 {{ $odd ? 'bg-light' : '' }}">
                        @switch($activity->category)
                            @case('generic')
                                {{ $activity->action_taken }}
                                @break
                            @case('new_store')
                            @case('update_store')
                            @case('store_transfer')
                                @php
                                    preg_match('/<ref_no>(.+)<\/ref_no>/i', $activity->action_taken, $match);
                                    $ref_no = $match[1];

                                    echo str_replace(
                                        [
                                            '<ref_no>',
                                            '</ref_no>'
                                        ],
                                        [
                                            '<a href="'
                                            .route('user.store-request-details', [$activity->user_id, $ref_no])
                                            .'">',
                                            '</a>'
                                        ],
                                        $activity->action_taken
                                    );
                                @endphp
                                @break
                            @case('new_product')
                            @case('update_product')
                                @php
                                    $product_id_raw = substr($activity->action_taken,
                                        strpos($activity->action_taken, '<product_id>'),
                                        strpos($activity->action_taken, '</product_id>')
                                            - strpos($activity->action_taken, '<product_id>')
                                            + 13,
                                    );

                                    $product_id = str_replace(['<product_id>', '</product_id>'], '', $product_id_raw);

                                    $store_id_raw = substr($activity->action_taken,
                                        strpos($activity->action_taken, '<store_id>'),
                                        strpos($activity->action_taken, '</store_id>')
                                            - strpos($activity->action_taken, '<store_id>')
                                            + 11,
                                    );

                                    $store_id = str_replace(['<store_id>', '</store_id>'], '', $store_id_raw);

                                    $activity->action_taken = str_replace(
                                        [
                                            '<product_name>',
                                            '</product_name>',
                                            '<store_name>',
                                            '</store_name>',
                                            $product_id_raw,
                                            $store_id_raw
                                        ],
                                        [
                                            '<a href="'.route('product.info', $product_id).'">',
                                            '</a>',
                                            '<a href="'.route('store.products', $store_id).'">',
                                            '</a>',
                                            '',
                                            ''
                                        ],
                                        $activity->action_taken
                                    );

                                    echo $activity->action_taken;
                                @endphp
                                @break;
                            @case('new_voucher')
                            @case('update_voucher')
                                @php
                                    $store_id_raw = substr($activity->action_taken,
                                        strpos($activity->action_taken, '<store_id>'),
                                        strpos($activity->action_taken, '</store_id>')
                                            - strpos($activity->action_taken, '<store_id>')
                                            + 11,
                                    );

                                    $store_id = str_replace(['<store_id>', '</store_id>'], '', $store_id_raw);

                                    $activity->action_taken = str_replace(
                                        [
                                            '<store_name>',
                                            '</store_name>',
                                            $store_id_raw
                                        ],
                                        [
                                            '<a href="'.route('store.products', $store_id).'">',
                                            '</a>',
                                            ''
                                        ],
                                        $activity->action_taken
                                    );

                                    echo $activity->action_taken;
                                @endphp
                                @break;
                        @endswitch
                        <span class="small text-muted">&ndash; {{ date('M j, Y h:iA', strtotime($activity->date_recorded)) }}</span>
                    </p>
                    @php $odd = !$odd; @endphp
                @endforeach

                @php echo $pagination @endphp
            @endif
        </div>
    </div>
@endsection