<div>
    <h1>My Profile</h1>
    <p>Email: {{ auth()->user()->email }}</p>
    <p>Profile Completeness: {{ $this->profileCompleteness }}%</p>
</div>
