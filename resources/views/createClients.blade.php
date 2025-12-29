@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">OAuth Clients</h4>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('client'))
                        <div class="alert alert-success" role="alert">
                            <h5 class="alert-heading">Client Created Successfully!</h5>
                            <p>Here are your client credentials. Make sure to copy your client secret now. You won't be able to see it again!</p>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label"><strong>Client ID:</strong></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="client-id" value="{{ session('client')['id'] }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('client-id')">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label"><strong>Client Secret:</strong></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="client-secret" value="{{ session('client')['secret'] }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('client-secret')">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Create Client Form -->
                    <div class="mb-4">
                        <h5>Create New OAuth Client</h5>
                        <form action="{{ route('clients-store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       placeholder="My Application"
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">A descriptive name for your application</small>
                            </div>

                            <div class="mb-3">
                                <label for="redirect" class="form-label">Redirect URL <span class="text-danger">*</span></label>
                                <input type="url"
                                       class="form-control @error('redirect') is-invalid @enderror"
                                       id="redirect"
                                       name="redirect"
                                       placeholder="https://example.com/callback"
                                       value="{{ old('redirect') }}"
                                       required>
                                @error('redirect')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">The URL where users will be redirected after authorization</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="confidential"
                                           id="confidential"
                                           value="1"
                                           {{ old('confidential') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="confidential">
                                        Confidential Client
                                    </label>
                                </div>
                                <small class="text-muted">Check this if your application can securely store the client secret (server-side apps)</small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create Client
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Clients List -->
                    <div>
                        <h5>Your OAuth Clients</h5>
                        @if($clients->isEmpty())
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You haven't created any OAuth clients yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Client ID</th>
                                            <th>Redirect URL</th>
                                            <th>Created</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clients as $client)
                                            <tr>
                                                <td>
                                                    <strong>{{ $client->name }}</strong>
                                                    @if($client->secret)
                                                        <br><span class="badge bg-secondary">Confidential</span>
                                                    @else
                                                        <br><span class="badge bg-info text-dark">Public</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <code class="small">{{ Str::limit($client->id) }}</code>
                                                    <button class="btn btn-sm btn-link p-0"
                                                            onclick="copyText('{{ $client->id }}')"
                                                            title="Copy Client ID">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ Str::limit($client->redirect_uri, 40) }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $client->created_at->format('M d, Y') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editModal{{ $client->id }}">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-warning me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#secretModal{{ $client->id }}">
                                                        <i class="bi bi-key"></i> New Secret
                                                    </button>
                                                    <form action="{{ route('clients-destroy', $client->id) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this client? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal{{ $client->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('clients-update', $client->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Client</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Client Name</label>
                                                                    <input type="text"
                                                                           class="form-control"
                                                                           name="name"
                                                                           value="{{ $client->name }}"
                                                                           required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Redirect URL</label>
                                                                    <input type="url"
                                                                           class="form-control"
                                                                           name="redirect"
                                                                           value="{{ $client->redirect_uri }}"
                                                                           required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update Client</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Regenerate Secret Modal -->
                                            <div class="modal fade" id="secretModal{{ $client->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('clients-secret', $client->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header bg-warning">
                                                                <h5 class="modal-title">
                                                                    <i class="bi bi-exclamation-triangle"></i> Regenerate Client Secret
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-warning">
                                                                    <strong>Warning!</strong> This will generate a new client secret and invalidate the old one.
                                                                    Any applications using the old secret will stop working.
                                                                </div>
                                                                <p>Are you sure you want to regenerate the secret for <strong>{{ $client->name }}</strong>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-warning">
                                                                    <i class="bi bi-arrow-repeat"></i> Regenerate Secret
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Info Section -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-info-circle"></i> About OAuth Clients
                                </h6>
                                <p class="card-text small mb-2">
                                    OAuth clients allow your applications to authenticate users via OAuth 2.0.
                                    Use confidential clients for server-side applications that can securely store secrets,
                                    and public clients for single-page or mobile applications.
                                </p>
                                <p class="card-text small mb-0">
                                    <strong>Important:</strong> Keep your client secrets secure and never expose them in client-side code or public repositories.
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
function copyToClipboard(elementId) {
    const input = document.getElementById(elementId);
    input.select();
    input.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(input.value).then(() => {
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

function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i>';

        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 1500);
    });
}
</script>
@endsection
