<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">

    <title>@yield('page-title')</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-custom bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}">
            </a>
            <ul class="navbar-nav flex-row ms-auto mt-2 me-3 d-lg-none nav-external">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('order.checkout') }}">
                        <div class="d-flex align-items-center">
                            <i class="material-icons material-icons-md">shopping_cart</i>
                            <span class="badge badge-notify rounded-pill bg-primary cart_item_count"></span>
                        </div>
                    </a>
                </li>
                @can('list', new \App\Models\StoreRequest())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('request.list') }}">
                            <div class="d-flex align-items-center">
                                <i class="material-icons">pending_actions</i>
                                <span class="badge badge-notify rounded-pill bg-primary pending_request_count"></span>
                            </div>
                        </a>
                    </li>
                @endcan
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.notifications', Auth::user()->id) }}">
                            <div class="d-flex align-items-center">
                                <i class="material-icons">notifications_active</i>
                                <span class="badge badge-notify rounded-pill bg-primary notification_count"></span>
                            </div>
                        </a>
                    </li>
                @endauth
            </ul>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-toggled" aria-controls="navbar-toggled" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-toggled">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shop') }}">SHOP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('store.list') }}">STORES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">ORDER STATUS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('order.checkout') }}" title="Cart">
                            <div class="d-flex align-items-center">
                                <i class="material-icons d-none d-lg-inline">shopping_cart</i>
                                <span class="me-1 d-lg-none">CART</span>
                                <span class="badge rounded-pill bg-primary cart_item_count"></span>
                            </div>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">LOGIN</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.register') }}">REGISTER</a>
                        </li>
                    @endguest

                    @auth
                        @can('list', new \App\Models\User())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.list') }}" title="Users">
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons d-none d-lg-inline">people</i>
                                        <span class="d-lg-none">USERS</span>
                                    </div>
                                </a>
                            </li>
                        @endcan
                        @can('list', new \App\Models\StoreRequest())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('request.list') }}" title="Requests">
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons d-none d-lg-inline">pending_actions</i>
                                        <span class="me-1 d-lg-none">REQUESTS</span>
                                        <span class="badge rounded-pill bg-primary pending_request_count"></span>
                                    </div>
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.notifications', Auth::user()->id) }}" title="Notifications">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons d-none d-lg-inline">notifications_active</i>
                                    <span class="me-1 d-lg-none">NOTIFICATIONS</span>
                                    <span class="badge rounded-pill bg-primary notification_count"></span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.activity-log', Auth::user()->id) }}">{{ strtoupper(Auth::user()->name) }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" title="Logout">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons d-none d-lg-inline">logout</i>
                                    <span class="me-1 d-lg-none">LOGOUT</span>
                                </div>
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

    <script defer>
        let role = '{{ Auth::check() ? Auth::user()->role : '' }}';
        count();

        setInterval(function () {
            count();
        }, 5000);

        function count() {
            if (role.match('admin')) {
                axios.get('{{ route('request.count-pending') }}')
                    .then(function (response) {
                        const pending = parseInt(response.data);
                        const el_pending_request_count = document.querySelectorAll('.pending_request_count');

                        el_pending_request_count.forEach(el => {
                            el.innerText = pending > 0 ? pending : '';
                        });
                    });
            }

            if (role) {
                axios.get('{{ route('notification.count-unread') }}')
                    .then(function (response) {
                        const unread = parseInt(response.data);
                        const el_notification_count = document.querySelectorAll('.notification_count');

                        el_notification_count.forEach(el => {
                            el.innerText = unread > 0 ? unread : '';
                        });
                    });
            }
        }
    </script>

    <!-- Cart -->
    <script defer>
        class Cart_class {
            constructor() {
                this.items = JSON.parse(localStorage.getItem('cart_items') ?? '[]');
                setInterval(() => {
                    this.items = JSON.parse(localStorage.getItem('cart_items') ?? '[]');
                }, 200);
            }

            addItem(id, qty) {
                id = parseInt(id);
                qty = parseInt(qty);

                const item_index = this.getItemIndex(id);

                if (item_index === null) {
                    this.items.push({id, qty});
                    this.save();
                } else {
                    console.log('Item already exists in cart.');
                }
            }

            removeItem(id) {
                id = parseInt(id);

                const item_index = this.getItemIndex(id);

                if (item_index) {
                    this.items.splice(item_index, 1);
                    this.save();
                } else {
                    console.log('Item not found in cart.');
                }
            }

            updateItem(id, qty) {
                id = parseInt(id);
                qty = parseInt(qty);

                const item_index = this.getItemIndex(id);

                if (item_index) {
                    this.items[item_index].qty = qty;
                    this.save();
                } else {
                    console.log('Item not found in cart.');
                }
            }

            getItems() {
                return this.items;
            }

            getItem(id) {
                id = parseInt(id);

                const item_index = this.getItemIndex(id);

                if (item_index) {
                    return this.items[item_index];
                } else {
                    console.log('Item not found in cart.');
                }
            }

            getItemIndex(id) {
                id = parseInt(id);

                let index = null;
                Object.keys(this.items).forEach(key => {
                    if (this.items[key].id === id) {
                        index = key;
                    }
                });

                return index;
            }

            count() {
                return this.items.length;
            }

            save() {
                localStorage.setItem('cart_items', JSON.stringify(this.items));
            }
        }

        window.Cart = new Cart_class();

        notifyCart();
        setInterval(() => {
            notifyCart()
        }, 100);

        function notifyCart() {
            const item_count = Cart.count();
            const cart_notifications = document.querySelectorAll('.cart_item_count');

            cart_notifications.forEach(notif => {
                notif.textContent = item_count > 0 ? item_count : '';
            });
        }
    </script>
    <!-- End Cart -->

    @yield('content')
</body>
</html>