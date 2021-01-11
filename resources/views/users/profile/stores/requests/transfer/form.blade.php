@section('page-title', $user->name.' - Store Transfer')

<div class="row">
    <div class="col">
        <h4 class="border-bottom mb-1 mt-3 pb-2">Store Transfer</h4>
        @if (session('message_type'))
            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
        @endif

        <form id="transfer_store" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Store Name</label>
                <input type="text" readonly class="form-control" value="{{ $store->name }}">
            </div>

            <div class="mb-3">
                <label>Store Contact Number</label>
                <input type="text" readonly class="form-control" value="{{ $store->contact_number }}">
            </div>

            <div class="mb-3">
                <label>Store Address</label>
                <input type="text" readonly class="form-control" value="{{ $store->address }}">
            </div>

            <div class="mb-3">
                <label>Store Map Address</label>
                <input type="text" readonly class="form-control" value="{{ $store->map_address }}">
            </div>

            <div class="mb-3">
                <label>Store Map Coordinates</label>
                <input type="text" readonly class="form-control" value="{{ $store->map_coordinates }}">
            </div>

            <div class="mb-3">
                <label>User Name</label>
                <input type="text" id="user_name" readonly class="form-control">
            </div>

            <div class="mb-3">
                <label>User Email</label>
                <div class="input-group">
                    <input type="email" name="email" id="user_email" class="form-control" placeholder="email address">
                    <button type="button" class="btn btn-primary" id="find_user">Find User</button>
                </div>
                <span class="text-danger small" id="find_user_error"></span>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Attachment</label>
                <input type="file" name="attachment" class="form-control form-control-file">
                @error('attachment')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 text-end">
                <button type="submit" class="btn btn-primary d-none" id="form_submit">Transfer Store</button>
            </div>
        </form>
    </div>
</div>

<script>
    var btnFindUser = document.getElementById('find_user');
    var userName = document.getElementById('user_name');
    var userEmail = document.getElementById('user_email');
    var findUserError = document.getElementById('find_user_error');
    var btnFormSubmit = document.getElementById('form_submit');

    userEmail.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnFindUser.click();
        }
    });

    btnFindUser.addEventListener('click', function (e) {
        e.preventDefault();

        axios.post('{{ route('user.find-email') }}', {
                email: userEmail.value
            })
            .then(function (response) {
                findUserError.innerText = '';
                userName.value = response.data.name;
                btnFormSubmit.classList.remove('d-none');
            })
            .catch(function (error) {
                findUserError.innerText = error.response.data;
                userName.value = '';
                btnFormSubmit.classList.add('d-none');
            });
    });
</script>