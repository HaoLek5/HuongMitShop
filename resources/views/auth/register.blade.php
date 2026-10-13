<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng ký - Hương Mít Shop</title>
</head>

<body>

    <h1>Đăng ký tài khoản</h1>

    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name">
                Họ tên
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                maxlength="100"
                required
            >
        </div>

        <br>

        <div>
            <label for="phone">
                Số điện thoại
            </label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                maxlength="20"
                required
            >
        </div>

        <br>

        <div>
            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                maxlength="255"
            >
        </div>

        <br>

        <div>
            <label for="password">
                Mật khẩu
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="new-password"
            >
        </div>

        <br>

        <div>
            <label for="password_confirmation">
                Xác nhận mật khẩu
            </label>

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                autocomplete="new-password"
            >
        </div>

        <br>

        <div>
            <label for="pin_code">
                PIN code
            </label>

            <input
                type="password"
                id="pin_code"
                name="pin_code"
                inputmode="numeric"
                maxlength="6"
            >

            <small>
                Không bắt buộc. PIN gồm 6 chữ số.
            </small>
        </div>

        <br>

        <div>
            <label for="pin_code_confirmation">
                Xác nhận PIN code
            </label>

            <input
                type="password"
                id="pin_code_confirmation"
                name="pin_code_confirmation"
                inputmode="numeric"
                maxlength="6"
            >
        </div>

        <br>

        <button type="submit">
            Đăng ký
        </button>
    </form>

    <p>
        Đã có tài khoản?
        <a href="{{ route('login') }}">
            Đăng nhập
        </a>
    </p>

</body>
</html>