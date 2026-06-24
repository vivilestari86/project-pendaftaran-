<h2>Detail User</h2>

<label>Nama</label>

<input
class="form-control"
value="{{$user->name}}"
readonly
>

<label>Email</label>

<input
class="form-control"
value="{{$user->email}}"
readonly
>

<label>Role</label>

<input
class="form-control"
value="{{$user->role}}"
readonly
>

<label>Status</label>

<input
class="form-control"
value="{{$user->status}}"
readonly
>

<hr>

<h4>Ubah Password</h4>

<form
action="{{route('manage-user.password',$user->id)}}"
method="POST"
>

@csrf

<input
type="password"
name="password"
class="form-control"
placeholder="Password Baru"
>

<br>

<input
type="password"
name="password_confirmation"
class="form-control"
placeholder="Konfirmasi Password"
>

<br>

<button class="btn btn-primary">

Simpan

</button>

</form>