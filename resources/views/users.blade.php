@extends('layouts.real')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="my-4">Users Management</h2>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Ward</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->role }}</td>
                            <td>Ward {{ $user->ward }}</td>
                            <td>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    Edit
                                </button>

                                <!-- Modal for Editing User -->
                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User: {{ $user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('users.update', $user->id) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <!-- Name -->
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                                    </div>

                                                    <!-- Phone -->
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                                                    </div>
                                                    <!-- Role -->
                                                    <div class="mb-3">
                                                        <label for="role" class="form-label">Role</label>
                                                        <select id="role" name="role" class="form-select" required>
                                                            <option value="" disabled {{ old('role', $user->role) ? '' : 'selected' }}>Select a role</option>
                                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                                            <option value="technician" {{ old('role', $user->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                                            <option value="resident" {{ old('role', $user->role) == 'resident' ? 'selected' : '' }}>Resident</option>
                                                        </select>
                                                    </div>

                                                    <!-- Ward -->
                                                    <div class="mb-3">
                                                        <label for="ward" class="form-label">Ward</label>
                                                        <select id="ward" name="ward" class="form-select" required>
                                                            <option value="" disabled {{ old('ward', $user->ward) ? '' : 'selected' }}>Select a ward</option>
                                                            @for ($i = 1; $i <= 25; $i++)
                                                                <option value="{{ $i }}" {{ old('ward', $user->ward) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <!-- Submit Button -->
                                                    <button type="submit" class="btn btn-primary">Update User</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
