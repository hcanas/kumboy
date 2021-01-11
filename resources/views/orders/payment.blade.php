@extends('layouts.app')
@section('page-title', 'Payment')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5">
                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2 d-flex align-items-center">
                        <div>
                            <i class="material-icons material-icons-lg text-primary">shopping_cart</i>
                        </div>
                        <div class="wizard-line bg-primary"></div>
                        <div>
                            <i class="material-icons material-icons-lg text-primary">house</i>
                        </div>
                        <div class="wizard-line bg-primary"></div>
                        <div>
                            <i class="material-icons material-icons-lg text-primary">payment</i>
                        </div>
                        <div class="wizard-line"></div>
                        <div>
                            <i class="material-icons material-icons-lg">check_circle_outline</i>
                        </div>
                    </div>
                </div>

                @if (session('message_type'))
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
                        </div>
                    </div>
                @endif

                <div class="row gx-5">
                    <div class="col-12 col-lg-7">
                        <div class="d-flex justify-content-between align-items-center border-bottom my-2">
                            <h5>Items</h5>
                            <a href="{{ route('order.cart') }}">Edit</a>
                        </div>
                        <div id="cart_items">
                            <div class="row py-2 d-none items" id="item_template">
                                <div class="col-12 col-lg-6 d-flex align-items-center mb-2">
                                    <img class="item_thumbnail">
                                    <div class="ms-1 ellipsis">
                                        <a href="#" class="h6 item_name"></a>
                                        <p class="text-secondary small my-0 ellipsis item_specifications"></p>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-2">
                                    <div class="row d-flex justify-content-between">
                                        <div class="col">
                                            <span class="item_qty"></span>
                                            <span class="text-secondary small item_stock"></span>
                                        </div>
                                        <div class="col">
                                            <div class="h6 text-primary text-center item_cost"></div>
                                            <div class="text-secondary text-center item_unit_price small"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="d-flex justify-content-between align-items-center border-bottom my-2">
                            <h5>Address</h5>
                            <a href="{{ route('order.select-address') }}">Edit</a>
                        </div>
                        <div id="contact_person"></div>
                        <div id="contact_number"></div>
                        <div id="address"></div>
                        <div id="map_coordinates"></div>

                        <div class="d-flex justify-content-between align-items-center border-bottom mt-3">
                            <h5>Payment</h5>
                        </div>
                        <div class="d-flex flex-column py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span id="item_count"></span>
                                <span class="h6 text-primary" id="subtotal"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Delivery Fee</span>
                                <span class="h6 text-primary" id="delivery_fee"></span>
                            </div>
                            <div class="input-group my-2">
                                <input type="text" class="form-control form-control-sm" id="voucher_code" placeholder="Voucher Code" aria-describedby="apply_voucher_code">
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="apply_voucher_code">Apply</button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top py-2 my-2">
                                <span>Total</span>
                                <span class="h6 text-primary" id="total"></span>
                            </div>
                            <a type="button" class="btn btn-primary btn-sm" id="place_order">Place Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}" defer></script>
    <script>
        const order_cart_status = sessionStorage.getItem('order_cart_status') ?? null;
        const order_address_status = sessionStorage.getItem('order_address_status') ?? null;
        const number_formatter = new Intl.NumberFormat('en-PH');
        const currency_formatter = new Intl.NumberFormat('en-PH', { style : 'currency', currency : 'PHP' });

        let voucher_code = null;
        let total_items = 0;
        let subtotal = 0;
        let delivery_fee = 0;
        let map_origins = [];

        if (order_cart_status !== 'ok') {
            window.location = '{{ route('order.cart') }}';
        } else if (order_cart_status === 'ok' && order_address_status !== 'ok') {
            window.location = '{{ route('order.select-address') }}';
        }

        const order_cart = Cart.getItems();
        const order_address = JSON.parse(sessionStorage.getItem('order_address'));

        axios.post('{{ route('order.get-items') }}', { ids : order_cart.map(item => item.id) })
            .then(response => {
                const items = response.data;

                let number_formatter = new Intl.NumberFormat('en-PH');
                let currency_formatter = new Intl.NumberFormat('en-PH', { style : 'currency', currency : 'PHP' });

                items.forEach(function (item) {
                    map_origins.push(item.store.map_coordinates);

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

                    // item qty
                    let qty = Cart.getItem(item.id).qty;

                    item_template.querySelector('.item_qty').textContent = 'Qty: ' + qty;
                    item_template.querySelector('.item_cost').textContent = currency_formatter.format(qty * item.price);
                    item_template.querySelector('.item_unit_price').textContent = '(' + currency_formatter.format(item.price) + ')';

                    total_items += qty;
                    subtotal += (qty * item.price);
                    // end item qty

                    // item stock
                    item_template.querySelector('.item_stock').textContent = item.qty > 0 ? ('(' + number_formatter.format(item.qty) + ' remaining)') : '(Out of Stock)';

                    if (item.qty === 0) {
                        item_template.querySelector('.item_stock').classList.add('text-danger');
                    } else {
                        item_template.querySelector('.item_stock').classList.remove('text-danger');
                    }
                    // end item stock

                    document.getElementById('cart_items').insertAdjacentElement('afterbegin', item_template);
                    // end item template
                });

                // remove duplicate entry
                map_origins = [...new Set(map_origins)];
                const origins = map_origins.map(origin => {
                    [store_lat, store_lng] = origin.split(',').map(x => parseFloat(x));
                    return new google.maps.LatLng(store_lat, store_lng);
                });

                // set destination
                [address_lat, address_lng] = order_address.map_coordinates.split(',').map(x => parseFloat(x));
                const destination = [new google.maps.LatLng(address_lat, address_lng)];

                let service = new google.maps.DistanceMatrixService();
                service.getDistanceMatrix(
                    {
                        origins: origins,
                        destinations: destination,
                        travelMode: 'DRIVING',
                    }, map_callback);
            })

        function map_callback(response, status) {
            if (status == 'OK') {
                let delivery_fee_rate = 10;
                let rows = response.rows;

                for (let i = 0; i < rows.length; i++) {
                    let distance = Math.round(rows[i].elements[0].distance.value / 1000);
                    delivery_fee += (distance * delivery_fee_rate);
                }
            }

            document.getElementById('item_count').textContent = number_formatter.format(total_items) + ' Items';
            document.getElementById('subtotal').textContent = currency_formatter.format(subtotal);
            document.getElementById('delivery_fee').textContent = currency_formatter.format(delivery_fee);
            document.getElementById('total').textContent = currency_formatter.format(subtotal + delivery_fee);
        }

        document.getElementById('contact_person').textContent = order_address.contact_person;
        document.getElementById('contact_number').textContent = order_address.contact_number;
        document.getElementById('address').textContent = order_address.address + ', ' + order_address.map_address;
        document.getElementById('map_coordinates').textContent = order_address.map_coordinates;

        document.getElementById('apply_voucher_code').addEventListener('click', e => {

        });

        document.getElementById('place_order').addEventListener('click', e => {
            e.preventDefault();

            let form = document.createElement('form');
            // form.setAttribute('class', 'd-none');
            form.setAttribute('action', '{{ route('order.create') }}');
            form.setAttribute('method', 'POST');
            form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_token" value="{{ csrf_token() }}">');

            order_cart.forEach(item => {
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="items[' + item.id + ']" value="' + item.qty + '">');
            });

            if (voucher_code) {
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="voucher_code" value="' + voucher_code + '">');
            }

            form.insertAdjacentHTML('beforeend', '<input type="hidden" name="contact_person" value="' + order_address.contact_person +'">');
            form.insertAdjacentHTML('beforeend', '<input type="hidden" name="contact_number" value="' + order_address.contact_number +'">');
            form.insertAdjacentHTML('beforeend', '<input type="hidden" name="address" value="' + order_address.address +'">');
            form.insertAdjacentHTML('beforeend', '<input type="hidden" name="map_address" value="' + order_address.map_address +'">');
            form.insertAdjacentHTML('beforeend', '<input type="hidden" name="map_coordinates" value="' + order_address.map_coordinates +'">');

            document.body.insertAdjacentElement('beforeend', form);
            form.submit();
        });
    </script>
@endsection