@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Personal Access Tokens</h4>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('token'))
                        <div class="alert alert-success" role="alert">
                            <h5 class="alert-heading">Token Created Successfully!</h5>
                            <p>Make sure to copy your new personal access token. You won't be able to see it again!</p>
                            <hr>
                            <div class="input-group">
                                <input type="text" class="form-control" id="new-token" value="{{ session('token') }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                    <i class="bi bi-clipboard"></i> Copy
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Create Token Form -->
                    <div class="mb-4">
                        <h5>Create New Token</h5>
                        <form action="{{ route('tokens.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           placeholder="Token Name (e.g., Mobile App, API Client)"
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-plus-circle"></i> Create Token
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <!-- Tokens List -->
                    <div>
                        <h5>Your Tokens</h5>
                        @if($tokens->isEmpty())
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You haven't created any personal access tokens yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Created</th>
                                            <th>Last Used</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tokens as $token)
                                            <tr>
                                                <td>
                                                    <strong>{{ $token->name }}</strong>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $token->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if($token->last_used_at)
                                                        <small class="text-muted">
                                                            {{ $token->last_used_at->diffForHumans() }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">Never</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('tokens.destroy', $token->id) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this token? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Token Scopes (Optional) -->
                    @if(isset($scopes) && !empty($scopes))
                    <div class="mt-4">
                        <h5>Available Scopes</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="small mb-2">Scopes let you specify what permissions the token has:</p>
                                <ul class="small mb-0">
                                    @foreach($scopes as $scope)
                                        <li><code>{{ $scope }}</code></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Info Section -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-shield-lock"></i> About Personal Access Tokens
                                </h6>
                                <p class="card-text small mb-0">
                                    Personal access tokens function like ordinary OAuth access tokens.
                                    They can be used to authenticate to your API on behalf of your user account.
                                    Treat them like passwords and keep them secret!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToken() {
    const tokenInput = document.getElementById('new-token');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(tokenInput.value).then(() => {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}
</script>
@endsection
