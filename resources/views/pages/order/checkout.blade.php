@extends('layouts.app')
@section('page-title', 'Checkout')

@section('content')
    <div class="container">
        <div class="row mt-3 mt-lg-5 d-none" id="cart_empty">
            <div class="col-12 text-center">
                <i class="material-icons fs-48">remove_shopping_cart</i>
                <p class="text-muted">Your cart is empty.</p>
                <a href="{{ route('shop') }}" class="btn btn-outline-primary p-3">CONTINUE SHOPPING</a>
            </div>
        </div>
        <div class="row mt-3 mt-lg-5" id="checkout">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <h4 class="my-3">CART</h4>
                        <hr>
                        <div id="cart_items">
                            <div class="d-flex align-items-stretch bg-light p-2 my-2 d-none item" id="item_template">
                                <img class="item_thumbnail">
                                <div class="flex-grow-1 mx-2 overflow-hidden">
                                    <a href="#" class="h6 my-0 item_name"></a>
                                    <div class="text-muted small my-0 ellipsis item_specifications"></div>
                                    <div class="align-self-start">
                                        <div class="h6 text-primary my-0 item_subtotal"></div>
                                        <div class="text-muted small item_price"></div>
                                        <div class="small">
                                            <span class="text-muted">Sold by</span>
                                            <a href="#" class="item_seller"></a>
                                            <span class="d-none item_seller_id"></span>
                                        </div>
                                    </div>
                                    <div class="d-none item_category"></div>
                                </div>
                                <div class="d-flex flex-column justify-content-evenly align-items-end" style="width: 87.58px;">
                                    <div>
                                        <form class="form_qty">
                                            <div class="input-group">
                                                <button class="btn btn-outline-dark btn-sm item_qty_dec" type="button">&lsaquo;</button>
                                                <input type="number" class="form-control form-control-sm text-center no-spin item_qty">
                                                <button class="btn btn-outline-dark btn-sm item_qty_inc" type="button">&rsaquo;</button>
                                            </div>
                                            <div class="form-text text-center item_stock"></div>
                                        </form>
                                    </div>
                                    <div>
                                        <button class="btn btn-outline-dark btn-sm mx-lg-auto remove_item">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <i class="material-icons fs-16">delete</i>
                                                <span class="ms-1">Remove</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-3 mt-lg-0">
                <div class="card">
                    <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body px-3 py-2" id="summary">
                        <h4 class="my-3">SUMMARY</h4>
                        <hr>
                        <p class="fw-bold border-bottom my-1">Contact Information</p>
                        <div class="d-none" id="complete_address">
                            <p class="my-0" id="contact_person"></p>
                            <p class="my-0" id="contact_number"></p>
                            <p class="my-0" id="address_line"></p>
                            <p class="my-0" id="map_address"></p>
                            <p class="my-0" id="map_coordinates"></p>
                        </div>
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modal_set_address">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">house</i>
                                <span class="ms-1">Set Address</span>
                            </div>
                        </button>

                        <p class="fw-bold border-bottom mt-3 mb-1">Payment</p>
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center">
                                <span id="item_count"></span>
                                <span class="h6 text-primary" id="item_subtotal"></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span>Delivery Fee</span>
                                <span class="h6 text-primary" id="delivery_fee"></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span>Voucher Discount</span>
                                <span class="h6 text-primary" id="voucher_discount"></span>
                            </div>

                            @if (Auth::check())
                                <form id="form_voucher_code">
                                    <div class="input-group my-2">
                                        <input type="text" class="form-control form-control-sm" name="code" placeholder="Voucher Code" aria-describedby="apply_voucher_code">
                                        <button class="btn btn-outline-dark btn-sm" type="button" id="apply_voucher_code">Apply</button>
                                    </div>
                                    <div class="text-danger small" id="voucher_error"></div>
                                </form>
                            @else
                                <div class="alert alert-dark small">
                                    You must be
                                    <a href="{{ route('login') }}">logged in</a>
                                    to apply voucher codes.
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center border-top py-2 my-2">
                                <span>Total</span>
                                <span class="h6 text-primary" id="total"></span>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm d-none" id="place_order">Place Order</button>
                            <div class="alert alert-dark small d-none" id="spending_limit"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_remove_item" tabindex="-1" aria-labelledby="attachment-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachment-modal-label">Remove Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="h6 text-primary my-0 remove_item_name"></p>
                    <p class="small ellipsis my-0 remove_item_specifications"></p>
                    <p class="my-0 remove_item_qty"></p>
                    <p class="text-primary my-0 remove_item_subtotal"></p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-outline-dark btn-sm confirm_remove_item">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_set_address" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="attachment-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title" id="attachment-modal-label">Set Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <div id="address_list">
                        <div class="bg-light p-1 my-1 d-none" id="item_template">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">label</i>
                                <span class="ms-1 item_label"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">person</i>
                                <span class="ms-1 item_contact_person"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">textsms</i>
                                <span class="ms-1 item_contact_number"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">house</i>
                                <span class="ms-1 item_address_line"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">map</i>
                                <span class="ms-1 item_map_address"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">place</i>
                                <span class="ms-1 item_map_coordinates"></span>
                            </div>
                            <div class="my-1">
                                <button type="button" class="btn btn-outline-dark btn-sm copy_address">
                                    COPY ADDRESS
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="form_set_address">
                        <div class="my-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">person</i></span>
                                <input type="text" class="form-control form-select-sm" name="contact_person" placeholder="Contact Person">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">textsms</i></span>
                                <input type="text" class="form-control form-select-sm" name="contact_number" placeholder="Contact Number">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">house</i></span>
                                <input type="text" class="form-control form-select-sm" name="address_line" placeholder="Address Line">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">map</i></span>
                                <input type="text" class="form-control form-select-sm" name="map_address" placeholder="Map Address" readonly>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">place</i></span>
                                <input type="text" class="form-control form-select-sm" name="map_coordinates" placeholder="Map Coordinates" readonly>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div style="width: 100%; height: 300px;" id="gmap_container"></div>
                            <div class="form-text">
                                Click on your location on the map to get additional location information.
                                Please select a location within {{ implode('/', config('system.service_area')) }}.
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="save_address">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script id="gmap_src"></script>
    <script defer>
        const el_cart_empty = document.getElementById('cart_empty');
        const el_checkout = document.getElementById('checkout');

        function showEmptyCart() {
            el_cart_empty.classList.remove('d-none');
            el_checkout.classList.remove('d-none');
            el_checkout.classList.add('d-none');
        }

        function showCheckout() {
            el_checkout.classList.remove('d-none');
            el_checkout.classList.remove('d-none');
            el_cart_empty.classList.add('d-none');
        }

        if (Cart.count() > 0) {
            showCheckout();

            el_checkout.querySelectorAll('.loading_spinner').forEach(spinner => {
                spinner.classList.remove('d-none');
            });

            const is_logged_in = '{{ Auth::check() ? Auth::id() : '' }}' ? true : false;
            const delivery_fee_rate = parseFloat('{{ config('system.delivery_fee_rate') }}');
            const spending_limit = parseFloat('{{ config('system.spending_limit.guest') }}');
            const number_formatter = new Intl.NumberFormat('en-PH');
            const currency_formatter = new Intl.NumberFormat('en-PH', { style : 'currency', currency : 'PHP' });

            const el_cart_items = checkout.querySelector('#cart_items');
            const el_summary = checkout.querySelector('#summary');

            const el_modal_remove_item = document.getElementById('modal_remove_item');
            const el_modal_set_address = document.getElementById('modal_set_address');

            let summary_item_count = 0;
            let summary_item_subtotal = 0;
            let summary_delivery_fee = 0;
            let summary_voucher_discount = 0;
            let summary_total = 0;
            let summary_voucher_code = null;
            let summary_address = sessionStorage.getItem('summary_address')
                ? JSON.parse(sessionStorage.getItem('summary_address'))
                : null;

            let stores = [];

            let gmap;
            let gmap_lat;
            let gmap_lng;
            let gmap_marker;

            // set summary defaults;
            setSummaryAddress(summary_address);
            setSummaryItemCount(summary_item_count);
            setSummaryItemSubtotal(summary_item_subtotal);
            setSummaryDeliveryFee(summary_delivery_fee);
            setSummaryVoucherDiscount(summary_voucher_discount)
            setSummaryTotal(summary_total);

            el_summary.querySelector('#form_voucher_code').addEventListener('submit', e => e.preventDefault());
            el_summary.querySelector('[name="code"]').value = summary_voucher_code;
            el_summary.querySelector('#apply_voucher_code').addEventListener('click', e => {
                e.preventDefault();
                summary_voucher_code = el_summary.querySelector('[name="code"]').value;
                addSummaryVoucherDiscount(summary_voucher_discount * -1);
                computeVoucherDiscount(summary_voucher_code);
            });

            // fetch cart item descriptions
            axios.post('{{ route('order.get-items') }}', { ids : Cart.getItems().map(item => item.id)})
                .then(response => response.data)
                .then(prepareCheckout);

            function prepareCheckout(items) {
                items.forEach(item => {
                    loadCartItem(item);
                });

                computeDeliveryValues(summary_address);

                el_checkout.querySelectorAll('.loading_spinner').forEach(spinner => {
                    spinner.classList.add('d-none');
                });
            }

            function loadCartItem(item) {
                const el_template = el_cart_items.querySelector('#item_template').cloneNode(true);
                el_template.classList.remove('d-none');
                el_template.setAttribute('id', 'item_' + item.id);

                el_template.querySelector('.item_thumbnail').src = '{{ asset('storage/products/images/thumbnail') }}/'
                    + (item.preview ?? 'placeholder.jpg');

                el_template.querySelector('.item_name').textContent = item.name;

                el_template.querySelector('.item_specifications').textContent = item.specifications.map(spec => {
                    return spec.name + ':' + spec.value;
                }).join(', ');

                el_template.querySelector('.item_subtotal').textContent = currency_formatter.format(
                    Cart.getItem(item.id).qty * item.price
                );

                el_template.querySelector('.item_price').textContent = '(' + currency_formatter.format(item.price) + ')';

                el_template.querySelector('.item_seller_id').textContent = item.store.id;
                el_template.querySelector('.item_seller').textContent = item.store.name;
                el_template.querySelector('.item_seller').href = '../stores/' + item.store.id + '/products';
                stores[item.store.id] = stores[item.store.id] ?? {};
                stores[item.store.id].map_coordinates = stores[item.store.id].map_coordinates ?? item.store.map_coordinates;
                stores[item.store.id].items = stores[item.store.id].items ?? [];
                stores[item.store.id].items.push(item.id);

                let el_template_item_qty = el_template.querySelector('.item_qty');
                el_template_item_qty.value = Cart.getItem(item.id).qty;
                addSummaryItemCount(el_template_item_qty.value);
                addSummaryItemSubtotal(el_template_item_qty.value * item.price);

                el_template.querySelector('.item_stock').textContent = '(' + item.qty + ' left)';
                el_template.querySelector('.item_category').textContent = item.main_category + '|' + (item.sub_category ?? 'all');

                el_cart_items.insertAdjacentElement('beforeEnd', el_template);

                addCartItemFunctions(el_template, item);
            }

            function addCartItemFunctions(el_template, item) {
                const el_template_item_qty = el_template.querySelector('.item_qty');
                const el_template_item_qty_dec = el_template.querySelector('.item_qty_dec');
                const el_template_item_qty_inc = el_template.querySelector('.item_qty_inc');

                const updateItemQty = (qty) => {
                    el_template_item_qty.value = qty;
                    Cart.updateItem(item.id, qty);
                    el_template.querySelector('.item_subtotal').textContent = currency_formatter.format(qty * item.price);
                    toggleItemQtySpinners();
                };

                const toggleItemQtySpinners = () => {
                    el_template_item_qty_dec.disabled = el_template_item_qty.value <= 1;
                    el_template_item_qty_inc.disabled = el_template_item_qty.value >= item.qty;
                };

                // toggle item qty spinners on init
                toggleItemQtySpinners();

                el_template.querySelector('.form_qty').addEventListener('submit', e => e.preventDefault());

                el_template_item_qty.addEventListener('change', e => {
                    e.preventDefault();

                    if (el_template_item_qty.value < 1) {
                        el_template_item_qty.value = 1;
                    } else if (el_template_item_qty.value > item.qty) {
                        el_template_item_qty.value = item.qty;
                    }

                    let difference = parseInt(el_template_item_qty.value) - Cart.getItem(item.id).qty;
                    addSummaryItemCount(difference);
                    addSummaryItemSubtotal(difference * item.price);
                    updateItemQty(parseInt(el_template_item_qty.value));

                    addSummaryVoucherDiscount(summary_voucher_discount * -1);
                    computeVoucherDiscount(summary_voucher_code);
                });

                el_template_item_qty_dec.addEventListener('click', e => {
                    e.preventDefault();
                    addSummaryItemCount(-1);
                    addSummaryItemSubtotal(item.price * -1);
                    updateItemQty(parseInt(el_template_item_qty.value) - 1);
                    addSummaryVoucherDiscount(summary_voucher_discount * -1);
                    computeVoucherDiscount(summary_voucher_code);
                });

                el_template_item_qty_inc.addEventListener('click', e => {
                    e.preventDefault();
                    addSummaryItemCount(1);
                    addSummaryItemSubtotal(item.price);
                    updateItemQty(parseInt(el_template_item_qty.value) + 1);
                    addSummaryVoucherDiscount(summary_voucher_discount * -1);
                    computeVoucherDiscount(summary_voucher_code);
                });

                el_template.querySelector('.remove_item').addEventListener('click', e => {
                    e.preventDefault();

                    const el_modal_remove_item_copy = el_modal_remove_item.cloneNode(true);
                    el_modal_remove_item_copy.setAttribute('id', 'modal_remove_item_' + item.id);
                    el_modal_remove_item_copy.querySelector('.remove_item_name').textContent = item.name;
                    el_modal_remove_item_copy.querySelector('.remove_item_specifications').textContent =
                        el_template.querySelector('.item_specifications').textContent;
                    el_modal_remove_item_copy.querySelector('.remove_item_qty').textContent = 'Qty: '
                        + Cart.getItem(item.id).qty;
                    el_modal_remove_item_copy.querySelector('.remove_item_subtotal').textContent =
                        el_template.querySelector('.item_subtotal').textContent;

                    el_modal_remove_item_copy.addEventListener('hidden.bs.modal', e => {
                        document.getElementById('modal_remove_item_' + item.id).remove();
                    });

                    const modal_remove_item = new bootstrap.Modal(el_modal_remove_item_copy, {
                        backdrop: 'static',
                        keyboard: false,
                    });
                    modal_remove_item.show();

                    el_modal_remove_item_copy.querySelector('.confirm_remove_item').addEventListener('click', e => {
                        e.preventDefault();

                        addSummaryItemCount(Cart.getItem(item.id).qty * -1);
                        addSummaryItemSubtotal(Cart.getItem(item.id).qty * item.price * -1);
                        addSummaryVoucherDiscount(summary_voucher_discount * -1);
                        computeVoucherDiscount(summary_voucher_code);

                        Cart.removeItem(item.id);
                        document.getElementById('item_' + item.id).remove();

                        Object.keys(stores[item.store.id].items).forEach(index => {
                            if (stores[item.store.id].items[index] === item.id) {
                                stores[item.store.id].items.splice(index, 1);
                            }
                        });

                        if (stores[item.store.id].items.length === 0) {
                            computeDeliveryFees(stores);
                        }

                        if (Cart.count() === 0) {
                            showEmptyCart();
                        }

                        modal_remove_item.hide();
                    });
                });
            }

            function computeDeliveryValues(address) {
                if (address) {
                    let origins = [];
                    stores.forEach(store => {
                        [lat, lng] = store.map_coordinates.split(',').map(x => parseFloat(x));
                        origins.push(new google.maps.LatLng(lat, lng));
                    });

                    let destinations = [];
                    [lat, lng] = address.map_coordinates.split(',').map(x => parseFloat(x));
                    destinations.push(new google.maps.LatLng(lat, lng));

                    let service = new google.maps.DistanceMatrixService();
                    service.getDistanceMatrix({
                        origins,
                        destinations,
                        travelMode: 'DRIVING',
                    }, (response, status) => {
                        if (status == 'OK') {
                            let rows = response.rows;
                            let rowIndex = 0;

                            stores.forEach(store => {
                                let distance = Math.round(rows[rowIndex].elements[0].distance.value / 1000);
                                store.delivery_distance = distance + 'km';
                                store.delivery_fee = delivery_fee_rate * distance;
                                rowIndex++;
                            });

                            computeDeliveryFees(stores);
                        }
                    });
                }
            }

            function computeDeliveryFees(stores) {
                let total = 0;
                stores.forEach(store => {
                    if (store.items.length > 0) {
                        total += store.delivery_fee;
                    }
                });

                addSummaryDeliveryFee(total - summary_delivery_fee);
            }

            function computeVoucherDiscount(voucher_code) {
                el_summary.querySelector('#voucher_error').textContent = '';
                if (voucher_code) {
                    axios.post('{{ route('order.voucher-details') }}', { code: voucher_code })
                        .then(response => {
                            const voucher = response.data;
                            const items = document.querySelectorAll('.item');
                            let temp_subtotal = 0;

                            items.forEach(item => {
                                if (item.querySelector('.item_name').textContent.trim() != '') {
                                    const item_subtotal = parseFloat(item.querySelector('.item_subtotal').textContent.replace(/\u20B1|\,/g, ''));
                                    const item_category = item.querySelector('.item_category').textContent.trim();
                                    const item_seller_id = parseInt(item.querySelector('.item_seller_id').textContent.trim());

                                    if (voucher.categories.includes(item_category) && voucher.store_id === item_seller_id) {
                                        temp_subtotal += item_subtotal;
                                    }
                                }
                            });

                            if (temp_subtotal > 0 && voucher.type === 'Flat Amount') {
                                addSummaryVoucherDiscount(voucher.amount);
                            } else if (temp_subtotal > 0 && voucher.type === 'Percentage') {
                                addSummaryVoucherDiscount(temp_subtotal * (voucher.amount / 100));
                            } else if (temp_subtotal === 0) {
                                el_summary.querySelector('#voucher_error').textContent = 'No item is eligible for this voucher.';
                            }
                        })
                        .catch(error => {
                            el_summary.querySelector('#voucher_error').textContent = error.response.data;
                        });
                }
            }

            function addSummaryItemCount(step) {
                summary_item_count += parseInt(step);
                setSummaryItemCount(summary_item_count);
            }

            function addSummaryItemSubtotal(step) {
                summary_item_subtotal += parseFloat(step);
                summary_total += parseFloat(step);
                setSummaryItemSubtotal(summary_item_subtotal);
                setSummaryTotal(summary_total);
            }

            function addSummaryDeliveryFee(step) {
                summary_delivery_fee += parseFloat(step);
                summary_total += parseFloat(step);
                setSummaryDeliveryFee(summary_delivery_fee);
                setSummaryTotal(summary_total);
            }

            function addSummaryVoucherDiscount(step) {
                summary_voucher_discount += parseFloat(step);
                summary_total -= parseFloat(step);
                setSummaryVoucherDiscount(summary_voucher_discount);
                setSummaryTotal(summary_total);
            }

            function setSummaryItemCount(count) {
                el_summary.querySelector('#item_count').textContent = count + ' Items';
            }

            function setSummaryItemSubtotal(subtotal) {
                el_summary.querySelector('#item_subtotal').textContent = currency_formatter.format(subtotal);
            }

            function setSummaryDeliveryFee(delivery_fee) {
                el_summary.querySelector('#delivery_fee').textContent = currency_formatter.format(delivery_fee);
            }

            function setSummaryVoucherDiscount(voucher_discount) {
                el_summary.querySelector('#voucher_discount').textContent = currency_formatter.format(voucher_discount);
            }

            function setSummaryTotal(total) {
                el_summary.querySelector('#total').textContent = currency_formatter.format(total);
                toggleSummarySubmit();
            }

            function setSummaryAddress(address) {
                if (address) {
                    el_summary.querySelector('#complete_address').classList.remove('d-none');
                    el_summary.querySelector('#contact_person').textContent = address.contact_person;
                    el_summary.querySelector('#contact_number').textContent = address.contact_number;
                    el_summary.querySelector('#address_line').textContent = address.address_line;
                    el_summary.querySelector('#map_address').textContent = address.map_address;
                    el_summary.querySelector('#map_coordinates').textContent = address.map_coordinates;
                } else {
                    el_summary.querySelector('#complete_address').classList.remove('d-none');
                    el_summary.querySelector('#complete_address').classList.add('d-none');
                }
            }

            function toggleSummarySubmit() {
                let el_place_order = el_summary.querySelector('#place_order');
                let el_spending_limit = el_summary.querySelector('#spending_limit');

                if (summary_total > spending_limit && is_logged_in === false) {
                    el_place_order.classList.remove('d-none');
                    el_place_order.classList.add('d-none');
                    el_spending_limit.classList.remove('d-none');
                    el_spending_limit.innerHTML = 'You have reached the spending limit of '
                        + currency_formatter.format(spending_limit) + '. Please '
                        + '<a href="{{ route('login') }}">login</a> to continue.';
                } else if (summary_address) {
                    el_spending_limit.classList.remove('d-none');
                    el_spending_limit.classList.add('d-none');
                    el_place_order.classList.remove('d-none');
                }
            }

            el_summary.querySelector('#place_order').addEventListener('click', e => {
                e.preventDefault();

                const form = document.createElement('form');
                form.setAttribute('class', 'd-none');
                form.setAttribute('action', '{{ route('order.create') }}');
                form.setAttribute('method', 'POST');
                form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_token" value="{{ csrf_token() }}">');

                Cart.getItems().forEach(item => {
                    form.insertAdjacentHTML('beforeend', '<input type="hidden" name="items[' + item.id + ']" value="' + item.qty + '">');
                });

                if (summary_voucher_code) {
                    form.insertAdjacentHTML('beforeend', '<input type="hidden" name="voucher_code" value="' + summary_voucher_code + '">');
                }

                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="contact_person" value="' + summary_address.contact_person +'">');
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="contact_number" value="' + summary_address.contact_number +'">');
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="address_line" value="' + summary_address.address_line +'">');
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="map_address" value="' + summary_address.map_address +'">');
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="map_coordinates" value="' + summary_address.map_coordinates +'">');

                document.body.insertAdjacentElement('beforeend', form);
                form.submit();
            });

            // load google map javascript API
            document.getElementById('gmap_src').src = "https://maps.googleapis.com/maps/api/js" +
                "?key={{ env('GMAP_API_KEY') }}" +
                "&callback=loadGmap";

            function loadGmap() {
                gmap = new google.maps.Map(el_modal_set_address.querySelector('#gmap_container'), {
                    zoom: 16,
                    center: { lat: gmap_lat ?? 16.409447, lng: gmap_lng ?? 120.599264 },
                    mapTypeId: google.maps.MapTypeId.HYBRID,
                });

                setGmapMarker(gmap_lat, gmap_lng);

                gmap.addListener('click', e => {
                    gmap_lat = e.latLng.toJSON().lat;
                    gmap_lng = e.latLng.toJSON().lng;

                    setGmapMarker(gmap_lat, gmap_lng);

                    // retrieve location details from Geocoding API
                    axios.get('{{ env('GEOCODING_API_URL') }}/json'
                        + '?latlng=' + gmap_lat + ',' + gmap_lng
                        + '&result_type=administrative_area_level_5|sublocality&key={{ env('GMAP_API_KEY') }}'
                    )
                        .then(response => {
                            if (response.data.status == 'OK') {
                                el_modal_set_address.querySelector('[name="map_address"]').value = response.data.results[0].formatted_address;
                                el_modal_set_address.querySelector('[name="map_coordinates"]').value = gmap_lat + ',' + gmap_lng;
                            } else {
                                alert('Unable to retrieve map information.');
                            }
                        });
                });
            }

            function setGmapMarker(lat, lng) {
                if (gmap_marker) {
                    gmap_marker.setMap(null);
                }

                if (lat && lng) {
                    gmap_marker = new google.maps.Marker({
                        position: { lat: lat, lng: lng },
                        map: gmap,
                    });

                    gmap.panTo(new google.maps.LatLng(lat, lng));
                }
            }

            el_modal_set_address.addEventListener('show.bs.modal', e => {
                el_modal_set_address.querySelector('#form_message').textContent = '';
                el_modal_set_address.querySelector('#form_message').removeAttribute('class');
                el_modal_set_address.querySelectorAll('.field_error').forEach(field => field.textContent = '');
                setModalAddress(summary_address);
            });

            el_modal_set_address.querySelector('#form_set_address').addEventListener('submit', e => e.preventDefault());

            el_modal_set_address.querySelector('#save_address').addEventListener('click', e => {
                e.preventDefault();
                el_modal_set_address.querySelector('.loading_spinner').classList.remove('d-none');

                const modal_form_message = el_modal_set_address.querySelector('#form_message');
                const modal_contact_person = el_modal_set_address.querySelector('[name="contact_person"]');
                const modal_contact_number = el_modal_set_address.querySelector('[name="contact_number"]');
                const modal_address_line = el_modal_set_address.querySelector('[name="address_line"]');
                const modal_map_address = el_modal_set_address.querySelector('[name="map_address"]');
                const modal_map_coordinates = el_modal_set_address.querySelector('[name="map_coordinates"]');
                const formData = new FormData(el_modal_set_address.querySelector('#form_set_address'));

                modal_form_message.removeAttribute('class');
                el_modal_set_address.querySelectorAll('.field_error').forEach(field => field.textContent = '');

                axios.post('{{ route('order.validate-address') }}', formData)
                    .then(response => {
                        summary_address = {
                            contact_person: modal_contact_person.value,
                            contact_number: modal_contact_number.value,
                            address_line: modal_address_line.value,
                            map_address: modal_map_address.value,
                            map_coordinates: modal_map_coordinates.value,
                        };

                        sessionStorage.setItem('summary_address', JSON.stringify(summary_address));
                        setSummaryAddress(summary_address);
                        computeDeliveryValues(summary_address);
                        el_modal_set_address.querySelector('.loading_spinner').classList.remove('d-none');

                        bootstrap.Modal.getInstance(el_modal_set_address).hide();
                    })
                    .catch(error => {
                        let errors = error.response.data;

                        if (typeof errors === 'object') {
                            modal_contact_person.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('contact_person') ? errors.contact_person : '';
                            modal_contact_number.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('contact_number') ? errors.contact_number : '';
                            modal_address_line.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('address_line') ? errors.address_line : '';
                            modal_map_address.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('map_address') ? errors.map_address : '';
                            modal_map_coordinates.parentNode.parentNode.querySelector('.field_error').textContent =
                                errors.hasOwnProperty('map_coordinates') ? errors.map_coordinates : '';
                        } else {
                            modal_form_message.setAttribute('class', 'alert alert-danger small');
                            modal_form_message.textContent = errors.toString();
                        }
                    })
                    .finally(() => {
                        el_modal_set_address.querySelector('.loading_spinner').classList.add('d-none');
                    });
            });

            function setModalAddress(address = null) {
                if (address) {
                    el_modal_set_address.querySelector('[name="contact_person"]').value = address.contact_person;
                    el_modal_set_address.querySelector('[name="contact_number"]').value = address.contact_number;
                    el_modal_set_address.querySelector('[name="address_line"]').value = address.address_line;
                    el_modal_set_address.querySelector('[name="map_address"]').value = address.map_address;
                    el_modal_set_address.querySelector('[name="map_coordinates"]').value = address.map_coordinates;

                    [gmap_lat, gmap_lng] = address.map_coordinates.split(',').map(x => parseFloat(x));
                    setGmapMarker(gmap_lat, gmap_lng);
                }
            }

            if (is_logged_in) {
                axios.get('/users/{{ Auth::id() }}/address-book')
                    .then(response => {
                        const items = response.data;

                        if (items.length > 0) {
                            const el_address_list = el_modal_set_address.querySelector('#address_list');
                            const el_item_template = el_modal_set_address.querySelector('#item_template');

                            items.forEach(item => {
                                const template_copy = el_item_template.cloneNode(true);
                                template_copy.classList.remove('d-none');
                                template_copy.removeAttribute('id');

                                template_copy.querySelector('.item_label').textContent = item.label;
                                template_copy.querySelector('.item_contact_person').textContent = item.contact_person;
                                template_copy.querySelector('.item_contact_number').textContent = item.contact_number;
                                template_copy.querySelector('.item_address_line').textContent = item.address_line;
                                template_copy.querySelector('.item_map_address').textContent = item.map_address;
                                template_copy.querySelector('.item_map_coordinates').textContent = item.map_coordinates;

                                template_copy.querySelector('.copy_address').addEventListener('click', e => {
                                    setModalAddress(item);
                                    el_modal_set_address.querySelector('form').scrollIntoView({behavior: 'smooth'});
                                });

                                el_address_list.insertAdjacentElement('beforeend', template_copy);
                            });
                        }
                    })
                    .catch(error => {
                        el_modal_set_address.querySelector('#form_message').textContent = error.response.data;
                        el_modal_set_address.querySelector('#form_message').setAttribute('class', 'alert alert-danger small');
                    });
            }
        } else {
            showEmptyCart();
        }
    </script>
@endsection