<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">

    <title>@yield('page-title')</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom bg-secondary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.jpg') }}">
            </a>
            <ul class="navbar-nav ms-auto me-3 d-lg-none">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="{{ route('order.checkout') }}">
                        <i class="material-icons material-icons-md">shopping_cart</i>
                        <span class="badge rounded-pill bg-primary cart_item_count"></span>
                    </a>
                </li>
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
                        <a class="nav-link" href="{{ route('order.checkout') }}">
                            CART <span class="badge rounded-pill bg-primary cart_item_count"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">ORDER STATUS</a>
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
                        @can('viewAll', new \App\Models\User())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.view-all') }}">Users</a>
                            </li>
                        @endcan
                        @can('viewAllRequests', new \App\Models\StoreRequest())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('request.view-all') }}">
                                    Requests <span class="badge rounded-pill bg-primary" id="pending_request_count"></span>
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.notifications', Auth::user()->id) }}">
                                Notifications <span class="badge rounded-pill bg-primary" id="notification_count"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.activity-log', Auth::user()->id) }}">{{ Auth::user()->name }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

    {{-- Check for pending requests and notifications every 5 seconds --}}
    <script>
        let role = '{{ Auth::check() ? Auth::user()->role : '' }}';
        count();

        setInterval(function () {
            count();
        }, 5000);

        function count() {
            if (role.match('admin')) {
                axios.get('{{ route('request.count-pending') }}')
                    .then(function (response) {
                        let pending = parseInt(response.data);
                        document.getElementById('pending_request_count').innerText = pending > 0 ? pending : '';
                    });
            }

            if (role) {
                axios.get('{{ route('notification.count-unread') }}')
                    .then(function (response) {
                        let unread = parseInt(response.data);
                        document.getElementById('notification_count').innerText = unread > 0 ? unread : '';
                    });
            }
        }
    </script>

    <!-- Cart -->
    <script>
        class Cart_class {
            constructor() {
                try {
                    let json = JSON.parse(sessionStorage.getItem('cart'));

                    if (json && typeof json === 'object') {
                        this.items = json;
                        this.notify();
                    } else {
                        this.items = [];
                        sessionStorage.setItem('cart', '');
                    }
                } catch (e) {
                    this.items = [];
                    sessionStorage.setItem('cart', '');
                }
            }

            getItems() {
                return this.items;
            }

            getItem(id) {
                id = this.parseInt(id);

                if (id) {
                    let ret = null;

                    this.items.some(item => {
                        if (item.id === id) {
                            ret = item;
                        }
                    })

                    return ret;
                } else {
                    console.log('Unknown id.');
                }
            }

            getItemIndex(id) {
                id = this.parseInt(id);
                let index = null;

                Object.keys(this.items).forEach(key => {
                    if (this.items[key].id === id) {
                        index = key
                    }
                });

                return index;
            }

            addItem(id, qty) {
                id = this.parseInt(id);
                qty = this.parseInt(qty);

                if (id && qty) {
                    // check duplicate
                    let index = this.getItemIndex(id);

                    if (index) {
                        this.items[index].qty += qty;
                    } else {
                        this.items.push({
                            id: id,
                            qty: qty,
                        });
                        this.notify();
                    }
                    sessionStorage.setItem('cart', JSON.stringify(this.items));
                    sessionStorage.setItem('order_cart_status', '');
                } else {
                    console.log('Invalid id or qty.');
                }
            }

            removeItem(id) {
                id = this.parseInt(id);

                if (id) {
                    let index = this.getItemIndex(id);

                    if (index) {
                        this.items.splice(index, 1);
                        sessionStorage.setItem('cart', JSON.stringify(this.items));
                        sessionStorage.setItem('order_cart_status', '');
                        this.notify();
                    } else {
                        console.log('Unknown id.');
                    }
                } else {
                    console.log('Unknown id.');
                }
            }

            updateItem(id, qty) {
                id = this.parseInt(id);
                qty = this.parseInt(qty);

                if (id && qty) {
                    let index = this.getItemIndex(id);

                    if (index) {
                        this.items[index].qty = qty;
                        sessionStorage.setItem('cart', JSON.stringify(this.items));
                    } else {
                        console.log('Unknown id.');
                    }
                } else {
                    console.log('Invalid id or qty.');
                }
            }

            count() {
                return this.items.length;
            }

            notify() {
                document.querySelectorAll('.cart_item_count').forEach(el => {
                    el.textContent = this.items.length > 0 ? this.items.length : '';
                });
            }

            parseInt(n) {
                return parseInt(n) > 0 ? parseInt(n) : null;
            }
        }

        window.Cart = new Cart_class();
    </script>
    <!-- End Cart -->

    @yield('content')
</body>
</html>