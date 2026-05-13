@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<p>Hãy kiểm tra email để xác minh tài khoản. Nếu bạn chưa nhận được email, bạn có thể yêu cầu gửi lại:</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">Gửi lại email xác minh</button>
</form>