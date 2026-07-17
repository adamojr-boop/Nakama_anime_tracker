<form action="{{ route('login') }}" method="POST">
    @csrf
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <span>{{ $message }}</span> @enderror
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Accedi</button>
</form>
<p>Non hai un account? <a href="{{ route('register') }}">Registrati</a></p>