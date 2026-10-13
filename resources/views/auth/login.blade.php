<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập - Hương Mít Shop</title>
</head>

<body>

    <h1>Đăng nhập Hương Mít Shop</h1>

    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
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
            >
        </div>

        <br>

        <button type="submit">
            Đăng nhập
        </button>
    </form>

</body>
</html>