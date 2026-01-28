<div class="card mt-4">
<div class="card-body">

<h5 class="mb-3">Role & Module Access</h5>

<table class="table table-bordered table-striped">
<thead class="thead-light">
<tr>
    <th width="25%">Role Name</th>
    <th width="55%">Assigned Modules</th>
    {{-- <th width="20%">Action</th> --}}
</tr>
</thead>

<tbody>
@forelse($roles as $role)
<tr>
    <td>{{ $role->name }}</td>

    <td>
        @forelse($role->modules as $module)
            <span class="badge badge-info mr-1 mb-1">
                {{ $module->name }}
            </span>
        @empty
            <span class="text-muted">No Modules</span>
        @endforelse
    </td>

    <td>
        {{-- <form action="{{ route('admin.user.destroy', $role->id) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete this role?');"
              style="display:inline;">
            @csrf
            @method('DELETE')

            <button class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i>
            </button>
        </form> --}}
    </td>
</tr>
@empty
<tr>
    <td colspan="3" class="text-center">No Roles Found</td>
</tr>
@endforelse
</tbody>
</table>

</div>
</div>
