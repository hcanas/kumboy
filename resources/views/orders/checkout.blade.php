@extends('layouts.app')
@section('page-title', 'Checkout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 mt-3 mt-lg-5">
                <h4 class="border-bottom my-0 pb-2">Cart</h4>

                <div class="alert alert-danger my-2 d-none" id="cart_empty">Your cart is empty.</div>

                <div id="cart_items_wrap">
                    <div class="d-flex align-items-stretch my-3 d-none" id="item_template">
                        <img class="item_thumbnail">
                        <div class="d-flex flex-column justify-content-between ms-2 overflow-hidden">
                            <a href="#" class="h6 my-0 item_name"></a>
                            <div class="text-muted small my-0 ellipsis item_specifications"></div>
                            <form class="form_qty">
                                <div class="d-flex">
                                    <div class="input-group">
                                        <button class="btn btn-outline-dark btn-sm item_qty_dec" type="button">-</button>
                                        <input type="number" class="form-control form-control-sm text-center no-spin item_qty">
                                        <button class="btn btn-outline-dark btn-sm item_qty_inc" type="button">+</button>
                                    </div>
                                    <div class="form-text text-center item_stock"></div>
                                </div>
                            </form>
                        </div>
                        <div class="d-flex flex-column justify-content-between align-items-end w-50">
                            <div>
                                <div class="h6 text-primary text-center item_cost my-0"></div>
                                <div class="text-muted text-center item_unit_price small"></div>
                            </div>
                            <div>
                                <button class="btn btn-outline-dark btn-sm d-flex justify-content-center align-items-center mx-lg-auto item_remove">
                                    <i class="material-icons fs-16">delete</i>
                                    <span class="ms-1">Remove</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row py-3 d-none">
                    <div class="col-12 col-lg-4 offset-lg-6 mb-2">
                        <div class="row d-flex justify-content-between">
                            <div class="col">
                                <h6>Total</h6>
                            </div>
                            <div class="col">
                                <div class="h6 text-primary text-center" id="total"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2 d-grid d-block d-lg-inline">
                        <a href="{{ route('order.select-address') }}" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center mx-lg-auto" id="set_address">
                            <i class="material-icons">house</i>
                            <span class="ms-1">Set Your Address</span>
                        </a>
                        <div class="text text-danger small d-none" id="spending_limit_reached"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script defer>
        sessionStorage.setItem('order_cart_status', '');
        let cart_items_wrap = document.getElementById('cart_items_wrap');
        let spending_limit = parseFloat('{{ config('system.spending_limit.guest') }}');
        let el_spending_limit = document.getElementById('spending_limit_reached');
        let btn_set_address = document.getElementById('set_address');
        let total = 0;
        let el_total = document.getElementById('total');
        let logged_in = '{{ Auth::check() ? Auth::user()->id : '' }}' ?? null;

        if (Cart.count() > 0) {
            axios.post('{{ route('order.get-items') }}', { ids : Cart.getItems().map(item => item.id) })
                .then(response => {
                    const items = response.data;

                    let number_formatter = new Intl.NumberFormat('en-PH');
                    let currency_formatter = new Intl.NumberFormat('en-PH', { style : 'currency', currency : 'PHP' });

                    items.forEach(function (item) {
                        el_total.parentElement.parentElement.parentElement.parentElement.classList.remove('d-none');

                        // item template
                        let item_template = document.getElementById('item_template').cloneNode(true);
                        item_template.classList.remove('d-none');
                        item_template.setAttribute('id', item.id);

                        // item thumbnail
                        let image = new Image();
                        let img_src = '{{ asset('storage/products/images/thumbnail') }}';

                        image.onload = function () {
                            item_template.querySelector('.item_thumbnail').setAttribute('src', img_src + '/' + item.preview);
                        }

                        image.onerror = function () {
                            item_template.querySelector('.item_thumbnail').setAttribute('src', img_src + '/placeholder.jpg');
                        }

                        image.src = img_src + '/' + (item.preview ?? 'none');
                        // end item thumbnail

                        // item name
                        item_template.querySelector('.item_name').setAttribute('href', '/products/' + item.id + '/info');
                        item_template.querySelector('.item_name').textContent = item.name;
                        // end item name

                        // item specifications
                        if (item.specifications.length > 0) {
                            let specifications = '';
                            for (let i = 0; i < item.specifications.length; i++) {
                                specifications += item.specifications[i].name + ' : ' + item.specifications[i].value;

                                if (i < item.specifications.length - 1) {
                                    specifications += ', ';
                                }
                            }
                            item_template.querySelector('.item_specifications').textContent = specifications;
                        } else {
                            item_template.querySelector('.item_specifications').textContent = 'No details';
                        }
                        // end item specifications

                        // item cost
                        let item_cost = item_template.querySelector('.item_cost');
                        item_template.querySelector('.item_unit_price').textContent = '(' + currency_formatter.format(item.price) + ')';
                        // end item cost

                        // item qty
                        let qty = Cart.getItem(item.id).qty;
                        let btn_item_qty_dec = item_template.querySelector('.item_qty_dec');
                        let btn_item_qty_inc = item_template.querySelector('.item_qty_inc');
                        let form_qty = item_template.querySelector('.form_qty');
                        let input_item_qty = item_template.querySelector('.item_qty');

                        input_item_qty.value = qty;
                        item_cost.textContent = currency_formatter.format(qty * item.price);
                        total += (qty * item.price);
                        checkSpendingLimit();

                        btn_item_qty_inc.disabled = qty >= item.qty;
                        btn_item_qty_dec.disabled = qty <= 1;

                        btn_item_qty_dec.addEventListener('click', function (e) {
                            e.preventDefault();

                            if (qty > 1) {
                                qty--;
                                Cart.updateItem(item.id, qty);
                                input_item_qty.value = qty;
                                item_cost.textContent = currency_formatter.format(qty * item.price);

                                total -= item.price;
                                el_total.textContent = currency_formatter.format(total);
                                checkSpendingLimit();
                            }

                            btn_item_qty_inc.disabled = qty >= item.qty;
                            btn_item_qty_dec.disabled = qty <= 1;
                        });

                        btn_item_qty_inc.addEventListener('click', function (e) {
                            e.preventDefault();

                            if (qty < item.qty) {
                                qty++;
                                Cart.updateItem(item.id, qty);
                                input_item_qty.value = qty;
                                item_cost.textContent = currency_formatter.format(qty * item.price);

                                total += parseFloat(item.price);
                                el_total.textContent = currency_formatter.format(total);
                                checkSpendingLimit();
                            }

                            btn_item_qty_inc.disabled = qty >= item.qty;
                            btn_item_qty_dec.disabled = qty <= 1;
                        });

                        form_qty.addEventListener('submit', function (e) {
                            e.preventDefault();
                        });

                        input_item_qty.addEventListener('change', function (e) {
                            if (this.value < 1) {
                                this.value = 1;
                            } else if (this.value > item.qty) {
                                this.value = item.qty;
                            }

                            total += parseFloat((this.value - qty) * item.price);
                            el_total.textContent = currency_formatter.format(total);
                            checkSpendingLimit();

                            qty = this.value;
                            item_cost.textContent = currency_formatter.format(qty * item.price);
                            Cart.updateItem(item.id, qty);

                            btn_item_qty_inc.disabled = qty >= item.qty;
                            btn_item_qty_dec.disabled = qty <= 1;
                        });
                        // end item qty

                        // item stock
                        item_template.querySelector('.item_stock').textContent = item.qty > 0 ? (number_formatter.format(item.qty) + ' remaining') : 'Out of Stock';

                        if (item.qty === 0) {
                            item_template.querySelector('.item_stock').classList.add('text-danger');
                        } else {
                            item_template.querySelector('.item_stock').classList.remove('text-danger');
                        }
                        // end item stock

                        // remove item
                        item_template.querySelector('.item_remove').addEventListener('click', e => {
                            e.preventDefault();

                            total -= (qty * item.price);
                            el_total.textContent = currency_formatter.format(total);
                            checkSpendingLimit();

                            Cart.removeItem(item.id);
                            document.getElementById(item.id.toString()).remove();

                            if (Cart.count() === 0) {
                                el_total.parentElement.parentElement.parentElement.parentElement.classList.add('d-none');
                                document.getElementById('cart_empty').classList.remove('d-none');
                            }
                        });
                        // end remove item

                        cart_items_wrap.insertAdjacentElement('afterbegin', item_template);
                        // end item template
                    });

                    el_total.textContent = currency_formatter.format(total);
                });
        } else {
            document.getElementById('cart_empty').classList.remove('d-none');
        }

        function checkSpendingLimit()
        {
            el_spending_limit.textContent = 'You are over the spending limit of ' + spending_limit + '. Please login to continue.';

            if (total > spending_limit && !logged_in) {
                btn_set_address.classList.add('d-none');
                el_spending_limit.classList.replace('d-none', 'd-inline');
            } else {
                btn_set_address.classList.replace('d-none', 'd-inline');
                el_spending_limit.classList.add('d-none');
            }
        }

        btn_set_address.addEventListener('click', e => {
            e.preventDefault();

            sessionStorage.setItem('order_cart_status', 'ok');
            window.location = btn_set_address.href;
        });
    </script>
@endsection