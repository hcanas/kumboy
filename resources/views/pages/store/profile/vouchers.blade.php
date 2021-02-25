@extends('pages.store.profile')
@section('page-title', $store->name.' - Vouchers')

@section('profile-content')
    <div class="row my-3">
        <div class="col-12">
            <div class="d-flex justify-content-end align-items-center my-3">
                @can('manage', [new \App\Models\Voucher(), $store->user_id])
                    <button type="button" class="btn btn-primary btn-sm me-2" id="add_voucher">
                        <div class="d-flex align-items-center">
                            <i class="material-icons fs-16">add</i>
                            <span class="ms-1 small">Add Voucher</span>
                        </div>
                    </button>
                @endcan
                <form action="{{ route('store.search-vouchers', $store->id) }}" METHOD="POST">
                    @csrf
                    <div class="input-group">
                        <input type="search" name="keyword" class="form-control form-control-sm" value="{{ $keyword ?? '' }}" placeholder="Search keyword...">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    </div>
                </form>
            </div>

            <div id="system_message"></div>

            @if ($vouchers->isEmpty())
                <div class="text-center text-muted">No vouchers available.</div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 gx-1 gx-lg-2 gy-2 g-lg-2" id="voucher_list">
                    @can('manage', [new \App\Models\Voucher(), $store->user_id])
                        <div class="col d-none" id="voucher_template">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-none voucher_id"></div>
                                            <h5 class="text-success my-0 voucher_code"></h5>
                                            <div class="d-grid">
                                                <span class="text-end">
                                                    <span class="d-none peso_symbol">&#8369;</span>
                                                    <span class="voucher_amount"></span>
                                                    <span class="d-none percent_symbol">%</span>
                                                    <span class="d-none voucher_amount_raw"></span>
                                                    <span class="d-none voucher_type_raw"></span>
                                                </span>
                                                <span class="text-muted text-end small">
                                                    <span class="voucher_qty"></span>
                                                    <span>LEFT</span>
                                                    <span class="d-none voucher_qty_raw"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="fst-italic small mt-2 voucher_categories"></div>
                                        <span class="d-none voucher_categories_raw"></span>
                                    </div>

                                    <div>
                                        <div class="d-flex flex-column align-items-start">
                                            <div class="text-muted small">
                                                Can only be used
                                                <span class="voucher_limit_per_user"></span>
                                                time(s) per user.
                                                <span class="d-none voucher_limit_per_user_raw"></span>
                                            </div>
                                            <div class="text-muted small">
                                                <span class="voucher_valid_from"></span>
                                                <span class="d-none voucher_valid_from_raw"></span>
                                                &ndash;
                                                <span class="voucher_valid_to"></span>
                                                <span class="d-none voucher_valid_to_raw"></span>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-dark btn-sm my-1 edit_voucher">
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons fs-14">edit</i>
                                                    <span class="ms-1 small">Edit</span>
                                                </div>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm my-1 deactivate_voucher">
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons fs-14">clear</i>
                                                    <span class="ms-1 small">Deactivate</span>
                                                </div>
                                            </button>
                                            <button type="button" class="d-none btn btn-success btn-sm my-1 activate_voucher">
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons fs-14">check</i>
                                                    <span class="ms-1 small">Activate</span>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @foreach ($vouchers AS $voucher)
                        <div class="col" id="voucher_{{ $voucher->id }}">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-none voucher_id">{{ $voucher->id }}</div>
                                            <h5 class="{{ $voucher->status === 'active' ? 'text-success' : 'text-danger' }} my-0 voucher_code">{{ $voucher->code }}</h5>
                                            <div class="d-grid">
                                            <span class="text-end">
                                                <span class="@if ($voucher->type !== 'Flat Amount') d-none @endif peso_symbol">&#8369;</span>
                                                <span class="voucher_amount">{{ number_format($voucher->amount, 2) }}</span>
                                                <span class="@if ($voucher->type !== 'Percentage') d-none @endif percent_symbol">%</span>
                                                <span class="d-none voucher_amount_raw">{{ $voucher->amount }}</span>
                                                <span class="d-none voucher_type_raw">{{ $voucher->type }}</span>
                                            </span>
                                            <span class="text-muted text-end small">
                                                <span class="voucher_qty">{{ $voucher->qty }}</span>
                                                <span>LEFT</span>
                                                <span class="d-none voucher_qty_raw">{{ $voucher->qty }}</span>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="fst-italic small mt-2 voucher_categories">
                                            {{ implode(', ', array_map(function ($category) {
                                                        return ucwords(str_replace('|', ' - ', $category));
                                                    }, $voucher->categories)) }}
                                        </div>
                                        <span class="d-none voucher_categories_raw">{{ json_encode($voucher->categories) }}</span>
                                    </div>

                                    <div>
                                        <div class="d-flex flex-column align-items-start">
                                            <div class="text-muted small">
                                                Can only be used
                                                <span class="voucher_limit_per_user">
                                                    {{ $voucher->limit_per_user }}
                                                </span>
                                                time(s) per user.
                                                <span class="d-none voucher_limit_per_user_raw">{{ $voucher->qty }}</span>
                                            </div>
                                            <div class="text-muted small">
                                                <span class="voucher_valid_from">
                                                    {{ date('M d, Y', strtotime($voucher->valid_from)) }}
                                                </span>
                                                <span class="d-none voucher_valid_from_raw">{{ date('Y-m-d', strtotime($voucher->valid_from)) }}</span>
                                                &ndash;
                                                <span class="voucher_valid_to">
                                                    {{ date('M d, Y', strtotime($voucher->valid_to)) }}
                                                </span>
                                                <span class="d-none voucher_valid_to_raw">{{ date('Y-m-d', strtotime($voucher->valid_to)) }}</span>
                                            </div>
                                        </div>

                                        @can('manage', [new \App\Models\Voucher(), $store->user_id])
                                            <div class="d-flex justify-content-between mt-2">
                                                <button type="button" class="btn btn-outline-dark btn-sm my-1 edit_voucher">
                                                    <div class="d-flex align-items-center">
                                                        <i class="material-icons fs-14">edit</i>
                                                        <span class="ms-1 small">Edit</span>
                                                    </div>
                                                </button>
                                                <button type="button" class="@if ($voucher->status === 'inactive') d-none @endif btn btn-danger btn-sm my-1 deactivate_voucher">
                                                    <div class="d-flex align-items-center">
                                                        <i class="material-icons fs-14">clear</i>
                                                        <span class="ms-1 small">Deactivate</span>
                                                    </div>
                                                </button>
                                                <button type="button" class="@if ($voucher->status === 'active') d-none @endif btn btn-success btn-sm my-1 activate_voucher">
                                                    <div class="d-flex align-items-center">
                                                        <i class="material-icons fs-14">check</i>
                                                        <span class="ms-1 small">Activate</span>
                                                    </div>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @php echo $pagination; @endphp
            @endif
        </div>
    </div>

    <div class="modal fade" id="modal_confirm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>
                    <form id="form_confirm">
                        <input type="hidden" name="id">
                        <input type="hidden" name="status">
                    </form>
                    <div id="question"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">No</button>
                    <button class="btn btn-sm" id="confirm">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_voucher" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title">Voucher Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <form id="form_voucher">
                        @csrf
                        <input type="hidden" name="id">

                        <div class="form-floating mb-3">
                            <input type="text" name="code" class="form-control" placeholder="Code">
                            <label>Code</label>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" name="amount" class="form-control no-spin" min="0.25" step="0.25" placeholder="Amount">
                            <label>Amount</label>
                            <div class="d-flex align-items-center mt-2">
                                <input type="radio" name="type" id="btnradio1" autocomplete="off" value="Flat Amount" checked>
                                <label class="ms-1 me-3 small" for="btnradio1">Flat Amount Discount</label>

                                <input type="radio" name="type" id="btnradio2" value="Percentage" autocomplete="off">
                                <label class="mx-1 small" for="btnradio2">Percentage Discount</label>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" name="qty" class="form-control no-spin" min="1" step="1" placeholder="Quantity">
                            <label>Quantity</label>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" name="limit_per_user" class="form-control no-spin" min="1" step="1" placeholder="Limit Per User">
                            <label>Limit Per User</label>
                            <div class="form-text">The number of times each user can use this voucher.</div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            @php
                                $categories = config('system.product_categories');
                                $count = 0;
                            @endphp
                            <select name="categories[]" class="form-select form-select-sm h-100 overflow-hidden" multiple>
                                @foreach ($categories AS $main_cat => $value)
                                    @php
                                        $category = $main_cat.'|all';
                                        $count++;
                                    @endphp
                                    <option value="{{ $category }}">{{ ucwords($main_cat).' - All' }}</option>

                                    @if (empty($value) === false AND is_array($value))
                                        @foreach ($value AS $sub_cat => $placeholder)
                                            @php
                                                $category = $main_cat.'|'.$sub_cat;
                                                $count++;
                                            @endphp
                                            <option value="{{ $category }}">
                                                {{ ucwords($main_cat.' - '.$sub_cat) }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                            <label>Categories</label>
                            <div class="d-none" id="category_count">{{ $count }}</div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="date" name="valid_from" class="form-control" placeholder="Valid From">
                            <label>Valid From</label>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="date" name="valid_to" class="form-control" placeholder="Valid To">
                            <label>Valid To</label>
                            <div class="text-danger small field_error"></div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="save_voucher">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script defer>
        const number_formatter = new Intl.NumberFormat('en-PH');
        const el_system_message = document.getElementById('system_message');
        const el_voucher_list = document.getElementById('voucher_list');
        const el_voucher_template = document.getElementById('voucher_template');
        const el_modal_confirm = document.getElementById('modal_confirm');
        const el_modal_voucher = document.getElementById('modal_voucher');
        const el_form_message = el_modal_voucher.querySelector('#form_message');
        const el_form_voucher = el_modal_voucher.querySelector('#form_voucher');
        const el_form_id = el_form_voucher.querySelector('[name="id"]');
        const el_form_code = el_form_voucher.querySelector('[name="code"]');
        const el_form_amount = el_form_voucher.querySelector('[name="amount"]');
        const el_form_type = el_form_voucher.querySelector('[name="type"]');
        const el_form_categories = el_form_voucher.querySelector('[name="categories[]"]');
        const el_form_limit_per_user = el_form_voucher.querySelector('[name="limit_per_user"]');
        const el_form_qty = el_form_voucher.querySelector('[name="qty"]');
        const el_form_valid_from = el_form_voucher.querySelector('[name="valid_from"]');
        const el_form_valid_to = el_form_voucher.querySelector('[name="valid_to"]');

        document.getElementById('add_voucher').addEventListener('click', e => {
            e.preventDefault();
            el_form_voucher.reset();
            el_form_id.value = '';

            const modal = new bootstrap.Modal(el_modal_voucher, {
                keyboard: false,
                backdrop: 'static',
            });
            modal.show();
        });

        document.querySelectorAll('.edit_voucher').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                edit_voucher(btn.parentNode.parentNode.parentNode);
            });
        });

        document.getElementById('save_voucher').addEventListener('click', e => {
            e.preventDefault();
            el_modal_voucher.querySelector('.loading_spinner').classList.remove('d-none');

            el_form_message.textContent = '';
            el_form_message.removeAttribute('class');
            el_form_voucher.querySelectorAll('.field_error').forEach(field => field.textContent = '');

            const form_data = new FormData(el_form_voucher);

            if (form_data.get('id')) {
                axios.post('{{ route('store.update-voucher', $store->id) }}', form_data)
                    .then(response => {
                        const data = response.data;
                        const voucher = document.getElementById('voucher_' + data.id);

                        voucher.querySelector('.voucher_code').textContent =
                            data.code;
                        voucher.querySelector('.voucher_amount').textContent =
                            number_formatter.format(data.amount);
                        voucher.querySelector('.voucher_amount_raw').textContent =
                            data.amount;
                        voucher.querySelector('.voucher_type_raw').textContent =
                            data.type;
                        voucher.querySelector('.voucher_categories').textContent =
                            data.categories.map(category => category.replace('|', ' - '))
                                .join(', ')
                                .replace(/\b[a-z]/g, letter => letter.toUpperCase());
                        voucher.querySelector('.voucher_categories_raw').textContent =
                            JSON.stringify(data.categories);
                        voucher.querySelector('.voucher_limit_per_user').textContent =
                            number_formatter.format(data.limit_per_user);
                        voucher.querySelector('.voucher_limit_per_user_raw').textContent =
                            data.limit_per_user;
                        voucher.querySelector('.voucher_qty').textContent =
                            data.qty;
                        voucher.querySelector('.voucher_qty_raw').textContent =
                            data.qty;
                        voucher.querySelector('.voucher_valid_from').textContent =
                            dateFormat(new Date(data.valid_from), 'mmm dd, yyyy');
                        voucher.querySelector('.voucher_valid_from_raw').textContent =
                            data.valid_from;
                        voucher.querySelector('.voucher_valid_to').textContent =
                            dateFormat(new Date(data.valid_to), 'mmm dd, yyyy');
                        voucher.querySelector('.voucher_valid_to_raw').textContent =
                            data.valid_to;

                        if (data.type === 'Flat Amount') {
                            voucher.querySelector('.peso_symbol').classList.remove('d-none');
                            voucher.querySelector('.percent_symbol').classList.remove('d-none');
                            voucher.querySelector('.percent_symbol').classList.add('d-none');
                        } else if (data.type === 'Percentage') {
                            voucher.querySelector('.percent_symbol').classList.remove('d-none');
                            voucher.querySelector('.peso_symbol').classList.remove('d-none');
                            voucher.querySelector('.peso_symbol').classList.add('d-none');
                        }

                        el_system_message.textContent = data.code + ' voucher has been updated.';
                        el_system_message.setAttribute('class', 'alert alert-success small');
                        el_modal_voucher.querySelector('.loading_spinner').classList.add('d-none');
                        bootstrap.Modal.getInstance(el_modal_voucher).hide();
                    })
                    .catch(error => {
                        const errors = error.response.data;

                        if (typeof errors === 'object') {
                            el_form_code.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('code') ? errors.code : '';
                            el_form_type.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('type') ? errors.type : '';
                            el_form_amount.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('amount') ? errors.amount : '';
                            el_form_qty.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('qty') ? errors.qty : '';
                            el_form_limit_per_user.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('limit_per_user') ? errors.limit_per_user : '';
                            el_form_categories.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('categories') ? errors.categories : '';
                            el_form_valid_from.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('valid_from') ? errors.valid_from : '';
                            el_form_valid_to.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('valid_to') ? errors.valid_to : '';
                        } else {
                            el_form_message.textContent = errors;
                            el_form_message.setAttribute('class', 'alert alert-danger small');
                        }

                        el_modal_voucher.querySelector('.loading_spinner').classList.add('d-none');
                    });
            } else {
                axios.post('{{ route('store.add-voucher', $store->id) }}', form_data)
                    .then(response => {
                        const data = response.data;

                        const template_copy = el_voucher_template.cloneNode(true);
                        template_copy.classList.remove('d-none');
                        template_copy.setAttribute('id', 'voucher_' + data.id);

                        template_copy.querySelector('.voucher_id').textContent =
                            data.id;
                        template_copy.querySelector('.voucher_code').textContent =
                            data.code;
                        template_copy.querySelector('.voucher_amount').textContent =
                            number_formatter.format(data.amount);
                        template_copy.querySelector('.voucher_amount_raw').textContent =
                            data.amount;
                        template_copy.querySelector('.voucher_type_raw').textContent =
                            data.type;
                        template_copy.querySelector('.voucher_categories').textContent =
                            data.categories.map(category => category.replace('|', ' - '))
                                .join(', ')
                                .replace(/\b[a-z]/g, letter => letter.toUpperCase());
                        template_copy.querySelector('.voucher_categories_raw').textContent =
                            JSON.stringify(data.categories);
                        template_copy.querySelector('.voucher_limit_per_user').textContent =
                            number_formatter.format(data.limit_per_user);
                        template_copy.querySelector('.voucher_limit_per_user_raw').textContent =
                            data.limit_per_user;
                        template_copy.querySelector('.voucher_qty').textContent =
                            data.qty;
                        template_copy.querySelector('.voucher_qty_raw').textContent =
                            data.qty;
                        template_copy.querySelector('.voucher_valid_from').textContent =
                            dateFormat(new Date(data.valid_from), 'mmm dd, yyyy');
                        template_copy.querySelector('.voucher_valid_from_raw').textContent =
                            data.valid_from;
                        template_copy.querySelector('.voucher_valid_to').textContent =
                            dateFormat(new Date(data.valid_to), 'mmm dd, yyyy');
                        template_copy.querySelector('.voucher_valid_to_raw').textContent =
                            data.valid_to;

                        if (data.type === 'Flat Amount') {
                            template_copy.querySelector('.peso_symbol').classList.remove('d-none');
                        } else if (data.type === 'Percentage') {
                            template_copy.querySelector('.percent_symbol').classList.remove('d-none');
                        }

                        template_copy.querySelector('.edit_voucher').addEventListener('click', e => {
                            e.preventDefault();
                            edit_voucher(template_copy);
                        });

                        template_copy.querySelector('.activate_voucher').addEventListener('click', e => {
                            e.preventDefault();
                            activate_voucher(template_copy);
                        });

                        template_copy.querySelector('.deactivate_voucher').addEventListener('click', e => {
                            e.preventDefault();
                            deactivate_voucher(template_copy);
                        });

                        el_voucher_template.insertAdjacentElement('afterend', template_copy);
                        el_system_message.textContent = data.code + ' voucher has been added.';
                        el_system_message.setAttribute('class', 'alert alert-success small');
                        el_modal_voucher.querySelector('.loading_spinner').classList.add('d-none');
                        bootstrap.Modal.getInstance(el_modal_voucher).hide();
                    })
                    .catch(error => {
                        console.log(error);
                        const errors = error.response.data;

                        if (typeof errors === 'object') {
                            el_form_code.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('code') ? errors.code : '';
                            el_form_type.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('type') ? errors.type : '';
                            el_form_amount.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('amount') ? errors.amount : '';
                            el_form_qty.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('qty') ? errors.qty : '';
                            el_form_limit_per_user.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('limit_per_user') ? errors.limit_per_user : '';
                            el_form_categories.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('categories') ? errors.categories : '';
                            el_form_valid_from.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('valid_from') ? errors.valid_from : '';
                            el_form_valid_to.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('valid_to') ? errors.valid_to : '';
                        } else {
                            el_form_message.textContent = errors;
                            el_form_message.setAttribute('class', 'alert alert-danger small');
                        }

                        el_modal_voucher.querySelector('.loading_spinner').classList.add('d-none');
                    });
            }
        });

        el_form_categories.setAttribute('size', parseInt(document.getElementById('category_count').textContent));
        el_form_categories.querySelectorAll('option').forEach(option => {
            option.addEventListener('mousedown', e => {
                option.selected = !option.selected;
                e.preventDefault();
            });
        });

        document.querySelectorAll('.activate_voucher').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                activate_voucher(btn.parentNode.parentNode.parentNode);
            });
        });

        document.querySelectorAll('.deactivate_voucher').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                deactivate_voucher(btn.parentNode.parentNode.parentNode);
            });
        });

        el_modal_confirm.querySelector('#confirm').addEventListener('click', e => {
            e.preventDefault();
            el_modal_confirm.querySelector('.loading_spinner').classList.remove('d-none');

            const url = '/stores/{{ $store->id }}/vouchers/'
                + (el_modal_confirm.querySelector('[name="status"]').value === 'active' ? 'activate' : 'deactivate');

            const form_data = new FormData(el_modal_confirm.querySelector('#form_confirm'));

            axios.post(url, form_data)
                .then(response => {
                    const data = response.data;

                    el_system_message.setAttribute('class', 'alert alert-success small');

                    if (data.status === 'active') {
                        el_system_message.textContent = data.code + ' voucher has been activated.';
                        document.getElementById('voucher_' + data.id).querySelector('.voucher_code').classList.remove('text-danger');
                        document.getElementById('voucher_' + data.id).querySelector('.voucher_code').classList.add('text-success');
                        document.getElementById('voucher_' + data.id).querySelector('.deactivate_voucher').classList.remove('d-none');
                        document.getElementById('voucher_' + data.id).querySelector('.activate_voucher').classList.remove('d-none');
                        document.getElementById('voucher_' + data.id).querySelector('.activate_voucher').classList.add('d-none');
                    } else if (data.status === 'inactive') {
                        el_system_message.textContent = data.code + ' voucher has been deactivated.';
                        document.getElementById('voucher_' + data.id).querySelector('.voucher_code').classList.remove('text-success');
                        document.getElementById('voucher_' + data.id).querySelector('.voucher_code').classList.add('text-danger');
                        document.getElementById('voucher_' + data.id).querySelector('.activate_voucher').classList.remove('d-none');
                        document.getElementById('voucher_' + data.id).querySelector('.deactivate_voucher').classList.remove('d-none');
                        document.getElementById('voucher_' + data.id).querySelector('.deactivate_voucher').classList.add('d-none');
                    }

                    el_modal_confirm.querySelector('.loading_spinner').classList.add('d-none');
                    bootstrap.Modal.getInstance(el_modal_confirm).hide();
                })
                .catch(error => {
                    console.log(form_data.get('id'));
                    el_modal_confirm.querySelector('.loading_spinner').classList.add('d-none');
                });
        });

        const edit_voucher = voucher => {
            el_form_voucher.reset();

            el_form_id.value = voucher.querySelector('.voucher_id').textContent.trim();
            el_form_code.value = voucher.querySelector('.voucher_code').textContent.trim();
            el_form_amount.value = voucher.querySelector('.voucher_amount_raw').textContent.trim();
            el_form_qty.value = voucher.querySelector('.voucher_qty_raw').textContent.trim();
            el_form_limit_per_user.value = voucher.querySelector('.voucher_limit_per_user_raw').textContent.trim();
            el_form_valid_from.value = voucher.querySelector('.voucher_valid_from_raw').textContent.trim();
            el_form_valid_to.value = voucher.querySelector('.voucher_valid_to_raw').textContent.trim();

            for (let category of JSON.parse(voucher.querySelector('.voucher_categories_raw').textContent)) {
                el_form_voucher.querySelector('[value="'+category+'"]').selected = true;
            }

            const type = voucher.querySelector('.voucher_type_raw').textContent.trim();
            el_form_voucher.querySelector('[value="' + type + '"]').checked = true;

            const modal = new bootstrap.Modal(el_modal_voucher, {
                keyboard: false,
                backdrop: 'static',
            });
            modal.show();
        }

        const activate_voucher = voucher => {
            el_modal_confirm.querySelector('[name="id"]').value =
                voucher.querySelector('.voucher_id').textContent;
            el_modal_confirm.querySelector('[name="status"]').value = 'active';
            el_modal_confirm.querySelector('#question').textContent = 'Activate '
                + voucher.querySelector('.voucher_code').textContent
                + ' voucher?';
            el_modal_confirm.querySelector('#confirm').classList.remove('btn-danger');
            el_modal_confirm.querySelector('#confirm').classList.remove('btn-success');
            el_modal_confirm.querySelector('#confirm').classList.add('btn-success');

            const modal = new bootstrap.Modal(el_modal_confirm, {
                keyboard: false,
                backdrop: 'static',
            })
            modal.show();
        };

        const deactivate_voucher = voucher => {
            el_modal_confirm.querySelector('[name="id"]').value =
                voucher.querySelector('.voucher_id').textContent;
            el_modal_confirm.querySelector('[name="status"]').value = 'inactive';
            el_modal_confirm.querySelector('#question').textContent = 'Deactivate '
                + voucher.querySelector('.voucher_code').textContent
                + ' voucher?';
            el_modal_confirm.querySelector('#confirm').classList.remove('btn-success');
            el_modal_confirm.querySelector('#confirm').classList.remove('btn-danger');
            el_modal_confirm.querySelector('#confirm').classList.add('btn-danger');

            const modal = new bootstrap.Modal(el_modal_confirm, {
                keyboard: false,
                backdrop: 'static',
            })
            modal.show();
        };
    </script>
@endsection