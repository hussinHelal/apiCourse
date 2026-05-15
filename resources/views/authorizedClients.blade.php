@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Authorized Applications</h4>
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

                    <p class="text-muted mb-4">
                        These are the third-party applications that you have authorized to access your account.
                    </p>

                    @if($tokens->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You haven't authorized any applications yet.
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($tokens as $token)
                                <div class="col-md-6">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title mb-1">
                                                        {{ $token->client->name }}
                                                    </h5>
                                                    @if($token->client->redirect)
                                                        <small class="text-muted">
                                                            <i class="bi bi-link-45deg"></i>
                                                            {{ Str::limit($token->client->redirect, 40) }}
                                                        </small>
                                                    @endif
                                                </div>
                                                @if($token->revoked)
                                                    <span class="badge bg-secondary">Revoked</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-calendar-check"></i>
                                                    <strong>Authorized:</strong> {{ $token->created_at->format('M d, Y') }}
                                                </small>
                                                @if($token->expires_at)
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-calendar-x"></i>
                                                        <strong>Expires:</strong>
                                                        @if($token->expires_at->isPast())
                                                            <span class="text-danger">Expired</span>
                                                        @else
                                                            {{ $token->expires_at->format('M d, Y') }}
                                                        @endif
                                                    </small>
                                                @endif
                                            </div>

                                            @if($token->scopes && count($token->scopes) > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted d-block mb-1">
                                                        <strong>Permissions:</strong>
                                                    </small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($token->scopes as $scope)
                                                            <span class="badge bg-light text-dark border">
                                                                {{ $scope }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if(!$token->revoked)
                                                <form action="{{ route('authorizedClients.destroy', $token->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Are you sure you want to revoke access for this application?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                        <i class="bi bi-x-circle"></i> Revoke Access
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Info Section -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-info-circle"></i> About Authorized Applications
                                </h6>
                                <p class="card-text small mb-2">
                                    When you authorize a third-party application, you give it permission to access your account data based on the scopes you've approved.
                                </p>
                                <p class="card-text small mb-0">
                                    You can revoke access at any time. Revoking access will prevent the application from accessing your account, but it won't delete any data the application may have already stored.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
