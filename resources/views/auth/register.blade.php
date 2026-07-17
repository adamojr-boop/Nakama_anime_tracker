<form action="{{ route('register') }}" method="POST">
    @csrf
    <div>
        <label>Nome</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <span>{{ $message }}</span> @enderror
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>Conferma Password</label>
        <input type="password" name="password_confirmation" required>
    </div>
    <button type="submit">Registrati</button>
</form>